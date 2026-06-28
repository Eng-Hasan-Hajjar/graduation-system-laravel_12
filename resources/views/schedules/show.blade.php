@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل جلسة المناقشة' : 'Defense Session Details')

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

    $fmtDate = fn($v) => $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-';
    $fmtTime = fn($v) => $v ? \Carbon\Carbon::parse($v)->format('H:i') : '-';

    $statusLabels = [
        'scheduled' => $isAr ? 'مجدولة'  : 'Scheduled',
        'confirmed' => $isAr ? 'مؤكدة'   : 'Confirmed',
        'postponed' => $isAr ? 'مؤجلة'   : 'Postponed',
        'cancelled' => $isAr ? 'ملغاة'   : 'Cancelled',
        'completed' => $isAr ? 'منتهية'  : 'Completed',
    ];

    $statusColors = [
        'scheduled' => 'secondary',
        'confirmed' => 'success',
        'postponed' => 'warning',
        'cancelled' => 'danger',
        'completed' => 'info',
    ];

    $project   = $schedule->project;
    $committee = $schedule->committee;
@endphp

<div class="container py-4">

    {{-- رأس الصفحة --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-{{ $statusColors[$schedule->status] ?? 'secondary' }} fs-6">
                    {{ $statusLabels[$schedule->status] ?? $schedule->status }}
                </span>
                @if ($schedule->scheduled_date->isToday())
                    <span class="badge bg-primary">
                        <i class="bi bi-star-fill"></i>
                        {{ $isAr ? 'اليوم' : 'Today' }}
                    </span>
                @endif
            </div>
            <h3 class="mb-1">{{ $isAr ? 'جلسة مناقشة' : 'Defense Session' }} #{{ $schedule->id }}</h3>
        </div>

        <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i>
            {{ $isAr ? 'رجوع' : 'Back' }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($schedule->status === 'postponed' && $schedule->postpone_reason)
        <div class="alert alert-warning">
            <i class="bi bi-clock-history"></i>
            <strong>{{ $isAr ? 'سبب التأجيل' : 'Postponement Reason' }}:</strong>
            {{ $schedule->postpone_reason }}
            @if ($schedule->new_scheduled_date)
                <br>
                <strong>{{ $isAr ? 'الموعد الجديد' : 'New Date' }}:</strong>
                {{ $fmtDate($schedule->new_scheduled_date) }}
                @if ($schedule->new_scheduled_time) - {{ $fmtTime($schedule->new_scheduled_time) }} @endif
            @endif
        </div>
    @endif

    @if ($schedule->status === 'cancelled' && $schedule->postpone_reason)
        <div class="alert alert-danger">
            <i class="bi bi-x-circle"></i>
            <strong>{{ $isAr ? 'سبب الإلغاء' : 'Cancellation Reason' }}:</strong>
            {{ $schedule->postpone_reason }}
        </div>
    @endif

    <div class="row g-4">

        {{-- العمود الرئيسي --}}
        <div class="col-lg-8">

            {{-- بيانات المشروع --}}
            @if ($project)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-folder2-open"></i>
                        {{ $isAr ? 'المشروع' : 'Project' }}
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <div>
                                <h5 class="mb-1">{{ $localized($project, 'title') }}</h5>
                                <span class="badge bg-secondary">{{ $project->project_number }}</span>
                            </div>
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                                {{ $isAr ? 'عرض المشروع' : 'View Project' }}
                            </a>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1">{{ $isAr ? 'المشرف' : 'Supervisor' }}</small>
                                <strong>{{ $project->supervisor?->name ?? '-' }}</strong>
                            </div>
                            @if ($project->students->count())
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">{{ $isAr ? 'الطلاب' : 'Students' }}</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($project->students as $student)
                                            <span class="badge bg-light text-dark border">{{ $student->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- بيانات اللجنة --}}
            @if ($committee)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-people"></i>
                        {{ $isAr ? 'لجنة المناقشة' : 'Defense Committee' }}
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong>{{ $committee->name_ar ?? ($isAr ? 'لجنة مناقشة' : 'Committee') }} #{{ $committee->id }}</strong>
                            <a href="{{ route('committees.show', $committee) }}" class="btn btn-sm btn-outline-secondary">
                                {{ $isAr ? 'عرض اللجنة' : 'View Committee' }}
                            </a>
                        </div>
                        @if ($committee->members->count())
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($committee->members as $member)
                                    <span class="badge bg-light text-dark border">
                                        {{ $member->name }}
                                        @if ($member->pivot->role === 'president')
                                            <i class="bi bi-star-fill text-warning ms-1"></i>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- الملاحظات --}}
            @if ($schedule->notes)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <i class="bi bi-chat-text"></i>
                        {{ $isAr ? 'ملاحظات' : 'Notes' }}
                    </div>
                    <div class="card-body">
                        <p style="white-space: pre-line;" class="mb-0">{{ $schedule->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- الشريط الجانبي --}}
        <div class="col-lg-4">

            {{-- تفاصيل الجلسة --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-calendar-event"></i>
                    {{ $isAr ? 'تفاصيل الجلسة' : 'Session Details' }}
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'التاريخ' : 'Date' }}</span>
                        <strong>{{ $fmtDate($schedule->scheduled_date) }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'الوقت' : 'Time' }}</span>
                        <strong>{{ $fmtTime($schedule->scheduled_time) }}</strong>
                    </li>
                    @if ($schedule->duration_minutes)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'المدة' : 'Duration' }}</span>
                            <strong>{{ $schedule->duration_minutes }} {{ $isAr ? 'دقيقة' : 'min' }}</strong>
                        </li>
                    @endif
                    @if ($schedule->location)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'المكان' : 'Location' }}</span>
                            <strong>{{ $schedule->location }}</strong>
                        </li>
                    @endif
                    @if ($schedule->room)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'القاعة' : 'Room' }}</span>
                            <strong>{{ $schedule->room }}</strong>
                        </li>
                    @endif
                    @if ($schedule->createdBy)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'جُدولت بواسطة' : 'Scheduled by' }}</span>
                            <strong>{{ $schedule->createdBy->name }}</strong>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'تاريخ الإنشاء' : 'Created' }}</span>
                        <strong>{{ $schedule->created_at->format('Y-m-d') }}</strong>
                    </li>
                </ul>
            </div>

            {{-- إجراءات الإدارة --}}
            @can('manage-schedule')
                @if (in_array($schedule->status, ['scheduled', 'confirmed']))
                    {{-- تسجيل الاكتمال --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <i class="bi bi-check2-circle"></i>
                            {{ $isAr ? 'تسجيل الاكتمال' : 'Mark Completed' }}
                        </div>
                        <div class="card-body">
                            <form action="{{ route('schedules.complete', $schedule) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('{{ $isAr ? 'تأكيد اكتمال الجلسة؟' : 'Confirm session completed?' }}')">
                                    <i class="bi bi-check-circle"></i>
                                    {{ $isAr ? 'تسجيل الاكتمال' : 'Mark as Completed' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- تأجيل --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <i class="bi bi-clock-history"></i>
                            {{ $isAr ? 'تأجيل المناقشة' : 'Postpone Defense' }}
                        </div>
                        <div class="card-body">
                            <form action="{{ route('schedules.postpone', $schedule) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ $isAr ? 'سبب التأجيل' : 'Postponement Reason' }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="postpone_reason" rows="2"
                                              class="form-control @error('postpone_reason') is-invalid @enderror"
                                              required>{{ old('postpone_reason') }}</textarea>
                                    @error('postpone_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ $isAr ? 'التاريخ الجديد' : 'New Date' }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="new_scheduled_date"
                                           class="form-control @error('new_scheduled_date') is-invalid @enderror"
                                           min="{{ now()->addDay()->toDateString() }}"
                                           value="{{ old('new_scheduled_date') }}" required>
                                    @error('new_scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ $isAr ? 'الوقت الجديد' : 'New Time' }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" name="new_scheduled_time"
                                           class="form-control @error('new_scheduled_time') is-invalid @enderror"
                                           value="{{ old('new_scheduled_time') }}" required>
                                    @error('new_scheduled_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-warning w-100"
                                        onclick="return confirm('{{ $isAr ? 'تأكيد تأجيل المناقشة وإنشاء موعد جديد؟' : 'Confirm postponement and create new schedule?' }}')">
                                    <i class="bi bi-clock-history"></i>
                                    {{ $isAr ? 'تأجيل وإعادة جدولة' : 'Postpone & Reschedule' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- إلغاء --}}
                    <div class="card shadow-sm border-danger">
                        <div class="card-header text-danger">
                            <i class="bi bi-x-circle"></i>
                            {{ $isAr ? 'إلغاء المناقشة' : 'Cancel Defense' }}
                        </div>
                        <div class="card-body">
                            <form action="{{ route('schedules.cancel', $schedule) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ $isAr ? 'سبب الإلغاء' : 'Cancellation Reason' }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="postpone_reason" rows="2"
                                              class="form-control @error('postpone_reason') is-invalid @enderror"
                                              required>{{ old('postpone_reason') }}</textarea>
                                    @error('postpone_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-danger w-100"
                                        onclick="return confirm('{{ $isAr ? 'هل أنت متأكد من إلغاء هذه المناقشة؟ لا يمكن التراجع.' : 'Are you sure you want to cancel this defense? This cannot be undone.' }}')">
                                    <i class="bi bi-x-circle"></i>
                                    {{ $isAr ? 'إلغاء المناقشة' : 'Cancel Defense' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endcan
        </div>
    </div>
</div>
@endsection