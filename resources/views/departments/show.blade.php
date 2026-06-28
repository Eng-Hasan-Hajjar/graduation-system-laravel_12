@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل القسم' : 'Department Details')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';
    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '-';

    $roleLabels = [
        'student'          => $isAr ? 'طالب'       : 'Student',
        'supervisor'       => $isAr ? 'مشرف'        : 'Supervisor',
        'coordinator'      => $isAr ? 'منسق'        : 'Coordinator',
        'committee_member' => $isAr ? 'عضو لجنة'   : 'Committee Member',
        'admin'            => $isAr ? 'مسؤول'       : 'Admin',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                @if ($department->is_active)
                    <span class="badge bg-success">{{ $isAr ? 'نشط' : 'Active' }}</span>
                @else
                    <span class="badge bg-secondary">{{ $isAr ? 'معطّل' : 'Inactive' }}</span>
                @endif
                @if ($department->code)
                    <span class="badge bg-light text-dark border">{{ $department->code }}</span>
                @endif
            </div>
            <h3 class="mb-1">{{ $department->name_ar }}</h3>
            @if ($department->name_en)
                <h6 class="text-muted">{{ $department->name_en }}</h6>
            @endif
            @if ($department->college)
                <span class="text-muted small">
                    <i class="bi bi-bank"></i>
                    {{ $localized($department->college, 'name') }}
                </span>
            @endif
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i>
                {{ $isAr ? 'رجوع' : 'Back' }}
            </a>
            <a href="{{ route('departments.edit', $department) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i>
                {{ $isAr ? 'تعديل' : 'Edit' }}
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">

        {{-- إحصائيات --}}
        <div class="col-12">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-people fs-2 text-primary"></i>
                            <h3 class="mt-2 mb-0">{{ $department->users->count() }}</h3>
                            <small class="text-muted">{{ $isAr ? 'المستخدمون' : 'Users' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-mortarboard fs-2 text-success"></i>
                            <h3 class="mt-2 mb-0">
                                {{ $department->users->where('role', 'student')->count() }}
                            </h3>
                            <small class="text-muted">{{ $isAr ? 'الطلاب' : 'Students' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-person-workspace fs-2 text-info"></i>
                            <h3 class="mt-2 mb-0">
                                {{ $department->users->where('role', 'supervisor')->count() }}
                            </h3>
                            <small class="text-muted">{{ $isAr ? 'المشرفون' : 'Supervisors' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-folder2-open fs-2 text-warning"></i>
                            <h3 class="mt-2 mb-0">{{ $department->projects->count() }}</h3>
                            <small class="text-muted">{{ $isAr ? 'المشاريع' : 'Projects' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- المستخدمون --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-people"></i>
                    {{ $isAr ? 'المستخدمون' : 'Users' }}
                    <span class="badge bg-primary ms-1">{{ $department->users->count() }}</span>
                </div>
                @if ($department->users->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        {{ $isAr ? 'لا يوجد مستخدمون في هذا القسم' : 'No users in this department' }}
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                                    <th>{{ $isAr ? 'الدور' : 'Role' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($department->users->take(10) as $user)
                                    <tr>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}"
                                               class="text-decoration-none">
                                                {{ $user->name_ar }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $roleLabels[$user->role] ?? $user->role }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($department->users->count() > 10)
                        <div class="card-footer text-muted small">
                            {{ $isAr ? 'يُعرض أول 10 فقط' : 'Showing first 10 only' }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- المشاريع --}}
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="bi bi-folder2-open"></i>
                    {{ $isAr ? 'المشاريع' : 'Projects' }}
                    <span class="badge bg-primary ms-1">{{ $department->projects->count() }}</span>
                </div>
                @if ($department->projects->isEmpty())
                    <div class="card-body text-center text-muted py-4">
                        {{ $isAr ? 'لا توجد مشاريع في هذا القسم' : 'No projects in this department' }}
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($department->projects->take(8) as $project)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="text-decoration-none">
                                    {{ $project->{'title_'.$locale} ?? $project->title_ar }}
                                </a>
                                <span class="badge bg-light text-dark border">
                                    {{ $project->project_number }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    @if ($department->projects->count() > 8)
                        <div class="card-footer text-muted small">
                            {{ $isAr ? 'يُعرض أول 8 فقط' : 'Showing first 8 only' }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection