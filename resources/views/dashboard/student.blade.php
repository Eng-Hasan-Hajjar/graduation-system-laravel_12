@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'لوحة تحكم الطالب' : 'Student Dashboard')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $localized = function ($model, $field) use ($locale) {
        if (!$model) return '';
        return $model->{$field . '_' . $locale}
            ?? $model->{$field . '_ar'}
            ?? $model->{$field}
            ?? '';
    };

    $fmtDate = function ($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
    };
    $fmtTime = function ($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i') : '';
    };

    $statusLabels = [
        'pending'     => $isAr ? 'قيد الانتظار' : 'Pending',
        'approved'    => $isAr ? 'معتمد' : 'Approved',
        'rejected'    => $isAr ? 'مرفوض' : 'Rejected',
        'in_progress' => $isAr ? 'قيد التنفيذ' : 'In Progress',
        'submitted'   => $isAr ? 'مُسلَّم' : 'Submitted',
        'defended'    => $isAr ? 'تمت المناقشة' : 'Defended',
        'archived'    => $isAr ? 'مؤرشف' : 'Archived',
    ];

    $statusColors = [
        'pending'     => 'warning',
        'approved'    => 'success',
        'rejected'    => 'danger',
        'in_progress' => 'info',
        'submitted'   => 'primary',
        'defended'    => 'secondary',
        'archived'    => 'dark',
    ];
@endphp

<div class="container py-4">

    <div class="mb-4">
        <h3 class="mb-1">
            <i class="bi bi-mortarboard text-primary"></i>
            {{ $isAr ? 'لوحة تحكم الطالب' : 'Student Dashboard' }}
        </h3>
        <span class="text-muted">
            {{ $isAr ? 'مرحباً' : 'Welcome' }}, {{ auth()->user()->name }}
        </span>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- المشروع الحالي --}}
    @if ($currentProject)
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span><i class="bi bi-star-fill"></i> {{ $isAr ? 'مشروعي الحالي' : 'My Current Project' }}</span>
                <span class="badge bg-{{ $statusColors[$currentProject->status] ?? 'light' }}">
                    {{ $statusLabels[$currentProject->status] ?? $currentProject->status }}
                </span>
            </div>
            <div class="card-body">
                <h5>{{ $localized($currentProject, 'title') }}</h5>
                <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit($localized($currentProject, 'description'), 200) }}</p>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <small class="text-muted d-block">{{ $isAr ? 'المشرف' : 'Supervisor' }}</small>
                        <strong>{{ $currentProject->supervisor?->name ?? '-' }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">{{ $isAr ? 'الفصل الدراسي' : 'Semester' }}</small>
                        <strong>{{ $localized($currentProject->semester, 'name') }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">{{ $isAr ? 'نسبة التقدم' : 'Progress' }}</small>
                        <strong>{{ $currentProject->progress_percentage ?? 0 }}%</strong>
                    </div>
                </div>

                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar" style="width: {{ $currentProject->progress_percentage ?? 0 }}%"></div>
                </div>

                @if ($currentProject->milestones && $currentProject->milestones->count())
                    <h6 class="mt-3">{{ $isAr ? 'مراحل المشروع' : 'Milestones' }}</h6>
                    <ul class="list-group list-group-flush mb-3">
                        @foreach ($currentProject->milestones as $milestone)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>
                                    @if ($milestone->is_completed ?? false)
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    @else
                                        <i class="bi bi-circle text-muted"></i>
                                    @endif
                                    {{ $localized($milestone, 'title') }}
                                </span>
                                @if ($milestone->due_date)
                                    <small class="text-muted">{{ $fmtDate($milestone->due_date) }}</small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($upcomingDefense)
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-calendar-event"></i>
                        {{ $isAr ? 'جلسة المناقشة القادمة' : 'Upcoming Defense' }}:
                        <strong>{{ $fmtDate($upcomingDefense->scheduled_date) }}</strong>
                        @if ($upcomingDefense->scheduled_time)
                            - {{ $fmtTime($upcomingDefense->scheduled_time) }}
                        @endif
                        @if ($upcomingDefense->location ?? $currentProject->defense_location ?? null)
                            - {{ $upcomingDefense->location ?? $currentProject->defense_location }}
                        @endif
                    </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('projects.show', $currentProject) }}" class="btn btn-primary">
                        <i class="bi bi-eye"></i> {{ $isAr ? 'عرض التفاصيل الكاملة' : 'View Full Details' }}
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-circle"></i>
            {{ $isAr ? 'لا يوجد لديك مشروع نشط حالياً. تصفح الأفكار المتاحة أدناه أو تواصل مع منسق القسم.' : "You don't have an active project yet. Browse available ideas below or contact your department coordinator." }}
        </div>
    @endif

    {{-- مشاريعي --}}
    @if ($myProjects->count())
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-folder2-open"></i> {{ $isAr ? 'مشاريعي' : 'My Projects' }}
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ $isAr ? 'العنوان' : 'Title' }}</th>
                            <th>{{ $isAr ? 'المشرف' : 'Supervisor' }}</th>
                            <th>{{ $isAr ? 'الفصل' : 'Semester' }}</th>
                            <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($myProjects as $project)
                            <tr>
                                <td>{{ $localized($project, 'title') }}</td>
                                <td>{{ $project->supervisor?->name ?? '-' }}</td>
                                <td>{{ $localized($project->semester, 'name') }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$project->status] ?? $project->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- أفكار مقترحة متاحة --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-lightbulb"></i> {{ $isAr ? 'أفكار مشاريع متاحة في قسمك' : 'Available Project Ideas in Your Department' }}</span>
            <a href="{{ route('ideas.index') }}" class="btn btn-sm btn-outline-primary">{{ $isAr ? 'عرض الكل' : 'View All' }}</a>
        </div>

        @if ($availableIdeas->isEmpty())
            <div class="card-body text-center text-muted py-4">
                {{ $isAr ? 'لا توجد أفكار متاحة حالياً' : 'No available ideas at the moment' }}
            </div>
        @else
            <div class="card-body">
                <div class="row g-3">
                    @foreach ($availableIdeas as $idea)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body d-flex flex-column">
                                    <span class="badge bg-{{ $idea->status_color }} mb-2 align-self-start">
                                        {{ $isAr ? 'معتمدة' : 'Approved' }}
                                    </span>
                                    <h6 class="card-title">{{ $idea->title }}</h6>
                                    <p class="card-text text-muted small flex-grow-1">
                                        {{ \Illuminate\Support\Str::limit($idea->description, 90) }}
                                    </p>
                                    <a href="{{ route('ideas.show', $idea) }}" class="btn btn-sm btn-outline-primary mt-2">
                                        {{ $isAr ? 'عرض التفاصيل' : 'View Details' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection