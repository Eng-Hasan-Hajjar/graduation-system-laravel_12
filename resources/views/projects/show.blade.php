@extends('layouts.app')
@section('title', $project->title)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('projects.index') }}" class="text-muted small">
            <i class="bi bi-arrow-{{ app()->getLocale()=='ar' ? 'right' : 'left' }} me-1"></i>{{ __('common.back') }}
        </a>
        <h5 class="fw-bold mt-1 mb-0">{{ $project->title }}</h5>
        <code class="text-muted small">{{ $project->project_number }}</code>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        {{-- Approve / Reject --}}
        @if($project->status === 'pending')
            @can('approve-project')
            <form action="{{ route('projects.approve', $project) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>{{ __('common.approve') }}</button>
            </form>
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-lg me-1"></i>{{ __('common.reject') }}
            </button>
            @endcan
        @endif

        {{-- Mark Discussed --}}
        @if(!$project->is_discussed && in_array($project->status, ['submitted','under_review']))
            @can('manage-committee')
            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#discussModal">
                <i class="bi bi-trophy me-1"></i>{{ __('projects.mark_discussed') }}
            </button>
            @endcan
        @endif

        {{-- Archive --}}
        @if($project->is_discussed && !$project->is_archived)
            @can('archive-project')
            <form action="{{ route('projects.archive', $project) }}" method="POST">
                @csrf @method('PATCH')
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-archive me-1"></i>{{ __('common.archive') }}
                </button>
            </form>
            @endcan
        @endif

        @can('update', $project)
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>{{ __('common.edit') }}
        </a>
        @endcan
    </div>
</div>

<div class="row g-4">

    {{-- ── Left Column ── --}}
    <div class="col-lg-8">

        {{-- Status Banner --}}
        <div class="alert alert-{{ $project->status_color }} d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-info-circle fs-5"></i>
            <div>
                <strong>{{ $project->status_label }}</strong>
                @if($project->is_discussed)
                    &mdash; {{ __('projects.discussed_on') }}: <strong>{{ $project->actual_defense_date?->format('d/m/Y') }}</strong>
                    @if($project->final_grade)
                        &mdash; {{ __('projects.grade') }}: <strong>{{ $project->final_grade }} ({{ $project->grade_letter }})</strong>
                    @endif
                @endif
            </div>
        </div>

        {{-- Description --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold border-bottom pb-2 mb-3">{{ __('projects.description') }}</h6>
                <p>{{ $project->description_ar }}</p>

                @if($project->objectives_ar)
                <h6 class="fw-semibold mt-3">{{ __('projects.objectives') }}</h6>
                <p>{{ $project->objectives_ar }}</p>
                @endif

                @if($project->expected_outcomes_ar)
                <h6 class="fw-semibold mt-3">{{ __('projects.expected_outcomes') }}</h6>
                <p>{{ $project->expected_outcomes_ar }}</p>
                @endif

                @if($project->methodology_ar)
                <h6 class="fw-semibold mt-3">{{ __('projects.methodology') }}</h6>
                <p>{{ $project->methodology_ar }}</p>
                @endif

                @if($project->tools)
                <h6 class="fw-semibold mt-3">{{ __('projects.tools') }}</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($project->tools as $tool)
                        <span class="badge bg-primary-subtle text-primary">{{ $tool }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Milestones --}}
        @if($project->milestones->count())
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bi bi-check2-square text-primary me-2"></i>{{ __('projects.milestones') }}</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($project->milestones as $ms)
                    <div class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-{{ $ms->status==='completed' ? 'check-circle-fill text-success' : ($ms->isOverdue() ? 'exclamation-circle-fill text-danger' : 'circle text-muted') }} fs-5"></i>
                                <div>
                                    <div class="fw-semibold small">{{ $ms->title }}</div>
                                    <div class="text-muted" style="font-size:.78rem">{{ $ms->due_date->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            <span class="badge bg-{{ $ms->status_color }}-subtle text-{{ $ms->status_color }}">
                                {{ __('milestone.'.$ms->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Reports --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>{{ __('projects.reports') }}</h6>
                @can('submit-report', $project)
                <a href="{{ route('reports.create', ['project_id'=>$project->id]) }}" class="btn btn-sm btn-outline-primary" style="font-size:.8rem">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('reports.new') }}
                </a>
                @endcan
            </div>
            <div class="card-body p-0">
                @forelse($project->reports->take(5) as $report)
                <a href="{{ route('reports.show', $report) }}" class="list-group-item list-group-item-action px-4 py-3 d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold small">{{ $report->title }}</div>
                        <div class="text-muted" style="font-size:.78rem">
                            {{ $report->submittedBy->name }} &bull; {{ $report->report_date->format('d/m/Y') }}
                        </div>
                    </div>
                    <span class="badge bg-{{ $report->status==='approved' ? 'success' : ($report->status==='rejected' ? 'danger' : 'warning') }}-subtle text-{{ $report->status==='approved' ? 'success' : ($report->status==='rejected' ? 'danger' : 'warning') }}">
                        {{ __('status.'.$report->status) }}
                    </span>
                </a>
                @empty
                <div class="px-4 py-3 text-muted small">{{ __('reports.no_reports') }}</div>
                @endforelse
            </div>
        </div>

        {{-- Defense Schedule --}}
        @if($project->defenseSchedules->count())
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar-event text-primary me-2"></i>{{ __('projects.defense_schedule') }}</h6>
            </div>
            <div class="card-body">
                @foreach($project->defenseSchedules as $ds)
                <div class="d-flex align-items-start gap-3 py-2 border-bottom last:border-0">
                    <div class="icon-box bg-{{ $ds->status_color }}-subtle text-{{ $ds->status_color }} rounded-3" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold small">
                                {{ $ds->scheduled_date->translatedFormat('l، d F Y') }}
                                {{ __('common.at') }} {{ $ds->scheduled_time }}
                            </span>
                            <span class="badge bg-{{ $ds->status_color }}-subtle text-{{ $ds->status_color }}">{{ $ds->status_label }}</span>
                        </div>
                        @if($ds->location || $ds->room)
                        <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $ds->location }} {{ $ds->room }}</div>
                        @endif
                        @if($ds->postpone_reason)
                        <div class="text-danger small mt-1"><i class="bi bi-info-circle me-1"></i>{{ $ds->postpone_reason }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Committee notes after discussion --}}
        @if($project->is_discussed && $project->committee_notes_ar)
        <div class="card shadow-sm border-0 border-start border-success border-4 mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-success mb-2"><i class="bi bi-trophy me-2"></i>{{ __('projects.committee_notes') }}</h6>
                <p class="mb-0">{{ $project->committee_notes_ar }}</p>
                @if($project->final_grade)
                <div class="mt-3 d-flex gap-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-success">{{ $project->final_grade }}</div>
                        <div class="text-muted small">{{ __('projects.grade') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-primary">{{ $project->grade_letter }}</div>
                        <div class="text-muted small">{{ __('projects.grade_letter') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- ── Right Column (Sidebar) ── --}}
    <div class="col-lg-4">

        {{-- Project Info --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold border-bottom pb-2 mb-3">{{ __('projects.details') }}</h6>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">{{ __('projects.type') }}</dt>
                    <dd class="col-7">{{ $project->project_type_label }}</dd>

                    @if($project->academic_year_level)
                    <dt class="col-5 text-muted">{{ __('projects.year_level') }}</dt>
                    <dd class="col-7">{{ __('projects.year') }} {{ $project->academic_year_level }}</dd>
                    @endif

                    <dt class="col-5 text-muted">{{ __('projects.department') }}</dt>
                    <dd class="col-7">{{ $project->department->name }}</dd>

                    <dt class="col-5 text-muted">{{ __('projects.semester') }}</dt>
                    <dd class="col-7">{{ $project->semester->name }}</dd>

                    @if($project->registration_date)
                    <dt class="col-5 text-muted">{{ __('projects.registered') }}</dt>
                    <dd class="col-7">{{ $project->registration_date->format('d/m/Y') }}</dd>
                    @endif

                    @if($project->start_date)
                    <dt class="col-5 text-muted">{{ __('projects.start_date') }}</dt>
                    <dd class="col-7">{{ $project->start_date->format('d/m/Y') }}</dd>
                    @endif

                    @if($project->expected_end_date)
                    <dt class="col-5 text-muted {{ $project->isOverdue() ? 'text-danger' : '' }}">{{ __('projects.end_date') }}</dt>
                    <dd class="col-7 {{ $project->isOverdue() ? 'text-danger fw-bold' : '' }}">
                        {{ $project->expected_end_date->format('d/m/Y') }}
                        @if($project->isOverdue()) <i class="bi bi-exclamation-triangle"></i> @endif
                    </dd>
                    @endif

                    @if($project->is_archived)
                    <dt class="col-5 text-muted">{{ __('projects.archived_at') }}</dt>
                    <dd class="col-7">{{ $project->archived_at->format('d/m/Y') }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Supervisor --}}
        @if($project->supervisor)
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold border-bottom pb-2 mb-3">{{ __('projects.supervisor') }}</h6>
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $project->supervisor->avatar_url }}" width="48" height="48" class="rounded-circle" alt="">
                    <div>
                        <div class="fw-semibold">{{ $project->supervisor->name }}</div>
                        <div class="text-muted small">{{ $project->supervisor->academic_rank }}</div>
                        <div class="text-muted small">{{ $project->supervisor->email }}</div>
                    </div>
                </div>
                @if($project->coSupervisor)
                <hr>
                <div class="small text-muted mb-1">{{ __('projects.co_supervisor') }}</div>
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $project->coSupervisor->avatar_url }}" width="32" height="32" class="rounded-circle" alt="">
                    <div>
                        <div class="fw-semibold small">{{ $project->coSupervisor->name }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Students --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold border-bottom pb-2 mb-3">{{ __('projects.team') }}</h6>
                @foreach($project->students as $student)
                <div class="d-flex align-items-center gap-3 py-2 border-bottom last:border-0">
                    <img src="{{ $student->avatar_url }}" width="40" height="40" class="rounded-circle" alt="">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $student->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $student->student_id }}</div>
                    </div>
                    @if($student->pivot->role === 'leader')
                        <span class="badge bg-warning text-dark" style="font-size:.7rem"><i class="bi bi-star-fill me-1"></i>{{ __('roles.leader') }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Files --}}
        @if($project->files->count())
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-paperclip me-2"></i>{{ __('projects.files') }}</h6>
            </div>
            <div class="card-body p-0">
                @foreach($project->files->take(6) as $file)
                <a href="{{ $file->file_url }}" target="_blank"
                   class="list-group-item list-group-item-action px-3 py-2 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-{{ $file->file_type === 'pdf' ? 'pdf text-danger' : ($file->file_type === 'zip' ? 'zip text-warning' : 'text-secondary') }} fs-5"></i>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="small text-truncate">{{ $file->original_name }}</div>
                        <div class="text-muted" style="font-size:.72rem">{{ $file->file_size_human }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ── Reject Modal ── --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('projects.reject', $project) }}" method="POST">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">{{ __('projects.reject_title') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="rejection_reason" rows="4" class="form-control" required
                              placeholder="{{ __('projects.rejection_reason_placeholder') }}"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button class="btn btn-sm btn-danger">{{ __('common.reject') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Discuss Modal ── --}}
<div class="modal fade" id="discussModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('projects.mark-discussed', $project) }}" method="POST">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">{{ __('projects.mark_discussed') }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">{{ __('projects.grade') }}</label>
                        <input type="number" name="final_grade" min="0" max="100" step="0.5" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">{{ __('projects.grade_letter') }}</label>
                        <select name="grade_letter" class="form-select" required>
                            @foreach(['A+','A','B+','B','C+','C','D+','D','F'] as $g)
                                <option value="{{ $g }}">{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">{{ __('projects.actual_defense_date') }}</label>
                        <input type="date" name="actual_defense_date" class="form-control" value="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">{{ __('projects.committee_notes') }}</label>
                        <textarea name="committee_notes_ar" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button class="btn btn-sm btn-success">{{ __('projects.confirm_discussion') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection