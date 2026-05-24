<?php

namespace App\Http\Controllers;

use App\Models\DefenseSchedule;
use App\Models\Project;
use App\Models\Committee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DefenseScheduleController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $schedules = DefenseSchedule::with(['project.supervisor','project.students'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date,   fn($q) => $q->where('scheduled_date', $request->date))
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->paginate(15);

        $todayDefenses = DefenseSchedule::with(['project.students','project.supervisor'])
                                        ->today()->get();

        return view('schedules.index', compact('schedules','todayDefenses'));
    }

    public function create()
    {
        $this->authorize('manage-schedule');
        $projects = Project::whereIn('status', ['approved','in_progress','submitted'])
                           ->with('students','supervisor')
                           ->get();
        $committees = Committee::with('project')->get();
        return view('schedules.create', compact('projects','committees'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-schedule');

        $data = $request->validate([
            'project_id'      => 'required|exists:projects,id',
            'committee_id'    => 'nullable|exists:committees,id',
            'scheduled_date'  => 'required|date|after_or_equal:today',
            'scheduled_time'  => 'required|date_format:H:i',
            'location'        => 'nullable|string|max:255',
            'room'            => 'nullable|string|max:100',
            'duration_minutes'=> 'nullable|integer|min:30|max:240',
            'notes'           => 'nullable|string',
        ]);

        $data['status']     = 'scheduled';
        $data['created_by'] = Auth::id();

        $schedule = DefenseSchedule::create($data);

        Project::find($data['project_id'])->update([
            'defense_date'     => $data['scheduled_date'],
            'defense_time'     => $data['scheduled_time'],
            'defense_location' => $data['location'] ?? null,
            'defense_room'     => $data['room'] ?? null,
        ]);

        return redirect()->route('schedules.index')
                         ->with('success', 'تم جدولة موعد المناقشة بنجاح');
    }

    public function show(DefenseSchedule $schedule)
    {
        $schedule->load(['project.students','project.supervisor','committee.members','createdBy']);
        return view('schedules.show', compact('schedule'));
    }

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

        DefenseSchedule::create([
            'project_id'       => $schedule->project_id,
            'committee_id'     => $schedule->committee_id,
            'scheduled_date'   => $request->new_scheduled_date,
            'scheduled_time'   => $request->new_scheduled_time,
            'location'         => $schedule->location,
            'room'             => $schedule->room,
            'duration_minutes' => $schedule->duration_minutes,
            'status'           => 'scheduled',
            'created_by'       => Auth::id(),
            'notes'            => 'أُعيد جدولته من تاريخ ' . $schedule->scheduled_date->format('d/m/Y'),
        ]);

        return back()->with('success', 'تم تأجيل المناقشة وجدولة موعد جديد');
    }

    public function cancel(Request $request, DefenseSchedule $schedule)
    {
        $this->authorize('manage-schedule');
        $request->validate(['postpone_reason' => 'required|string']);
        $schedule->update(['status' => 'cancelled', 'postpone_reason' => $request->postpone_reason]);
        return back()->with('success', 'تم إلغاء المناقشة');
    }

    public function markCompleted(DefenseSchedule $schedule)
    {
        $this->authorize('manage-schedule');
        $schedule->update(['status' => 'completed']);
        return back()->with('success', 'تم تسجيل اكتمال المناقشة');
    }
}