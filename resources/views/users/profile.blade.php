@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'My Profile')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';
    $user   = auth()->user();

    $roleLabels = [
        'admin'            => $isAr ? 'مسؤول النظام' : 'Admin',
        'coordinator'      => $isAr ? 'منسق'          : 'Coordinator',
        'supervisor'       => $isAr ? 'مشرف'          : 'Supervisor',
        'committee_member' => $isAr ? 'عضو لجنة'      : 'Committee Member',
        'student'          => $isAr ? 'طالب'          : 'Student',
    ];
@endphp

<div class="container py-4">

    <h3 class="mb-4">
        <i class="bi bi-person-circle text-primary"></i>
        {{ $isAr ? 'الملف الشخصي' : 'My Profile' }}
    </h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

        {{-- الشريط الجانبي --}}
        <div class="col-lg-4">
            <div class="card shadow-sm text-center p-4 mb-4">
                <img src="{{ $user->avatar_url }}"
                     class="rounded-circle mx-auto mb-3"
                     style="width:100px;height:100px;object-fit:cover;"
                     alt="{{ $user->name }}">
                <h5 class="mb-1">{{ $user->name_ar }}</h5>
                @if ($user->name_en)
                    <div class="text-muted small mb-2">{{ $user->name_en }}</div>
                @endif
                <span class="badge bg-primary">{{ $roleLabels[$user->role] ?? $user->role }}</span>

                <hr>

                <ul class="list-unstyled text-start small text-muted">
                    <li class="mb-1"><i class="bi bi-envelope"></i> {{ $user->email }}</li>
                    @if ($user->phone)
                        <li class="mb-1"><i class="bi bi-phone"></i> {{ $user->phone }}</li>
                    @endif
                    @if ($user->department)
                        <li class="mb-1">
                            <i class="bi bi-building"></i>
                            {{ $user->department->{'name_'.$locale} ?? $user->department->name_ar }}
                        </li>
                    @endif
                    @if ($user->department?->college)
                        <li class="mb-1">
                            <i class="bi bi-bank"></i>
                            {{ $user->department->college->{'name_'.$locale} ?? $user->department->college->name_ar }}
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- النماذج --}}
        <div class="col-lg-8">

            {{-- تعديل المعلومات الشخصية --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-person-gear"></i>
                    {{ $isAr ? 'تعديل المعلومات الشخصية' : 'Edit Personal Information' }}
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'الاسم (عربي)' : 'Name (Arabic)' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name_ar"
                                       class="form-control @error('name_ar') is-invalid @enderror"
                                       value="{{ old('name_ar', $user->name_ar) }}" required>
                                @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الاسم (إنجليزي)' : 'Name (English)' }}</label>
                                <input type="text" name="name_en"
                                       class="form-control"
                                       value="{{ old('name_en', $user->name_en) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'رقم الجوال' : 'Phone' }}</label>
                                <input type="text" name="phone"
                                       class="form-control"
                                       value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الصورة الشخصية' : 'Avatar' }}</label>
                                <input type="file" name="avatar" class="form-control" accept="image/*">
                                <small class="text-muted">{{ $isAr ? 'الحد الأقصى 2 ميغابايت' : 'Max 2MB' }}</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle"></i>
                                {{ $isAr ? 'حفظ التغييرات' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- تغيير كلمة المرور --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-lock"></i>
                    {{ $isAr ? 'تغيير كلمة المرور' : 'Change Password' }}
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'كلمة المرور الحالية' : 'Current Password' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror" required>
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'كلمة المرور الجديدة' : 'New Password' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'تأكيد كلمة المرور' : 'Confirm Password' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation"
                                       class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-key"></i>
                                {{ $isAr ? 'تغيير كلمة المرور' : 'Change Password' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- تفضيلات النظام --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-gear"></i>
                    {{ $isAr ? 'تفضيلات النظام' : 'System Preferences' }}
                </div>
                <div class="card-body">
                    <form action="{{ route('user.preferences') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'لغة الواجهة' : 'Interface Language' }}</label>
                                <select name="lang" class="form-select">
                                    <option value="ar" {{ ($user->lang_preference ?? 'ar') === 'ar' ? 'selected' : '' }}>
                                        العربية
                                    </option>
                                    <option value="en" {{ ($user->lang_preference ?? 'ar') === 'en' ? 'selected' : '' }}>
                                        English
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'مظهر النظام' : 'Theme' }}</label>
                                <select name="theme" class="form-select">
                                    <option value="light" {{ ($user->theme_preference ?? 'light') === 'light' ? 'selected' : '' }}>
                                        {{ $isAr ? 'فاتح' : 'Light' }}
                                    </option>
                                    <option value="dark" {{ ($user->theme_preference ?? 'light') === 'dark' ? 'selected' : '' }}>
                                        {{ $isAr ? 'داكن' : 'Dark' }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-save"></i>
                                {{ $isAr ? 'حفظ التفضيلات' : 'Save Preferences' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection