@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل بيانات المستخدم' : 'Edit User')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $roleLabels = [
        'admin'            => $isAr ? 'مسؤول النظام' : 'Admin',
        'coordinator'      => $isAr ? 'منسق'          : 'Coordinator',
        'supervisor'       => $isAr ? 'مشرف'          : 'Supervisor',
        'committee_member' => $isAr ? 'عضو لجنة'      : 'Committee Member',
        'student'          => $isAr ? 'طالب'          : 'Student',
    ];

    $statusLabels = [
        'active'    => $isAr ? 'نشط'   : 'Active',
        'inactive'  => $isAr ? 'معطّل' : 'Inactive',
        'suspended' => $isAr ? 'موقوف' : 'Suspended',
    ];

    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '';
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-pencil-square text-primary"></i>
                {{ $isAr ? 'تعديل بيانات المستخدم' : 'Edit User' }}
            </h3>
            <span class="text-muted">{{ $user->name_ar }}</span>
        </div>
        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
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

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-8">

                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-person"></i>
                        {{ $isAr ? 'المعلومات الأساسية' : 'Basic Information' }}
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
                                <label class="form-label">
                                    {{ $isAr ? 'البريد الإلكتروني' : 'Email' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'رقم الجوال' : 'Phone' }}</label>
                                <input type="text" name="phone"
                                       class="form-control"
                                       value="{{ old('phone', $user->phone) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'الدور' : 'Role' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="role"
                                        class="form-select @error('role') is-invalid @enderror" required>
                                    @foreach ($roleLabels as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('role', $user->role) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'القسم' : 'Department' }}</label>
                                <select name="department_id"
                                        class="form-select @error('department_id') is-invalid @enderror">
                                    <option value="">{{ $isAr ? '-- بدون قسم --' : '-- None --' }}</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $localized($department, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'الحالة' : 'Status' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="status"
                                        class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach ($statusLabels as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('status', $user->status) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            @if ($user->isStaff())
                                <div class="col-md-6">
                                    <label class="form-label">{{ $isAr ? 'الرتبة الأكاديمية' : 'Academic Rank' }}</label>
                                    <select name="academic_rank" class="form-select">
                                        <option value="">{{ $isAr ? '-- اختر --' : '-- Select --' }}</option>
                                        @foreach ([
                                            'professor'           => $isAr ? 'أستاذ' : 'Professor',
                                            'associate_professor' => $isAr ? 'أستاذ مشارك' : 'Associate Professor',
                                            'assistant_professor' => $isAr ? 'أستاذ مساعد' : 'Assistant Professor',
                                            'lecturer'            => $isAr ? 'محاضر' : 'Lecturer',
                                            'instructor'          => $isAr ? 'مدرّس' : 'Instructor',
                                        ] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('academic_rank', $user->academic_rank) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <i class="bi bi-person-circle"></i>
                        {{ $isAr ? 'معلومات الحساب' : 'Account Info' }}
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="{{ $user->avatar_url }}"
                                 class="rounded-circle mb-2"
                                 style="width:80px;height:80px;object-fit:cover;"
                                 alt="{{ $user->name }}">
                            <div class="fw-bold">{{ $user->name_ar }}</div>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>

                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ $isAr ? 'تاريخ الإنشاء' : 'Created' }}</span>
                                <strong>{{ $user->created_at->format('Y-m-d') }}</strong>
                            </li>
                            @if ($user->last_login_at)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>{{ $isAr ? 'آخر دخول' : 'Last Login' }}</span>
                                    <strong>{{ $user->last_login_at->diffForHumans() }}</strong>
                                </li>
                            @endif
                        </ul>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle"></i>
                                {{ $isAr ? 'حفظ التعديلات' : 'Save Changes' }}
                            </button>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                                {{ $isAr ? 'إلغاء' : 'Cancel' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection