@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تعديل لجنة المناقشة' : 'Edit Defense Committee')

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

    // الأعضاء الحاليين للجنة مع أدوارهم
    $currentMemberIds = $committee->members->pluck('pivot.role', 'id');
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-pencil-square text-primary"></i>
                {{ $isAr ? 'تعديل اللجنة' : 'Edit Committee' }}
            </h3>
            <span class="text-muted">#{{ $committee->id }}</span>
        </div>
        <a href="{{ route('committees.show', $committee) }}" class="btn btn-outline-secondary">
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

    <form action="{{ route('committees.update', $committee) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
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
                                       value="{{ old('name_ar', $committee->name_ar) }}" required>
                                @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    {{ $isAr ? 'المشروع' : 'Project' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="project_id" id="project_id"
                                        class="form-select @error('project_id') is-invalid @enderror" required>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                                data-supervisor="{{ $project->supervisor?->name }}"
                                                data-students="{{ $project->students->pluck('name')->implode(', ') }}"
                                            {{ old('project_id', $committee->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->project_number }} — {{ $localized($project, 'title') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                <div id="projectInfo" class="mt-2 p-2 bg-light rounded small">
                                    <span id="projectSupervisor"></span>
                                    <span id="projectStudents"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">{{ $isAr ? 'موعد الجلسة' : 'Scheduled Date & Time' }}</label>
                                <input type="datetime-local" name="scheduled_at"
                                       class="form-control @error('scheduled_at') is-invalid @enderror"
                                       value="{{ old('scheduled_at', $committee->scheduled_at?->format('Y-m-d\TH:i')) }}">
                                @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'المكان' : 'Location' }}</label>
                                <input type="text" name="location" class="form-control"
                                       value="{{ old('location', $committee->location) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">{{ $isAr ? 'القاعة' : 'Room' }}</label>
                                <input type="text" name="room" class="form-control"
                                       value="{{ old('room', $committee->room) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- تعديل الأعضاء --}}
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="bi bi-people"></i>
                            {{ $isAr ? 'أعضاء اللجنة' : 'Committee Members' }}
                        </span>
                        <span class="badge bg-primary" id="selectedCount">
                            {{ $currentMemberIds->count() }} {{ $isAr ? 'محدد' : 'selected' }}
                        </span>
                    </div>
                    <div class="card-body">

                        @if ($committee->is_completed)
                            <div class="alert alert-warning py-2 small">
                                <i class="bi bi-exclamation-triangle"></i>
                                {{ $isAr ? 'هذه اللجنة مكتملة — تعديل الأعضاء سيؤثر على السجل فقط.' : 'This committee is completed — editing members will only affect the record.' }}
                            </div>
                        @endif

                        @error('member_ids')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                                        <th>{{ $isAr ? 'الدور الوظيفي' : 'Job Role' }}</th>
                                        <th>{{ $isAr ? 'الدور في اللجنة' : 'Committee Role' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($staff as $member)
                                        @php
                                            $isCurrent   = $currentMemberIds->has($member->id);
                                            $isOldChecked = in_array($member->id, old('member_ids', []));
                                            $isChecked   = request()->isMethod('get') ? $isCurrent : $isOldChecked;
                                            $currentRole = $currentMemberIds->get($member->id, 'member');
                                            $oldRole     = old("member_roles.{$member->id}", $currentRole);
                                        @endphp
                                        <tr class="member-row {{ $isChecked ? 'table-primary' : '' }}">
                                            <td>
                                                <input class="form-check-input member-checkbox"
                                                       type="checkbox"
                                                       name="member_ids[]"
                                                       value="{{ $member->id }}"
                                                       id="member_{{ $member->id }}"
                                                    {{ $isChecked ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <label for="member_{{ $member->id }}" class="mb-0">
                                                    <strong>{{ $member->name }}</strong>
                                                    @if ($isCurrent)
                                                        <span class="badge bg-success-subtle text-success border border-success ms-1 small">
                                                            {{ $isAr ? 'عضو حالي' : 'Current' }}
                                                        </span>
                                                    @endif
                                                </label>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $member->role === 'supervisor' ? 'info' : 'secondary' }}">
                                                    {{ $member->role === 'supervisor'
                                                        ? ($isAr ? 'مشرف' : 'Supervisor')
                                                        : ($isAr ? 'عضو لجنة' : 'Committee Member') }}
                                                </span>
                                            </td>
                                            <td>
                                                <select name="member_roles[{{ $member->id }}]"
                                                        class="form-select form-select-sm member-role-select"
                                                        style="min-width: 140px;"
                                                    {{ !$isChecked ? 'disabled' : '' }}>
                                                    @foreach ($memberRoles as $value => $label)
                                                        <option value="{{ $value }}"
                                                            {{ $oldRole === $value ? 'selected' : '' }}>
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
                        <i class="bi bi-info-circle"></i>
                        {{ $isAr ? 'الحالة الحالية' : 'Current Status' }}
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ $isAr ? 'الحالة' : 'Status' }}</span>
                                @if ($committee->is_completed)
                                    <span class="badge bg-success">{{ $isAr ? 'مكتملة' : 'Completed' }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $isAr ? 'نشطة' : 'Active' }}</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ $isAr ? 'عدد الأعضاء' : 'Members' }}</span>
                                <strong>{{ $committee->members->count() }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ $isAr ? 'تاريخ الإنشاء' : 'Created' }}</span>
                                <strong>{{ $committee->created_at->format('Y-m-d') }}</strong>
                            </li>
                        </ul>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle"></i>
                                {{ $isAr ? 'حفظ التعديلات' : 'Save Changes' }}
                            </button>
                            <a href="{{ route('committees.show', $committee) }}" class="btn btn-outline-secondary">
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

    const projectSelect = document.getElementById('project_id');

    function updateProjectInfo() {
        const opt = projectSelect.options[projectSelect.selectedIndex];
        if (!opt || !opt.value) return;
        const supervisor = opt.dataset.supervisor || '';
        const students   = opt.dataset.students   || '';
        document.getElementById('projectSupervisor').innerHTML =
            supervisor ? `<i class="bi bi-person-badge"></i> <strong>{{ $isAr ? 'المشرف' : 'Supervisor' }}:</strong> ${supervisor} &nbsp;` : '';
        document.getElementById('projectStudents').innerHTML =
            students ? `<i class="bi bi-people"></i> <strong>{{ $isAr ? 'الطلاب' : 'Students' }}:</strong> ${students}` : '';
    }

    projectSelect.addEventListener('change', updateProjectInfo);
    updateProjectInfo();

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
            const count = document.querySelectorAll('.member-checkbox:checked').length;
            countBadge.textContent = count + ' {{ $isAr ? 'محدد' : 'selected' }}';
        });
    });
});
</script>
@endsection