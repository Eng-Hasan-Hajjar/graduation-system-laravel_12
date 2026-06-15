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
        $query = Project::with(['supervisor', 'students', 'department', 'semester'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type, fn($q) => $q->byType($request->type))
            ->when($request->semester_id, fn($q) => $q->where('semester_id', $request->semester_id))
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when(
                $request->search,
                fn($q) =>
                $q->where(
                    fn($q2) =>
                    $q2->where('title_ar', 'like', "%{$request->search}%")
                        ->orWhere('title_en', 'like', "%{$request->search}%")
                        ->orWhere('project_number', 'like', "%{$request->search}%")
                )
            )
            ->when($request->is_discussed, fn($q) => $q->where('is_discussed', $request->is_discussed == '1'))
            ->when($request->is_archived, fn($q) => $q->where('is_archived', $request->is_archived == '1'));

        // للطلاب: عرض مشاريعهم فقط
        if (Auth::user()->isStudent()) {
            $query->whereHas('students', fn($q) => $q->where('student_id', Auth::id()));
        }

        // للمشرفين: عرض مشاريعهم
        if (Auth::user()->isSupervisor()) {
            $query->where(
                fn($q) =>
                $q->where('supervisor_id', Auth::id())
                    ->orWhere('co_supervisor_id', Auth::id())
            );
        }

        $projects = $query->latest()->paginate(12)->withQueryString();
        $semesters = Semester::orderByDesc('id')->get();
        $departments = Department::where('is_active', true)->get();

        return view('projects.index', compact('projects', 'semesters', 'departments'));
    }

    // إنشاء مشروع جديد
    public function create()
    {
        $this->authorize('create', Project::class);

        $departments = Department::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $supervisors = User::where('role', 'supervisor')->where('status', 'active')->get();

        // نجلب كل الطلاب النشطين، وتتم فلترتهم حسب القسم في الواجهة عبر JS
        $students = User::where('role', 'student')->where('status', 'active')->get();

        // اختياري: إن كان لديك جدول أفكار مشاريع معتمدة يمكن ربط المشروع بها
        $projectIdeas = collect();
        if (class_exists(\App\Models\ProjectIdea::class)) {
            $projectIdeas = \App\Models\ProjectIdea::where('status', 'approved')->get();
        }

        return view('projects.create', compact(
            'departments',
            'semesters',
            'supervisors',
            'students',
            'projectIdeas'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'semester_id' => 'required|exists:semesters,id',
            'project_type' => 'nullable|string|max:50',
            'academic_year_level' => 'nullable|integer|min:1|max:6',

            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'objectives_ar' => 'nullable|string',
            'objectives_en' => 'nullable|string',
            'expected_outcomes_ar' => 'nullable|string',
            'expected_outcomes_en' => 'nullable|string',
            'methodology_ar' => 'nullable|string',
            'methodology_en' => 'nullable|string',

            'supervisor_id' => 'required|exists:users,id',
            'co_supervisor_id' => 'nullable|exists:users,id|different:supervisor_id',
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id',

            'tools' => 'nullable|string',
            'technologies' => 'nullable|string',

            'registration_date' => 'nullable|date',
            'start_date' => 'nullable|date|after_or_equal:registration_date',
            'expected_end_date' => 'nullable|date|after_or_equal:start_date',

            'project_idea_id' => 'nullable|exists:project_ideas,id',
        ]);

        // تحويل النصوص المفصولة بفواصل إلى مصفوفات (نفس مشكلة validation.array السابقة)
        $validated['tools'] = $request->filled('tools')
            ? array_map('trim', explode(',', $request->tools))
            : [];

        $validated['technologies'] = $request->filled('technologies')
            ? array_map('trim', explode(',', $request->technologies))
            : [];

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['progress_percentage'] = 0;
        $validated['project_number'] = $this->generateProjectNumber();

        // إزالة الحقل students لأنه ليس عموداً في جدول projects (سيتم ربطه عبر pivot)
        $studentIds = $validated['students'] ?? [];
        unset($validated['students']);

        $project = Project::create($validated);

        if (method_exists($project, 'students') && !empty($studentIds)) {
            $project->students()->sync($studentIds);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', app()->getLocale() === 'ar'
                ? 'تم إنشاء المشروع بنجاح.'
                : 'Project created successfully.');
    }

    /**
     * توليد رقم تسلسلي للمشروع بصيغة GRAD-YYYY-NNNN
     */
    private function generateProjectNumber(): string
    {
        $year = now()->year;
        $count = Project::whereYear('created_at', $year)->count() + 1;

        return sprintf('GRAD-%d-%04d', $year, $count);
    }

    // عرض تفاصيل المشروع
    public function show(Project $project)
    {
        $project->load([
            'supervisor',
            'coSupervisor',
            'students',
            'department.college.university',
            'semester.academicYear',
            'milestones',
            'reports.submittedBy',
            'files',
            'committees.members',
            'defenseSchedules',
            'evaluations.student',
            'idea',
        ]);

        return view('projects.show', compact('project'));
    }

    // تعديل المشروع

    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $departments = Department::where('is_active', true)->get();
        $semesters = Semester::where('is_active', true)->get();
        $supervisors = User::where('role', 'supervisor')->where('status', 'active')->get();
        $students = User::where('role', 'student')
            ->where('department_id', $project->department_id)
            ->get();

        return view('projects.edit', compact(
            'project',
            'departments',
            'semesters',
            'supervisors',
            'students'
        ));
    }
    // حفظ التعديلات
    // حفظ التعديلات
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $isAdmin = in_array(Auth::user()->role ?? '', ['admin', 'coordinator']);

        $rules = [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'semester_id' => 'required|exists:semesters,id',
            'project_type' => 'nullable|string|max:50',
            'academic_year_level' => 'nullable|integer|min:1|max:6',

            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'objectives_ar' => 'nullable|string',
            'objectives_en' => 'nullable|string',
            'expected_outcomes_ar' => 'nullable|string',
            'expected_outcomes_en' => 'nullable|string',
            'methodology_ar' => 'nullable|string',
            'methodology_en' => 'nullable|string',

            'supervisor_id' => 'required|exists:users,id',
            'co_supervisor_id' => 'nullable|exists:users,id|different:supervisor_id',
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id',

            'tools' => 'nullable|string',
            'technologies' => 'nullable|string',

            'registration_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'submission_date' => 'nullable|date',
            'defense_date' => 'nullable|date',
            'defense_time' => 'nullable|date_format:H:i',
            'defense_location' => 'nullable|string|max:255',
            'defense_room' => 'nullable|string|max:255',
            'actual_defense_date' => 'nullable|date',
            'is_discussed' => 'nullable|boolean',
        ];

        // حقول إضافية يراها فقط المسؤول/المنسق (تتطابق مع تابات "الحالة والتقييم" و"الأرشفة" في الواجهة)
        if ($isAdmin) {
            $rules += [
                'status' => 'nullable|in:pending,approved,rejected,in_progress,submitted,defended,archived',
                'final_grade' => 'nullable|numeric|min:0|max:100',
                'grade_letter' => 'nullable|string|max:2',
                'rejection_reason' => 'nullable|string',
                'committee_notes_ar' => 'nullable|string',
                'committee_notes_en' => 'nullable|string',
                'supervisor_notes' => 'nullable|string',
                'progress_percentage' => 'nullable|integer|min:0|max:100',
                'is_archived' => 'nullable|boolean',
                'archived_at' => 'nullable|date',
                'archive_notes' => 'nullable|string',
            ];
        }

        $validated = $request->validate($rules);

        // تحويل النصوص المفصولة بفواصل إلى مصفوفات (tools / technologies)
        $validated['tools'] = $request->filled('tools')
            ? array_map('trim', explode(',', $request->tools))
            : [];

        $validated['technologies'] = $request->filled('technologies')
            ? array_map('trim', explode(',', $request->technologies))
            : [];

        // معالجة checkbox is_discussed (لا يُرسل أصلاً إن لم يكن محدداً)
        $validated['is_discussed'] = $request->boolean('is_discussed');

        if ($isAdmin) {
            $validated['is_archived'] = $request->boolean('is_archived');
        }

        // فصل قائمة الطلاب عن باقي الأعمدة (تُحفظ عبر علاقة Many-to-Many، لا كعمود في جدول projects)
        $studentIds = $validated['students'] ?? [];
        unset($validated['students']);

        $project->update($validated);

        if (method_exists($project, 'students')) {
            $project->students()->sync($studentIds);
        }

        $this->logActivity('updated', $project, __('log.project_updated'));

        return back()->with('success', __('messages.project_updated'));
    }

    // اعتماد المشروع (Admin / Coordinator)
    public function approve(Request $request, Project $project)
    {
        $this->authorize('approve-project');

        abort_if($project->status !== 'pending', 403, __('messages.already_processed'));

        $project->update([
            'status' => 'approved',
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
            'status' => 'rejected',
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
            'final_grade' => 'required|numeric|min:0|max:100',
            'grade_letter' => 'required|in:A+,A,B+,B,C+,C,D+,D,F',
            'committee_notes_ar' => 'nullable|string',
            'actual_defense_date' => 'nullable|date',
        ]);

        $project->update([
            'is_discussed' => true,
            'discussed_at' => now(),
            'status' => 'defended',
            'final_grade' => $request->final_grade,
            'grade_letter' => $request->grade_letter,
            'committee_notes_ar' => $request->committee_notes_ar,
            'actual_defense_date' => $request->actual_defense_date ?? now()->toDateString(),
        ]);

        $this->logActivity('discussed', $project, __('log.project_discussed'));

        return back()->with('success', __('messages.project_discussed'));
    }

    // أرشفة المشروع
    public function archive(Request $request, Project $project)
    {
        $this->authorize('archive-project');

        $project->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archive_notes' => $request->archive_notes,
            'status' => 'archived',
        ]);

        $this->logActivity('archived', $project, __('log.project_archived'));

        return back()->with('success', __('messages.project_archived'));
    }

    // المشاريع المؤرشفة
    public function archived(Request $request)
    {
        $projects = Project::where('is_archived', true)
            ->with(['supervisor', 'students', 'semester'])
            ->when(
                $request->search,
                fn($q) =>
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
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => 'Project',
            'model_id' => $project->id,
            'description_ar' => $desc,
            'ip_address' => request()->ip(),
        ]);
    }
}