@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إدارة المستخدمين' : 'User Management')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $roleLabels = [
        'admin'            => $isAr ? 'مسؤول النظام'   : 'Admin',
        'coordinator'      => $isAr ? 'منسق'            : 'Coordinator',
        'supervisor'       => $isAr ? 'مشرف'            : 'Supervisor',
        'committee_member' => $isAr ? 'عضو لجنة'        : 'Committee Member',
        'student'          => $isAr ? 'طالب'            : 'Student',
    ];

    $roleColors = [
        'admin'            => 'danger',
        'coordinator'      => 'warning',
        'supervisor'       => 'primary',
        'committee_member' => 'info',
        'student'          => 'success',
    ];

    $statusLabels = [
        'active'    => $isAr ? 'نشط'     : 'Active',
        'inactive'  => $isAr ? 'معطّل'   : 'Inactive',
        'suspended' => $isAr ? 'موقوف'   : 'Suspended',
    ];

    $statusColors = [
        'active'    => 'success',
        'inactive'  => 'secondary',
        'suspended' => 'danger',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-people-fill text-primary"></i>
                {{ $isAr ? 'إدارة المستخدمين' : 'User Management' }}
            </h3>
            <span class="text-muted">
                {{ $isAr ? 'إجمالي المستخدمين' : 'Total Users' }}: {{ $users->total() }}
            </span>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i>
            {{ $isAr ? 'إضافة مستخدم جديد' : 'Add New User' }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- فلاتر --}}
    <form method="GET" action="{{ route('users.index') }}" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'بحث' : 'Search' }}</label>
                    <input type="text" name="search" class="form-control"
                           value="{{ request('search') }}"
                           placeholder="{{ $isAr ? 'الاسم أو البريد الإلكتروني...' : 'Name or email...' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الدور' : 'Role' }}</label>
                    <select name="role" class="form-select">
                        <option value="">{{ $isAr ? 'كل الأدوار' : 'All Roles' }}</option>
                        @foreach ($roleLabels as $value => $label)
                            <option value="{{ $value }}" {{ request('role') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الحالة' : 'Status' }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ $isAr ? 'كل الحالات' : 'All Statuses' }}</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                @if (request()->anyFilled(['search','role','status']))
                    <div class="col-md-1">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </form>

    {{-- الجدول --}}
    @if ($users->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block mb-3"></i>
                {{ $isAr ? 'لا يوجد مستخدمون' : 'No users found' }}
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ $isAr ? 'المستخدم' : 'User' }}</th>
                            <th>{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</th>
                            <th>{{ $isAr ? 'الدور' : 'Role' }}</th>
                            <th>{{ $isAr ? 'القسم' : 'Department' }}</th>
                            <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                            <th>{{ $isAr ? 'آخر دخول' : 'Last Login' }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $user->avatar_url }}"
                                             class="rounded-circle"
                                             style="width:36px;height:36px;object-fit:cover;"
                                             alt="{{ $user->name }}">
                                        <div>
                                            <div class="fw-bold">{{ $user->name_ar }}</div>
                                            @if ($user->name_en)
                                                <small class="text-muted">{{ $user->name_en }}</small>
                                            @endif
                                            @if ($user->student_id)
                                                <div class="small text-muted">
                                                    <i class="bi bi-card-text"></i> {{ $user->student_id }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $roleColors[$user->role] ?? 'secondary' }}">
                                        {{ $roleLabels[$user->role] ?? $user->role }}
                                    </span>
                                </td>
                                <td>
                                    {{ $user->department?->{'name_' . $locale} ?? $user->department?->name_ar ?? '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$user->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$user->status] ?? $user->status }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '-' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('users.show', $user) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="{{ $isAr ? 'عرض' : 'View' }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="{{ $isAr ? 'تعديل' : 'Edit' }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                  onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد؟' : 'Are you sure?' }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="{{ $isAr ? 'حذف' : 'Delete' }}">
                                                    <i class="bi bi-trash"></i>
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

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection