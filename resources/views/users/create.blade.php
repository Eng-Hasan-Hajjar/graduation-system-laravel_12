@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إضافة مستخدم جديد' : 'Add New User')

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

    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '';
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="mb-0">
            <i class="bi bi-person-plus text-primary"></i>
            {{ $isAr ? 'إضافة مستخدم جديد' : 'Add New User' }}
        </h3>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
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

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">

                {{-- المعلومات الأساسية --}}
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
                                       value="{{ old('name_ar') }}" required>
                                @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الاسم (إنجليزي)' : 'Name (English)' }}</label>
                                <input type="text" name="name_en"
                                       class="form-control @error('name_en') is-invalid @enderror"
                                       value="{{ old('name_en') }}">
                                @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'البريد الإلكتروني' : 'Email' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'رقم الجوال' : 'Phone' }}</label>
                                <input type="text" name="phone"
                                       class="form-control"
                                       value="{{ old('phone') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'كلمة المرور' : 'Password' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'تأكيد كلمة المرور' : 'Confirm Password' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation"
                                       class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- الدور والقسم --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-shield-check"></i>
                        {{ $isAr ? 'الدور والتخصص' : 'Role & Department' }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'الدور' : 'Role' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="role" id="roleSelect"
                                        class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">{{ $isAr ? '-- اختر الدور --' : '-- Select Role --' }}</option>
                                    @foreach ($roleLabels as $value => $label)
                                        <option value="{{ $value }}" {{ old('role') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'الكلية' : 'College' }}</label>
                                <select name="college_id" class="form-select">
                                    <option value="">{{ $isAr ? '-- اختر الكلية --' : '-- Select College --' }}</option>
                                    @foreach ($colleges as $college)
                                        <option value="{{ $college->id }}" {{ old('college_id') == $college->id ? 'selected' : '' }}>
                                            {{ $localized($college, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'القسم' : 'Department' }}</label>
                                <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                    <option value="">{{ $isAr ? '-- اختر القسم --' : '-- Select Department --' }}</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $localized($department, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- حقول الطالب --}}
                            <div class="col-md-6 student-fields" style="display:none;">
                                <label class="form-label">{{ $isAr ? 'الرقم الجامعي' : 'Student ID' }}</label>
                                <input type="text" name="student_id"
                                       class="form-control @error('student_id') is-invalid @enderror"
                                       value="{{ old('student_id') }}">
                                @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- حقول الكادر الأكاديمي --}}
                            <div class="col-md-6 staff-fields" style="display:none;">
                                <label class="form-label">{{ $isAr ? 'الرقم الوظيفي' : 'Employee ID' }}</label>
                                <input type="text" name="employee_id"
                                       class="form-control @error('employee_id') is-invalid @enderror"
                                       value="{{ old('employee_id') }}">
                                @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 staff-fields" style="display:none;">
                                <label class="form-label">{{ $isAr ? 'الرتبة الأكاديمية' : 'Academic Rank' }}</label>
                                <select name="academic_rank" class="form-select">
                                    <option value="">{{ $isAr ? '-- اختر الرتبة --' : '-- Select Rank --' }}</option>
                                    @foreach ([
                                        'professor'           => $isAr ? 'أستاذ' : 'Professor',
                                        'associate_professor' => $isAr ? 'أستاذ مشارك' : 'Associate Professor',
                                        'assistant_professor' => $isAr ? 'أستاذ مساعد' : 'Assistant Professor',
                                        'lecturer'            => $isAr ? 'محاضر' : 'Lecturer',
                                        'instructor'          => $isAr ? 'مدرّس' : 'Instructor',
                                    ] as $value => $label)
                                        <option value="{{ $value }}" {{ old('academic_rank') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الشريط الجانبي --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <i class="bi bi-info-circle"></i>
                        {{ $isAr ? 'ملاحظات' : 'Notes' }}
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small text-muted mb-4">
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> {{ $isAr ? 'الحقول المعلّمة بـ * إجبارية' : 'Fields marked * are required' }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> {{ $isAr ? 'كلمة المرور يجب أن تكون 8 أحرف على الأقل' : 'Password must be at least 8 characters' }}</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> {{ $isAr ? 'سيكون المستخدم نشطاً تلقائياً عند الإنشاء' : 'User will be active by default' }}</li>
                        </ul>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle"></i>
                                {{ $isAr ? 'إنشاء المستخدم' : 'Create User' }}
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
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
    const roleSelect    = document.getElementById('roleSelect');
    const studentFields = document.querySelectorAll('.student-fields');
    const staffFields   = document.querySelectorAll('.staff-fields');

    function toggleFields() {
        const role = roleSelect.value;
        studentFields.forEach(el => el.style.display = role === 'student' ? '' : 'none');
        staffFields.forEach(el => el.style.display =
            ['supervisor','coordinator','committee_member','admin'].includes(role) ? '' : 'none');
    }

    roleSelect.addEventListener('change', toggleFields);
    toggleFields();
});
</script>
@endsection