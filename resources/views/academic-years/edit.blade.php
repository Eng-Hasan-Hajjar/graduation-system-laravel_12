@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل السنة الأكاديمية' : 'Edit Academic Year')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';
@endphp

<div class="container py-4" style="max-width: 760px;">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-pencil-square text-primary"></i>
                {{ $isAr ? 'تعديل السنة الأكاديمية' : 'Edit Academic Year' }}
            </h3>
            <span class="text-muted">{{ $academicYear->name_ar }}</span>
        </div>
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

    <form action="{{ route('academic-years.update', $academicYear) }}" method="POST">
        @csrf
        @method('PUT')

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
                               value="{{ old('name_ar', $academicYear->name_ar) }}" required>
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'الاسم (إنجليزي)' : 'Name (English)' }}</label>
                        <input type="text" name="name_en"
                               class="form-control @error('name_en') is-invalid @enderror"
                               value="{{ old('name_en', $academicYear->name_en) }}">
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            {{ $isAr ? 'تاريخ البداية' : 'Start Date' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="start_date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $academicYear->start_date->format('Y-m-d')) }}"
                               required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            {{ $isAr ? 'تاريخ النهاية' : 'End Date' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="end_date"
                               class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date', $academicYear->end_date->format('Y-m-d')) }}"
                               required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_current"
                                   id="is_current" value="1"
                                   {{ old('is_current', $academicYear->is_current) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_current">
                                {{ $isAr ? 'تعيين كالسنة الأكاديمية الحالية' : 'Set as current academic year' }}
                            </label>
                        </div>
                        @if ($academicYear->is_current)
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i>
                                {{ $isAr ? 'هذه هي السنة الأكاديمية الحالية حالياً' : 'This is currently the active academic year' }}
                            </small>
                        @else
                            <small class="text-muted">
                                {{ $isAr ? 'تفعيل هذا الخيار سيلغي تعيين أي سنة أكاديمية حالية أخرى.' : 'Enabling this will unset any other current academic year.' }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- الفصول الدراسية المرتبطة --}}
        @if ($academicYear->semesters->count())
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-calendar-week"></i>
                    {{ $isAr ? 'الفصول الدراسية' : 'Semesters' }}
                    <span class="badge bg-primary ms-1">{{ $academicYear->semesters->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                                    <th>{{ $isAr ? 'تاريخ البداية' : 'Start' }}</th>
                                    <th>{{ $isAr ? 'تاريخ النهاية' : 'End' }}</th>
                                    <th>{{ $isAr ? 'الحالي' : 'Current' }}</th>
                                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($academicYear->semesters as $semester)
                                    <tr>
                                        <td>
                                            <strong>{{ $semester->{'name_'.$locale} ?? $semester->name_ar }}</strong>
                                        </td>
                                        <td>{{ $semester->start_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>{{ $semester->end_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>
                                            @if ($semester->is_current)
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-star-fill"></i>
                                                    {{ $isAr ? 'الحالي' : 'Current' }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if ($semester->is_active)
                                                <span class="badge bg-success">{{ $isAr ? 'نشط' : 'Active' }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $isAr ? 'غير نشط' : 'Inactive' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        {{ $isAr ? 'لإدارة الفصول الدراسية بشكل تفصيلي، استخدم صفحة الأقسام.' : 'To manage semesters in detail, use the departments section.' }}
                    </small>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary">
                {{ $isAr ? 'إلغاء' : 'Cancel' }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle"></i>
                {{ $isAr ? 'حفظ التعديلات' : 'Save Changes' }}
            </button>
        </div>
    </form>
</div>
@endsection