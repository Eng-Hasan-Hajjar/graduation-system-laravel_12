@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الأرشيف الأكاديمي' : 'Academic Archive')

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

        $gradeLabelColors = [
            'A+' => 'success',
            'A' => 'success',
            'B+' => 'primary',
            'B' => 'primary',
            'C+' => 'info',
            'C' => 'info',
            'D+' => 'warning',
            'D' => 'warning',
            'F' => 'danger',
        ];

        $projectTypeLabels = [
            'graduation' => $isAr ? 'مشروع تخرج' : 'Graduation Project',
            'research' => $isAr ? 'بحثي' : 'Research',
            'capstone' => $isAr ? 'مشروع تكاملي' : 'Capstone Project',
            'industry' => $isAr ? 'مشروع صناعي' : 'Industry Project',
            'other' => $isAr ? 'أخرى' : 'Other',
        ];


    @endphp

    <div class="container py-4">

        {{-- رأس الصفحة --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="mb-1">
                    <i class="bi bi-archive-fill text-secondary"></i>
                    {{ $isAr ? 'الأرشيف الأكاديمي' : 'Academic Archive' }}
                </h3>
                <span class="text-muted">
                    {{ $isAr ? 'إجمالي المشاريع المؤرشفة' : 'Total Archived Projects' }}:
                    <strong>{{ $projects->total() }}</strong>
                </span>
            </div>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i>
                {{ $isAr ? 'المشاريع النشطة' : 'Active Projects' }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- فلاتر --}}
        <form method="GET" action="{{ route('projects.archived') }}" class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">{{ $isAr ? 'بحث' : 'Search' }}</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="{{ $isAr ? 'عنوان المشروع...' : 'Project title...' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'نوع المشروع' : 'Project Type' }}</label>
                        <select name="type" class="form-select">
                            <option value="">{{ $isAr ? 'كل الأنواع' : 'All Types' }}</option>
                            @foreach ($projectTypeLabels as $value => $label)
                                <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    @if (request()->anyFilled(['search', 'type']))
                        <div class="col-md-2">
                            <a href="{{ route('projects.archived') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </form>

        {{-- القائمة --}}
        @if ($projects->isEmpty())
            <div class="card shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-archive fs-1 d-block mb-3"></i>
                    {{ $isAr ? 'لا توجد مشاريع مؤرشفة' : 'No archived projects found' }}
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach ($projects as $project)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">

                                {{-- رأس البطاقة --}}
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-archive"></i>
                                        {{ $isAr ? 'مؤرشف' : 'Archived' }}
                                    </span>
                                    @if ($project->grade_letter)
                                        <span class="badge bg-{{ $gradeLabelColors[$project->grade_letter] ?? 'secondary' }} fs-6">
                                            {{ $project->grade_letter }}
                                        </span>
                                    @endif
                                </div>

                                {{-- العنوان --}}
                                <h6 class="card-title mb-1">{{ $localized($project, 'title') }}</h6>
                                <small class="text-muted mb-3">{{ $project->project_number }}</small>

                                {{-- التفاصيل --}}
                                <ul class="list-unstyled small text-muted flex-grow-1">
                                    <li class="mb-1">
                                        <i class="bi bi-person-badge"></i>
                                        {{ $isAr ? 'المشرف' : 'Supervisor' }}:
                                        {{ $project->supervisor?->name ?? '-' }}
                                    </li>
                                    <li class="mb-1">
                                        <i class="bi bi-people"></i>
                                        {{ $isAr ? 'الطلاب' : 'Students' }}:
                                        {{ $project->students->pluck('name')->implode('، ') ?: '-' }}
                                    </li>
                                    <li class="mb-1">
                                        <i class="bi bi-calendar3"></i>
                                        {{ $isAr ? 'الفصل' : 'Semester' }}:
                                        {{ $localized($project->semester, 'name') ?: '-' }}
                                    </li>
                                    @if ($project->final_grade)
                                        <li class="mb-1">
                                            <i class="bi bi-star-half"></i>
                                            {{ $isAr ? 'الدرجة النهائية' : 'Final Grade' }}:
                                            <strong>{{ $project->final_grade }}</strong>
                                        </li>
                                    @endif
                                    @if ($project->actual_defense_date ?? $project->defense_date)
                                        <li class="mb-1">
                                            <i class="bi bi-mortarboard"></i>
                                            {{ $isAr ? 'تاريخ المناقشة' : 'Defense Date' }}:
                                            {{ \Carbon\Carbon::parse($project->actual_defense_date ?? $project->defense_date)->format('Y-m-d') }}
                                        </li>
                                    @endif
                                    @if ($project->archived_at)
                                        <li>
                                            <i class="bi bi-archive"></i>
                                            {{ $isAr ? 'أُرشف في' : 'Archived' }}:
                                            {{ \Carbon\Carbon::parse($project->archived_at)->format('Y-m-d') }}
                                        </li>
                                    @endif
                                </ul>

                                {{-- أدوات المشروع --}}
                                @if (!empty($project->tools))
                                    <div class="mb-3">
                                        @foreach (array_slice((array) $project->tools, 0, 3) as $tool)
                                            <span class="badge bg-light text-dark border me-1">{{ $tool }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- الأزرار --}}
                                <div class="d-flex gap-2 mt-auto">
                                    <a href="{{ route('projects.show', $project) }}"
                                        class="btn btn-sm btn-outline-primary flex-grow-1">
                                        <i class="bi bi-eye"></i>
                                        {{ $isAr ? 'عرض' : 'View' }}
                                    </a>
                                    @can('update', $project)
                                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
@endsection