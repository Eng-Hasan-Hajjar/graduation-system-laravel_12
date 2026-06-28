@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'جداول المناقشات' : 'Defense Schedules')

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

    $statusLabels = [
        'scheduled' => $isAr ? 'مجدولة'  : 'Scheduled',
        'confirmed' => $isAr ? 'مؤكدة'   : 'Confirmed',
        'postponed' => $isAr ? 'مؤجلة'   : 'Postponed',
        'cancelled' => $isAr ? 'ملغاة'   : 'Cancelled',
        'completed' => $isAr ? 'منتهية'  : 'Completed',
    ];

    $statusColors = [
        'scheduled' => 'secondary',
        'confirmed' => 'success',
        'postponed' => 'warning',
        'cancelled' => 'danger',
        'completed' => 'info',
    ];
@endphp

<div class="container py-4">

    {{-- رأس الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-calendar-event text-primary"></i>
                {{ $isAr ? 'جداول المناقشات' : 'Defense Schedules' }}
            </h3>
            <span class="text-muted">
                {{ $isAr ? 'إجمالي الجلسات' : 'Total Sessions' }}: {{ $schedules->total() }}
            </span>
        </div>
        @can('manage-schedule')
            <a href="{{ route('schedules.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                {{ $isAr ? 'جدولة مناقشة جديدة' : 'Schedule New Defense' }}
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- جلسات اليوم --}}
    @if ($todayDefenses->count())
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-star-fill"></i>
                {{ $isAr ? 'مناقشات اليوم' : "Today's Defenses" }}
                <span class="badge bg-white text-primary ms-2">{{ $todayDefenses->count() }}</span>
            </div>
            <div class="row g-0">
                @foreach ($todayDefenses as $defense)
                    <div class="col-md-6 col-lg-4 p-3 border-bottom border-end">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-{{ $statusColors[$defense->status] ?? 'secondary' }}">
                                {{ $statusLabels[$defense->status] ?? $defense->status }}
                            </span>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i>
                                {{ $defense->scheduled_time ? \Carbon\Carbon::parse($defense->scheduled_time)->format('H:i') : '-' }}
                            </small>
                        </div>
                        <a href="{{ route('schedules.show', $defense) }}" class="fw-bold text-decoration-none d-block mb-1">
                            {{ $localized($defense->project, 'title') }}
                        </a>
                        <div class="small text-muted">
                            <i class="bi bi-person-badge"></i>
                            {{ $defense->project?->supervisor?->name ?? '-' }}
                        </div>
                        @if ($defense->location || $defense->room)
                            <div class="small text-muted">
                                <i class="bi bi-geo-alt"></i>
                                {{ $defense->location }}
                                @if ($defense->room) — {{ $defense->room }} @endif
                            </div>
                        @endif
                        @if ($defense->project?->students->count())
                            <div class="small text-muted mt-1">
                                <i class="bi bi-people"></i>
                                {{ $defense->project->students->pluck('name')->implode(', ') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- فلاتر البحث --}}
    <form method="GET" action="{{ route('schedules.index') }}" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'تاريخ محدد' : 'Specific Date' }}</label>
                    <input type="date" name="date" class="form-control"
                           value="{{ request('date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'الحالة' : 'Status' }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ $isAr ? 'كل الحالات' : 'All Statuses' }}</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                        {{ $isAr ? 'بحث' : 'Search' }}
                    </button>
                </div>
                <div class="col-md-2">
                    @if (request()->anyFilled(['date', 'status']))
                        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle"></i>
                            {{ $isAr ? 'مسح' : 'Clear' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- قائمة الجلسات --}}
    @if ($schedules->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                {{ $isAr ? 'لا توجد جلسات مناقشة' : 'No defense sessions found' }}
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ $isAr ? 'التاريخ والوقت' : 'Date & Time' }}</th>
                            <th>{{ $isAr ? 'المشروع' : 'Project' }}</th>
                            <th>{{ $isAr ? 'المشرف' : 'Supervisor' }}</th>
                            <th>{{ $isAr ? 'الطلاب' : 'Students' }}</th>
                            <th>{{ $isAr ? 'المكان' : 'Location' }}</th>
                            <th>{{ $isAr ? 'المدة' : 'Duration' }}</th>
                            <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            @php
                                $isPast = $schedule->scheduled_date->isPast()
                                          && !in_array($schedule->status, ['completed','cancelled']);
                            @endphp
                            <tr class="{{ $isPast ? 'table-warning' : '' }}">
                                <td>
                                    <div class="fw-bold">
                                        {{ $schedule->scheduled_date->format('Y-m-d') }}
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i>
                                        {{ $schedule->scheduled_time
                                            ? \Carbon\Carbon::parse($schedule->scheduled_time)->format('H:i')
                                            : '-' }}
                                    </small>
                                    @if ($schedule->scheduled_date->isToday())
                                        <span class="badge bg-primary ms-1">
                                            {{ $isAr ? 'اليوم' : 'Today' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $schedule->project) }}"
                                       class="text-decoration-none fw-bold">
                                        {{ $localized($schedule->project, 'title') }}
                                    </a>
                                    <div class="small text-muted">
                                        {{ $schedule->project?->project_number }}
                                    </div>
                                </td>
                                <td>{{ $schedule->project?->supervisor?->name ?? '-' }}</td>
                                <td>
                                    <div class="small">
                                        {{ $schedule->project?->students->pluck('name')->implode('، ') ?: '-' }}
                                    </div>
                                </td>
                                <td>
                                    {{ $schedule->location ?? '-' }}
                                    @if ($schedule->room)
                                        <div class="small text-muted">{{ $schedule->room }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($schedule->duration_minutes)
                                        {{ $schedule->duration_minutes }}
                                        {{ $isAr ? 'د' : 'min' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$schedule->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$schedule->status] ?? $schedule->status }}
                                    </span>
                                    @if ($isPast)
                                        <div class="small text-warning">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            {{ $isAr ? 'تجاوز الموعد' : 'Overdue' }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('schedules.show', $schedule) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('manage-schedule')
                                            @if (in_array($schedule->status, ['scheduled','confirmed']))
                                                <form action="{{ route('schedules.complete', $schedule) }}"
                                                      method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                            title="{{ $isAr ? 'تسجيل الاكتمال' : 'Mark Completed' }}"
                                                            onclick="return confirm('{{ $isAr ? 'تأكيد اكتمال الجلسة؟' : 'Confirm session completed?' }}')">
                                                        <i class="bi bi-check2"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $schedules->links() }}
        </div>
    @endif
</div>
@endsection