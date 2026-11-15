<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi – Sistem Iuran Sampah Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f4ff; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; padding: 2rem 0; }
        .login-card { background: #fff; border-radius: .875rem; border: 1px solid #e9ecef; padding: 2.5rem 2rem; width: 100%; max-width: 480px; box-shadow: 0 4px 24px rgba(37,99,235,.07); }
        .login-icon { width: 60px; height: 60px; background: #eff6ff; border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: #2563EB; margin: 0 auto 1.25rem; }
        .login-title { font-weight: 700; font-size: 1.2rem; }
        .login-sub { font-size: .85rem; color: #6c757d; }
        .form-label { font-size: .875rem; font-weight: 500; }
        .btn-login { background: #2563EB; border: none; color: #fff; width: 100%; padding: .65rem; border-radius: .5rem; font-weight: 600; }
        .btn-login:hover { background: #1d4ed8; color: #fff; }
        .hint { font-size: .78rem; color: #6c757d; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-icon"><i class="bi bi-person-plus"></i></div>
    <div class="text-center mb-4">
        <h5 class="login-title">Daftar Akun Warga</h5>
        <p class="login-sub mb-0">Sistem Iuran Sampah Desa</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 mb-3" style="font-size:.85rem;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" maxlength="100" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" maxlength="150" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">No. HP</label>
                <input type="text" name="no_hp" maxlength="15" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Alamat</label>
                <input type="text" name="alamat" maxlength="255" class="form-control @error('alamat') is-invalid @enderror" value="{{ old('alamat') }}" placeholder="RT/RW, Dusun" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            <div class="hint mt-1">Min. 8 karakter, berisi huruf besar, huruf kecil, angka, dan simbol.</div>
        </div>
        <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-login"><i class="bi bi-person-check me-2"></i>Daftar</button>
    </form>

    <p class="text-center mt-3 mb-0" style="font-size:.85rem;">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </p>
</div>
</body>
</html>
