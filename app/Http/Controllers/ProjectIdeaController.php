<?php

namespace App\Http\Controllers;

use App\Models\ProjectIdea;
use App\Models\Department;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ─── Project Idea Controller ─────────────────────────────────────────────────
class ProjectIdeaController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $ideas = ProjectIdea::with(['proposedBy','department','semester'])
            ->when($request->status,       fn($q) => $q->where('status', $request->status))
            ->when($request->type,         fn($q) => $q->where('project_type', $request->type))
            ->when($request->department_id,fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('title_ar', 'like', "%{$request->search}%")
                       ->orWhere('title_en', 'like', "%{$request->search}%")
                )
            )
            ->when(Auth::user()->isSupervisor(),
                fn($q) => $q->where('proposed_by', Auth::id())
            )
            ->latest()
            ->paginate(12);

        $departments = Department::where('is_active', true)->get();

        return view('ideas.index', compact('ideas', 'departments'));
    }

    public function create()
    {
        $this->authorize('create-idea');
        $departments = Department::where('is_active', true)->get();
        $semesters   = Semester::where('is_active', true)->get();
        return view('ideas.create', compact('departments', 'semesters'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-idea');

        $data = $request->validate([
            'title_ar'           => 'required|string|max:255',
            'title_en'           => 'nullable|string|max:255',
            'description_ar'     => 'required|string',
            'description_en'     => 'nullable|string',
            'objectives_ar'      => 'nullable|string',
            'expected_outcomes_ar' => 'nullable|string',
            'project_type'       => 'required|in:graduation,semester,year4,research',
            'category_ar'        => 'nullable|string|max:100',
            'department_id'      => 'required|exists:departments,id',
            'semester_id'        => 'nullable|exists:semesters,id',
            'tools'              => 'nullable|array',
            'min_students'       => 'nullable|integer|min:1',
            'max_students'       => 'nullable|integer|min:1|max:5',
            'priority'           => 'nullable|in:low,medium,high',
        ]);

        $data['proposed_by'] = Auth::id();
        $data['status']      = Auth::user()->isAdmin() ? 'approved' : 'pending';

        ProjectIdea::create($data);

        return redirect()->route('ideas.index')->with('success', __('messages.idea_created'));
    }

    public function show(ProjectIdea $idea)
    {
        $idea->load(['proposedBy','department.college','semester','projects.students']);
        return view('ideas.show', compact('idea'));
    }

    public function edit(ProjectIdea $idea)
    {
        $this->authorize('update', $idea);
        $departments = Department::where('is_active', true)->get();
        $semesters   = Semester::where('is_active', true)->get();
        return view('ideas.edit', compact('idea', 'departments', 'semesters'));
    }

    public function update(Request $request, ProjectIdea $idea)
    {
        $this->authorize('update', $idea);
        $data = $request->validate([
            'title_ar'       => 'required|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description_ar' => 'required|string',
            'tools'          => 'nullable|array',
            'min_students'   => 'nullable|integer|min:1',
            'max_students'   => 'nullable|integer|min:1|max:5',
        ]);
        $idea->update($data);
        return back()->with('success', __('messages.idea_updated'));
    }

    public function approve(ProjectIdea $idea)
    {
        $this->authorize('approve-idea');
        $idea->update(['status' => 'approved', 'approved_by' => Auth::id(), 'approved_at' => now()]);
        return back()->with('success', __('messages.idea_approved'));
    }

    public function reject(Request $request, ProjectIdea $idea)
    {
        $this->authorize('approve-idea');
        $request->validate(['rejection_reason' => 'required|string']);
        $idea->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
        return back()->with('success', __('messages.idea_rejected'));
    }

    public function destroy(ProjectIdea $idea)
    {
        $this->authorize('delete', $idea);
        $idea->delete();
        return redirect()->route('ideas.index')->with('success', __('messages.idea_deleted'));
    }
}