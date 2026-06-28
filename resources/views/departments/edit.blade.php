@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل القسم' : 'Edit Department')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';
    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '';
@endphp

<div class="container py-4" style="max-width: 720px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-pencil-square text-primary"></i>
                {{ $isAr ? 'تعديل القسم' : 'Edit Department' }}
            </h3>
            <span class="text-muted">{{ $department->name_ar }}</span>
        </div>
        <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
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

    <form action="{{ route('departments.update', $department) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label">{{ $isAr ? 'الكلية' : 'College' }}</label>
                        <select name="college_id" class="form-select" disabled>
                            @foreach ($colleges as $college)
                                <option value="{{ $college->id }}"
                                    {{ $department->college_id == $college->id ? 'selected' : '' }}>
                                    {{ $localized($college, 'name') }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            {{ $isAr ? 'لا يمكن تغيير الكلية بعد الإنشاء' : 'College cannot be changed after creation' }}
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            {{ $isAr ? 'اسم القسم (عربي)' : 'Department Name (Arabic)' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name_ar"
                               class="form-control @error('name_ar') is-invalid @enderror"
                               value="{{ old('name_ar', $department->name_ar) }}" required>
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'اسم القسم (إنجليزي)' : 'Department Name (English)' }}</label>
                        <input type="text" name="name_en"
                               class="form-control @error('name_en') is-invalid @enderror"
                               value="{{ old('name_en', $department->name_en) }}">
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ $isAr ? 'رمز القسم' : 'Department Code' }}</label>
                        <input type="text" name="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $department->code) }}"
                               maxlength="20">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active"
                                   id="is_active" value="1"
                                   {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{ $isAr ? 'القسم نشط' : 'Department Active' }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
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