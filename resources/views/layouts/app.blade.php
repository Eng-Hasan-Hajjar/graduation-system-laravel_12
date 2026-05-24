<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      data-bs-theme="{{ auth()->user()?->theme_preference ?? 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('app.name')) - {{ config('app.name') }}</title>

    {{-- Bootstrap RTL/LTR --}}
    @if(app()->getLocale() === 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @endif

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:       #4f46e5;
            --primary-dark:  #3730a3;
            --secondary:     #7c3aed;
            --accent:        #06b6d4;
            --sidebar-width: 260px;
            --font-ar:       'Cairo', sans-serif;
            --font-en:       'Inter', sans-serif;
        }

        [data-bs-theme="dark"] {
            --bs-body-bg:      #0f172a;
            --bs-body-color:   #e2e8f0;
            --bs-card-bg:      #1e293b;
            --sidebar-bg:      #1e293b;
            --sidebar-color:   #e2e8f0;
        }

        [data-bs-theme="light"] {
            --bs-body-bg:      #f8fafc;
            --sidebar-bg:      #ffffff;
            --sidebar-color:   #334155;
        }

        body {
            font-family: {{ app()->getLocale() === 'ar' ? 'var(--font-ar)' : 'var(--font-en)' }};
            background: var(--bs-body-bg);
        }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            position: fixed;
            top: 0;
            {{ app()->getLocale() === 'ar' ? 'right: 0;' : 'left: 0;' }}
            z-index: 1000;
            box-shadow: {{ app()->getLocale() === 'ar' ? '-2px' : '2px' }} 0 10px rgba(0,0,0,.08);
            transition: transform .3s ease;
            overflow-y: auto;
        }

        #sidebar .brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(0,0,0,.08);
        }

        #sidebar .brand h5 {
            color: var(--primary);
            font-weight: 700;
            font-size: .95rem;
            margin: 0;
            line-height: 1.4;
        }

        #sidebar .nav-link {
            color: var(--sidebar-color);
            border-radius: 8px;
            margin: 2px 8px;
            padding: .55rem 1rem;
            font-size: .88rem;
            transition: all .2s;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: var(--primary);
            color: #fff !important;
        }

        #sidebar .nav-link i {
            font-size: 1rem;
            width: 22px;
        }

        #sidebar .section-label {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            opacity: .5;
            padding: 1rem 1.25rem .35rem;
        }

        /* ── Main Content ── */
        #main-content {
            {{ app()->getLocale() === 'ar' ? 'margin-right:' : 'margin-left:' }} var(--sidebar-width);
            min-height: 100vh;
        }

        /* ── Topbar ── */
        .topbar {
            background: var(--bs-body-bg);
            border-bottom: 1px solid rgba(0,0,0,.06);
            padding: .75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        /* ── Cards ── */
        .stat-card {
            border: none;
            border-radius: 12px;
            transition: transform .2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card .icon-box {
            width: 50px; height: 50px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }

        /* ── Badges ── */
        .badge-status { font-size: .78rem; padding: .35em .7em; border-radius: 6px; }

        /* ── Table ── */
        .table { font-size: .88rem; }

        /* ── Progress ── */
        .progress { height: 8px; border-radius: 4px; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            #sidebar { transform: {{ app()->getLocale() === 'ar' ? 'translateX(100%)' : 'translateX(-100%)' }}; }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin: 0 !important; }
        }
    </style>

    @stack('styles')
</head>
<body>

@auth
{{-- ═══════════════ SIDEBAR ═══════════════ --}}
<nav id="sidebar">
    <div class="brand text-center">
        <i class="bi bi-mortarboard-fill fs-2 text-primary"></i>
        <h5 class="mt-2">{{ __('app.name') }}</h5>
        <small class="opacity-50">{{ auth()->user()->university?->name ?? config('app.name') }}</small>
    </div>

    <ul class="nav flex-column py-2">

        {{-- الرئيسية --}}
        <li><a href="{{ route('dashboard') }}" class="nav-link @active('dashboard')">
            <i class="bi bi-speedometer2 me-2"></i> {{ __('nav.dashboard') }}
        </a></li>

        {{-- إدارة المشاريع --}}
        <div class="section-label">{{ __('nav.projects') }}</div>

        <li><a href="{{ route('projects.index') }}" class="nav-link @active('projects.*')">
            <i class="bi bi-folder2-open me-2"></i> {{ __('nav.all_projects') }}
        </a></li>

        @if(auth()->user()->isStudent())
        <li><a href="{{ route('projects.index', ['status'=>'my']) }}" class="nav-link">
            <i class="bi bi-person-workspace me-2"></i> {{ __('nav.my_project') }}
        </a></li>
        @endif

        <li><a href="{{ route('ideas.index') }}" class="nav-link @active('ideas.*')">
            <i class="bi bi-lightbulb me-2"></i> {{ __('nav.ideas') }}
        </a></li>

        @if(auth()->user()->isStudent())
        <li><a href="{{ route('reports.index') }}" class="nav-link @active('reports.*')">
            <i class="bi bi-file-earmark-text me-2"></i> {{ __('nav.my_reports') }}
        </a></li>
        @endif

        {{-- إدارة المناقشات --}}
        @if(auth()->user()->isStaff())
        <div class="section-label">{{ __('nav.discussions') }}</div>

        <li><a href="{{ route('schedules.index') }}" class="nav-link @active('schedules.*')">
            <i class="bi bi-calendar-event me-2"></i> {{ __('nav.schedules') }}
        </a></li>

        <li><a href="{{ route('committees.index') }}" class="nav-link @active('committees.*')">
            <i class="bi bi-people me-2"></i> {{ __('nav.committees') }}
        </a></li>

        <li><a href="{{ route('projects.archived') }}" class="nav-link">
            <i class="bi bi-archive me-2"></i> {{ __('nav.archive') }}
        </a></li>
        @endif

        {{-- الإدارة --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isCoordinator())
        <div class="section-label">{{ __('nav.management') }}</div>

        <li><a href="{{ route('users.index') }}" class="nav-link @active('users.*')">
            <i class="bi bi-person-lines-fill me-2"></i> {{ __('nav.users') }}
        </a></li>

        <li><a href="{{ route('academic-years.index') }}" class="nav-link">
            <i class="bi bi-calendar3 me-2"></i> {{ __('nav.academic_years') }}
        </a></li>

        <li><a href="{{ route('departments.index') }}" class="nav-link">
            <i class="bi bi-building me-2"></i> {{ __('nav.departments') }}
        </a></li>

        @if(auth()->user()->isAdmin())
        <li><a href="{{ route('settings.index') }}" class="nav-link @active('settings.*')">
            <i class="bi bi-gear me-2"></i> {{ __('nav.settings') }}
        </a></li>
        @endif
        @endif

    </ul>

    {{-- User info at bottom --}}
    <div class="p-3 border-top mt-auto" style="position:sticky;bottom:0;background:inherit;">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ auth()->user()->avatar_url }}" width="36" height="36"
                 class="rounded-circle object-fit-cover" alt="">
            <div class="flex-grow-1 overflow-hidden">
                <div class="fw-semibold small text-truncate">{{ auth()->user()->name }}</div>
                <div class="opacity-50" style="font-size:.75rem">{{ auth()->user()->role_label }}</div>
            </div>
        </div>
    </div>
</nav>

{{-- ═══════════════ MAIN ═══════════════ --}}
<div id="main-content">

    {{-- Topbar --}}
    <div class="topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                @yield('breadcrumb')
            </nav>
        </div>

        <div class="d-flex align-items-center gap-2">

            {{-- Language Toggle --}}
            <a href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
               class="btn btn-sm btn-outline-secondary" title="{{ __('app.switch_lang') }}">
                <i class="bi bi-translate me-1"></i>
                {{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}
            </a>

            {{-- Theme Toggle --}}
            <button class="btn btn-sm btn-outline-secondary" id="themeToggle" title="{{ __('app.toggle_theme') }}">
                <i class="bi bi-{{ auth()->user()->theme_preference === 'dark' ? 'sun' : 'moon' }}"></i>
            </button>

            {{-- Notifications --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="dropdown">
                    <i class="bi bi-bell"></i>
                    @php $unread = auth()->user()->unreadNotifications->count() @endphp
                    @if($unread > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              style="font-size:.65rem">{{ $unread }}</span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width:300px;max-height:400px;overflow-y:auto">
                    <li class="px-3 py-2 fw-semibold border-bottom">{{ __('app.notifications') }}</li>
                    @forelse(auth()->user()->notifications->take(8) as $n)
                        <li>
                            <a class="dropdown-item py-2 {{ $n->read_at ? '' : 'bg-primary bg-opacity-10' }}"
                               href="{{ $n->data['url'] ?? '#' }}">
                                <div class="small fw-semibold">{{ $n->data['title'] ?? '' }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ $n->created_at->diffForHumans() }}</div>
                            </a>
                        </li>
                    @empty
                        <li class="px-3 py-2 text-muted small">{{ __('app.no_notifications') }}</li>
                    @endforelse
                </ul>
            </div>

            {{-- User Menu --}}
            <div class="dropdown">
                <button class="btn btn-sm d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->avatar_url }}" width="30" height="30"
                         class="rounded-circle" alt="">
                    <span class="d-none d-md-inline small fw-semibold">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile') }}">
                        <i class="bi bi-person me-2"></i>{{ __('nav.profile') }}
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>{{ __('nav.logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Page Content --}}
    <main class="p-4">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

@endauth

{{-- Guest layout --}}
@guest
    @yield('content')
@endguest

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Theme Toggle
document.getElementById('themeToggle')?.addEventListener('click', function() {
    const html  = document.documentElement;
    const theme = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-bs-theme', theme);
    fetch('{{ route("user.preferences") }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({theme})
    });
    location.reload();
});

// Sidebar Toggle (mobile)
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
});

// Auto-dismiss alerts
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(el => {
        new bootstrap.Alert(el).close();
    });
}, 5000);
</script>

@stack('scripts')
</body>
</html>