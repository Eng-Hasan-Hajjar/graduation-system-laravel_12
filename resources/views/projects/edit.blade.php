@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل المشروع' : 'Edit Project')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    // دالة مساعدة لجلب الحقل المترجم (name_ar / name_en) مع fallback
    $localized = function ($model, $field) use ($locale) {
        if (!$model) return '';
        return $model->{$field . '_' . $locale}
            ?? $model->{$field . '_ar'}
            ?? $model->{$field}
            ?? '';
    };

    // تحويل التاريخ/الوقت بأمان مهما كان نوعه (سلسلة أو Carbon)
    $fmtDate = function ($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '';
    };
    $fmtTime = function ($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i') : '';
    };

    $isAdmin = in_array(auth()->user()->role ?? '', ['admin', 'coordinator']);
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-pencil-square text-primary"></i>
                {{ $isAr ? 'تعديل المشروع' : 'Edit Project' }}
            </h3>
            <span class="text-muted">
                {{ $isAr ? 'رقم المشروع' : 'Project Number' }}: <strong>{{ $project->project_number }}</strong>
            </span>
        </div>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
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

    <form action="{{ route('projects.update', $project) }}" method="POST">
        @csrf
        @method('PUT')

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
                    {{ $isAr ? 'الإشراف' : 'Supervision' }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tools" type="button">
                    {{ $isAr ? 'الأدوات والتقنيات' : 'Tools & Tech' }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-dates" type="button">
                    {{ $isAr ? 'التواريخ والدفاع' : 'Dates & Defense' }}
                </button>
            </li>
            @if($isAdmin)
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-status" type="button">
                    {{ $isAr ? 'الحالة والتقييم' : 'Status & Evaluation' }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-archive" type="button">
                    {{ $isAr ? 'الأرشفة' : 'Archiving' }}
                </button>
            </li>
            @endif
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
                                       value="{{ old('title_ar', $project->title_ar) }}" required>
                                @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'العنوان (إنجليزي)' : 'Title (English)' }}</label>
                                <input type="text" name="title_en"
                                       class="form-control @error('title_en') is-invalid @enderror"
                                       value="{{ old('title_en', $project->title_en) }}">
                                @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'القسم' : 'Department' }} <span class="text-danger">*</span>
                                </label>
                                <select name="department_id"
                                        class="form-select @error('department_id') is-invalid @enderror" required>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id', $project->department_id) == $department->id ? 'selected' : '' }}>
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
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}"
                                            {{ old('semester_id', $project->semester_id) == $semester->id ? 'selected' : '' }}>
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
                                        <option value="{{ $type }}"
                                            {{ old('project_type', $project->project_type) === $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">{{ $isAr ? 'المستوى الدراسي' : 'Academic Level' }}</label>
                                <input type="number" name="academic_year_level" class="form-control" min="1" max="6"
                                       value="{{ old('academic_year_level', $project->academic_year_level) }}">
                            </div>
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
                                <textarea name="description_ar" rows="4" class="form-control">{{ old('description_ar', $project->description_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الوصف (إنجليزي)' : 'Description (English)' }}</label>
                                <textarea name="description_en" rows="4" class="form-control">{{ old('description_en', $project->description_en) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الأهداف (عربي)' : 'Objectives (Arabic)' }}</label>
                                <textarea name="objectives_ar" rows="4" class="form-control">{{ old('objectives_ar', $project->objectives_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الأهداف (إنجليزي)' : 'Objectives (English)' }}</label>
                                <textarea name="objectives_en" rows="4" class="form-control">{{ old('objectives_en', $project->objectives_en) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'النتائج المتوقعة (عربي)' : 'Expected Outcomes (Arabic)' }}</label>
                                <textarea name="expected_outcomes_ar" rows="4" class="form-control">{{ old('expected_outcomes_ar', $project->expected_outcomes_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'النتائج المتوقعة (إنجليزي)' : 'Expected Outcomes (English)' }}</label>
                                <textarea name="expected_outcomes_en" rows="4" class="form-control">{{ old('expected_outcomes_en', $project->expected_outcomes_en) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'منهجية العمل (عربي)' : 'Methodology (Arabic)' }}</label>
                                <textarea name="methodology_ar" rows="4" class="form-control">{{ old('methodology_ar', $project->methodology_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'منهجية العمل (إنجليزي)' : 'Methodology (English)' }}</label>
                                <textarea name="methodology_en" rows="4" class="form-control">{{ old('methodology_en', $project->methodology_en) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ الإشراف ============ --}}
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
                                    @foreach ($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}"
                                            {{ old('supervisor_id', $project->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'المشرف المساعد' : 'Co-Supervisor' }}</label>
                                <select name="co_supervisor_id" class="form-select">
                                    <option value="">{{ $isAr ? '— بدون مشرف مساعد —' : '— None —' }}</option>
                                    @foreach ($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}"
                                            {{ old('co_supervisor_id', $project->co_supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ $isAr ? 'طلاب القسم' : 'Department Students' }}</label>
                                <div class="border rounded p-3" style="max-height: 220px; overflow-y: auto;">
                                    @forelse ($students as $student)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="students[]"
                                                   id="student_{{ $student->id }}" value="{{ $student->id }}"
                                                   {{ in_array($student->id, old('students', method_exists($project, 'students') ? $project->students->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="student_{{ $student->id }}">
                                                {{ $student->name }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">
                                            {{ $isAr ? 'لا يوجد طلاب في هذا القسم' : 'No students in this department' }}
                                        </p>
                                    @endforelse
                                </div>
                                <small class="text-muted">
                                    @if($isAr)
                                        ملاحظة: هذا القسم اختياري ويتطلب وجود علاقة students() في موديل Project (جدول وسيط project_student).
                                    @else
                                        Note: this section is optional and requires a students() relation on the Project model (pivot table project_student).
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
                                       value="{{ old('tools', is_array($project->tools) ? implode(', ', $project->tools) : $project->tools) }}"
                                       placeholder="Laravel, Vue.js, MySQL">
                                <small class="text-muted">
                                    {{ $isAr ? 'افصل بين الأدوات بفاصلة' : 'Separate tools with commas' }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'التقنيات المستخدمة' : 'Technologies Used' }}</label>
                                <input type="text" name="technologies" class="form-control"
                                       value="{{ old('technologies', is_array($project->technologies) ? implode(', ', $project->technologies) : $project->technologies) }}"
                                       placeholder="REST API, WebSockets">
                                <small class="text-muted">
                                    {{ $isAr ? 'افصل بين التقنيات بفاصلة' : 'Separate technologies with commas' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ التواريخ والدفاع ============ --}}
            <div class="tab-pane fade" id="tab-dates">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'تاريخ التسجيل' : 'Registration Date' }}</label>
                                <input type="date" name="registration_date" class="form-control"
                                       value="{{ old('registration_date', $fmtDate($project->registration_date)) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'تاريخ البدء' : 'Start Date' }}</label>
                                <input type="date" name="start_date" class="form-control"
                                       value="{{ old('start_date', $fmtDate($project->start_date)) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'تاريخ الانتهاء المتوقع' : 'Expected End Date' }}</label>
                                <input type="date" name="expected_end_date" class="form-control"
                                       value="{{ old('expected_end_date', $fmtDate($project->expected_end_date)) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'تاريخ التسليم' : 'Submission Date' }}</label>
                                <input type="date" name="submission_date" class="form-control"
                                       value="{{ old('submission_date', $fmtDate($project->submission_date)) }}">
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'تاريخ المناقشة' : 'Defense Date' }}</label>
                                <input type="date" name="defense_date" class="form-control"
                                       value="{{ old('defense_date', $fmtDate($project->defense_date)) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'وقت المناقشة' : 'Defense Time' }}</label>
                                <input type="time" name="defense_time" class="form-control"
                                       value="{{ old('defense_time', $fmtTime($project->defense_time)) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'مكان المناقشة' : 'Defense Location' }}</label>
                                <input type="text" name="defense_location" class="form-control"
                                       value="{{ old('defense_location', $project->defense_location) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'القاعة' : 'Room' }}</label>
                                <input type="text" name="defense_room" class="form-control"
                                       value="{{ old('defense_room', $project->defense_room) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'تاريخ المناقشة الفعلي' : 'Actual Defense Date' }}</label>
                                <input type="date" name="actual_defense_date" class="form-control"
                                       value="{{ old('actual_defense_date', $fmtDate($project->actual_defense_date)) }}">
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_discussed" id="is_discussed" value="1"
                                           {{ old('is_discussed', $project->is_discussed) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_discussed">
                                        {{ $isAr ? 'تمت المناقشة' : 'Discussed' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($isAdmin)
            {{-- ============ الحالة والتقييم (للمشرفين/الإدارة فقط) ============ --}}
            <div class="tab-pane fade" id="tab-status">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'حالة المشروع' : 'Project Status' }}</label>
                                <select name="status" class="form-select">
                                    @foreach (['pending','approved','rejected','in_progress','submitted','defended','archived'] as $status)
                                        <option value="{{ $status }}"
                                            {{ old('status', $project->status) === $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'الدرجة النهائية' : 'Final Grade' }}</label>
                                <input type="number" step="0.01" min="0" max="100" name="final_grade" class="form-control"
                                       value="{{ old('final_grade', $project->final_grade) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'التقدير' : 'Grade Letter' }}</label>
                                <input type="text" name="grade_letter" maxlength="2" class="form-control"
                                       value="{{ old('grade_letter', $project->grade_letter) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ $isAr ? 'سبب الرفض (إن وجد)' : 'Rejection Reason' }}</label>
                                <textarea name="rejection_reason" rows="2" class="form-control">{{ old('rejection_reason', $project->rejection_reason) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'ملاحظات اللجنة (عربي)' : 'Committee Notes (Arabic)' }}</label>
                                <textarea name="committee_notes_ar" rows="3" class="form-control">{{ old('committee_notes_ar', $project->committee_notes_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'ملاحظات اللجنة (إنجليزي)' : 'Committee Notes (English)' }}</label>
                                <textarea name="committee_notes_en" rows="3" class="form-control">{{ old('committee_notes_en', $project->committee_notes_en) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">{{ $isAr ? 'ملاحظات المشرف' : 'Supervisor Notes' }}</label>
                                <textarea name="supervisor_notes" rows="3" class="form-control">{{ old('supervisor_notes', $project->supervisor_notes) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'نسبة الإنجاز' : 'Progress' }}:
                                    <span id="progressValue">{{ old('progress_percentage', $project->progress_percentage) }}</span>%
                                </label>
                                <input type="range" name="progress_percentage" min="0" max="100" class="form-range"
                                       value="{{ old('progress_percentage', $project->progress_percentage) }}"
                                       oninput="document.getElementById('progressValue').textContent = this.value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ الأرشفة (للإدارة فقط) ============ --}}
            <div class="tab-pane fade" id="tab-archive">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_archived" id="is_archived" value="1"
                                           {{ old('is_archived', $project->is_archived) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_archived">
                                        {{ $isAr ? 'مؤرشف' : 'Archived' }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'تاريخ الأرشفة' : 'Archived At' }}</label>
                                <input type="date" name="archived_at" class="form-control"
                                       value="{{ old('archived_at', $fmtDate($project->archived_at)) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ $isAr ? 'ملاحظات الأرشفة' : 'Archive Notes' }}</label>
                                <textarea name="archive_notes" rows="3" class="form-control">{{ old('archive_notes', $project->archive_notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>{{-- tab-content --}}

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
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