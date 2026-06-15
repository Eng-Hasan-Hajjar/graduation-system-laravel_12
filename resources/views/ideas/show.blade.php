@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'تفاصيل فكرة المشروع' : 'Project Idea Details')

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

    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-{{ $idea->status_color }} fs-6">{{ $statusLabels[$idea->status] ?? $idea->status }}</span>
                @if ($idea->is_featured)
                    <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> {{ $isAr ? 'مميزة' : 'Featured' }}</span>
                @endif
                @if (!empty($idea->priority))
                    <span class="badge bg-light text-dark border">{{ $priorityLabels[$idea->priority] ?? $idea->priority }}</span>
                @endif
            </div>
            <h3 class="mb-1">{{ $idea->title_ar }}</h3>
            @if ($idea->title_en)
                <h6 class="text-muted">{{ $idea->title_en }}</h6>
            @endif
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('ideas.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-{{ $isAr ? 'right' : 'left' }}"></i> {{ $isAr ? 'رجوع' : 'Back' }}
            </a>
            @can('update', $idea)
                <a href="{{ route('ideas.edit', $idea) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> {{ $isAr ? 'تعديل' : 'Edit' }}
                </a>
            @endcan
            @can('delete', $idea)
                <form action="{{ route('ideas.destroy', $idea) }}" method="POST"
                      onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من حذف هذه الفكرة؟' : 'Are you sure you want to delete this idea?' }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> {{ $isAr ? 'حذف' : 'Delete' }}
                    </button>
                </form>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($idea->status === 'rejected' && $idea->rejection_reason)
        <div class="alert alert-danger">
            <strong>{{ $isAr ? 'سبب الرفض' : 'Rejection Reason' }}:</strong> {{ $idea->rejection_reason }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- الوصف --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-card-text"></i> {{ $isAr ? 'الوصف' : 'Description' }}</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ $isAr ? 'العربية' : 'Arabic' }}</h6>
                            <p style="white-space: pre-line;">{{ $idea->description_ar }}</p>
                        </div>
                        @if ($idea->description_en)
                            <div class="col-md-6">
                                <h6 class="text-muted">English</h6>
                                <p style="white-space: pre-line;">{{ $idea->description_en }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- الأهداف --}}
            @if ($idea->objectives_ar || $idea->objectives_en)
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><i class="bi bi-bullseye"></i> {{ $isAr ? 'الأهداف' : 'Objectives' }}</div>
                    <div class="card-body">
                        <div class="row">
                            @if ($idea->objectives_ar)
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ $isAr ? 'العربية' : 'Arabic' }}</h6>
                                    <p style="white-space: pre-line;">{{ $idea->objectives_ar }}</p>
                                </div>
                            @endif
                            @if ($idea->objectives_en)
                                <div class="col-md-6">
                                    <h6 class="text-muted">English</h6>
                                    <p style="white-space: pre-line;">{{ $idea->objectives_en }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- النتائج المتوقعة --}}
            @if ($idea->expected_outcomes_ar || $idea->expected_outcomes_en)
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><i class="bi bi-graph-up-arrow"></i> {{ $isAr ? 'النتائج المتوقعة' : 'Expected Outcomes' }}</div>
                    <div class="card-body">
                        <div class="row">
                            @if ($idea->expected_outcomes_ar)
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ $isAr ? 'العربية' : 'Arabic' }}</h6>
                                    <p style="white-space: pre-line;">{{ $idea->expected_outcomes_ar }}</p>
                                </div>
                            @endif
                            @if ($idea->expected_outcomes_en)
                                <div class="col-md-6">
                                    <h6 class="text-muted">English</h6>
                                    <p style="white-space: pre-line;">{{ $idea->expected_outcomes_en }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- المتطلبات المسبقة --}}
            @if ($idea->requirements_ar || $idea->requirements_en)
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><i class="bi bi-list-check"></i> {{ $isAr ? 'المتطلبات المسبقة' : 'Requirements' }}</div>
                    <div class="card-body">
                        <div class="row">
                            @if ($idea->requirements_ar)
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ $isAr ? 'العربية' : 'Arabic' }}</h6>
                                    <p style="white-space: pre-line;">{{ $idea->requirements_ar }}</p>
                                </div>
                            @endif
                            @if ($idea->requirements_en)
                                <div class="col-md-6">
                                    <h6 class="text-muted">English</h6>
                                    <p style="white-space: pre-line;">{{ $idea->requirements_en }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- الأدوات والتقنيات والوسوم --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-tools"></i> {{ $isAr ? 'الأدوات والتقنيات' : 'Tools & Technologies' }}</div>
                <div class="card-body">
                    @if (!empty($idea->tools))
                        <div class="mb-2">
                            <strong>{{ $isAr ? 'الأدوات' : 'Tools' }}:</strong>
                            @foreach ($idea->tools as $tool)
                                <span class="badge bg-light text-dark border me-1">{{ $tool }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if (!empty($idea->technologies))
                        <div class="mb-2">
                            <strong>{{ $isAr ? 'التقنيات' : 'Technologies' }}:</strong>
                            @foreach ($idea->technologies as $tech)
                                <span class="badge bg-light text-dark border me-1">{{ $tech }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if (!empty($idea->tags))
                        <div>
                            <strong>{{ $isAr ? 'الوسوم' : 'Tags' }}:</strong>
                            @foreach ($idea->tags as $tag)
                                <span class="badge bg-primary-subtle text-primary-emphasis border me-1">#{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if (empty($idea->tools) && empty($idea->technologies) && empty($idea->tags))
                        <p class="text-muted mb-0">{{ $isAr ? 'لا توجد بيانات' : 'No data' }}</p>
                    @endif
                </div>
            </div>

            {{-- المشاريع المرتبطة --}}
            @if ($idea->projects->count())
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><i class="bi bi-folder2-open"></i> {{ $isAr ? 'المشاريع المرتبطة بهذه الفكرة' : 'Projects Linked to This Idea' }}</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($idea->projects as $project)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('projects.show', $project) }}">{{ $localized($project, 'title') }}</a>
                                <span class="badge bg-secondary">{{ $project->project_number }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>

        <div class="col-lg-4">

            {{-- معلومات الفكرة --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header"><i class="bi bi-info-circle"></i> {{ $isAr ? 'معلومات الفكرة' : 'Idea Information' }}</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'القسم' : 'Department' }}</span>
                        <strong>{{ $localized($idea->department, 'name') }}</strong>
                    </li>
                    @if ($idea->semester)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'الفصل الدراسي' : 'Semester' }}</span>
                            <strong>{{ $localized($idea->semester, 'name') }}</strong>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'نوع المشروع' : 'Project Type' }}</span>
                        <strong>{{ $projectTypeLabels[$idea->project_type] ?? $idea->project_type }}</strong>
                    </li>
                    @if ($idea->category_ar || $idea->category_en)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'التصنيف' : 'Category' }}</span>
                            <strong>{{ $localized($idea, 'category') }}</strong>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'عدد الطلاب' : 'Students' }}</span>
                        <strong>{{ $idea->min_students ?? '-' }} - {{ $idea->max_students ?? '-' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'مُقدّمة من' : 'Proposed by' }}</span>
                        <strong>{{ $idea->proposedBy?->name }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $isAr ? 'تاريخ الإضافة' : 'Created at' }}</span>
                        <strong>{{ $idea->created_at->format('Y-m-d') }}</strong>
                    </li>
                    @if ($idea->status === 'approved' && $idea->approvedBy)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'اعتمدها' : 'Approved by' }}</span>
                            <strong>{{ $idea->approvedBy->name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $isAr ? 'تاريخ الاعتماد' : 'Approved at' }}</span>
                            <strong>{{ $idea->approved_at?->format('Y-m-d') }}</strong>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- إجراءات المراجعة --}}
            @can('approve-idea')
                @if ($idea->status === 'pending')
                    <div class="card shadow-sm mb-4">
                        <div class="card-header"><i class="bi bi-check2-square"></i> {{ $isAr ? 'إجراءات المراجعة' : 'Review Actions' }}</div>
                        <div class="card-body">
                            <form action="{{ route('ideas.approve', $idea) }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle"></i> {{ $isAr ? 'اعتماد الفكرة' : 'Approve Idea' }}
                                </button>
                            </form>

                            <form action="{{ route('ideas.reject', $idea) }}" method="POST">
                                @csrf
                                <label class="form-label">{{ $isAr ? 'سبب الرفض' : 'Rejection Reason' }}</label>
                                <textarea name="rejection_reason" rows="3"
                                          class="form-control mb-2 @error('rejection_reason') is-invalid @enderror" required>{{ old('rejection_reason') }}</textarea>
                                @error('rejection_reason') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <button type="submit" class="btn btn-danger w-100"
                                        onclick="return confirm('{{ $isAr ? 'هل أنت متأكد من رفض هذه الفكرة؟' : 'Are you sure you want to reject this idea?' }}')">
                                    <i class="bi bi-x-circle"></i> {{ $isAr ? 'رفض الفكرة' : 'Reject Idea' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endcan

            {{-- تحويل إلى مشروع --}}
            @can('create', \App\Models\Project::class)
                @if ($idea->status === 'approved')
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <p class="text-muted mb-3">
                                {{ $isAr ? 'يمكن تحويل هذه الفكرة إلى مشروع رسمي' : 'This idea can be converted into an official project' }}
                            </p>
                            <a href="{{ route('projects.create', ['idea_id' => $idea->id]) }}" class="btn btn-primary w-100">
                                <i class="bi bi-arrow-{{ $isAr ? 'left' : 'right' }}-circle"></i>
                                {{ $isAr ? 'تحويل إلى مشروع' : 'Convert to Project' }}
                            </a>
                        </div>
                    </div>
                @endif
            @endcan

        </div>
    </div>
</div>
@endsection