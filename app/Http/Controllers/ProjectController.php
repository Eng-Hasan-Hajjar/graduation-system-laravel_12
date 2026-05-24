<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectIdea;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // قائمة المشاريع
    public function index(Request $request)
    {
        $query = Project::with(['supervisor','students','department','semester'])
                        ->when($request->status,       fn($q) => $q->where('status', $request->status))
                        ->when($request->type,         fn($q) => $q->byType($request->type))
                        ->when($request->semester_id,  fn($q) => $q->where('semester_id', $request->semester_id))
                        ->when($request->department_id,fn($q) => $q->where('department_id', $request->department_id))
                        ->when($request->search, fn($q) =>
                            $q->where(fn($q2) =>
                                $q2->where('title_ar', 'like', "%{$request->search}%")
                                   ->orWhere('title_en', 'like', "%{$request->search}%")
                                   ->orWhere('project_number', 'like', "%{$request->search}%")
                            )
                        )
                        ->when($request->is_discussed, fn($q) => $q->where('is_discussed', $request->is_discussed == '1'))
                        ->when($request->is_archived,  fn($q) => $q->where('is_archived', $request->is_archived == '1'));

        // للطلاب: عرض مشاريعهم فقط
        if (Auth::user()->isStudent()) {
            $query->whereHas('students', fn($q) => $q->where('student_id', Auth::id()));
        }

        // للمشرفين: عرض مشاريعهم
        if (Auth::user()->isSupervisor()) {
            $query->where(fn($q) =>
                $q->where('supervisor_id', Auth::id())
                  ->orWhere('co_supervisor_id', Auth::id())
            );
        }

        $projects    = $query->latest()->paginate(12)->withQueryString();
        $semesters   = Semester::orderByDesc('id')->get();
        $departments = Department::where('is_active', true)->get();

        return view('projects.index', compact('projects', 'semesters', 'departments'));
    }

    // إنشاء مشروع جديد
    public function create()
    {
        $this->authorize('create-project');

        $ideas       = ProjectIdea::where('status', 'approved')->get();
        $departments = Department::where('is_active', true)->get();
        $semesters   = Semester::where('is_active', true)->get();
        $supervisors = User::where('role', 'supervisor')->where('status', 'active')->get();

        return view('projects.create', compact('ideas', 'departments', 'semesters', 'supervisors'));
    }

    // حفظ مشروع جديد
    public function store(Request $request)
    {
        $this->authorize('create-project');

        $validated = $request->validate([
            'title_ar'           => 'required|string|max:255',
            'title_en'           => 'nullable|string|max:255',
            'description_ar'     => 'required|string',
            'description_en'     => 'nullable|string',
            'objectives_ar'      => 'nullable|string',
            'expected_outcomes_ar' => 'nullable|string',
            'methodology_ar'     => 'nullable|string',
            'project_type'       => 'required|in:graduation,semester,year4,research',
            'academic_year_level'=> 'nullable|integer|min:1|max:5',
            'department_id'      => 'required|exists:departments,id',
            'semester_id'        => 'required|exists:semesters,id',
            'supervisor_id'      => 'nullable|exists:users,id',
            'project_idea_id'    => 'nullable|exists:project_ideas,id',
            'tools'              => 'nullable|array',
            'student_ids'        => 'required|array|min:1|max:5',
            'student_ids.*'      => 'exists:users,id',
            'start_date'         => 'nullable|date',
            'expected_end_date'  => 'nullable|date|after:start_date',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $validated['project_number'] = Project::generateProjectNumber($validated['project_type']);
            $validated['created_by']     = Auth::id();
            $validated['registration_date'] = now()->toDateString();
            $validated['status']         = Auth::user()->isAdmin() ? 'approved' : 'pending';

            $project = Project::create($validated);

            // إضافة الطلاب
            $students = collect($request->student_ids)->mapWithKeys(fn($id, $i) => [
                $id => ['role' => $i === 0 ? 'leader' : 'member', 'joined_at' => now()]
            ]);
            $project->students()->attach($students);

            // تحديث حالة فكرة المشروع
            if ($request->project_idea_id) {
                ProjectIdea::find($request->project_idea_id)?->update(['status' => 'taken']);
            }

            $this->logActivity('created', $project, __('log.project_created'));
        });

        return redirect()->route('projects.index')
                         ->with('success', __('messages.project_created'));
    }

    // عرض تفاصيل المشروع
    public function show(Project $project)
    {
        $project->load([
            'supervisor', 'coSupervisor', 'students',
            'department.college.university',
            'semester.academicYear',
            'milestones', 'reports.submittedBy', 'files',
            'committees.members', 'defenseSchedules',
            'evaluations.student', 'idea',
        ]);

        return view('projects.show', compact('project'));
    }

    // تعديل المشروع
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $departments = Department::where('is_active', true)->get();
        $semesters   = Semester::where('is_active', true)->get();
        $supervisors = User::where('role', 'supervisor')->where('status', 'active')->get();
        $students    = User::where('role', 'student')
                           ->where('department_id', $project->department_id)
                           ->get();

        return view('projects.edit', compact('project','departments','semesters','supervisors','students'));
    }

    // حفظ التعديلات
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title_ar'            => 'required|string|max:255',
            'title_en'            => 'nullable|string|max:255',
            'description_ar'      => 'required|string',
            'objectives_ar'       => 'nullable|string',
            'expected_outcomes_ar'=> 'nullable|string',
            'methodology_ar'      => 'nullable|string',
            'supervisor_id'       => 'nullable|exists:users,id',
            'co_supervisor_id'    => 'nullable|exists:users,id',
            'start_date'          => 'nullable|date',
            'expected_end_date'   => 'nullable|date',
            'defense_date'        => 'nullable|date',
            'defense_time'        => 'nullable|date_format:H:i',
            'defense_location'    => 'nullable|string|max:255',
            'tools'               => 'nullable|array',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'supervisor_notes'    => 'nullable|string',
        ]);

        $project->update($validated);
        $this->logActivity('updated', $project, __('log.project_updated'));

        return back()->with('success', __('messages.project_updated'));
    }

    // اعتماد المشروع (Admin / Coordinator)
    public function approve(Request $request, Project $project)
    {
        $this->authorize('approve-project');

        abort_if($project->status !== 'pending', 403, __('messages.already_processed'));

        $project->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->logActivity('approved', $project, __('log.project_approved'));

        // إرسال إشعار للطلاب والمشرف
        // event(new ProjectApproved($project));

        return back()->with('success', __('messages.project_approved'));
    }

    // رفض المشروع
    public function reject(Request $request, Project $project)
    {
        $this->authorize('approve-project');

        $request->validate(['rejection_reason' => 'required|string|min:10']);

        $project->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $this->logActivity('rejected', $project, __('log.project_rejected'));

        return back()->with('success', __('messages.project_rejected'));
    }

    // تأكيد المناقشة (بعد انتهاء المناقشة)
    public function markDiscussed(Request $request, Project $project)
    {
        $this->authorize('mark-discussed', $project);

        $request->validate([
            'final_grade'       => 'required|numeric|min:0|max:100',
            'grade_letter'      => 'required|in:A+,A,B+,B,C+,C,D+,D,F',
            'committee_notes_ar'=> 'nullable|string',
            'actual_defense_date'=> 'nullable|date',
        ]);

        $project->update([
            'is_discussed'       => true,
            'discussed_at'       => now(),
            'status'             => 'defended',
            'final_grade'        => $request->final_grade,
            'grade_letter'       => $request->grade_letter,
            'committee_notes_ar' => $request->committee_notes_ar,
            'actual_defense_date'=> $request->actual_defense_date ?? now()->toDateString(),
        ]);

        $this->logActivity('discussed', $project, __('log.project_discussed'));

        return back()->with('success', __('messages.project_discussed'));
    }

    // أرشفة المشروع
    public function archive(Request $request, Project $project)
    {
        $this->authorize('archive-project');

        $project->update([
            'is_archived'   => true,
            'archived_at'   => now(),
            'archive_notes' => $request->archive_notes,
            'status'        => 'archived',
        ]);

        $this->logActivity('archived', $project, __('log.project_archived'));

        return back()->with('success', __('messages.project_archived'));
    }

    // المشاريع المؤرشفة
    public function archived(Request $request)
    {
        $projects = Project::where('is_archived', true)
                           ->with(['supervisor','students','semester'])
                           ->when($request->search, fn($q) =>
                               $q->where('title_ar', 'like', "%{$request->search}%")
                           )
                           ->when($request->type, fn($q) => $q->byType($request->type))
                           ->paginate(15);

        return view('projects.archived', compact('projects'));
    }

    // حذف المشروع
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        $this->logActivity('deleted', $project, __('log.project_deleted'));

        return redirect()->route('projects.index')->with('success', __('messages.project_deleted'));
    }

    // ─── Helper ───────────────────────────────────────────────────────────────
    private function logActivity(string $action, Project $project, string $desc): void
    {
        \App\Models\ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'model_type'  => 'Project',
            'model_id'    => $project->id,
            'description_ar' => $desc,
            'ip_address'  => request()->ip(),
        ]);
    }
}