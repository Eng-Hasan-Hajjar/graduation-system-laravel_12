@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'لوحة تحكم المسؤول' : 'Admin Dashboard')

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

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-speedometer2 text-primary"></i>
                {{ $isAr ? 'لوحة تحكم المسؤول' : 'Admin Dashboard' }}
            </h3>
            @if ($currentSemester)
                <span class="text-muted">
                    {{ $isAr ? 'الفصل الدراسي الحالي' : 'Current Semester' }}: <strong>{{ $localized($currentSemester, 'name') }}</strong>
                </span>
            @endif
        </div>
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
                    <h3 class="mt-2 mb-0">{{ $stats['total_projects'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'إجمالي المشاريع' : 'Total Projects' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['pending_projects'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'بانتظار الاعتماد' : 'Pending Approval' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-play-circle fs-2 text-info"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['active_projects'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'مشاريع نشطة' : 'Active Projects' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-mortarboard fs-2 text-success"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['defended_projects'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'تمت مناقشتها' : 'Defended' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-archive fs-2 text-secondary"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['archived_projects'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'مؤرشفة' : 'Archived' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 text-primary"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total_students'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'عدد الطلاب' : 'Students' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-person-badge fs-2 text-info"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total_supervisors'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'عدد المشرفين' : 'Supervisors' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-lightbulb fs-2 text-warning"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['pending_ideas'] }}</h3>
                    <small class="text-muted">{{ $isAr ? 'أفكار قيد المراجعة' : 'Pending Ideas' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- أحدث المشاريع --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history"></i> {{ $isAr ? 'أحدث المشاريع' : 'Recent Projects' }}</span>
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-primary">
                        {{ $isAr ? 'عرض الكل' : 'View All' }}
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ $isAr ? 'العنوان' : 'Title' }}</th>
                                <th>{{ $isAr ? 'المشرف' : 'Supervisor' }}</th>
                                <th>{{ $isAr ? 'الطلاب' : 'Students' }}</th>
                                <th>{{ $isAr ? 'الفصل' : 'Semester' }}</th>
                                <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentProjects as $project)
                                <tr>
                                    <td>{{ $localized($project, 'title') }}</td>
                                    <td>{{ $project->supervisor?->name ?? '-' }}</td>
                                    <td>{{ $project->students->count() }}</td>
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
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">{{ $isAr ? 'لا توجد مشاريع' : 'No projects' }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- مشاريع تحتاج اعتماد --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-check2-square"></i> {{ $isAr ? 'مشاريع بانتظار الاعتماد' : 'Projects Pending Approval' }}
                </div>
                @if ($pendingApprovals->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        {{ $isAr ? 'لا توجد مشاريع بانتظار الاعتماد' : 'No projects pending approval' }}
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($pendingApprovals as $project)
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <strong>{{ $localized($project, 'title') }}</strong>
                                    <div class="small text-muted">
                                        {{ $localized($project->department, 'name') }}
                                        @if ($project->creator)
                                            &nbsp;|&nbsp; {{ $isAr ? 'مقدّم من' : 'By' }}: {{ $project->creator->name }}
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('projects.approve', $project) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i> {{ $isAr ? 'اعتماد' : 'Approve' }}
                                        </button>
                                    </form>
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">
                                        {{ $isAr ? 'مراجعة' : 'Review' }}
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- الشريط الجانبي --}}
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
                                @if ($defense->project?->supervisor)
                                    <div class="small text-muted">
                                        <i class="bi bi-person"></i> {{ $defense->project->supervisor->name }}
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- توزيع المشاريع حسب النوع --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-pie-chart"></i> {{ $isAr ? 'توزيع المشاريع حسب النوع' : 'Projects by Type' }}
                </div>
                <div class="card-body">
                    @forelse ($projectsByType as $type => $count)
                        @php $percent = $stats['total_projects'] > 0 ? round(($count / $stats['total_projects']) * 100) : 0; @endphp
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $type }}</span>
                                <span>{{ $count }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ $isAr ? 'لا توجد بيانات' : 'No data' }}</p>
                    @endforelse
                </div>
            </div>

            {{-- توزيع المشاريع حسب الحالة --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-bar-chart"></i> {{ $isAr ? 'توزيع المشاريع حسب الحالة' : 'Projects by Status' }}
                </div>
                <div class="card-body">
                    @forelse ($projectsByStatus as $status => $count)
                        @php $percent = $stats['total_projects'] > 0 ? round(($count / $stats['total_projects']) * 100) : 0; @endphp
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $statusLabels[$status] ?? $status }}</span>
                                <span>{{ $count }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-{{ $statusColors[$status] ?? 'secondary' }}" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ $isAr ? 'لا توجد بيانات' : 'No data' }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection