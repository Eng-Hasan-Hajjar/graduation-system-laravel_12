@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الأقسام الأكاديمية' : 'Academic Departments')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';
    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '-';
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-building text-primary"></i>
                {{ $isAr ? 'الأقسام الأكاديمية' : 'Academic Departments' }}
            </h3>
            <span class="text-muted">
                {{ $isAr ? 'إجمالي الأقسام' : 'Total Departments' }}: {{ $departments->count() }}
            </span>
        </div>
        <a href="{{ route('departments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            {{ $isAr ? 'إضافة قسم جديد' : 'Add Department' }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($departments->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-building fs-1 d-block mb-3"></i>
                {{ $isAr ? 'لا توجد أقسام بعد' : 'No departments yet' }}
            </div>
        </div>
    @else
        {{-- تجميع الأقسام حسب الكلية --}}
        @foreach ($departments->groupBy(fn($d) => $d->college_id) as $collegeId => $collegeDepts)
            @php $college = $collegeDepts->first()->college; @endphp
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-bank"></i>
                        {{ $localized($college, 'name') }}
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ $isAr ? 'اسم القسم' : 'Department Name' }}</th>
                                <th>{{ $isAr ? 'الاسم (إنجليزي)' : 'English Name' }}</th>
                                <th>{{ $isAr ? 'الرمز' : 'Code' }}</th>
                                <th>{{ $isAr ? 'عدد المستخدمين' : 'Users' }}</th>
                                <th>{{ $isAr ? 'عدد المشاريع' : 'Projects' }}</th>
                                <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collegeDepts as $department)
                                <tr>
                                    <td><strong>{{ $department->name_ar }}</strong></td>
                                    <td>{{ $department->name_en ?? '-' }}</td>
                                    <td>
                                        @if ($department->code)
                                            <span class="badge bg-light text-dark border">{{ $department->code }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $department->users_count ?? $department->users->count() }}</td>
                                    <td>{{ $department->projects_count ?? $department->projects->count() }}</td>
                                    <td>
                                        @if ($department->is_active)
                                            <span class="badge bg-success">{{ $isAr ? 'نشط' : 'Active' }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $isAr ? 'معطّل' : 'Inactive' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('departments.show', $department) }}"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('departments.edit', $department) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if ($department->is_active)
                                                <form action="{{ route('departments.destroy', $department) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من تعطيل هذا القسم؟' : 'Deactivate this department?' }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                                            title="{{ $isAr ? 'تعطيل' : 'Deactivate' }}">
                                                        <i class="bi bi-slash-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection