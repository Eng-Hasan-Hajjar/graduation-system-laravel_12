<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
      data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة مشاريع التخرج</title>

    {{-- Bootstrap RTL --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
        }

        * { font-family: 'Cairo', sans-serif; }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,.25);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }

        .login-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            padding: 2.5rem 2rem;
            text-align: center;
            color: #fff;
        }

        .login-header .icon-circle {
            width: 80px; height: 80px;
            background: rgba(255,255,255,.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.2rem;
            backdrop-filter: blur(10px);
        }

        .login-header h4 {
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: .3rem;
        }

        .login-header p {
            opacity: .8;
            font-size: .88rem;
            margin: 0;
        }

        .login-body { padding: 2rem; }

        .form-control {
            border-radius: 10px;
            padding: .75rem 1rem;
            border: 1.5px solid #e2e8f0;
            font-size: .9rem;
            transition: border-color .2s;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,.15);
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 1.5px solid #e2e8f0;
            border-left: none;
            background: #f8fafc;
            color: #64748b;
        }

        [dir="rtl"] .input-group-text {
            border-radius: 0 10px 10px 0;
            border-right: none;
            border-left: 1.5px solid #e2e8f0;
        }

        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            border-radius: 12px;
            padding: .85rem;
            font-size: 1rem;
            font-weight: 700;
            transition: transform .2s, box-shadow .2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79,70,229,.4);
        }

        .demo-badge {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: .75rem 1rem;
            font-size: .82rem;
        }

        .user-badge {
            display: flex; align-items: center; gap: .5rem;
            padding: .4rem .75rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            font-size: .8rem;
            transition: background .15s;
        }
        .user-badge:hover { background: #f1f5f9; }
        .user-badge .dot {
            width: 8px; height: 8px; border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="login-card">

    {{-- Header --}}
    <div class="login-header">
        <div class="icon-circle">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <h4>نظام إدارة مشاريع التخرج</h4>
        <p>جامعة حلب - كلية الحاسبات وتقنية المعلومات</p>
    </div>

    {{-- Body --}}
    <div class="login-body">

        @if($errors->any())
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3" style="font-size:.88rem">
            <i class="bi bi-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if(session('status'))
        <div class="alert alert-success py-2 mb-3" style="font-size:.88rem">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label fw-semibold small">البريد الإلكتروني</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', 'admin@kau.edu.sa') }}"
                           placeholder="example@university.edu.sa"
                           autocomplete="email" required autofocus>
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label class="form-label fw-semibold small">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           autocomplete="current-password" required>
                </div>
            </div>

            {{-- Remember --}}
            <div class="mb-4 d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">تذكرني</label>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-login btn-primary w-100 text-white mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                تسجيل الدخول
            </button>
        </form>

        {{-- Demo Accounts --}}
        <div class="demo-badge">
            <div class="fw-semibold text-success mb-2 small">
                <i class="bi bi-info-circle me-1"></i>حسابات تجريبية (كلمة المرور: password)
            </div>
            <div class="d-flex flex-wrap gap-2">
                <div class="user-badge" onclick="fillLogin('admin@kau.edu.sa')">
                    <span class="dot bg-danger"></span> مدير
                </div>
                <div class="user-badge" onclick="fillLogin('coordinator@kau.edu.sa')">
                    <span class="dot bg-warning"></span> منسق
                </div>
                <div class="user-badge" onclick="fillLogin('ahmed.ghamdi@kau.edu.sa')">
                    <span class="dot bg-primary"></span> مشرف
                </div>
                <div class="user-badge" onclick="fillLogin('s.mohammed@kau.edu.sa')">
                    <span class="dot bg-success"></span> طالب
                </div>
                <div class="user-badge" onclick="fillLogin('qahtani@kau.edu.sa')">
                    <span class="dot bg-info"></span> لجنة
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function fillLogin(email) {
    document.querySelector('input[name="email"]').value = email;
    document.querySelector('input[name="password"]').value = 'password';
}
</script>
</body>
</html>