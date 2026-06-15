@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'بنك أفكار المشاريع' : 'Project Ideas Bank')

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

    $projectTypeLabels = [
        'graduation' => $isAr ? 'مشروع تخرج' : 'Graduation Project',
        'semester'   => $isAr ? 'مشروع فصلي' : 'Semester Project',
        'year4'      => $isAr ? 'مشروع سنة رابعة' : 'Year 4 Project',
        'research'   => $isAr ? 'بحثي' : 'Research',
    ];

    $statusLabels = [
        'pending'  => $isAr ? 'قيد المراجعة' : 'Pending',
        'approved' => $isAr ? 'معتمدة' : 'Approved',
        'rejected' => $isAr ? 'مرفوضة' : 'Rejected',
        'taken'    => $isAr ? 'مأخوذة' : 'Taken',
        'archived' => $isAr ? 'مؤرشفة' : 'Archived',
    ];

    $priorityLabels = [
        'low'    => $isAr ? 'منخفضة' : 'Low',
        'medium' => $isAr ? 'متوسطة' : 'Medium',
        'high'   => $isAr ? 'عالية' : 'High',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-lightbulb text-warning"></i>
                {{ $isAr ? 'بنك أفكار المشاريع' : 'Project Ideas Bank' }}
            </h3>
            <span class="text-muted">
                {{ $isAr ? 'إجمالي الأفكار' : 'Total Ideas' }}: {{ $ideas->total() }}
            </span>
        </div>

        @can('create-idea')
            <a href="{{ route('ideas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                {{ $isAr ? 'إضافة فكرة جديدة' : 'Add New Idea' }}
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- الفلاتر --}}
    <form method="GET" action="{{ route('ideas.index') }}" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'بحث' : 'Search' }}</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                           placeholder="{{ $isAr ? 'عنوان الفكرة...' : 'Idea title...' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'القسم' : 'Department' }}</label>
                    <select name="department_id" class="form-select">
                        <option value="">{{ $isAr ? 'كل الأقسام' : 'All Departments' }}</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $localized($department, 'name') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ $isAr ? 'نوع المشروع' : 'Type' }}</label>
                    <select name="type" class="form-select">
                        <option value="">{{ $isAr ? 'الكل' : 'All' }}</option>
                        @foreach ($projectTypeLabels as $value => $label)
                            <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ $isAr ? 'الحالة' : 'Status' }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ $isAr ? 'الكل' : 'All' }}</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            @if (request()->anyFilled(['search', 'department_id', 'type', 'status']))
                <div class="mt-2">
                    <a href="{{ route('ideas.index') }}" class="btn btn-sm btn-link text-decoration-none">
                        <i class="bi bi-x-circle"></i> {{ $isAr ? 'إزالة الفلاتر' : 'Clear filters' }}
                    </a>
                </div>
            @endif
        </div>
    </form>

    {{-- قائمة الأفكار --}}
    @if ($ideas->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-lightbulb fs-1 d-block mb-3"></i>
                {{ $isAr ? 'لا توجد أفكار مشاريع حتى الآن' : 'No project ideas yet' }}
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach ($ideas as $idea)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm position-relative">
                        @if ($idea->is_featured)
                            <span class="badge bg-warning text-dark position-absolute top-0 {{ $isAr ? 'start-0' : 'end-0' }} m-2">
                                <i class="bi bi-star-fill"></i> {{ $isAr ? 'مميزة' : 'Featured' }}
                            </span>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-{{ $idea->status_color }}">
                                    {{ $statusLabels[$idea->status] ?? $idea->status }}
                                </span>
                                @if (!empty($idea->priority))
                                    <span class="badge bg-light text-dark border">
                                        {{ $priorityLabels[$idea->priority] ?? $idea->priority }}
                                    </span>
                                @endif
                            </div>

                            <h5 class="card-title">{{ $idea->title }}</h5>

                            <div class="mb-2 small text-muted">
                                <i class="bi bi-building"></i> {{ $localized($idea->department, 'name') }}
                                @if ($idea->semester)
                                    &nbsp;|&nbsp; <i class="bi bi-calendar3"></i> {{ $localized($idea->semester, 'name') }}
                                @endif
                            </div>

                            <p class="card-text text-muted flex-grow-1">
                                {{ \Illuminate\Support\Str::limit($idea->description, 120) }}
                            </p>

                            @if (!empty($idea->tools))
                                <div class="mb-2">
                                    @foreach (array_slice($idea->tools, 0, 4) as $tool)
                                        <span class="badge bg-light text-dark border me-1">{{ $tool }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="small text-muted mb-3">
                                <i class="bi bi-people"></i>
                                {{ $isAr ? 'عدد الطلاب' : 'Students' }}:
                                {{ $idea->min_students ?? '?' }} - {{ $idea->max_students ?? '?' }}
                                &nbsp;|&nbsp;
                                <i class="bi bi-person"></i> {{ $idea->proposedBy?->name }}
                            </div>

                            <div class="d-flex gap-2 mt-auto">
                                <a href="{{ route('ideas.show', $idea) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                    {{ $isAr ? 'عرض التفاصيل' : 'View Details' }}
                                </a>
                                @can('update', $idea)
                                    <a href="{{ route('ideas.edit', $idea) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan
                                @can('delete', $idea)
                                    <form action="{{ route('ideas.destroy', $idea) }}" method="POST"
                                          onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من حذف هذه الفكرة؟' : 'Are you sure you want to delete this idea?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>

                            @can('approve-idea')
                                @if ($idea->status === 'pending')
                                    <form action="{{ route('ideas.approve', $idea) }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100">
                                            <i class="bi bi-check-circle"></i> {{ $isAr ? 'اعتماد سريع' : 'Quick Approve' }}
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $ideas->links() }}
        </div>
    @endif
</div>
@endsection