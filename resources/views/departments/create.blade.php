@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إضافة قسم جديد' : 'Add Department')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';
    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '';
@endphp

<div class="container py-4" style="max-width: 720px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            <i class="bi bi-building-add text-primary"></i>
            {{ $isAr ? 'إضافة قسم أكاديمي جديد' : 'Add New Academic Department' }}
        </h3>
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

    <form action="{{ route('departments.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12">
                        <label class="form-label">
                            {{ $isAr ? 'الكلية' : 'College' }}
                            <span class="text-danger">*</span>
                        </label>
                        <select name="college_id"
                                class="form-select @error('college_id') is-invalid @enderror" required>
                            <option value="">{{ $isAr ? '-- اختر الكلية --' : '-- Select College --' }}</option>
                            @foreach ($colleges as $college)
                                <option value="{{ $college->id }}"
                                    {{ old('college_id') == $college->id ? 'selected' : '' }}>
                                    {{ $localized($college, 'name') }}
                                </option>
                            @endforeach
                        </select>
                        @error('college_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            {{ $isAr ? 'اسم القسم (عربي)' : 'Department Name (Arabic)' }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name_ar"
                               class="form-control @error('name_ar') is-invalid @enderror"
                               value="{{ old('name_ar') }}"
                               placeholder="{{ $isAr ? 'مثال: قسم علوم الحاسب' : 'e.g. Computer Science' }}"
                               required>
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'اسم القسم (إنجليزي)' : 'Department Name (English)' }}</label>
                        <input type="text" name="name_en"
                               class="form-control @error('name_en') is-invalid @enderror"
                               value="{{ old('name_en') }}"
                               placeholder="e.g. Computer Science">
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ $isAr ? 'رمز القسم' : 'Department Code' }}</label>
                        <input type="text" name="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code') }}"
                               placeholder="CS"
                               maxlength="20">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                {{ $isAr ? 'حفظ القسم' : 'Save Department' }}
            </button>
        </div>
    </form>
</div>
@endsection