@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل فكرة المشروع' : 'Edit Project Idea')

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

    $toCsv = function ($value) {
        return is_array($value) ? implode(', ', $value) : $value;
    };

    $types = [
        'graduation' => $isAr ? 'مشروع تخرج' : 'Graduation Project',
        'semester'   => $isAr ? 'مشروع فصلي' : 'Semester Project',
        'year4'      => $isAr ? 'مشروع سنة رابعة' : 'Year 4 Project',
        'research'   => $isAr ? 'بحثي' : 'Research',
    ];

    $priorities = [
        'low'    => $isAr ? 'منخفضة' : 'Low',
        'medium' => $isAr ? 'متوسطة' : 'Medium',
        'high'   => $isAr ? 'عالية' : 'High',
    ];

    $statusLabels = [
        'pending'  => $isAr ? 'قيد المراجعة' : 'Pending',
        'approved' => $isAr ? 'معتمدة' : 'Approved',
        'rejected' => $isAr ? 'مرفوضة' : 'Rejected',
        'taken'    => $isAr ? 'مأخوذة' : 'Taken',
        'archived' => $isAr ? 'مؤرشفة' : 'Archived',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-pencil-square text-primary"></i>
                {{ $isAr ? 'تعديل فكرة المشروع' : 'Edit Project Idea' }}
            </h3>
            <span class="badge bg-{{ $idea->status_color }}">
                {{ $statusLabels[$idea->status] ?? $idea->status }}
            </span>
        </div>
        <a href="{{ route('ideas.show', $idea) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i> {{ $isAr ? 'رجوع' : 'Back' }}
        </a>
    </div>

    @if ($idea->status === 'rejected' && $idea->rejection_reason)
        <div class="alert alert-danger">
            <strong>{{ $isAr ? 'سبب الرفض السابق' : 'Previous Rejection Reason' }}:</strong>
            {{ $idea->rejection_reason }}
        </div>
    @endif

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

    <form action="{{ route('ideas.update', $idea) }}" method="POST">
        @csrf
        @method('PUT')

        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-basic" type="button">
                    {{ $isAr ? 'المعلومات الأساسية' : 'Basic Info' }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-details" type="button">
                    {{ $isAr ? 'الوصف والتفاصيل' : 'Description & Details' }}
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tools" type="button">
                    {{ $isAr ? 'الأدوات والمتطلبات' : 'Tools & Requirements' }}
                </button>
            </li>
            @if (Auth::user()->isAdmin())
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-admin" type="button">
                        {{ $isAr ? 'إعدادات إضافية' : 'Additional Settings' }}
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
                                    {{ $isAr ? 'عنوان الفكرة (عربي)' : 'Idea Title (Arabic)' }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title_ar"
                                       class="form-control @error('title_ar') is-invalid @enderror"
                                       value="{{ old('title_ar', $idea->title_ar) }}" required>
                                @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'عنوان الفكرة (إنجليزي)' : 'Idea Title (English)' }}</label>
                                <input type="text" name="title_en"
                                       class="form-control @error('title_en') is-invalid @enderror"
                                       value="{{ old('title_en', $idea->title_en) }}">
                                @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    {{ $isAr ? 'القسم' : 'Department' }} <span class="text-danger">*</span>
                                </label>
                                <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id', $idea->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $localized($department, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'الفصل الدراسي' : 'Semester' }}</label>
                                <select name="semester_id" class="form-select @error('semester_id') is-invalid @enderror">
                                    <option value="">{{ $isAr ? '-- غير محدد --' : '-- Not specified --' }}</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->id }}"
                                            {{ old('semester_id', $idea->semester_id) == $semester->id ? 'selected' : '' }}>
                                            {{ $localized($semester, 'name') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('semester_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    {{ $isAr ? 'نوع المشروع' : 'Project Type' }} <span class="text-danger">*</span>
                                </label>
                                <select name="project_type" class="form-select @error('project_type') is-invalid @enderror" required>
                                    @foreach ($types as $value => $label)
                                        <option value="{{ $value }}" {{ old('project_type', $idea->project_type) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'الأولوية' : 'Priority' }}</label>
                                <select name="priority" class="form-select">
                                    @foreach ($priorities as $value => $label)
                                        <option value="{{ $value }}" {{ old('priority', $idea->priority) === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'التصنيف (عربي)' : 'Category (Arabic)' }}</label>
                                <input type="text" name="category_ar" class="form-control" value="{{ old('category_ar', $idea->category_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'التصنيف (إنجليزي)' : 'Category (English)' }}</label>
                                <input type="text" name="category_en" class="form-control" value="{{ old('category_en', $idea->category_en) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ الوصف والتفاصيل ============ --}}
            <div class="tab-pane fade" id="tab-details">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'وصف الفكرة (عربي)' : 'Description (Arabic)' }} <span class="text-danger">*</span>
                                </label>
                                <textarea name="description_ar" rows="4"
                                          class="form-control @error('description_ar') is-invalid @enderror" required>{{ old('description_ar', $idea->description_ar) }}</textarea>
                                @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'وصف الفكرة (إنجليزي)' : 'Description (English)' }}</label>
                                <textarea name="description_en" rows="4" class="form-control">{{ old('description_en', $idea->description_en) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الأهداف (عربي)' : 'Objectives (Arabic)' }}</label>
                                <textarea name="objectives_ar" rows="4" class="form-control">{{ old('objectives_ar', $idea->objectives_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'الأهداف (إنجليزي)' : 'Objectives (English)' }}</label>
                                <textarea name="objectives_en" rows="4" class="form-control">{{ old('objectives_en', $idea->objectives_en) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'النتائج المتوقعة (عربي)' : 'Expected Outcomes (Arabic)' }}</label>
                                <textarea name="expected_outcomes_ar" rows="4" class="form-control">{{ old('expected_outcomes_ar', $idea->expected_outcomes_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'النتائج المتوقعة (إنجليزي)' : 'Expected Outcomes (English)' }}</label>
                                <textarea name="expected_outcomes_en" rows="4" class="form-control">{{ old('expected_outcomes_en', $idea->expected_outcomes_en) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'المتطلبات المسبقة (عربي)' : 'Requirements (Arabic)' }}</label>
                                <textarea name="requirements_ar" rows="3" class="form-control">{{ old('requirements_ar', $idea->requirements_ar) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'المتطلبات المسبقة (إنجليزي)' : 'Requirements (English)' }}</label>
                                <textarea name="requirements_en" rows="3" class="form-control">{{ old('requirements_en', $idea->requirements_en) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ الأدوات والمتطلبات ============ --}}
            <div class="tab-pane fade" id="tab-tools">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'الأدوات المقترحة' : 'Suggested Tools' }}</label>
                                <input type="text" name="tools" class="form-control"
                                       value="{{ old('tools', $toCsv($idea->tools)) }}" placeholder="Laravel, Flutter, MySQL">
                                <small class="text-muted">{{ $isAr ? 'افصل بفاصلة' : 'Comma separated' }}</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'التقنيات المقترحة' : 'Suggested Technologies' }}</label>
                                <input type="text" name="technologies" class="form-control"
                                       value="{{ old('technologies', $toCsv($idea->technologies)) }}" placeholder="AI, Machine Learning">
                                <small class="text-muted">{{ $isAr ? 'افصل بفاصلة' : 'Comma separated' }}</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'الوسوم (Tags)' : 'Tags' }}</label>
                                <input type="text" name="tags" class="form-control"
                                       value="{{ old('tags', $toCsv($idea->tags)) }}" placeholder="AI, Mobile, Healthcare">
                                <small class="text-muted">{{ $isAr ? 'افصل بفاصلة' : 'Comma separated' }}</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'الحد الأدنى لعدد الطلاب' : 'Min Students' }}</label>
                                <input type="number" name="min_students" min="1"
                                       class="form-control @error('min_students') is-invalid @enderror"
                                       value="{{ old('min_students', $idea->min_students) }}">
                                @error('min_students') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'الحد الأقصى لعدد الطلاب' : 'Max Students' }}</label>
                                <input type="number" name="max_students" min="1"
                                       class="form-control @error('max_students') is-invalid @enderror"
                                       value="{{ old('max_students', $idea->max_students) }}">
                                @error('max_students') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (Auth::user()->isAdmin())
            {{-- ============ إعدادات إضافية ============ --}}
            <div class="tab-pane fade" id="tab-admin">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                                   {{ old('is_featured', $idea->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                {{ $isAr ? 'تمييز هذه الفكرة (Featured)' : 'Feature this idea' }}
                            </label>
                        </div>
                        <p class="text-muted mt-3 mb-0">
                            @if($isAr)
                                لتغيير حالة الاعتماد/الرفض، استخدم أزرار "اعتماد" أو "رفض" من صفحة عرض التفاصيل.
                            @else
                                To change the approval/rejection status, use the "Approve" or "Reject" buttons on the details page.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endif

        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('ideas.show', $idea) }}" class="btn btn-outline-secondary">{{ $isAr ? 'إلغاء' : 'Cancel' }}</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle"></i> {{ $isAr ? 'حفظ التعديلات' : 'Save Changes' }}
            </button>
        </div>
    </form>
</div>
@endsection