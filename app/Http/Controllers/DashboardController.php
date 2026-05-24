<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectIdea;
use App\Models\User;
use App\Models\DefenseSchedule;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        return match($user->role) {
            'admin', 'coordinator' => $this->adminDashboard(),
            'supervisor'           => $this->supervisorDashboard($user),
            'committee_member'     => $this->committeeDashboard($user),
            default                => $this->studentDashboard($user),
        };
    }

    // ─── Admin / Coordinator Dashboard ───────────────────────────────────────
    private function adminDashboard()
    {
        $stats = [
            'total_projects'    => Project::count(),
            'pending_projects'  => Project::where('status', 'pending')->count(),
            'active_projects'   => Project::whereIn('status', ['approved','in_progress'])->count(),
            'defended_projects' => Project::where('is_discussed', true)->count(),
            'archived_projects' => Project::where('is_archived', true)->count(),
            'total_students'    => User::where('role', 'student')->count(),
            'total_supervisors' => User::where('role', 'supervisor')->count(),
            'pending_ideas'     => ProjectIdea::where('status', 'pending')->count(),
        ];

        $recentProjects    = Project::with(['supervisor','students','semester'])
                                    ->latest()->take(8)->get();

        $upcomingDefenses  = DefenseSchedule::with(['project.students','project.supervisor'])
                                            ->upcoming()->take(5)->get();

        $pendingApprovals  = Project::where('status', 'pending')
                                    ->with(['department','creator'])
                                    ->latest()->take(5)->get();

        $projectsByType = Project::selectRaw('project_type, count(*) as count')
                                 ->groupBy('project_type')->pluck('count','project_type');

        $projectsByStatus = Project::selectRaw('status, count(*) as count')
                                   ->groupBy('status')->pluck('count','status');

        $currentSemester = Semester::current();

        return view('dashboard.admin', compact(
            'stats', 'recentProjects', 'upcomingDefenses',
            'pendingApprovals', 'projectsByType', 'projectsByStatus', 'currentSemester'
        ));
    }

    // ─── Supervisor Dashboard ─────────────────────────────────────────────────
    private function supervisorDashboard(User $user)
    {
        $myProjects = Project::where('supervisor_id', $user->id)
                             ->with(['students','semester','department'])
                             ->get();

        $stats = [
            'total'     => $myProjects->count(),
            'active'    => $myProjects->whereIn('status', ['approved','in_progress'])->count(),
            'pending'   => $myProjects->where('status', 'pending')->count(),
            'defended'  => $myProjects->where('is_discussed', true)->count(),
        ];

        $pendingReports = \App\Models\ProjectReport::whereHas('project', fn($q) =>
            $q->where('supervisor_id', $user->id)
        )->where('status', 'submitted')->with('project','submittedBy')->get();

        $upcomingDefenses = DefenseSchedule::whereHas('project', fn($q) =>
            $q->where('supervisor_id', $user->id)
        )->upcoming()->take(5)->get();

        $myIdeas = ProjectIdea::where('proposed_by', $user->id)->latest()->take(5)->get();

        return view('dashboard.supervisor', compact(
            'myProjects', 'stats', 'pendingReports', 'upcomingDefenses', 'myIdeas'
        ));
    }

    // ─── Committee Member Dashboard ───────────────────────────────────────────
    private function committeeDashboard(User $user)
    {
        $myCommittees = \App\Models\Committee::whereHas('members', fn($q) =>
            $q->where('user_id', $user->id)
        )->with('project.students','project.supervisor')->get();

        $upcomingDefenses = DefenseSchedule::whereHas('committee.members', fn($q) =>
            $q->where('user_id', $user->id)
        )->upcoming()->take(5)->get();

        return view('dashboard.committee', compact('myCommittees', 'upcomingDefenses'));
    }

    // ─── Student Dashboard ────────────────────────────────────────────────────
    private function studentDashboard(User $user)
    {
        $myProjects = $user->studentProjects()
                           ->with(['supervisor','semester','milestones','defenseSchedules'])
                           ->get();

        $currentProject = $myProjects->whereIn('status', ['approved','in_progress'])->first();

        $availableIdeas = ProjectIdea::where('status', 'approved')
                                     ->where('department_id', $user->department_id)
                                     ->whereDoesntHave('projects')
                                     ->take(6)->get();

        $upcomingDefense = $currentProject
            ? $currentProject->defenseSchedules()->upcoming()->first()
            : null;

        return view('dashboard.student', compact(
            'myProjects', 'currentProject', 'availableIdeas', 'upcomingDefense'
        ));
    }
}