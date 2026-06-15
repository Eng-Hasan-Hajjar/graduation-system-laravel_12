@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'لوحة تحكم لجنة المناقشة' : 'Committee Dashboard')

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

    $fmtDate = function ($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '-';
    };
    $fmtTime = function ($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i') : '';
    };

    $defenseStatusLabels = [
        'scheduled' => $isAr ? 'مجدولة' : 'Scheduled',
        'confirmed' => $isAr ? 'مؤكدة' : 'Confirmed',
        'completed' => $isAr ? 'منتهية' : 'Completed',
        'cancelled' => $isAr ? 'ملغاة' : 'Cancelled',
        'postponed' => $isAr ? 'مؤجلة' : 'Postponed',
    ];
@endphp

<div class="container py-4">

    <div class="mb-4">
        <h3 class="mb-1">
            <i class="bi bi-people-fill text-primary"></i>
            {{ $isAr ? 'لوحة تحكم لجنة المناقشة' : 'Committee Member Dashboard' }}
        </h3>
        <span class="text-muted">
            {{ $isAr ? 'مرحباً' : 'Welcome' }}, {{ auth()->user()->name }}
        </span>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            {{-- اللجان التي أنا عضو فيها --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-diagram-3"></i> {{ $isAr ? 'اللجان التي أنا عضو فيها' : 'Committees I Am a Member Of' }}
                </div>

                @if ($myCommittees->isEmpty())
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-inboxes fs-1 d-block mb-3"></i>
                        {{ $isAr ? 'لست عضواً في أي لجنة حالياً' : 'You are not a member of any committee yet' }}
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($myCommittees as $committee)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                    <h6 class="mb-0">
                                        {{ $committee->name ?? ($isAr ? 'لجنة مناقشة' : 'Defense Committee') }}
                                        #{{ $committee->id }}
                                    </h6>
                                    @if ($committee->project)
                                        <a href="{{ route('projects.show', $committee->project) }}" class="btn btn-sm btn-outline-primary">
                                            {{ $isAr ? 'عرض المشروع' : 'View Project' }}
                                        </a>
                                    @endif
                                </div>

                                @if ($committee->project)
                                    <div class="mb-2">
                                        <strong>{{ $isAr ? 'المشروع' : 'Project' }}:</strong>
                                        {{ $localized($committee->project, 'title') }}
                                        <span class="badge bg-secondary">{{ $committee->project->project_number }}</span>
                                    </div>

                                    <div class="row small text-muted">
                                        <div class="col-md-6">
                                            <i class="bi bi-person-badge"></i>
                                            {{ $isAr ? 'المشرف' : 'Supervisor' }}:
                                            {{ $committee->project->supervisor?->name ?? '-' }}
                                        </div>
                                        <div class="col-md-6">
                                            <i class="bi bi-people"></i>
                                            {{ $isAr ? 'الطلاب' : 'Students' }}:
                                            {{ $committee->project->students->pluck('name')->implode(', ') ?: '-' }}
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">
                                        {{ $isAr ? 'لا يوجد مشروع مرتبط بهذه اللجنة' : 'No project linked to this committee' }}
                                    </p>
                                @endif

                                @if ($committee->members && $committee->members->count())
                                    <div class="mt-2 small">
                                        <strong>{{ $isAr ? 'أعضاء اللجنة' : 'Committee Members' }}:</strong>
                                        @foreach ($committee->members as $member)
                                            <span class="badge bg-light text-dark border me-1">
                                                {{ $member->name }}
                                                @if (!empty($member->pivot?->role))
                                                    ({{ $member->pivot->role }})
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-5">
            {{-- أقرب جلسات المناقشة --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-calendar-event"></i> {{ $isAr ? 'أقرب جلسات المناقشة' : 'Upcoming Defense Sessions' }}
                </div>

                @if ($upcomingDefenses->isEmpty())
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                        {{ $isAr ? 'لا توجد جلسات مناقشة قادمة' : 'No upcoming defense sessions' }}
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($upcomingDefenses as $defense)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold">{{ $localized($defense->project, 'title') }}</div>
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-calendar3"></i> {{ $fmtDate($defense->scheduled_date) }}
                                            @if ($defense->scheduled_time)
                                                &nbsp;<i class="bi bi-clock"></i> {{ $fmtTime($defense->scheduled_time) }}
                                            @endif
                                        </div>
                                        @if ($defense->location ?? $defense->project?->defense_location ?? null)
                                            <div class="small text-muted">
                                                <i class="bi bi-geo-alt"></i>
                                                {{ $defense->location ?? $defense->project->defense_location }}
                                                @if ($defense->room ?? $defense->project?->defense_room ?? null)
                                                    - {{ $defense->room ?? $defense->project->defense_room }}
                                                @endif
                                            </div>
                                        @endif
                                        @if ($defense->project?->supervisor)
                                            <div class="small text-muted">
                                                <i class="bi bi-person-badge"></i> {{ $defense->project->supervisor->name }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="badge bg-info">
                                        {{ $defenseStatusLabels[$defense->status] ?? $defense->status }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection