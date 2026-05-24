<?php

namespace App\Http\Controllers;

use App\Models\ProjectReport;
use App\Models\ProjectFile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// ─── Report Controller ────────────────────────────────────────────────────────
class ReportController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $reports = ProjectReport::with(['project','submittedBy'])
            ->when(Auth::user()->isStudent(),   fn($q) => $q->where('submitted_by', Auth::id()))
            ->when(Auth::user()->isSupervisor(), fn($q) =>
                $q->whereHas('project', fn($q2) => $q2->where('supervisor_id', Auth::id()))
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return view('reports.index', compact('reports'));
    }

    public function create(Request $request)
    {
        $projects = Auth::user()->isStudent()
            ? Auth::user()->studentProjects()->whereIn('status',['approved','in_progress'])->get()
            : Project::where('supervisor_id', Auth::id())->get();

        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        return view('reports.create', compact('projects', 'selectedProject'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'title_ar'    => 'required|string|max:255',
            'content_ar'  => 'required|string',
            'report_type' => 'required|in:weekly,monthly,milestone,final,other',
            'week_number' => 'nullable|integer|min:1|max:52',
            'report_date' => 'required|date',
            'files.*'     => 'nullable|file|max:20480', // 20MB
        ]);

        $data['submitted_by'] = Auth::id();
        $data['status']       = 'submitted';

        $report = ProjectReport::create($data);

        // رفع الملفات
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('projects/' . $data['project_id'] . '/reports', 'public');
                ProjectFile::create([
                    'project_id'    => $data['project_id'],
                    'uploaded_by'   => Auth::id(),
                    'report_id'     => $report->id,
                    'file_name'     => basename($path),
                    'original_name' => $file->getClientOriginalName(),
                    'file_path'     => $path,
                    'file_type'     => $file->getClientOriginalExtension(),
                    'file_size'     => $file->getSize(),
                    'category'      => 'report',
                ]);
            }
        }

        return redirect()->route('reports.show', $report)->with('success', __('messages.report_submitted'));
    }

    public function show(ProjectReport $report)
    {
        $report->load(['project.supervisor','submittedBy','files','reviewedBy']);
        return view('reports.show', compact('report'));
    }

    // مراجعة التقرير من المشرف
    public function review(Request $request, ProjectReport $report)
    {
        $this->authorize('review-report', $report);

        $request->validate([
            'supervisor_feedback' => 'required|string',
            'status' => 'required|in:reviewed,approved,rejected',
            'grade'  => 'nullable|numeric|min:0|max:100',
        ]);

        $report->update([
            'supervisor_feedback' => $request->supervisor_feedback,
            'status'              => $request->status,
            'grade'               => $request->grade,
            'reviewed_by'         => Auth::id(),
            'reviewed_at'         => now(),
        ]);

        return back()->with('success', __('messages.report_reviewed'));
    }
}

// ─── User Management Controller ───────────────────────────────────────────────
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(fn($r,$n) => Auth::user()->isAdmin() ? $n($r) : abort(403))->except(['profile','updateProfile','changePassword','updatePreferences']);
    }

    public function index(Request $request)
    {
        $users = User::with(['university','department'])
            ->when($request->role,   fn($q) => $q->where('role', $request->role))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('name_ar', 'like', "%{$request->search}%")
                       ->orWhere('email',  'like', "%{$request->search}%")
                )
            )
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $departments = \App\Models\Department::where('is_active',true)->get();
        $colleges    = \App\Models\College::where('is_active',true)->get();
        return view('users.create', compact('departments','colleges'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:8|confirmed',
            'role'          => 'required|in:admin,supervisor,coordinator,committee_member,student',
            'department_id' => 'nullable|exists:departments,id',
            'college_id'    => 'nullable|exists:colleges,id',
            'student_id'    => 'nullable|string|unique:users',
            'employee_id'   => 'nullable|string|unique:users',
            'academic_rank' => 'nullable|string',
            'phone'         => 'nullable|string',
        ]);

        User::create($data);

        return redirect()->route('users.index')->with('success', __('messages.user_created'));
    }

    public function show(User $user)
    {
        $user->load(['department.college','studentProjects','supervisedProjects','committees']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $departments = \App\Models\Department::where('is_active',true)->get();
        return view('users.edit', compact('user','departments'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'email'         => 'required|email|unique:users,email,'.$user->id,
            'role'          => 'required|in:admin,supervisor,coordinator,committee_member,student',
            'department_id' => 'nullable|exists:departments,id',
            'status'        => 'required|in:active,inactive,suspended',
            'academic_rank' => 'nullable|string',
        ]);

        $user->update($data);

        return back()->with('success', __('messages.user_updated'));
    }

    // الملف الشخصي
    public function profile()
    {
        $user = Auth::user()->load(['department.college.university']);
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'phone'   => 'nullable|string',
            'avatar'  => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', __('messages.profile_updated'));
    }

    public function updatePreferences(Request $request)
    {
        Auth::user()->update([
            'lang_preference'  => $request->lang ?? Auth::user()->lang_preference,
            'theme_preference' => $request->theme ?? Auth::user()->theme_preference,
        ]);
        return back();
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|current_password',
            'password'              => 'required|min:8|confirmed',
        ]);

        Auth::user()->update(['password' => $request->password]);

        return back()->with('success', __('messages.password_changed'));
    }

    public function destroy(User $user)
    {
        abort_if($user->id === Auth::id(), 403);
        $user->delete();
        return redirect()->route('users.index')->with('success', __('messages.user_deleted'));
    }
}