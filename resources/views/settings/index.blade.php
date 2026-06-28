@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'إعدادات النظام' : 'System Settings')

@section('content')
@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';

    $groupLabels = [
        'general'      => $isAr ? 'إعدادات عامة'          : 'General Settings',
        'academic'     => $isAr ? 'الإعدادات الأكاديمية'  : 'Academic Settings',
        'projects'     => $isAr ? 'إعدادات المشاريع'      : 'Project Settings',
        'defense'      => $isAr ? 'إعدادات المناقشات'     : 'Defense Settings',
        'notifications'=> $isAr ? 'إعدادات الإشعارات'    : 'Notification Settings',
        'appearance'   => $isAr ? 'المظهر والعرض'         : 'Appearance',
        'security'     => $isAr ? 'الأمان'                 : 'Security',
        'email'        => $isAr ? 'البريد الإلكتروني'     : 'Email Settings',
    ];

    $groupIcons = [
        'general'       => 'bi-gear',
        'academic'      => 'bi-mortarboard',
        'projects'      => 'bi-folder2-open',
        'defense'       => 'bi-people',
        'notifications' => 'bi-bell',
        'appearance'    => 'bi-palette',
        'security'      => 'bi-shield-lock',
        'email'         => 'bi-envelope',
    ];
@endphp

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="mb-0">
            <i class="bi bi-gear-fill text-primary"></i>
            {{ $isAr ? 'إعدادات النظام' : 'System Settings' }}
        </h3>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($settings->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-gear fs-1 d-block mb-3"></i>
                {{ $isAr ? 'لا توجد إعدادات متاحة' : 'No settings available' }}
            </div>
        </div>
    @else
        @foreach ($settings as $group => $groupSettings)
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi {{ $groupIcons[$group] ?? 'bi-sliders' }}"></i>
                        {{ $groupLabels[$group] ?? ucfirst($group) }}
                        <span class="badge bg-secondary ms-1">{{ $groupSettings->count() }}</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    @foreach ($groupSettings as $setting)
                        <div class="border-bottom px-4 py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">

                            {{-- الاسم والوصف --}}
                            <div style="min-width: 200px;">
                                <div class="fw-bold">
                                    {{ $isAr ? ($setting->label_ar ?? $setting->key) : ($setting->label_en ?? $setting->key) }}
                                </div>
                                <small class="text-muted font-monospace">{{ $setting->key }}</small>
                            </div>

                            {{-- حقل التعديل --}}
                            <form action="{{ route('settings.update', $setting) }}"
                                  method="POST"
                                  class="d-flex align-items-center gap-2"
                                  style="min-width: 280px;">
                                @csrf
                                @method('PUT')

                                @if ($setting->type === 'boolean')
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox"
                                               name="value" value="1"
                                               id="setting_{{ $setting->id }}"
                                               {{ $setting->value ? 'checked' : '' }}
                                               onchange="this.form.submit()">
                                        <label class="form-check-label" for="setting_{{ $setting->id }}">
                                            {{ $setting->value
                                                ? ($isAr ? 'مفعّل' : 'Enabled')
                                                : ($isAr ? 'معطّل' : 'Disabled') }}
                                        </label>
                                    </div>

                                @elseif ($setting->type === 'integer')
                                    <input type="number"
                                           name="value"
                                           class="form-control form-control-sm"
                                           style="width: 120px;"
                                           value="{{ $setting->getRawOriginal('value') }}">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check2"></i>
                                    </button>

                                @elseif ($setting->type === 'text' || $setting->type === 'textarea')
                                    <textarea name="value" rows="2"
                                              class="form-control form-control-sm"
                                              style="min-width: 240px;">{{ $setting->getRawOriginal('value') }}</textarea>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check2"></i>
                                    </button>

                                @elseif ($setting->type === 'select')
                                    @php
                                        $options = is_array($setting->getRawOriginal('value'))
                                            ? $setting->getRawOriginal('value')
                                            : [];
                                    @endphp
                                    <input type="text" name="value"
                                           class="form-control form-control-sm"
                                           style="min-width: 200px;"
                                           value="{{ $setting->getRawOriginal('value') }}">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check2"></i>
                                    </button>

                                @else
                                    {{-- string / default --}}
                                    <input type="text"
                                           name="value"
                                           class="form-control form-control-sm"
                                           style="min-width: 200px;"
                                           value="{{ $setting->getRawOriginal('value') }}">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                @endif

                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection