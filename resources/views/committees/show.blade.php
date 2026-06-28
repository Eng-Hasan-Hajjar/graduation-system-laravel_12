@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل اللجنة' : 'Committee Details')

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

        $fmtDate = fn($v) => $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '-';
        $fmtDateTime = fn($v) => $v ? \Carbon\Carbon::parse($v)->format('Y-m-d H:i') : '-';

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

        $project = $committee->project;
    @endphp

    <div class="container py-4">

        {{-- رأس الصفحة --}}
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    @if ($committee->is_completed)
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-check-circle-fill"></i>
                            {{ $isAr ? 'مكتملة' : 'Completed' }}
                        </span>
                    @else
                        <span class="badge bg-warning text-dark fs-6">
                            {{ $isAr ? 'نشطة' : 'Active' }}
                        </span>
                    @endif
                </div>
                <h3 class="mb-1">{{ $committee->name_ar ?? ($isAr ? 'لجنة مناقشة' : 'Defense Committee') }}
                    #{{ $committee->id }}</h3>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('committees.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i>
                    {{ $isAr ? 'رجوع' : 'Back' }}
                </a>

                @can('manage-committee')
                    <a href="{{ route('committees.edit', $committee) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                        {{ $isAr ? 'تعديل' : 'Edit' }}
                    </a>
                @endcan


                @can('manage-committee')
                    <form action="{{ route('committees.destroy', $committee) }}" method="POST"
                        onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من حذف هذه اللجنة؟' : 'Are you sure you want to delete this committee?' }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash"></i>
                            {{ $isAr ? 'حذف' : 'Delete' }}
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4">

            {{-- العمود الرئيسي --}}
            <div class="col-lg-8">

                {{-- بيانات المشروع --}}
                @if ($project)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <i class="bi bi-folder2-open"></i>
                            {{ $isAr ? 'المشروع المرتبط' : 'Linked Project' }}
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                <div>
                                    <h5 class="mb-1">{{ $localized($project, 'title') }}</h5>
                                    <span class="badge bg-secondary">{{ $project->project_number }}</span>
                                </div>
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                    {{ $isAr ? 'عرض المشروع' : 'View Project' }}
                                </a>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">{{ $isAr ? 'المشرف' : 'Supervisor' }}</small>
                                    <strong>{{ $project->supervisor?->name ?? '-' }}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block mb-1">{{ $isAr ? 'القسم' : 'Department' }}</small>
                                    <strong>{{ $localized($project->department, 'name') }}</strong>
                                </div>
                                @if ($project->students->count())
                                    <div class="col-12">
                                        <small class="text-muted d-block mb-1">{{ $isAr ? 'الطلاب' : 'Students' }}</small>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($project->students as $student)
                                                <span class="badge bg-light text-dark border">{{ $student->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- أعضاء اللجنة --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-people"></i>
                        {{ $isAr ? 'أعضاء اللجنة' : 'Committee Members' }}
                        <span class="badge bg-primary ms-1">{{ $committee->members->count() }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                                    <th>{{ $isAr ? 'الدور' : 'Role' }}</th>
                                    <th>{{ $isAr ? 'حضر؟' : 'Attended?' }}</th>
                                    <th>{{ $isAr ? 'الدرجة المُعطاة' : 'Grade Given' }}</th>
                                    <th>{{ $isAr ? 'الملاحظات' : 'Feedback' }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($committee->members as $member)
                                    <tr>
                                        <td>
                                            <strong>{{ $member->name }}</strong>
                                            <div class="small text-muted">{{ $member->email }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $memberRoleColors[$member->pivot->role] ?? 'secondary' }}">
                                                {{ $memberRoleLabels[$member->pivot->role] ?? $member->pivot->role }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($member->pivot->attended === null)
                                                <span class="text-muted">—</span>
                                            @elseif ($member->pivot->attended)
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else
                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $member->pivot->grade_given ?? '—' }}
                                        </td>
                                        <td class="small text-muted">
                                            {{ $member->pivot->feedback ?? '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            {{ $isAr ? 'لا يوجد أعضاء' : 'No members' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ملاحظات اللجنة (إن وجدت) --}}
                @if ($committee->notes_ar || $committee->notes_en)
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <i class="bi bi-chat-text"></i>
                            {{ $isAr ? 'ملاحظات' : 'Notes' }}
                        </div>
                        <div class="card-body">
                            @if ($committee->notes_ar)
                                <p style="white-space: pre-line;">{{ $committee->notes_ar }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- الشريط الجانبي --}}
            <div class="col-lg-4">

                {{-- معلومات الجلسة --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-calendar-event"></i>
                        {{ $isAr ? 'معلومات الجلسة' : 'Session Information' }}
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'الموعد المحدد' : 'Scheduled At' }}</span>
                            <strong>{{ $fmtDateTime($committee->scheduled_at) }}</strong>
                        </li>
                        @if ($committee->actual_start_at)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $isAr ? 'وقت البدء الفعلي' : 'Actual Start' }}</span>
                                <strong>{{ $fmtDateTime($committee->actual_start_at) }}</strong>
                            </li>
                        @endif
                        @if ($committee->actual_end_at)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $isAr ? 'وقت الانتهاء الفعلي' : 'Actual End' }}</span>
                                <strong>{{ $fmtDateTime($committee->actual_end_at) }}</strong>
                            </li>
                        @endif
                        @if ($committee->location || $committee->room)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $isAr ? 'المكان' : 'Location' }}</span>
                                <strong>
                                    {{ $committee->location }}
                                    @if ($committee->room) — {{ $committee->room }} @endif
                                </strong>
                            </li>
                        @endif
                        @if ($committee->is_completed && $committee->completed_at)
                            <li class="list-group-item d-flex justify-content-between text-success">
                                <span>{{ $isAr ? 'تاريخ الاكتمال' : 'Completed At' }}</span>
                                <strong>{{ $fmtDate($committee->completed_at) }}</strong>
                            </li>
                        @endif
                    </ul>
                </div>

                {{-- إجراءات --}}
                @can('manage-committee')
                    @if (!$committee->is_completed)
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <i class="bi bi-check2-square"></i>
                                {{ $isAr ? 'تسجيل اكتمال الجلسة' : 'Mark Session as Completed' }}
                            </div>
                            <div class="card-body">
                                <form action="{{ route('committees.complete', $committee) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <div class="mb-3">
                                        <label class="form-label">{{ $isAr ? 'وقت البدء الفعلي' : 'Actual Start Time' }}</label>
                                        <input type="datetime-local" name="actual_start_at" class="form-control"
                                            value="{{ old('actual_start_at') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ $isAr ? 'وقت الانتهاء الفعلي' : 'Actual End Time' }}</label>
                                        <input type="datetime-local" name="actual_end_at" class="form-control"
                                            value="{{ old('actual_end_at') }}">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ $isAr ? 'ملاحظات الجلسة' : 'Session Notes' }}</label>
                                        <textarea name="notes_ar" rows="3" class="form-control"
                                            placeholder="{{ $isAr ? 'أي ملاحظات حول سير الجلسة...' : 'Any notes about the session...' }}">{{ old('notes_ar', $committee->notes_ar) }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('{{ $isAr ? 'تأكيد تسجيل اكتمال الجلسة؟' : 'Confirm marking session as completed?' }}')">
                                        <i class="bi bi-check-circle"></i>
                                        {{ $isAr ? 'تسجيل الاكتمال' : 'Mark as Completed' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill"></i>
                            {{ $isAr ? 'تم تسجيل اكتمال هذه الجلسة بتاريخ ' : 'Session was completed on ' }}
                            {{ $fmtDate($committee->completed_at) }}
                        </div>
                    @endif
                @endcan

            </div>
        </div>
    </div>
@endsection