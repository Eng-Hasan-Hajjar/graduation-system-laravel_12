@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'لوحة تحكم المشرف' : 'Supervisor Dashboard')

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

    $ideaStatusLabels = [
        'pending'  => $isAr ? 'قيد المراجعة' : 'Pending',
        'approved' => $isAr ? 'معتمدة' : 'Approved',
        'rejected' => $isAr ? 'مرفوضة' : 'Rejected',
        'taken'    => $isAr ? 'مأخوذة' : 'Taken',
        'archived' => $isAr ? 'مؤرشفة' : 'Archived',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="mb-0">
            <i class="bi bi-person-workspace text-primary"></i>
            {{ $isAr ? 'لوحة تحكم المشرف' : 'Supervisor Dashboard' }}
        </h3>
        <a href="{{ route('ideas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> {{ $isAr ? 'إضافة فكرة جديدة' : 'Add New Idea' }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- بطاقات الإحصائيات --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-folder2-open fs-2 text-primary"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'إجمالي مشاريعي' : 'My Projects' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-play-circle fs-2 text-info"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['active'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'نشطة' : 'Active' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['pending'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'بانتظار الاعتماد' : 'Pending' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-mortarboard fs-2 text-success"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['defended'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'تمت مناقشتها' : 'Defended' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- مشاريعي --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-folder2-open"></i> {{ $isAr ? 'مشاريعي' : 'My Projects' }}
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ $isAr ? 'العنوان' : 'Title' }}</th>
                                <th>{{ $isAr ? 'الطلاب' : 'Students' }}</th>
                                <th>{{ $isAr ? 'القسم' : 'Department' }}</th>
                                <th>{{ $isAr ? 'الفصل' : 'Semester' }}</th>
                                <th>{{ $isAr ? 'التقدم' : 'Progress' }}</th>
                                <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($myProjects as $project)
                                <tr>
                                    <td>{{ $localized($project, 'title') }}</td>
                                    <td>{{ $project->students->pluck('name')->implode(', ') ?: '-' }}</td>
                                    <td>{{ $localized($project->department, 'name') }}</td>
                                    <td>{{ $localized($project->semester, 'name') }}</td>
                                    <td style="min-width: 120px;">
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar" style="width: {{ $project->progress_percentage ?? 0 }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $project->progress_percentage ?? 0 }}%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$project->status] ?? $project->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">{{ $isAr ? 'لا توجد مشاريع حتى الآن' : 'No projects yet' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- تقارير تحتاج مراجعة --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-file-earmark-text"></i> {{ $isAr ? 'تقارير بانتظار المراجعة' : 'Reports Pending Review' }}
                </div>
                @if ($pendingReports->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        {{ $isAr ? 'لا توجد تقارير بانتظار المراجعة' : 'No reports pending review' }}
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($pendingReports as $report)
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <strong>{{ $localized($report->project, 'title') }}</strong>
                                    <div class="small text-muted">
                                        {{ $isAr ? 'مُقدّم من' : 'Submitted by' }}: {{ $report->submittedBy?->name }}
                                        &nbsp;|&nbsp;
                                        {{ $fmtDate($report->created_at) }}
                                    </div>
                                </div>
                                <a href="{{ route('projects.show', $report->project) }}" class="btn btn-sm btn-outline-primary">
                                    {{ $isAr ? 'مراجعة' : 'Review' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            {{-- أقرب جلسات المناقشة --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-calendar-event"></i> {{ $isAr ? 'أقرب جلسات المناقشة' : 'Upcoming Defenses' }}
                </div>
                @if ($upcomingDefenses->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        {{ $isAr ? 'لا توجد جلسات قادمة' : 'No upcoming defenses' }}
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($upcomingDefenses as $defense)
                            <li class="list-group-item">
                                <div class="fw-bold">{{ $localized($defense->project, 'title') }}</div>
                                <div class="small text-muted">
                                    <i class="bi bi-calendar3"></i> {{ $fmtDate($defense->scheduled_date) }}
                                    @if ($defense->scheduled_time)
                                        - <i class="bi bi-clock"></i> {{ $fmtTime($defense->scheduled_time) }}
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- أفكاري المقترحة --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-lightbulb"></i> {{ $isAr ? 'أفكاري المقترحة' : 'My Proposed Ideas' }}</span>
                    <a href="{{ route('ideas.index') }}" class="btn btn-sm btn-outline-primary">{{ $isAr ? 'عرض الكل' : 'View All' }}</a>
                </div>
                @if ($myIdeas->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        {{ $isAr ? 'لم تقترح أي أفكار حتى الآن' : 'No ideas proposed yet' }}
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($myIdeas as $idea)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('ideas.show', $idea) }}" class="text-decoration-none">{{ $idea->title }}</a>
                                <span class="badge bg-{{ $idea->status_color }}">{{ $ideaStatusLabels[$idea->status] ?? $idea->status }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection