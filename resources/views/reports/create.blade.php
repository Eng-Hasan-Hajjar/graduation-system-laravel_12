@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'رفع تقرير جديد' : 'Submit New Report')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '';

    $typeLabels = [
        'weekly'    => $isAr ? 'أسبوعي'  : 'Weekly',
        'monthly'   => $isAr ? 'شهري'    : 'Monthly',
        'milestone' => $isAr ? 'مرحلة'   : 'Milestone',
        'final'     => $isAr ? 'نهائي'   : 'Final',
        'other'     => $isAr ? 'أخرى'    : 'Other',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="mb-0">
            <i class="bi bi-file-earmark-plus text-primary"></i>
            {{ $isAr ? 'رفع تقرير جديد' : 'Submit New Report' }}
        </h3>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i>
            {{ $isAr ? 'رجوع' : 'Back' }}
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>{{ $isAr ? 'يوجد أخطاء:' : 'There were some problems:' }}</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">

                {{-- معلومات التقرير الأساسية --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-info-circle"></i>
                        {{ $isAr ? 'معلومات التقرير' : 'Report Information' }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">
                                    {{ $isAr ? 'المشروع' : 'Project' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="project_id"
                                        class="form-select @error('project_id') is-invalid @enderror" required>
                                    <option value="">
                                        {{ $isAr ? '-- اختر المشروع --' : '-- Select Project --' }}
                                    </option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                            {{ old('project_id', $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->project_number }} — {{ $localized($project, 'title') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'عنوان التقرير (عربي)' : 'Report Title (Arabic)' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title_ar"
                                       class="form-control @error('title_ar') is-invalid @enderror"
                                       value="{{ old('title_ar') }}" required>
                                @error('title_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'عنوان التقرير (إنجليزي)' : 'Report Title (English)' }}
                                </label>
                                <input type="text" name="title_en"
                                       class="form-control"
                                       value="{{ old('title_en') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'نوع التقرير' : 'Report Type' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="report_type" id="reportType"
                                        class="form-select @error('report_type') is-invalid @enderror" required>
                                    @foreach ($typeLabels as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('report_type', 'weekly') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('report_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4" id="weekField">
                                <label class="form-label">{{ $isAr ? 'رقم الأسبوع' : 'Week Number' }}</label>
                                <input type="number" name="week_number"
                                       class="form-control @error('week_number') is-invalid @enderror"
                                       value="{{ old('week_number') }}"
                                       min="1" max="52">
                                @error('week_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'تاريخ التقرير' : 'Report Date' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="report_date"
                                       class="form-control @error('report_date') is-invalid @enderror"
                                       value="{{ old('report_date', now()->format('Y-m-d')) }}" required>
                                @error('report_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- محتوى التقرير --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-card-text"></i>
                        {{ $isAr ? 'محتوى التقرير' : 'Report Content' }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'المحتوى (عربي)' : 'Content (Arabic)' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea name="content_ar" rows="8"
                                          class="form-control @error('content_ar') is-invalid @enderror"
                                          required>{{ old('content_ar') }}</textarea>
                                @error('content_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'المحتوى (إنجليزي)' : 'Content (English)' }}</label>
                                <textarea name="content_en" rows="8" class="form-control">{{ old('content_en') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- المرفقات --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <i class="bi bi-paperclip"></i>
                        {{ $isAr ? 'المرفقات (اختياري)' : 'Attachments (Optional)' }}
                    </div>
                    <div class="card-body">
                        <input type="file" name="files[]" class="form-control" multiple accept="*/*">
                        <small class="text-muted">
                            {{ $isAr ? 'الحد الأقصى لحجم كل ملف: 20 ميغابايت' : 'Max file size: 20MB per file' }}
                        </small>
                    </div>
                </div>
            </div>

            {{-- الشريط الجانبي --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <i class="bi bi-info-circle"></i>
                        {{ $isAr ? 'إرشادات التقرير' : 'Report Guidelines' }}
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small text-muted mb-4">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                {{ $isAr ? 'تأكد من اختيار المشروع الصحيح' : 'Make sure to select the correct project' }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                {{ $isAr ? 'التقارير الأسبوعية تتطلب رقم الأسبوع' : 'Weekly reports require week number' }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                {{ $isAr ? 'يمكن إرفاق ملفات متعددة' : 'Multiple files can be attached' }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success"></i>
                                {{ $isAr ? 'سيتم إشعار المشرف عند رفع التقرير' : 'Supervisor will be notified upon submission' }}
                            </li>
                        </ul>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i>
                                {{ $isAr ? 'رفع التقرير' : 'Submit Report' }}
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                {{ $isAr ? 'إلغاء' : 'Cancel' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const reportType = document.getElementById('reportType');
    const weekField  = document.getElementById('weekField');

    function toggleWeekField() {
        weekField.style.display = reportType.value === 'weekly' ? '' : 'none';
    }

    reportType.addEventListener('change', toggleWeekField);
    toggleWeekField();
});
</script>
@endsection