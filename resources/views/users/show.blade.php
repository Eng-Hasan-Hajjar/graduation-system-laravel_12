@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'بيانات المستخدم' : 'User Profile')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $localized = fn($m, $f) => $m?->{$f.'_'.$locale} ?? $m?->{$f.'_ar'} ?? '-';

    $roleLabels = [
        'admin'            => $isAr ? 'مسؤول النظام' : 'Admin',
        'coordinator'      => $isAr ? 'منسق'          : 'Coordinator',
        'supervisor'       => $isAr ? 'مشرف'          : 'Supervisor',
        'committee_member' => $isAr ? 'عضو لجنة'      : 'Committee Member',
        'student'          => $isAr ? 'طالب'          : 'Student',
    ];

    $roleColors = [
        'admin'            => 'danger',
        'coordinator'      => 'warning',
        'supervisor'       => 'primary',
        'committee_member' => 'info',
        'student'          => 'success',
    ];

    $statusLabels = [
        'active'    => $isAr ? 'نشط'   : 'Active',
        'inactive'  => $isAr ? 'معطّل' : 'Inactive',
        'suspended' => $isAr ? 'موقوف' : 'Suspended',
    ];

    $statusColors = [
        'active'    => 'success',
        'inactive'  => 'secondary',
        'suspended' => 'danger',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $user->avatar_url }}"
                 class="rounded-circle"
                 style="width:72px;height:72px;object-fit:cover;"
                 alt="{{ $user->name }}">
            <div>
                <h3 class="mb-1">{{ $user->name_ar }}</h3>
                @if ($user->name_en)
                    <div class="text-muted">{{ $user->name_en }}</div>
                @endif
                <div class="d-flex gap-2 mt-1">
                    <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">
                        {{ $roleLabels[$user->role] ?? $user->role }}
                    </span>
                    <span class="badge bg-{{ $statusColors[$user->status] ?? 'secondary' }}">
                        {{ $statusLabels[$user->status] ?? $user->status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i>
                {{ $isAr ? 'رجوع' : 'Back' }}
            </a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i>
                    {{ $isAr ? 'تعديل' : 'Edit' }}
                </a>
                @if ($user->id !== auth()->id())
                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد؟' : 'Are you sure?' }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i>
                            {{ $isAr ? 'حذف' : 'Delete' }}
                        </button>
                    </form>
                @endif
            @endif
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
                    {{ $isAr ? 'المعلومات الشخصية' : 'Personal Information' }}
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</span>
                        <strong class="text-break">{{ $user->email }}</strong>
                    </li>
                    @if ($user->phone)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'الجوال' : 'Phone' }}</span>
                            <strong>{{ $user->phone }}</strong>
                        </li>
                    @endif
                    @if ($user->student_id)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'الرقم الجامعي' : 'Student ID' }}</span>
                            <strong>{{ $user->student_id }}</strong>
                        </li>
                    @endif
                    @if ($user->employee_id)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'الرقم الوظيفي' : 'Employee ID' }}</span>
                            <strong>{{ $user->employee_id }}</strong>
                        </li>
                    @endif
                    @if ($user->academic_rank)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'الرتبة الأكاديمية' : 'Academic Rank' }}</span>
                            <strong>{{ $user->academic_rank }}</strong>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'القسم' : 'Department' }}</span>
                        <strong>{{ $localized($user->department, 'name') }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'تاريخ الإنشاء' : 'Created' }}</span>
                        <strong>{{ $user->created_at->format('Y-m-d') }}</strong>
                    </li>
                    @if ($user->last_login_at)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'آخر دخول' : 'Last Login' }}</span>
                            <strong>{{ $user->last_login_at->diffForHumans() }}</strong>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- المشاريع (للطالب) --}}
            @if ($user->isStudent() && $user->studentProjects->count())
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-folder2-open"></i>
                        {{ $isAr ? 'المشاريع' : 'Projects' }}
                        <span class="badge bg-primary ms-1">{{ $user->studentProjects->count() }}</span>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($user->studentProjects as $project)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                    {{ $project->{'title_' . $locale} ?? $project->title_ar }}
                                </a>
                                <span class="badge bg-secondary">{{ $project->project_number }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- المشاريع المُشرف عليها (للمشرف) --}}
            @if ($user->isSupervisor() && $user->supervisedProjects->count())
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-person-workspace"></i>
                        {{ $isAr ? 'المشاريع المُشرف عليها' : 'Supervised Projects' }}
                        <span class="badge bg-primary ms-1">{{ $user->supervisedProjects->count() }}</span>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($user->supervisedProjects as $project)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                    {{ $project->{'title_' . $locale} ?? $project->title_ar }}
                                </a>
                                <span class="badge bg-secondary">{{ $project->project_number }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- اللجان --}}
            @if ($user->committees->count())
                <div class="card shadow-sm">
                    <div class="card-header">
                        <i class="bi bi-people"></i>
                        {{ $isAr ? 'اللجان' : 'Committees' }}
                        <span class="badge bg-primary ms-1">{{ $user->committees->count() }}</span>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($user->committees as $committee)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('committees.show', $committee) }}" class="text-decoration-none">
                                    {{ $committee->name_ar ?? ($isAr ? 'لجنة مناقشة' : 'Committee') }} #{{ $committee->id }}
                                </a>
                                <span class="badge bg-info">{{ $committee->pivot->role }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection