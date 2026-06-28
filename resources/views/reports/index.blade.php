@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'التقارير' : 'Reports')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '-';

    $statusLabels = [
        'submitted' => $isAr ? 'مُقدَّم'     : 'Submitted',
        'reviewed'  => $isAr ? 'تمت المراجعة' : 'Reviewed',
        'approved'  => $isAr ? 'مقبول'        : 'Approved',
        'rejected'  => $isAr ? 'مرفوض'        : 'Rejected',
    ];

    $statusColors = [
        'submitted' => 'warning',
        'reviewed'  => 'info',
        'approved'  => 'success',
        'rejected'  => 'danger',
    ];

    $typeLabels = [
        'weekly'    => $isAr ? 'أسبوعي'       : 'Weekly',
        'monthly'   => $isAr ? 'شهري'          : 'Monthly',
        'milestone' => $isAr ? 'مرحلة'         : 'Milestone',
        'final'     => $isAr ? 'نهائي'         : 'Final',
        'other'     => $isAr ? 'أخرى'          : 'Other',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-file-earmark-text text-primary"></i>
                {{ $isAr ? 'التقارير' : 'Reports' }}
            </h3>
            <span class="text-muted">
                {{ $isAr ? 'إجمالي التقارير' : 'Total Reports' }}: {{ $reports->total() }}
            </span>
        </div>
        <a href="{{ route('reports.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            {{ $isAr ? 'رفع تقرير جديد' : 'Submit New Report' }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- فلتر الحالة --}}
    <form method="GET" action="{{ route('reports.index') }}" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
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
                    </button>
                </div>
                @if (request('status'))
                    <div class="col-md-2">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </form>

    @if ($reports->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-file-earmark-x fs-1 d-block mb-3"></i>
                {{ $isAr ? 'لا توجد تقارير حتى الآن' : 'No reports yet' }}
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ $isAr ? 'عنوان التقرير' : 'Title' }}</th>
                            <th>{{ $isAr ? 'المشروع' : 'Project' }}</th>
                            <th>{{ $isAr ? 'النوع' : 'Type' }}</th>
                            <th>{{ $isAr ? 'مُقدَّم من' : 'Submitted By' }}</th>
                            <th>{{ $isAr ? 'تاريخ التقرير' : 'Report Date' }}</th>
                            <th>{{ $isAr ? 'الدرجة' : 'Grade' }}</th>
                            <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>
                                    <strong>{{ $report->title }}</strong>
                                    @if ($report->week_number)
                                        <div class="small text-muted">
                                            {{ $isAr ? 'الأسبوع' : 'Week' }} {{ $report->week_number }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($report->project)
                                        <a href="{{ route('projects.show', $report->project) }}"
                                           class="text-decoration-none small">
                                            {{ $localized($report->project, 'title') }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $typeLabels[$report->report_type] ?? $report->report_type }}
                                    </span>
                                </td>
                                <td>{{ $report->submittedBy?->name ?? '-' }}</td>
                                <td>{{ $report->report_date?->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    @if ($report->grade !== null)
                                        <strong>{{ $report->grade }}/100</strong>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$report->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$report->status] ?? $report->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('reports.show', $report) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection