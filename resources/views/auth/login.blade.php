<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Sistem Iuran Sampah Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f0f4ff;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: #fff;
            border-radius: .875rem;
            border: 1px solid #e9ecef;
            padding: 2.5rem 2rem;
            width: 100%; max-width: 420px;
            box-shadow: 0 4px 24px rgba(37,99,235,.07);
        }
        .login-icon {
            width: 60px; height: 60px;
            background: #eff6ff;
            border-radius: 1rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.75rem; color: #2563EB;
            margin: 0 auto 1.25rem;
        }
        .login-title { font-weight: 700; font-size: 1.2rem; }
        .login-sub { font-size: .85rem; color: #6c757d; }
        .form-label { font-size: .875rem; font-weight: 500; }
        .btn-login {
            background: #2563EB; border: none; color: #fff;
            width: 100%; padding: .65rem;
            border-radius: .5rem; font-weight: 600;
            transition: background .2s;
        }
        .btn-login:hover { background: #1d4ed8; color: #fff; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-icon"><i class="bi bi-recycle"></i></div>
    <div class="text-center mb-4">
        <h5 class="login-title">Sistem Iuran Sampah Desa</h5>
        <p class="login-sub mb-0">Masuk untuk melanjutkan</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="admin@desa.id" required autofocus>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••" required>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember" style="font-size:.85rem;">Ingat saya</label>
            </div>
        </div>
        <button type="submit" class="btn btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
    </form>

    <p class="text-center mt-3 mb-0" style="font-size:.85rem;">
        Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai warga</a>
    </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
