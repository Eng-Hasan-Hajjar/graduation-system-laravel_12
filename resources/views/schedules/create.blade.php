@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'جدولة مناقشة جديدة' : 'Schedule New Defense')

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
        <h3 class="mb-0">
            <i class="bi bi-calendar-plus text-primary"></i>
            {{ $isAr ? 'جدولة مناقشة جديدة' : 'Schedule New Defense Session' }}
        </h3>
        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
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

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        {{ $isAr
            ? 'جدولة المناقشة ستُحدّث تلقائياً بيانات تاريخ المناقشة في سجل المشروع المختار.'
            : 'Scheduling a defense will automatically update the defense date on the selected project record.' }}
    </div>

    <form action="{{ route('schedules.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <i class="bi bi-calendar-event"></i>
                        {{ $isAr ? 'تفاصيل جلسة المناقشة' : 'Defense Session Details' }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- المشروع --}}
                            <div class="col-12">
                                <label class="form-label">
                                    {{ $isAr ? 'المشروع' : 'Project' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="project_id" id="project_id"
                                        class="form-select @error('project_id') is-invalid @enderror" required>
                                    <option value="">
                                        {{ $isAr ? '-- اختر المشروع --' : '-- Select Project --' }}
                                    </option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}"
                                                data-supervisor="{{ $project->supervisor?->name }}"
                                                data-students="{{ $project->students->pluck('name')->implode('، ') }}"
                                                data-number="{{ $project->project_number }}"
                                            {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->project_number }} — {{ $localized($project, 'title') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                {{-- معلومات المشروع المختار --}}
                                <div id="projectInfo" class="mt-2 p-2 rounded border bg-light small d-none">
                                    <div id="projectSupervisorInfo"></div>
                                    <div id="projectStudentsInfo"></div>
                                </div>
                            </div>

                            {{-- اللجنة --}}
                            <div class="col-12">
                                <label class="form-label">{{ $isAr ? 'لجنة المناقشة' : 'Defense Committee' }}</label>
                                <select name="committee_id"
                                        class="form-select @error('committee_id') is-invalid @enderror">
                                    <option value="">
                                        {{ $isAr ? '-- بدون ربط بلجنة --' : '-- No linked committee --' }}
                                    </option>
                                    @foreach ($committees as $committee)
                                        <option value="{{ $committee->id }}"
                                            {{ old('committee_id') == $committee->id ? 'selected' : '' }}>
                                            #{{ $committee->id }}
                                            {{ $committee->name_ar ?? ($isAr ? 'لجنة مناقشة' : 'Committee') }}
                                            @if ($committee->project)
                                                — {{ $localized($committee->project, 'title') }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('committee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- التاريخ والوقت --}}
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'تاريخ المناقشة' : 'Defense Date' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="scheduled_date"
                                       class="form-control @error('scheduled_date') is-invalid @enderror"
                                       value="{{ old('scheduled_date') }}"
                                       min="{{ now()->toDateString() }}" required>
                                @error('scheduled_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'وقت البدء' : 'Start Time' }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="scheduled_time"
                                       class="form-control @error('scheduled_time') is-invalid @enderror"
                                       value="{{ old('scheduled_time') }}" required>
                                @error('scheduled_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ $isAr ? 'المدة (بالدقائق)' : 'Duration (minutes)' }}
                                </label>
                                <input type="number" name="duration_minutes"
                                       class="form-control @error('duration_minutes') is-invalid @enderror"
                                       value="{{ old('duration_minutes', 60) }}"
                                       min="30" max="240" step="15">
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- المكان --}}
                            <div class="col-md-8">
                                <label class="form-label">{{ $isAr ? 'المكان' : 'Location' }}</label>
                                <input type="text" name="location" class="form-control"
                                       value="{{ old('location') }}"
                                       placeholder="{{ $isAr ? 'كلية الحاسبات' : 'Computing College' }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'القاعة' : 'Room' }}</label>
                                <input type="text" name="room" class="form-control"
                                       value="{{ old('room') }}"
                                       placeholder="{{ $isAr ? 'قاعة 3أ' : 'Room 3A' }}">
                            </div>

                            {{-- ملاحظات --}}
                            <div class="col-12">
                                <label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label>
                                <textarea name="notes" rows="3" class="form-control"
                                          placeholder="{{ $isAr ? 'أي ملاحظات إضافية...' : 'Any additional notes...' }}">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الشريط الجانبي --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 80px;">
                    <div class="card-header">
                        <i class="bi bi-list-check"></i>
                        {{ $isAr ? 'ملخص الجدولة' : 'Scheduling Summary' }}
                    </div>
                    <div class="card-body">
                        <div id="scheduleSummary" class="text-muted small mb-3">
                            <p>{{ $isAr ? 'اختر المشروع والتاريخ لرؤية الملخص' : 'Select a project and date to see summary' }}</p>
                        </div>

                        <div class="alert alert-warning py-2 small">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ $isAr
                                ? 'تأكد من أن جميع أعضاء اللجنة أُبلغوا بالموعد قبل الجدولة.'
                                : 'Ensure all committee members are notified before scheduling.' }}
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-calendar-check"></i>
                                {{ $isAr ? 'تأكيد الجدولة' : 'Confirm Schedule' }}
                            </button>
                            <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
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
    const projectInfo   = document.getElementById('projectInfo');

    function updateProjectInfo() {
        const opt = projectSelect.options[projectSelect.selectedIndex];
        if (!opt || !opt.value) {
            projectInfo.classList.add('d-none');
            updateSummary();
            return;
        }
        document.getElementById('projectSupervisorInfo').innerHTML =
            opt.dataset.supervisor
                ? `<i class="bi bi-person-badge"></i> <strong>{{ $isAr ? 'المشرف' : 'Supervisor' }}:</strong> ${opt.dataset.supervisor}`
                : '';
        document.getElementById('projectStudentsInfo').innerHTML =
            opt.dataset.students
                ? `<i class="bi bi-people"></i> <strong>{{ $isAr ? 'الطلاب' : 'Students' }}:</strong> ${opt.dataset.students}`
                : '';
        projectInfo.classList.remove('d-none');
        updateSummary();
    }

    function updateSummary() {
        const opt      = projectSelect.options[projectSelect.selectedIndex];
        const projText = opt && opt.value ? opt.text : '—';
        const date     = document.querySelector('[name=scheduled_date]').value || '—';
        const time     = document.querySelector('[name=scheduled_time]').value || '—';
        const location = document.querySelector('[name=location]').value;
        const room     = document.querySelector('[name=room]').value;
        const duration = document.querySelector('[name=duration_minutes]').value;

        document.getElementById('scheduleSummary').innerHTML = `
            <div class="mb-1"><strong>{{ $isAr ? 'المشروع' : 'Project' }}:</strong><br>${projText}</div>
            <div class="mb-1"><strong>{{ $isAr ? 'التاريخ' : 'Date' }}:</strong> ${date}</div>
            <div class="mb-1"><strong>{{ $isAr ? 'الوقت' : 'Time' }}:</strong> ${time}</div>
            ${location ? `<div class="mb-1"><strong>{{ $isAr ? 'المكان' : 'Location' }}:</strong> ${location} ${room ? '- ' + room : ''}</div>` : ''}
            ${duration ? `<div><strong>{{ $isAr ? 'المدة' : 'Duration' }}:</strong> ${duration} {{ $isAr ? 'دقيقة' : 'min' }}</div>` : ''}
        `;
    }

    projectSelect.addEventListener('change', updateProjectInfo);
    ['scheduled_date','scheduled_time','location','room','duration_minutes'].forEach(function (name) {
        document.querySelector(`[name=${name}]`).addEventListener('input', updateSummary);
    });

    updateProjectInfo();
});
</script>
@endsection