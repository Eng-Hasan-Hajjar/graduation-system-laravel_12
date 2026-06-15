@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إضافة مشروع جديد' : 'Add New Project')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $localized = function ($model, $field) use ($locale) {
        if (!$model) return '';
        return $model->{$field . '_' . $locale}
            ?? $model->{$field . '_ar'}
            ?? $model->{$field}
            ?? '';
    };
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-folder-plus text-primary"></i>
                {{ $isAr ? 'إضافة مشروع جديد' : 'Add New Project' }}
            </h3>
            <span class="text-muted">
                {{ $isAr ? 'رقم المشروع سيتم توليده تلقائياً عند الحفظ' : 'Project number will be generated automatically on save' }}
            </span>
        </div>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i>
            {{ $isAr ? 'رجوع' : 'Back' }}
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>{{ $isAr ? 'يوجد أخطاء في النموذج:' : 'There were some problems:' }}</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('projects.store') }}" method="POST">
        @csrf

        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-basic" type="button">
                    {{ $isAr ? 'المعلومات الأساسية' : 'Basic Info' }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-description" type="button">
                    {{ $isAr ? 'الوصف والأهداف' : 'Description' }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-supervision" type="button">
                    {{ $isAr ? 'الإشراف والفريق' : 'Supervision & Team' }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tools" type="button">
                    {{ $isAr ? 'الأدوات والتقنيات' : 'Tools & Tech' }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-dates" type="button">
                    {{ $isAr ? 'الخطة الزمنية' : 'Timeline' }}
                </button>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ============ المعلومات الأساسية ============ --}}
            <div class="tab-pane fade show active" id="tab-basic">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'العنوان (عربي)' : 'Title (Arabic)' }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title_ar"
                                       class="form-control @error('title_ar') is-invalid @enderror"
                                       value="{{ old('title_ar') }}" required>
                                @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'العنوان (إنجليزي)' : 'Title (English)' }}</label>
                                <input type="text" name="title_en"
                                       class="form-control @error('title_en') is-invalid @enderror"
                                       value="{{ old('title_en') }}">
                                @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'القسم' : 'Department' }} <span class="text-danger">*</span>
                                </label>
                                <select name="department_id" id="department_id"
                                        class="form-select @error('department_id') is-invalid @enderror" required>
                                    <option value="">{{ $isAr ? '-- اختر القسم --' : '-- Select Department --' }}</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $localized($department, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'الفصل الدراسي' : 'Semester' }} <span class="text-danger">*</span>
                                </label>
                                <select name="semester_id"
                                        class="form-select @error('semester_id') is-invalid @enderror" required>
                                    <option value="">{{ $isAr ? '-- اختر الفصل --' : '-- Select Semester --' }}</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}"
                                            {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                            {{ $localized($semester, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('semester_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">{{ $isAr ? 'نوع المشروع' : 'Project Type' }}</label>
                                <select name="project_type" class="form-select">
                                    @foreach (['graduation','research','capstone','industry','other'] as $type)
                                        <option value="{{ $type }}" {{ old('project_type', 'graduation') === $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">{{ $isAr ? 'المستوى الدراسي' : 'Academic Level' }}</label>
                                <input type="number" name="academic_year_level" class="form-control" min="1" max="6"
                                       value="{{ old('academic_year_level', 4) }}">
                            </div>

                            @if(isset($projectIdeas) && $projectIdeas->count())
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'فكرة المشروع (اختياري)' : 'Project Idea (optional)' }}</label>
                                <select name="project_idea_id" class="form-select">
                                    <option value="">{{ $isAr ? '-- بدون ربط بفكرة --' : '-- No linked idea --' }}</option>
                                    @foreach ($projectIdeas as $idea)
                                        <option value="{{ $idea->id }}"
                                            {{ old('project_idea_id', request('idea_id')) == $idea->id ? 'selected' : '' }}>
                                            {{ $localized($idea, 'title') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ الوصف والأهداف ============ --}}
            <div class="tab-pane fade" id="tab-description">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الوصف (عربي)' : 'Description (Arabic)' }}</label>
                                <textarea name="description_ar" rows="4" class="form-control">{{ old('description_ar') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الوصف (إنجليزي)' : 'Description (English)' }}</label>
                                <textarea name="description_en" rows="4" class="form-control">{{ old('description_en') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الأهداف (عربي)' : 'Objectives (Arabic)' }}</label>
                                <textarea name="objectives_ar" rows="4" class="form-control">{{ old('objectives_ar') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الأهداف (إنجليزي)' : 'Objectives (English)' }}</label>
                                <textarea name="objectives_en" rows="4" class="form-control">{{ old('objectives_en') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'النتائج المتوقعة (عربي)' : 'Expected Outcomes (Arabic)' }}</label>
                                <textarea name="expected_outcomes_ar" rows="4" class="form-control">{{ old('expected_outcomes_ar') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'النتائج المتوقعة (إنجليزي)' : 'Expected Outcomes (English)' }}</label>
                                <textarea name="expected_outcomes_en" rows="4" class="form-control">{{ old('expected_outcomes_en') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'منهجية العمل (عربي)' : 'Methodology (Arabic)' }}</label>
                                <textarea name="methodology_ar" rows="4" class="form-control">{{ old('methodology_ar') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'منهجية العمل (إنجليزي)' : 'Methodology (English)' }}</label>
                                <textarea name="methodology_en" rows="4" class="form-control">{{ old('methodology_en') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ الإشراف والفريق ============ --}}
            <div class="tab-pane fade" id="tab-supervision">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'المشرف الرئيسي' : 'Main Supervisor' }} <span class="text-danger">*</span>
                                </label>
                                <select name="supervisor_id"
                                        class="form-select @error('supervisor_id') is-invalid @enderror" required>
                                    <option value="">{{ $isAr ? '-- اختر المشرف --' : '-- Select Supervisor --' }}</option>
                                    @foreach ($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}"
                                            {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'المشرف المساعد' : 'Co-Supervisor' }}</label>
                                <select name="co_supervisor_id" class="form-select">
                                    <option value="">{{ $isAr ? '-- بدون مشرف مساعد --' : '-- None --' }}</option>
                                    @foreach ($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}"
                                            {{ old('co_supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ $isAr ? 'أعضاء فريق المشروع' : 'Project Team Members' }}</label>
                                <div id="studentsList" class="border rounded p-3" style="max-height: 240px; overflow-y: auto;">
                                    @forelse ($students as $student)
                                        <div class="form-check" data-dept="{{ $student->department_id }}">
                                            <input class="form-check-input" type="checkbox" name="students[]"
                                                   id="student_{{ $student->id }}" value="{{ $student->id }}"
                                                   {{ in_array($student->id, old('students', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="student_{{ $student->id }}">
                                                {{ $student->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">
                                            {{ $isAr ? 'لا يوجد طلاب نشطون' : 'No active students' }}
                                        </p>
                                    @endforelse
                                </div>
                                <small class="text-muted">
                                    @if($isAr)
                                        سيتم عرض الطلاب التابعين للقسم المختار أعلاه فقط. اختر القسم أولاً.
                                    @else
                                        Only students belonging to the selected department above will be shown. Select a department first.
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ الأدوات والتقنيات ============ --}}
            <div class="tab-pane fade" id="tab-tools">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الأدوات المستخدمة' : 'Tools Used' }}</label>
                                <input type="text" name="tools" class="form-control"
                                       value="{{ old('tools') }}"
                                       placeholder="Laravel, Vue.js, MySQL">
                                <small class="text-muted">
                                    {{ $isAr ? 'افصل بين الأدوات بفاصلة' : 'Separate tools with commas' }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'التقنيات المستخدمة' : 'Technologies Used' }}</label>
                                <input type="text" name="technologies" class="form-control"
                                       value="{{ old('technologies') }}"
                                       placeholder="REST API, WebSockets">
                                <small class="text-muted">
                                    {{ $isAr ? 'افصل بين التقنيات بفاصلة' : 'Separate technologies with commas' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ الخطة الزمنية ============ --}}
            <div class="tab-pane fade" id="tab-dates">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'تاريخ التسجيل' : 'Registration Date' }}</label>
                                <input type="date" name="registration_date" class="form-control"
                                       value="{{ old('registration_date', now()->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'تاريخ البدء المتوقع' : 'Start Date' }}</label>
                                <input type="date" name="start_date" class="form-control"
                                       value="{{ old('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'تاريخ الانتهاء المتوقع' : 'Expected End Date' }}</label>
                                <input type="date" name="expected_end_date" class="form-control"
                                       value="{{ old('expected_end_date') }}">
                            </div>
                        </div>
                        <p class="text-muted mt-3 mb-0">
                            @if($isAr)
                                تواريخ المناقشة والتقييم والأرشفة تتم إدارتها لاحقاً من صفحة تعديل المشروع.
                            @else
                                Defense, evaluation, and archiving dates are managed later from the project edit page.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

        </div>{{-- tab-content --}}

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                {{ $isAr ? 'إلغاء' : 'Cancel' }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle"></i>
                {{ $isAr ? 'حفظ المشروع' : 'Save Project' }}
            </button>
        </div>

    </form>
</div>

<script>
    function filterStudentsByDepartment() {
        var deptSelect = document.getElementById('department_id');
        var deptId = deptSelect ? deptSelect.value : '';
        document.querySelectorAll('#studentsList .form-check').forEach(function (item) {
            var matches = !deptId || item.dataset.dept === deptId;
            item.style.display = matches ? '' : 'none';
            if (!matches) {
                var input = item.querySelector('input[type="checkbox"]');
                if (input) input.checked = false;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var deptSelect = document.getElementById('department_id');
        if (deptSelect) {
            deptSelect.addEventListener('change', filterStudentsByDepartment);
            filterStudentsByDepartment();
        }
    });
</script>
@endsection