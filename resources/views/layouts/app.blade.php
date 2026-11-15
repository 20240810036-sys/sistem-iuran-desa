<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Iuran Sampah Desa')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 240px;
            --topbar-height: 56px;
            --primary: #2563EB;
            --primary-dark: #1d4ed8;
            --gray-light: #f8f9fa;
            --border: #e9ecef;
        }

        body { background: #f4f6fb; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background: #fff;
            border-right: 1px solid var(--border);
            z-index: 100;
            display: flex; flex-direction: column;
        }
        .sidebar-brand {
            height: var(--topbar-height);
            display: flex; align-items: center;
            padding: 0 1.25rem;
            border-bottom: 1px solid var(--border);
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            gap: .5rem;
        }
        .sidebar-nav { flex: 1; overflow-y: auto; padding: .75rem 0; }
        .sidebar-label {
            font-size: .7rem; font-weight: 600; color: #adb5bd;
            text-transform: uppercase; letter-spacing: .08em;
            padding: .75rem 1.25rem .25rem;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: .625rem;
            padding: .55rem 1.25rem;
            color: #495057;
            text-decoration: none;
            font-size: .875rem;
            border-radius: 0;
            transition: background .15s, color .15s;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: #eff6ff;
            color: var(--primary);
        }
        .sidebar-link i { font-size: 1rem; width: 1.25rem; }

        /* Topbar */
        .topbar {
            position: fixed; top: 0; left: var(--sidebar-width); right: 0;
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 1.5rem;
            z-index: 99;
            justify-content: space-between;
        }
        .topbar-title { font-weight: 600; font-size: 1rem; color: #212529; }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 1.75rem;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: .625rem;
            border: 1px solid var(--border);
            padding: 1.25rem 1.5rem;
        }
        .stat-card .stat-icon {
            width: 48px; height: 48px;
            border-radius: .5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }
        .stat-card .stat-value { font-size: 1.6rem; font-weight: 700; }
        .stat-card .stat-label { font-size: .8rem; color: #6c757d; }

        /* Table card */
        .card {
            border: 1px solid var(--border);
            border-radius: .625rem;
            box-shadow: none;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            padding: .9rem 1.25rem;
        }
        .table thead th {
            background: #f8f9fa;
            font-size: .8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            border-bottom: 1px solid var(--border);
        }
        .table td { font-size: .875rem; vertical-align: middle; }
        .table-hover tbody tr:hover { background: #f8fbff; }

        /* Badges */
        .badge { font-size: .75rem; font-weight: 500; }

        /* Forms */
        .form-label { font-size: .875rem; font-weight: 500; }
        .form-control, .form-select { font-size: .875rem; }

        /* Buttons */
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* Page header */
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem;
        }
        .page-header h4 { margin: 0; font-weight: 700; font-size: 1.15rem; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform .25s; }
            .sidebar.show { transform: translateX(0); }
            .topbar, .main-content { left: 0; margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-recycle"></i>
        Iuran Sampah Desa
    </div>
    <nav class="sidebar-nav">
        @if(auth()->user()->isAdmin())
            <div class="sidebar-label">Utama</div>
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="sidebar-label">Kelola Data</div>
            <a href="{{ route('admin.warga.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.warga.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Data Warga
            </a>
            <a href="{{ route('admin.tagihan.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.tagihan.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Tagihan
            </a>
            <a href="{{ route('admin.pembayaran.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Pembayaran
            </a>
            <a href="{{ route('admin.pembayaran.verifikasi') }}"
                class="sidebar-link {{ request()->routeIs('admin.pembayaran.verifikasi') ? 'active' : '' }}">
                <i class="bi bi-hourglass-split"></i> Verifikasi Pembayaran
            </a>

            <a href="{{ route('admin.payment-setting.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.payment-setting.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Pengaturan Pembayaran
            </a>
            <a href="{{ route('admin.user.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Kelola User
            </a>
            <a href="{{ route('admin.activity-log.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.activity-log.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> Log Aktivitas
            </a>

            <div class="sidebar-label">Laporan</div>
            <a href="{{ route('admin.laporan.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan
            </a>
        @else
            <div class="sidebar-label">Menu</div>
            <a href="{{ route('warga.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('warga.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house"></i> Dashboard
            </a>
            <a href="{{ route('warga.tagihan.index') }}"
               class="sidebar-link {{ request()->routeIs('warga.tagihan.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Tagihan Saya
            </a>
            <a href="{{ route('warga.riwayat.index') }}"
               class="sidebar-link {{ request()->routeIs('warga.riwayat.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Riwayat Bayar
            </a>
        @endif
    </nav>
</aside>

{{-- Topbar --}}
<header class="topbar">
    <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted" style="font-size:.85rem;">
            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
        </span>
        <form action="{{ route('logout') }}" method="POST" class="mb-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</header>

{{-- Main Content --}}
<main class="main-content">
    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
