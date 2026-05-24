<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\DefenseSchedule;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ─── Committee Controller ─────────────────────────────────────────────────────
class CommitteeController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index()
    {
        $committees = Committee::with(['project.supervisor', 'members', 'project.students'])
                               ->latest()
                               ->paginate(15);
        return view('committees.index', compact('committees'));
    }

    public function create(Request $request)
    {
        $this->authorize('manage-committee');
        $projects = Project::whereIn('status', ['approved','in_progress','submitted'])
                           ->with('students','supervisor')
                           ->get();
        $staff = User::whereIn('role', ['supervisor','committee_member'])
                     ->where('status', 'active')
                     ->get();
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        return view('committees.create', compact('projects', 'staff', 'selectedProject'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-committee');

        $data = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'name_ar'      => 'required|string|max:255',
            'scheduled_at' => 'nullable|date',
            'location'     => 'nullable|string|max:255',
            'room'         => 'nullable|string|max:100',
            'member_ids'   => 'required|array|min:2',
            'member_ids.*' => 'exists:users,id',
            'member_roles' => 'nullable|array',
        ]);

        $committee = Committee::create([
            'project_id'   => $data['project_id'],
            'name_ar'      => $data['name_ar'],
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'location'     => $data['location'] ?? null,
            'room'         => $data['room'] ?? null,
        ]);

        // إضافة الأعضاء مع أدوارهم
        $members = collect($data['member_ids'])->mapWithKeys(function ($id) use ($request) {
            $role = $request->member_roles[$id] ?? 'member';
            return [$id => ['role' => $role]];
        });
        $committee->members()->attach($members);

        return redirect()->route('committees.show', $committee)
                         ->with('success', __('messages.committee_created'));
    }

    public function show(Committee $committee)
    {
        $committee->load([
            'project.students', 'project.supervisor',
            'project.department', 'members',
        ]);
        return view('committees.show', compact('committee'));
    }

    // تأكيد اكتمال المناقشة من اللجنة
    public function markCompleted(Request $request, Committee $committee)
    {
        $this->authorize('manage-committee');

        $request->validate([
            'actual_start_at' => 'nullable|date',
            'actual_end_at'   => 'nullable|date',
            'notes_ar'        => 'nullable|string',
        ]);

        $committee->update([
            'is_completed'    => true,
            'completed_at'    => now(),
            'actual_start_at' => $request->actual_start_at,
            'actual_end_at'   => $request->actual_end_at,
            'notes_ar'        => $request->notes_ar,
        ]);

        return back()->with('success', __('messages.committee_completed'));
    }

    public function destroy(Committee $committee)
    {
        $this->authorize('manage-committee');
        $committee->delete();
        return redirect()->route('committees.index')->with('success', __('messages.committee_deleted'));
    }
}

// ─── Defense Schedule Controller ──────────────────────────────────────────────
class DefenseScheduleController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $schedules = DefenseSchedule::with(['project.supervisor', 'project.students'])
            ->when($request->status,   fn($q) => $q->where('status', $request->status))
            ->when($request->date,     fn($q) => $q->where('scheduled_date', $request->date))
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->paginate(15);

        $todayDefenses = DefenseSchedule::with(['project.students','project.supervisor'])
                                        ->today()->get();

        return view('schedules.index', compact('schedules', 'todayDefenses'));
    }

    public function create(Request $request)
    {
        $this->authorize('manage-schedule');
        $projects = Project::whereIn('status', ['approved','in_progress','submitted'])
                           ->with('students','supervisor')
                           ->get();
        $committees = \App\Models\Committee::with('project')->get();

        return view('schedules.create', compact('projects', 'committees'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-schedule');

        $data = $request->validate([
            'project_id'     => 'required|exists:projects,id',
            'committee_id'   => 'nullable|exists:committees,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
            'location'       => 'nullable|string|max:255',
            'room'           => 'nullable|string|max:100',
            'duration_minutes'=> 'nullable|integer|min:30|max:240',
            'notes'          => 'nullable|string',
        ]);

        $data['status']     = 'scheduled';
        $data['created_by'] = Auth::id();

        $schedule = DefenseSchedule::create($data);

        // تحديث تاريخ المناقشة في المشروع
        Project::find($data['project_id'])->update([
            'defense_date'     => $data['scheduled_date'],
            'defense_time'     => $data['scheduled_time'],
            'defense_location' => $data['location'],
            'defense_room'     => $data['room'],
        ]);

        return redirect()->route('schedules.index')
                         ->with('success', __('messages.schedule_created'));
    }

    public function show(DefenseSchedule $schedule)
    {
        $schedule->load(['project.students', 'project.supervisor', 'committee.members', 'createdBy']);
        return view('schedules.show', compact('schedule'));
    }

    // تأجيل المناقشة
    public function postpone(Request $request, DefenseSchedule $schedule)
    {
        $this->authorize('manage-schedule');

        $request->validate([
            'postpone_reason'    => 'required|string',
            'new_scheduled_date' => 'required|date|after:today',
            'new_scheduled_time' => 'required|date_format:H:i',
        ]);

        $schedule->update([
            'status'             => 'postponed',
            'postpone_reason'    => $request->postpone_reason,
            'new_scheduled_date' => $request->new_scheduled_date,
            'new_scheduled_time' => $request->new_scheduled_time,
        ]);

        // إنشاء جدول جديد بالتاريخ الجديد
        DefenseSchedule::create([
            'project_id'      => $schedule->project_id,
            'committee_id'    => $schedule->committee_id,
            'scheduled_date'  => $request->new_scheduled_date,
            'scheduled_time'  => $request->new_scheduled_time,
            'location'        => $schedule->location,
            'room'            => $schedule->room,
            'duration_minutes'=> $schedule->duration_minutes,
            'status'          => 'scheduled',
            'created_by'      => Auth::id(),
            'notes'           => __('schedule.rescheduled_from') . ' ' . $schedule->scheduled_date,
        ]);

        return back()->with('success', __('messages.schedule_postponed'));
    }

    // إلغاء المناقشة
    public function cancel(Request $request, DefenseSchedule $schedule)
    {
        $this->authorize('manage-schedule');
        $request->validate(['postpone_reason' => 'required|string']);
        $schedule->update([
            'status'          => 'cancelled',
            'postpone_reason' => $request->postpone_reason,
        ]);
        return back()->with('success', __('messages.schedule_cancelled'));
    }

    // تأكيد اكتمال المناقشة
    public function markCompleted(DefenseSchedule $schedule)
    {
        $this->authorize('manage-schedule');
        $schedule->update(['status' => 'completed']);
        return back()->with('success', __('messages.schedule_completed'));
    }
}