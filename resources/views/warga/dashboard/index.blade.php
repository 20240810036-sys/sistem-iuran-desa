@extends('layouts.app')

@section('title', 'Dashboard Warga')
@section('page-title', 'Dashboard')

@section('content')
@if(!$warga)
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data warga Anda belum terdaftar. Hubungi admin untuk mendaftarkan data Anda.
</div>
@else

<div class="mb-3">
    <h5 class="fw-600 mb-0">Selamat datang, {{ $warga->nama }}!</h5>
    <small class="text-muted">{{ $warga->alamat }}</small>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-label mb-1">Total Tagihan</div>
            <div class="stat-value text-primary">{{ $totalTagihanAktif }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-label mb-1">Sudah Lunas</div>
            <div class="stat-value text-success">{{ $totalLunas }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="stat-label mb-1">Belum Lunas</div>
            <div class="stat-value text-danger">{{ $totalBelumLunas }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Pembayaran Terbaru</span>
        <a href="{{ route('warga.riwayat.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tagihan</th>
                    <th>Tanggal Bayar</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayatPembayaran as $i => $p)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>{{ $p->tagihan->label ?? '-' }}</td>
                    <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
                    <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                    <td>{!! $p->status_badge !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada riwayat pembayaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
