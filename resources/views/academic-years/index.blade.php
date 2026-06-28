@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'السنوات الأكاديمية' : 'Academic Years')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '';

    $semesterTypeLabels = [
        'first'  => $isAr ? 'الفصل الأول'  : 'First Semester',
        'second' => $isAr ? 'الفصل الثاني' : 'Second Semester',
        'summer' => $isAr ? 'الفصل الصيفي' : 'Summer Semester',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-calendar3 text-primary"></i>
                {{ $isAr ? 'السنوات الأكاديمية' : 'Academic Years' }}
            </h3>
            <span class="text-muted">
                {{ $isAr ? 'إجمالي السنوات' : 'Total Years' }}: {{ $years->count() }}
            </span>
        </div>
        <a href="{{ route('academic-years.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            {{ $isAr ? 'إضافة سنة أكاديمية' : 'Add Academic Year' }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($years->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                {{ $isAr ? 'لا توجد سنوات أكاديمية بعد' : 'No academic years yet' }}
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach ($years as $year)
                <div class="col-12">
                    <div class="card shadow-sm {{ $year->is_current ? 'border-primary' : '' }}">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0">{{ $year->name_ar }}</h5>
                                @if ($year->name_en)
                                    <span class="text-muted small">({{ $year->name_en }})</span>
                                @endif
                                @if ($year->is_current)
                                    <span class="badge bg-primary">
                                        <i class="bi bi-star-fill"></i>
                                        {{ $isAr ? 'السنة الحالية' : 'Current Year' }}
                                    </span>
                                @endif
                                @if (!$year->is_active)
                                    <span class="badge bg-secondary">
                                        {{ $isAr ? 'غير نشطة' : 'Inactive' }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('academic-years.show', $year) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('academic-years.edit', $year) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('academic-years.destroy', $year) }}" method="POST"
                                      onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من حذف هذه السنة الأكاديمية؟' : 'Are you sure?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <small class="text-muted d-block">{{ $isAr ? 'سنة البداية' : 'Start Year' }}</small>
                                    <strong>{{ $year->year_start }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">{{ $isAr ? 'سنة النهاية' : 'End Year' }}</small>
                                    <strong>{{ $year->year_end }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">{{ $isAr ? 'تاريخ البداية' : 'Start Date' }}</small>
                                    <strong>{{ $year->start_date->format('Y-m-d') }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">{{ $isAr ? 'تاريخ النهاية' : 'End Date' }}</small>
                                    <strong>{{ $year->end_date->format('Y-m-d') }}</strong>
                                </div>
                            </div>

                            {{-- الفصول الدراسية --}}
                            @if ($year->semesters->count())
                                <div>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-calendar-week"></i>
                                        {{ $isAr ? 'الفصول الدراسية' : 'Semesters' }}
                                        ({{ $year->semesters->count() }})
                                    </small>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($year->semesters as $semester)
                                            <div class="badge {{ $semester->is_current ? 'bg-primary' : 'bg-light text-dark border' }} px-3 py-2">
                                                <span>{{ $localized($semester, 'name') }}</span>
                                                @if ($semester->is_current)
                                                    <i class="bi bi-star-fill ms-1"></i>
                                                @endif
                                                @if ($semester->start_date && $semester->end_date)
                                                    <span class="d-block small opacity-75">
                                                        {{ $semester->start_date->format('Y-m-d') }}
                                                        — {{ $semester->end_date->format('Y-m-d') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-muted small">
                                    <i class="bi bi-info-circle"></i>
                                    {{ $isAr ? 'لا توجد فصول دراسية مضافة بعد' : 'No semesters added yet' }}
                                    — <a href="{{ route('academic-years.show', $year) }}">
                                        {{ $isAr ? 'إضافة فصول' : 'Add Semesters' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection