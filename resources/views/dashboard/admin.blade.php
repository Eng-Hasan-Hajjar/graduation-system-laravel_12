@extends('layouts.app')

@section('title', __('nav.dashboard'))

@section('breadcrumb')
    <span class="text-muted small">{{ __('nav.dashboard') }}</span>
@endsection

@section('content')
{{-- ── Page Header ── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">{{ __('dashboard.welcome') }}, {{ auth()->user()->name }} 👋</h4>
        <p class="text-muted mb-0 small">
            {{ __('dashboard.today') }}: {{ now()->translatedFormat('l, d F Y') }}
            @if($currentSemester)
                &bull; {{ __('dashboard.current_semester') }}: <strong>{{ $currentSemester->name }}</strong>
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>{{ __('projects.new_project') }}
        </a>
        <a href="{{ route('ideas.create') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-lightbulb me-1"></i>{{ __('ideas.new_idea') }}
        </a>
    </div>
</div>

{{-- ── Stats Cards ── --}}
<div class="row g-3 mb-4">
    @php
    $cards = [
        ['label' => __('stats.total_projects'),   'value' => $stats['total_projects'],    'icon' => 'folder2-open',   'color' => 'primary'],
        ['label' => __('stats.active_projects'),  'value' => $stats['active_projects'],   'icon' => 'play-circle',    'color' => 'success'],
        ['label' => __('stats.pending'),          'value' => $stats['pending_projects'],  'icon' => 'clock-history',  'color' => 'warning'],
        ['label' => __('stats.defended'),         'value' => $stats['defended_projects'], 'icon' => 'trophy',         'color' => 'info'],
        ['label' => __('stats.archived'),         'value' => $stats['archived_projects'], 'icon' => 'archive',        'color' => 'secondary'],
        ['label' => __('stats.students'),         'value' => $stats['total_students'],    'icon' => 'mortarboard',    'color' => 'primary'],
        ['label' => __('stats.supervisors'),      'value' => $stats['total_supervisors'], 'icon' => 'person-badge',   'color' => 'secondary'],
        ['label' => __('stats.pending_ideas'),    'value' => $stats['pending_ideas'],     'icon' => 'lightbulb',      'color' => 'warning'],
    ];
    @endphp

    @foreach($cards as $card)
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="icon-box bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }}">
                    <i class="bi bi-{{ $card['icon'] }}"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4">{{ $card['value'] }}</div>
                    <div class="text-muted small">{{ $card['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">

    {{-- ── Today's Defenses ── --}}
    @if($upcomingDefenses->count())
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar-event text-primary me-2"></i>{{ __('dashboard.upcoming_defenses') }}</h6>
                <a href="{{ route('schedules.index') }}" class="small text-primary">{{ __('common.view_all') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($upcomingDefenses as $defense)
                    <a href="{{ route('schedules.show', $defense) }}" class="list-group-item list-group-item-action px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold small">{{ $defense->project->title }}</div>
                                <div class="text-muted" style="font-size:.8rem">
                                    <i class="bi bi-calendar3 me-1"></i>{{ $defense->scheduled_date->format('d/m/Y') }}
                                    <i class="bi bi-clock ms-2 me-1"></i>{{ $defense->scheduled_time }}
                                    @if($defense->room)
                                        <i class="bi bi-geo-alt ms-2 me-1"></i>{{ $defense->room }}
                                    @endif
                                </div>
                            </div>
                            <span class="badge bg-{{ $defense->status_color }} badge-status">{{ $defense->status_label }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Pending Approvals ── --}}
    @if($pendingApprovals->count())
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-clock-history text-warning me-2"></i>{{ __('dashboard.pending_approvals') }}
                    <span class="badge bg-warning text-dark ms-1">{{ $pendingApprovals->count() }}</span>
                </h6>
                <a href="{{ route('projects.index', ['status'=>'pending']) }}" class="small text-primary">{{ __('common.view_all') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($pendingApprovals as $proj)
                    <a href="{{ route('projects.show', $proj) }}" class="list-group-item list-group-item-action px-4 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold small">{{ $proj->title }}</div>
                                <div class="text-muted" style="font-size:.8rem">
                                    {{ $proj->department->name }} &bull;
                                    {{ $proj->project_type_label }} &bull;
                                    {{ $proj->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <form action="{{ route('projects.approve', $proj) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-success" style="font-size:.75rem;padding:.2rem .5rem" title="{{ __('common.approve') }}">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Recent Projects ── --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-0 pt-3 pb-0 d-flex justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bi bi-folder2-open text-primary me-2"></i>{{ __('dashboard.recent_projects') }}</h6>
                <a href="{{ route('projects.index') }}" class="small text-primary">{{ __('common.view_all') }}</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('projects.number') }}</th>
                                <th>{{ __('projects.title') }}</th>
                                <th>{{ __('projects.type') }}</th>
                                <th>{{ __('projects.supervisor') }}</th>
                                <th>{{ __('projects.students') }}</th>
                                <th>{{ __('projects.progress') }}</th>
                                <th>{{ __('projects.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProjects as $proj)
                            <tr>
                                <td><code class="small">{{ $proj->project_number }}</code></td>
                                <td>
                                    <div class="fw-semibold small">{{ Str::limit($proj->title, 40) }}</div>
                                    @if($proj->is_discussed)
                                        <span class="badge bg-success-subtle text-success" style="font-size:.7rem">
                                            <i class="bi bi-check2-circle me-1"></i>{{ __('projects.discussed') }}
                                        </span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $proj->project_type_label }}</span></td>
                                <td class="small">{{ $proj->supervisor?->name ?? '-' }}</td>
                                <td>
                                    @foreach($proj->students->take(3) as $s)
                                        <img src="{{ $s->avatar_url }}" width="24" height="24"
                                             class="rounded-circle border border-white" style="margin-right:-6px"
                                             title="{{ $s->name }}" alt="">
                                    @endforeach
                                    @if($proj->students->count() > 3)
                                        <span class="badge bg-secondary" style="font-size:.7rem">+{{ $proj->students->count()-3 }}</span>
                                    @endif
                                </td>
                                <td style="min-width:100px">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar bg-primary" style="width:{{ $proj->progress_percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $proj->progress_percentage }}%</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $proj->status_color }}-subtle text-{{ $proj->status_color }} badge-status">
                                        {{ $proj->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $proj) }}" class="btn btn-sm btn-outline-primary" style="font-size:.75rem;padding:.2rem .6rem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection