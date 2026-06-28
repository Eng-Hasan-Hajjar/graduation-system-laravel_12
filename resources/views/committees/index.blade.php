@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'لجان المناقشة' : 'Defense Committees')

@section('content')
    @php
        $locale = app()->getLocale();
        $isAr = $locale === 'ar';

        $localized = function ($model, $field) use ($locale) {
            if (!$model)
                return '';
            return $model->{$field . '_' . $locale}
                ?? $model->{$field . '_ar'}
                ?? $model->{$field}
                ?? '';
        };

        $fmtDateTime = function ($value) {
            return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i') : '-';
        };

        $memberRoleLabels = [
            'president' => $isAr ? 'رئيس اللجنة' : 'President',
            'member' => $isAr ? 'عضو' : 'Member',
            'secretary' => $isAr ? 'أمين السر' : 'Secretary',
            'external' => $isAr ? 'عضو خارجي' : 'External Member',
        ];

        $memberRoleColors = [
            'president' => 'primary',
            'member' => 'secondary',
            'secretary' => 'info',
            'external' => 'warning',
        ];
    @endphp

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="mb-1">
                    <i class="bi bi-people-fill text-primary"></i>
                    {{ $isAr ? 'لجان المناقشة' : 'Defense Committees' }}
                </h3>
                <span class="text-muted">
                    {{ $isAr ? 'إجمالي اللجان' : 'Total Committees' }}: {{ $committees->total() }}
                </span>
            </div>
            @can('manage-committee')
                <a href="{{ route('committees.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    {{ $isAr ? 'إنشاء لجنة جديدة' : 'Create New Committee' }}
                </a>
            @endcan
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($committees->isEmpty())
            <div class="card shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-3"></i>
                    {{ $isAr ? 'لا توجد لجان مناقشة حتى الآن' : 'No defense committees yet' }}
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach ($committees as $committee)
                    @php
                        $project = $committee->project;
                        $members = $committee->members;
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm {{ $committee->is_completed ? 'border-success' : '' }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{ $committee->name_ar ?? ($isAr ? 'لجنة مناقشة' : 'Committee') }}
                                    #{{ $committee->id }}</span>
                                @if ($committee->is_completed)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i>
                                        {{ $isAr ? 'مكتملة' : 'Completed' }}
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        {{ $isAr ? 'نشطة' : 'Active' }}
                                    </span>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column">
                                {{-- المشروع --}}
                                @if ($project)
                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">{{ $isAr ? 'المشروع' : 'Project' }}</small>
                                        <a href="{{ route('projects.show', $project) }}" class="fw-bold text-decoration-none">
                                            {{ $localized($project, 'title') }}
                                        </a>
                                        <span class="badge bg-light text-dark border ms-1">{{ $project->project_number }}</span>
                                        @if ($project->supervisor)
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-person-badge"></i> {{ $project->supervisor->name }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- الموعد والمكان --}}
                                <div class="mb-3 small">
                                    <div class="text-muted mb-1">
                                        <i class="bi bi-calendar-event"></i>
                                        {{ $fmtDateTime($committee->scheduled_at) }}
                                    </div>
                                    @if ($committee->location || $committee->room)
                                        <div class="text-muted">
                                            <i class="bi bi-geo-alt"></i>
                                            {{ $committee->location }}
                                            @if ($committee->room) — {{ $committee->room }} @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- الأعضاء --}}
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">
                                        {{ $isAr ? 'أعضاء اللجنة' : 'Members' }} ({{ $members->count() }})
                                    </small>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($members as $member)
                                            <span class="badge bg-{{ $memberRoleColors[$member->pivot->role] ?? 'secondary' }}">
                                                {{ $member->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- الطلاب --}}
                                @if ($project && $project->students->count())
                                    <div class="mb-3 small text-muted">
                                        <i class="bi bi-people"></i>
                                        {{ $isAr ? 'الطلاب' : 'Students' }}:
                                        {{ $project->students->pluck('name')->implode(', ') }}
                                    </div>
                                @endif

                                {{-- الأزرار --}}
                                <div class="d-flex gap-2 mt-auto">
                                    <a href="{{ route('committees.show', $committee) }}"
                                        class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="bi bi-eye"></i> {{ $isAr ? 'عرض التفاصيل' : 'View Details' }}
                                    </a>
                                    @can('manage-committee')
                                        <a href="{{ route('committees.edit', $committee) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan

                                    @can('manage-committee')
                                        @if (!$committee->is_completed)
                                            <form action="{{ route('committees.complete', $committee) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-success"
                                                    onclick="return confirm('{{ $isAr ? 'تأكيد اكتمال اجتماع اللجنة؟' : 'Confirm committee session completed?' }}')">
                                                    <i class="bi bi-check2"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('committees.destroy', $committee) }}" method="POST"
                                            onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من الحذف؟' : 'Are you sure?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $committees->links() }}
            </div>
        @endif
    </div>
@endsection