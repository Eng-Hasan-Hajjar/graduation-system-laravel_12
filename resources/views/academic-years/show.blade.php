@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل السنة الأكاديمية' : 'Academic Year Details')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '-';
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                @if ($academicYear->is_current)
                    <span class="badge bg-primary fs-6">
                        <i class="bi bi-star-fill"></i>
                        {{ $isAr ? 'السنة الحالية' : 'Current Year' }}
                    </span>
                @endif
                @if (!$academicYear->is_active)
                    <span class="badge bg-secondary">{{ $isAr ? 'غير نشطة' : 'Inactive' }}</span>
                @endif
            </div>
            <h3 class="mb-1">{{ $academicYear->name_ar }}</h3>
            @if ($academicYear->name_en)
                <h6 class="text-muted">{{ $academicYear->name_en }}</h6>
            @endif
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('academic-years.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i>
                {{ $isAr ? 'رجوع' : 'Back' }}
            </a>
            <a href="{{ route('academic-years.edit', $academicYear) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i>
                {{ $isAr ? 'تعديل' : 'Edit' }}
            </a>
            <form action="{{ route('academic-years.destroy', $academicYear) }}" method="POST"
                  onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من حذف هذه السنة؟' : 'Are you sure?' }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash"></i>
                    {{ $isAr ? 'حذف' : 'Delete' }}
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i>
                    {{ $isAr ? 'معلومات السنة' : 'Year Information' }}
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'الاسم (عربي)' : 'Name (AR)' }}</span>
                        <strong>{{ $academicYear->name_ar }}</strong>
                    </li>
                    @if ($academicYear->name_en)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'الاسم (إنجليزي)' : 'Name (EN)' }}</span>
                            <strong>{{ $academicYear->name_en }}</strong>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'سنة البداية' : 'Start Year' }}</span>
                        <strong>{{ $academicYear->year_start }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'سنة النهاية' : 'End Year' }}</span>
                        <strong>{{ $academicYear->year_end }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'تاريخ البداية' : 'Start Date' }}</span>
                        <strong>{{ $academicYear->start_date->format('Y-m-d') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'تاريخ النهاية' : 'End Date' }}</span>
                        <strong>{{ $academicYear->end_date->format('Y-m-d') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'السنة الحالية' : 'Is Current' }}</span>
                        @if ($academicYear->is_current)
                            <span class="badge bg-primary">{{ $isAr ? 'نعم' : 'Yes' }}</span>
                        @else
                            <span class="text-muted">{{ $isAr ? 'لا' : 'No' }}</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'الحالة' : 'Status' }}</span>
                        @if ($academicYear->is_active)
                            <span class="badge bg-success">{{ $isAr ? 'نشطة' : 'Active' }}</span>
                        @else
                            <span class="badge bg-secondary">{{ $isAr ? 'غير نشطة' : 'Inactive' }}</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'عدد الفصول' : 'Semesters' }}</span>
                        <strong>{{ $academicYear->semesters->count() }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-calendar-week"></i>
                        {{ $isAr ? 'الفصول الدراسية' : 'Semesters' }}
                    </span>
                </div>

                @if ($academicYear->semesters->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                        {{ $isAr ? 'لا توجد فصول دراسية مضافة لهذه السنة' : 'No semesters added for this year' }}
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                                    <th>{{ $isAr ? 'تاريخ البداية' : 'Start Date' }}</th>
                                    <th>{{ $isAr ? 'تاريخ النهاية' : 'End Date' }}</th>
                                    <th>{{ $isAr ? 'الفصل الحالي' : 'Current' }}</th>
                                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($academicYear->semesters as $semester)
                                    <tr {{ $semester->is_current ? 'class=table-primary' : '' }}>
                                        <td>
                                            <strong>{{ $localized($semester, 'name') }}</strong>
                                        </td>
                                        <td>{{ $semester->start_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>{{ $semester->end_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>
                                            @if ($semester->is_current)
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-star-fill"></i>
                                                    {{ $isAr ? 'الحالي' : 'Current' }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if ($semester->is_active)
                                                <span class="badge bg-success">{{ $isAr ? 'نشط' : 'Active' }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $isAr ? 'غير نشط' : 'Inactive' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection