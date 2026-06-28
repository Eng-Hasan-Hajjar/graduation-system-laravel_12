@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إضافة سنة أكاديمية' : 'Add Academic Year')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';
@endphp

<div class="container py-4" style="max-width: 760px;">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="mb-0">
            <i class="bi bi-calendar-plus text-primary"></i>
            {{ $isAr ? 'إضافة سنة أكاديمية جديدة' : 'Add New Academic Year' }}
        </h3>
        <a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary">
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

    <form action="{{ route('academic-years.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle"></i>
                {{ $isAr ? 'معلومات السنة الأكاديمية' : 'Academic Year Information' }}
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">
                            {{ $isAr ? 'الاسم (عربي)' : 'Name (Arabic)' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name_ar"
                               class="form-control @error('name_ar') is-invalid @enderror"
                               value="{{ old('name_ar') }}"
                               placeholder="{{ $isAr ? 'مثال: السنة الأكاديمية 2024-2025' : 'e.g. Academic Year 2024-2025' }}"
                               required>
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'الاسم (إنجليزي)' : 'Name (English)' }}</label>
                        <input type="text" name="name_en"
                               class="form-control @error('name_en') is-invalid @enderror"
                               value="{{ old('name_en') }}"
                               placeholder="e.g. Academic Year 2024-2025">
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            {{ $isAr ? 'سنة البداية' : 'Start Year' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="year_start"
                               class="form-control @error('year_start') is-invalid @enderror"
                               value="{{ old('year_start', now()->year) }}"
                               min="2000" max="2100" required>
                        @error('year_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            {{ $isAr ? 'سنة النهاية' : 'End Year' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="year_end"
                               class="form-control @error('year_end') is-invalid @enderror"
                               value="{{ old('year_end', now()->year + 1) }}"
                               min="2000" max="2100" required>
                        @error('year_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            {{ $isAr ? 'تاريخ البداية' : 'Start Date' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="start_date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date') }}" required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            {{ $isAr ? 'تاريخ النهاية' : 'End Date' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="end_date"
                               class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date') }}" required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_current"
                                   id="is_current" value="1"
                                   {{ old('is_current') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_current">
                                {{ $isAr ? 'تعيين كالسنة الأكاديمية الحالية' : 'Set as current academic year' }}
                            </label>
                        </div>
                        <small class="text-muted">
                            {{ $isAr ? 'تفعيل هذا الخيار سيلغي تعيين أي سنة أكاديمية حالية أخرى.' : 'Enabling this will unset any other current academic year.' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary">
                {{ $isAr ? 'إلغاء' : 'Cancel' }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle"></i>
                {{ $isAr ? 'حفظ السنة الأكاديمية' : 'Save Academic Year' }}
            </button>
        </div>
    </form>
</div>
@endsection