@extends('layouts.app')
@section('title', __('nav.all_projects'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">{{ __('nav.all_projects') }}</h5>
        <p class="text-muted small mb-0">{{ $projects->total() }} {{ __('projects.results') }}</p>
    </div>
    @can('create-project')
    <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>{{ __('projects.new_project') }}
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="{{ __('common.search') }}..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">{{ __('common.all_statuses') }}</option>
                    @foreach(['pending','approved','in_progress','submitted','defended','archived'] as $s)
                        <option value="{{ $s }}" @selected(request('status')===$s)>{{ __('status.'.$s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">{{ __('common.all_types') }}</option>
                    @foreach(['graduation','semester','year4','research'] as $t)
                        <option value="{{ $t }}" @selected(request('type')===$t)>{{ __('project_type.'.$t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="semester_id" class="form-select form-select-sm">
                    <option value="">{{ __('common.all_semesters') }}</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" @selected(request('semester_id')==$sem->id)>{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="is_discussed" class="form-select form-select-sm">
                    <option value="">{{ __('common.all') }}</option>
                    <option value="1" @selected(request('is_discussed')==='1')>{{ __('projects.discussed') }}</option>
                    <option value="0" @selected(request('is_discussed')==='0')>{{ __('projects.not_discussed') }}</option>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

{{-- Projects Grid --}}
@if($projects->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-folder-x fs-1 opacity-25"></i>
        <p class="mt-3">{{ __('projects.no_projects') }}</p>
    </div>
@else
<div class="row g-3">
    @foreach($projects as $project)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-primary-subtle text-primary">{{ $project->project_type_label }}</span>
                    <span class="badge bg-{{ $project->status_color }}-subtle text-{{ $project->status_color }} badge-status">
                        {{ $project->status_label }}
                    </span>
                </div>

                {{-- Title --}}
                <h6 class="fw-bold mb-1">{{ Str::limit($project->title, 55) }}</h6>
                <code class="text-muted small">{{ $project->project_number }}</code>

                {{-- Details --}}
                <div class="mt-3 d-flex flex-column gap-1" style="font-size:.82rem">
                    <div><i class="bi bi-person-badge text-muted me-1"></i>
                        {{ $project->supervisor?->name ?? __('projects.no_supervisor') }}
                    </div>
                    <div><i class="bi bi-building text-muted me-1"></i>{{ $project->department->name }}</div>
                    <div><i class="bi bi-calendar3 text-muted me-1"></i>{{ $project->semester->name }}</div>
                    @if($project->defense_date)
                    <div class="{{ $project->is_discussed ? 'text-success' : 'text-warning' }}">
                        <i class="bi bi-{{ $project->is_discussed ? 'check2-circle' : 'calendar-event' }} me-1"></i>
                        @if($project->is_discussed)
                            {{ __('projects.discussed_on') }}: {{ $project->actual_defense_date?->format('d/m/Y') }}
                        @else
                            {{ __('projects.defense_on') }}: {{ $project->defense_date->format('d/m/Y') }}
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Students --}}
                <div class="mt-3 d-flex align-items-center gap-2">
                    <div>
                        @foreach($project->students->take(4) as $s)
                            <img src="{{ $s->avatar_url }}" width="28" height="28"
                                 class="rounded-circle border-2 border-white" style="margin-right:-8px"
                                 title="{{ $s->name }}" alt="">
                        @endforeach
                    </div>
                    <small class="text-muted">{{ $project->students->count() }} {{ __('projects.students') }}</small>
                </div>

                {{-- Progress --}}
                <div class="mt-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>{{ __('projects.progress') }}</span>
                        <span>{{ $project->progress_percentage }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-{{ $project->progress_percentage >= 100 ? 'success' : 'primary' }}"
                             style="width:{{ $project->progress_percentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3 d-flex gap-2">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                    <i class="bi bi-eye me-1"></i>{{ __('common.view') }}
                </a>
                @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil"></i>
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $projects->links() }}
</div>
@endif
@endsection