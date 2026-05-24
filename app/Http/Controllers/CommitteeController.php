<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                     ->where('status','active')
                     ->get();
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;
        return view('committees.create', compact('projects','staff','selectedProject'));
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

        $members = collect($data['member_ids'])->mapWithKeys(function ($id) use ($request) {
            return [$id => ['role' => $request->member_roles[$id] ?? 'member']];
        });
        $committee->members()->attach($members);

        return redirect()->route('committees.show', $committee)
                         ->with('success', 'تم إنشاء اللجنة بنجاح');
    }

    public function show(Committee $committee)
    {
        $committee->load(['project.students','project.supervisor','project.department','members']);
        return view('committees.show', compact('committee'));
    }

    public function markCompleted(Request $request, Committee $committee)
    {
        $this->authorize('manage-committee');

        $request->validate([
            'notes_ar' => 'nullable|string',
        ]);

        $committee->update([
            'is_completed'    => true,
            'completed_at'    => now(),
            'actual_start_at' => $request->actual_start_at,
            'actual_end_at'   => $request->actual_end_at,
            'notes_ar'        => $request->notes_ar,
        ]);

        return back()->with('success', 'تم تسجيل اكتمال اجتماع اللجنة');
    }

    public function destroy(Committee $committee)
    {
        $this->authorize('manage-committee');
        $committee->delete();
        return redirect()->route('committees.index')->with('success', 'تم حذف اللجنة');
    }
}