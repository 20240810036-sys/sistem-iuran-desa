@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan</h4>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-cash-coin text-success" style="font-size:2.5rem;"></i>
                </div>
                <h6 class="fw-600">Laporan Pembayaran</h6>
                <p class="text-muted small mb-3">Rekap seluruh data pembayaran iuran warga.</p>
                <a href="{{ route('admin.laporan.pembayaran') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-receipt text-primary" style="font-size:2.5rem;"></i>
                </div>
                <h6 class="fw-600">Laporan Tagihan</h6>
                <p class="text-muted small mb-3">Rekap tagihan per bulan beserta status pembayaran.</p>
                <a href="{{ route('admin.laporan.tagihan') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-people text-warning" style="font-size:2.5rem;"></i>
                </div>
                <h6 class="fw-600">Laporan Warga</h6>
                <p class="text-muted small mb-3">Rekap data warga dan status pembayaran mereka.</p>
                <a href="{{ route('admin.laporan.warga') }}" class="btn btn-warning btn-sm text-white">
                    <i class="bi bi-eye me-1"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
