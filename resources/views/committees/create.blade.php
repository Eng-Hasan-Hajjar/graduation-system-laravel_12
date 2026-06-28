@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إنشاء لجنة مناقشة' : 'Create Defense Committee')

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

    $memberRoles = [
        'president' => $isAr ? 'رئيس اللجنة' : 'President',
        'member'    => $isAr ? 'عضو' : 'Member',
        'secretary' => $isAr ? 'أمين السر' : 'Secretary',
        'external'  => $isAr ? 'عضو خارجي' : 'External Member',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="mb-0">
            <i class="bi bi-people-fill text-primary"></i>
            {{ $isAr ? 'إنشاء لجنة مناقشة جديدة' : 'Create New Defense Committee' }}
        </h3>
        <a href="{{ route('committees.index') }}" class="btn btn-outline-secondary">
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

    <form action="{{ route('committees.store') }}" method="POST" id="committeeForm">
        @csrf

        <div class="row g-4">
            {{-- العمود الرئيسي --}}
            <div class="col-lg-8">

                {{-- معلومات اللجنة --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-info-circle"></i>
                        {{ $isAr ? 'معلومات اللجنة' : 'Committee Information' }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">
                                    {{ $isAr ? 'اسم اللجنة (عربي)' : 'Committee Name (Arabic)' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name_ar"
                                       class="form-control @error('name_ar') is-invalid @enderror"
                                       value="{{ old('name_ar') }}"
                                       placeholder="{{ $isAr ? 'مثال: لجنة مناقشة مشروع نظام إدارة المستشفيات' : 'e.g. Hospital Management System Defense Committee' }}"
                                       required>
                                @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    {{ $isAr ? 'المشروع' : 'Project' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="project_id" id="project_id"
                                        class="form-select @error('project_id') is-invalid @enderror" required>
                                    <option value="">{{ $isAr ? '-- اختر المشروع --' : '-- Select Project --' }}</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                                data-supervisor="{{ $project->supervisor?->name }}"
                                                data-students="{{ $project->students->pluck('name')->implode(', ') }}"
                                            {{ old('project_id', $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->project_number }} — {{ $localized($project, 'title') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                {{-- معلومات المشروع المختار --}}
                                <div id="projectInfo" class="mt-2 p-2 bg-light rounded small d-none">
                                    <span id="projectSupervisor"></span>
                                    <span id="projectStudents"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    {{ $isAr ? 'موعد الجلسة' : 'Scheduled Date & Time' }}
                                </label>
                                <input type="datetime-local" name="scheduled_at"
                                       class="form-control @error('scheduled_at') is-invalid @enderror"
                                       value="{{ old('scheduled_at') }}">
                                @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'المكان' : 'Location' }}</label>
                                <input type="text" name="location" class="form-control"
                                       value="{{ old('location') }}"
                                       placeholder="{{ $isAr ? 'كلية الحاسبات' : 'Computing College' }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'القاعة' : 'Room' }}</label>
                                <input type="text" name="room" class="form-control"
                                       value="{{ old('room') }}"
                                       placeholder="{{ $isAr ? 'قاعة 3أ' : 'Room 3A' }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- اختيار الأعضاء --}}
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-people"></i>
                            {{ $isAr ? 'أعضاء اللجنة' : 'Committee Members' }}
                        </span>
                        <span class="badge bg-primary" id="selectedCount">0 {{ $isAr ? 'محدد' : 'selected' }}</span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            {{ $isAr ? 'يجب اختيار عضوين على الأقل. حدد دور كل عضو من القائمة بجانب اسمه.' : 'At least 2 members required. Select a role for each member.' }}
                        </p>

                        @error('member_ids')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                                        <th>{{ $isAr ? 'الدور' : 'Role' }}</th>
                                        <th>{{ $isAr ? 'المسمى الوظيفي' : 'Job Title' }}</th>
                                        <th>{{ $isAr ? 'الدور في اللجنة' : 'Committee Role' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($staff as $member)
                                        @php
                                            $oldMembers  = old('member_ids', []);
                                            $isChecked   = in_array($member->id, $oldMembers);
                                            $oldRole     = old("member_roles.{$member->id}", 'member');
                                        @endphp
                                        <tr class="member-row {{ $isChecked ? 'table-primary' : '' }}"
                                            data-id="{{ $member->id }}">
                                            <td>
                                                <input class="form-check-input member-checkbox"
                                                       type="checkbox"
                                                       name="member_ids[]"
                                                       value="{{ $member->id }}"
                                                       id="member_{{ $member->id }}"
                                                    {{ $isChecked ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <label for="member_{{ $member->id }}" class="mb-0 cursor-pointer">
                                                    <strong>{{ $member->name }}</strong>
                                                </label>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $member->role === 'supervisor' ? 'info' : 'secondary' }}">
                                                    {{ $member->role === 'supervisor'
                                                        ? ($isAr ? 'مشرف' : 'Supervisor')
                                                        : ($isAr ? 'عضو لجنة' : 'Committee Member') }}
                                                </span>
                                            </td>
                                            <td class="small text-muted">{{ $member->email }}</td>
                                            <td>
                                                <select name="member_roles[{{ $member->id }}]"
                                                        class="form-select form-select-sm member-role-select"
                                                        style="min-width: 140px;"
                                                    {{ !$isChecked ? 'disabled' : '' }}>
                                                    @foreach ($memberRoles as $value => $label)
                                                        <option value="{{ $value }}" {{ $oldRole === $value ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الشريط الجانبي --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <i class="bi bi-list-check"></i>
                        {{ $isAr ? 'ملخص اللجنة' : 'Committee Summary' }}
                    </div>
                    <div class="card-body">
                        <div id="summary" class="text-muted small">
                            <p>{{ $isAr ? 'اختر المشروع والأعضاء لرؤية الملخص' : 'Select project and members to see summary' }}</p>
                        </div>

                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle"></i>
                            {{ $isAr ? 'يمكنك إضافة جدول المناقشة لاحقاً من صفحة "جداول المناقشات"' : 'You can add the defense schedule later from the Schedules page' }}
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle"></i>
                                {{ $isAr ? 'إنشاء اللجنة' : 'Create Committee' }}
                            </button>
                            <a href="{{ route('committees.index') }}" class="btn btn-outline-secondary">
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

    // عرض معلومات المشروع عند الاختيار
    const projectSelect = document.getElementById('project_id');
    const projectInfo   = document.getElementById('projectInfo');

    function updateProjectInfo() {
        const opt = projectSelect.options[projectSelect.selectedIndex];
        if (!opt || !opt.value) {
            projectInfo.classList.add('d-none');
            return;
        }
        const supervisor = opt.dataset.supervisor;
        const students   = opt.dataset.students;
        document.getElementById('projectSupervisor').innerHTML =
            supervisor ? `<i class="bi bi-person-badge"></i> <strong>{{ $isAr ? 'المشرف' : 'Supervisor' }}:</strong> ${supervisor} &nbsp;` : '';
        document.getElementById('projectStudents').innerHTML =
            students   ? `<i class="bi bi-people"></i> <strong>{{ $isAr ? 'الطلاب' : 'Students' }}:</strong> ${students}` : '';
        projectInfo.classList.remove('d-none');
        updateSummary();
    }

    projectSelect.addEventListener('change', updateProjectInfo);
    updateProjectInfo();

    // التحكم في تفعيل/تعطيل اختيار الدور
    const checkboxes = document.querySelectorAll('.member-checkbox');
    const countBadge = document.getElementById('selectedCount');

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', function () {
            const row    = this.closest('.member-row');
            const select = row.querySelector('.member-role-select');
            if (this.checked) {
                row.classList.add('table-primary');
                select.disabled = false;
            } else {
                row.classList.remove('table-primary');
                select.disabled = true;
            }
            const checked = document.querySelectorAll('.member-checkbox:checked').length;
            countBadge.textContent = checked + ' {{ $isAr ? 'محدد' : 'selected' }}';
            updateSummary();
        });
    });

    // تحديث العداد عند التحميل
    const initialChecked = document.querySelectorAll('.member-checkbox:checked').length;
    countBadge.textContent = initialChecked + ' {{ $isAr ? 'محدد' : 'selected' }}';

    // ملخص اللجنة
    function updateSummary() {
        const opt     = projectSelect.options[projectSelect.selectedIndex];
        const projName = opt && opt.value ? opt.text : '—';
        const checked = document.querySelectorAll('.member-checkbox:checked');

        let membersHtml = '';
        checked.forEach(function (cb) {
            const row    = cb.closest('.member-row');
            const name   = row.querySelector('label').textContent.trim();
            const role   = row.querySelector('.member-role-select').value;
            membersHtml += `<li>${name}</li>`;
        });

        document.getElementById('summary').innerHTML = `
            <div class="mb-2">
                <strong>{{ $isAr ? 'المشروع' : 'Project' }}:</strong><br>
                <span>${projName}</span>
            </div>
            <div>
                <strong>{{ $isAr ? 'الأعضاء' : 'Members' }} (${checked.length}):</strong>
                <ul class="mb-0 mt-1 ps-3">${membersHtml || '<li>—</li>'}</ul>
            </div>
        `;
    }

    updateSummary();
});
</script>
@endsection