<?php

namespace App\Http\Controllers;

use App\Models\ProjectReport;
use App\Models\ProjectFile;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $query = ProjectReport::with(['project', 'submittedBy']);

        if (Auth::user()->isStudent()) {
            $query->where('submitted_by', Auth::id());
        } elseif (Auth::user()->isSupervisor()) {
            $query->whereHas('project', fn($q) => $q->where('supervisor_id', Auth::id()));
        }

        $reports = $query->when($request->status, fn($q) => $q->where('status', $request->status))
                         ->latest()
                         ->paginate(15);

        return view('reports.index', compact('reports'));
    }

    public function create(Request $request)
    {
        $projects = Auth::user()->isStudent()
            ? Auth::user()->studentProjects()->whereIn('status', ['approved','in_progress'])->get()
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
            'files.*'     => 'nullable|file|max:20480',
        ]);

        $data['submitted_by'] = Auth::id();
        $data['status']       = 'submitted';

        $report = ProjectReport::create($data);

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

        return redirect()->route('reports.show', $report)
                         ->with('success', 'تم رفع التقرير بنجاح');
    }

    public function show(ProjectReport $report)
    {
        $report->load(['project.supervisor', 'submittedBy', 'files', 'reviewedBy']);
        return view('reports.show', compact('report'));
    }

    public function review(Request $request, ProjectReport $report)
    {
        $request->validate([
            'supervisor_feedback' => 'required|string',
            'status'              => 'required|in:reviewed,approved,rejected',
            'grade'               => 'nullable|numeric|min:0|max:100',
        ]);

        $report->update([
            'supervisor_feedback' => $request->supervisor_feedback,
            'status'              => $request->status,
            'grade'               => $request->grade,
            'reviewed_by'         => Auth::id(),
            'reviewed_at'         => now(),
        ]);

        return back()->with('success', 'تم مراجعة التقرير بنجاح');
    }
}