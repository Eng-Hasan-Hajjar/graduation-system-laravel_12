@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل التقرير' : 'Report Details')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '-';

    $statusLabels = [
        'submitted' => $isAr ? 'مُقدَّم'      : 'Submitted',
        'reviewed'  => $isAr ? 'تمت المراجعة' : 'Reviewed',
        'approved'  => $isAr ? 'مقبول'         : 'Approved',
        'rejected'  => $isAr ? 'مرفوض'         : 'Rejected',
    ];

    $statusColors = [
        'submitted' => 'warning',
        'reviewed'  => 'info',
        'approved'  => 'success',
        'rejected'  => 'danger',
    ];

    $typeLabels = [
        'weekly'    => $isAr ? 'أسبوعي'  : 'Weekly',
        'monthly'   => $isAr ? 'شهري'    : 'Monthly',
        'milestone' => $isAr ? 'مرحلة'   : 'Milestone',
        'final'     => $isAr ? 'نهائي'   : 'Final',
        'other'     => $isAr ? 'أخرى'    : 'Other',
    ];

    $isSupervisor = auth()->user()->isSupervisor();
    $isAdmin      = auth()->user()->isAdmin() || auth()->user()->isCoordinator();
    $canReview    = ($isSupervisor || $isAdmin)
                    && in_array($report->status, ['submitted', 'reviewed']);
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-{{ $statusColors[$report->status] ?? 'secondary' }} fs-6">
                    {{ $statusLabels[$report->status] ?? $report->status }}
                </span>
                <span class="badge bg-light text-dark border">
                    {{ $typeLabels[$report->report_type] ?? $report->report_type }}
                </span>
                @if ($report->week_number)
                    <span class="badge bg-light text-dark border">
                        {{ $isAr ? 'الأسبوع' : 'Week' }} {{ $report->week_number }}
                    </span>
                @endif
            </div>
            <h3 class="mb-1">{{ $report->title }}</h3>
            <small class="text-muted">
                {{ $isAr ? 'تاريخ التقرير' : 'Report Date' }}: {{ $report->report_date?->format('Y-m-d') }}
            </small>
        </div>

        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i>
            {{ $isAr ? 'رجوع' : 'Back' }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

        {{-- المحتوى الرئيسي --}}
        <div class="col-lg-8">

            {{-- محتوى التقرير --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-card-text"></i>
                    {{ $isAr ? 'محتوى التقرير' : 'Report Content' }}
                </div>
                <div class="card-body">
                    @if ($report->content_ar)
                        <h6 class="text-muted">{{ $isAr ? 'العربية' : 'Arabic' }}</h6>
                        <div style="white-space: pre-line;" class="mb-4">{{ $report->content_ar }}</div>
                    @endif
                    @if ($report->content_en)
                        <h6 class="text-muted">English</h6>
                        <div style="white-space: pre-line;">{{ $report->content_en }}</div>
                    @endif
                </div>
            </div>

            {{-- المرفقات --}}
            @if ($report->files->count())
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-paperclip"></i>
                        {{ $isAr ? 'المرفقات' : 'Attachments' }}
                        <span class="badge bg-primary ms-1">{{ $report->files->count() }}</span>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($report->files as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark text-primary fs-5"></i>
                                    <div>
                                        <div>{{ $file->original_name }}</div>
                                        <small class="text-muted">{{ $file->file_size_human }}</small>
                                    </div>
                                </div>
                                <a href="{{ $file->file_url }}" target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i>
                                    {{ $isAr ? 'تحميل' : 'Download' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ملاحظات المشرف --}}
            @if ($report->supervisor_feedback)
                <div class="card shadow-sm mb-4 border-{{ $statusColors[$report->status] ?? 'secondary' }}">
                    <div class="card-header bg-{{ $statusColors[$report->status] ?? 'secondary' }} bg-opacity-10">
                        <i class="bi bi-chat-text"></i>
                        {{ $isAr ? 'ملاحظات المشرف' : "Supervisor's Feedback" }}
                        @if ($report->grade !== null)
                            <span class="badge bg-primary {{ $isAr ? 'me-2' : 'ms-2' }}">
                                {{ $isAr ? 'الدرجة' : 'Grade' }}: {{ $report->grade }}/100
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        <p style="white-space: pre-line;" class="mb-0">{{ $report->supervisor_feedback }}</p>
                        @if ($report->reviewedBy && $report->reviewed_at)
                            <div class="text-muted small mt-3">
                                <i class="bi bi-person-check"></i>
                                {{ $report->reviewedBy->name }}
                                — {{ $report->reviewed_at->format('Y-m-d H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- نموذج مراجعة المشرف --}}
            @if ($canReview)
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary bg-opacity-10">
                        <i class="bi bi-check2-square"></i>
                        {{ $isAr ? 'مراجعة التقرير' : 'Review Report' }}
                    </div>
                    <div class="card-body">
                        <form action="{{ route('reports.review', $report) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label class="form-label">
                                    {{ $isAr ? 'ملاحظاتك على التقرير' : 'Your Feedback' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea name="supervisor_feedback" rows="4"
                                          class="form-control @error('supervisor_feedback') is-invalid @enderror"
                                          required>{{ old('supervisor_feedback', $report->supervisor_feedback) }}</textarea>
                                @error('supervisor_feedback')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        {{ $isAr ? 'الحالة' : 'Status' }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="status"
                                            class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="reviewed" {{ old('status') === 'reviewed' ? 'selected' : '' }}>
                                            {{ $isAr ? 'تمت المراجعة' : 'Reviewed' }}
                                        </option>
                                        <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>
                                            {{ $isAr ? 'قبول' : 'Approve' }}
                                        </option>
                                        <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>
                                            {{ $isAr ? 'رفض' : 'Reject' }}
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ $isAr ? 'الدرجة (اختياري)' : 'Grade (Optional)' }}</label>
                                    <input type="number" name="grade" min="0" max="100" step="0.5"
                                           class="form-control @error('grade') is-invalid @enderror"
                                           value="{{ old('grade', $report->grade) }}">
                                    @error('grade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle"></i>
                                {{ $isAr ? 'حفظ المراجعة' : 'Save Review' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- الشريط الجانبي --}}
        <div class="col-lg-4">

            {{-- معلومات التقرير --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i>
                    {{ $isAr ? 'معلومات التقرير' : 'Report Information' }}
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'النوع' : 'Type' }}</span>
                        <strong>{{ $typeLabels[$report->report_type] ?? $report->report_type }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'تاريخ التقرير' : 'Report Date' }}</span>
                        <strong>{{ $report->report_date?->format('Y-m-d') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'مُقدَّم من' : 'Submitted By' }}</span>
                        <strong>{{ $report->submittedBy?->name ?? '-' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'تاريخ الرفع' : 'Submitted At' }}</span>
                        <strong>{{ $report->created_at->format('Y-m-d') }}</strong>
                    </li>
                    @if ($report->grade !== null)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'الدرجة' : 'Grade' }}</span>
                            <strong class="text-primary">{{ $report->grade }}/100</strong>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- بيانات المشروع --}}
            @if ($report->project)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <i class="bi bi-folder2-open"></i>
                        {{ $isAr ? 'المشروع' : 'Project' }}
                    </div>
                    <div class="card-body">
                        <div class="fw-bold mb-1">{{ $localized($report->project, 'title') }}</div>
                        <div class="small text-muted mb-2">{{ $report->project->project_number }}</div>
                        @if ($report->project->supervisor)
                            <div class="small text-muted mb-3">
                                <i class="bi bi-person-badge"></i>
                                {{ $report->project->supervisor->name }}
                            </div>
                        @endif
                        <a href="{{ route('projects.show', $report->project) }}"
                           class="btn btn-sm btn-outline-primary w-100">
                            {{ $isAr ? 'عرض المشروع' : 'View Project' }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection