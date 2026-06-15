<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('error_code') | @if(app()->getLocale() === 'ar')نظام إدارة مشاريع التخرج @else Graduation Project Management System @endif</title>

    {{-- تطبيق الوضع الليلي قبل عرض الصفحة لمنع الفليكر --}}
    <script>
        (function () {
            try {
                var theme = localStorage.getItem('app-theme') ||
                    (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {}
        })();
    </script>

    <style>
        :root {
            --bg-1: #eef2ff;
            --bg-2: #f8fafc;
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --danger: #ef4444;
            --danger-bg: #fee2e2;
            --warning: #f59e0b;
            --warning-bg: #fef3c7;
            --info: #0ea5e9;
            --info-bg: #e0f2fe;
            --shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15);
        }

        html[data-theme="dark"] {
            --bg-1: #0b1120;
            --bg-2: #111827;
            --card-bg: #1e293b;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --border: #334155;
            --primary: #818cf8;
            --primary-dark: #6366f1;
            --danger: #f87171;
            --danger-bg: #3f1d1d;
            --warning: #fbbf24;
            --warning-bg: #422006;
            --info: #38bdf8;
            --info-bg: #0c2d3d;
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, 'Cairo', Arial, sans-serif;
            background: linear-gradient(135deg, var(--bg-1), var(--bg-2));
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            transition: background .3s ease, color .3s ease;
        }

        .error-wrapper { width: 100%; max-width: 520px; text-align: center; }

        .error-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 44px 36px;
            box-shadow: var(--shadow);
        }

        .brand {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--border);
        }
        .brand-icon { font-size: 28px; }
        .brand-name { font-size: 14px; font-weight: 600; color: var(--text-muted); }

        .status-icon {
            width: 84px; height: 84px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .status-icon svg { width: 40px; height: 40px; }
        .status-icon--danger  { background: var(--danger-bg);  color: var(--danger); }
        .status-icon--warning { background: var(--warning-bg); color: var(--warning); }
        .status-icon--info    { background: var(--info-bg);    color: var(--info); }

        .error-code {
            font-size: 64px; font-weight: 800; line-height: 1;
            background: linear-gradient(135deg, var(--primary), var(--info));
            -webkit-background-clip: text; background-clip: text; color: transparent;
            margin-bottom: 8px;
        }
        .error-title { font-size: 22px; font-weight: 700; margin-bottom: 12px; }
        .error-message { font-size: 14.5px; color: var(--text-muted); line-height: 1.9; margin-bottom: 28px; }

        .error-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 26px; border-radius: 10px; font-size: 14px; font-weight: 600;
            text-decoration: none; border: 1px solid transparent; cursor: pointer; transition: .2s;
            font-family: inherit;
        }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: transparent; color: var(--text); border-color: var(--border); }
        .btn-outline:hover { background: var(--bg-1); }

        .error-footer { margin-top: 20px; font-size: 12.5px; color: var(--text-muted); }

        .theme-toggle {
            position: fixed; top: 20px; inset-inline-end: 20px;
            width: 44px; height: 44px; border-radius: 50%;
            background: var(--card-bg); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; box-shadow: var(--shadow); color: var(--text);
        }
        .theme-toggle .icon { width: 20px; height: 20px; }
        .theme-toggle .icon-moon { display: none; }
        html[data-theme="dark"] .theme-toggle .icon-sun { display: none; }
        html[data-theme="dark"] .theme-toggle .icon-moon { display: block; }
    </style>
</head>
<body>

    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
        <svg class="icon icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="5"></circle>
            <line x1="12" y1="1" x2="12" y2="3"></line>
            <line x1="12" y1="21" x2="12" y2="23"></line>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
            <line x1="1" y1="12" x2="3" y2="12"></line>
            <line x1="21" y1="12" x2="23" y2="12"></line>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
        </svg>
        <svg class="icon icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
        </svg>
    </button>

    <main class="error-wrapper">
        <div class="error-card">
            <div class="brand">
                <div class="brand-icon">🎓</div>
                <div class="brand-name">
                    @if(app()->getLocale() === 'ar')
                        نظام إدارة مشاريع التخرج
                    @else
                        Graduation Project Management System
                    @endif
                </div>
            </div>

            <div class="status-icon status-icon--@yield('icon_type', 'danger')">
                @yield('icon')
            </div>

            <div class="error-code">@yield('error_code')</div>
            <h1 class="error-title">@yield('error_title')</h1>
            <p class="error-message">@yield('error_message')</p>

            <div class="error-actions">
                @yield('actions')
            </div>
        </div>

        <p class="error-footer">
            &copy; {{ date('Y') }}
            @if(app()->getLocale() === 'ar') جميع الحقوق محفوظة @else All rights reserved @endif
        </p>
    </main>

    <script>
        function toggleTheme() {
            var html = document.documentElement;
            var current = html.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            var next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            try { localStorage.setItem('app-theme', next); } catch (e) {}
        }
    </script>
</body>
</html>