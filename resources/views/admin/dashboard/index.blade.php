@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    {{-- Stat Cards --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Total Warga</div>
                    <div class="stat-value text-primary">{{ $totalWarga }}</div>
                </div>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Total Tagihan</div>
                    <div class="stat-value text-warning">{{ $totalTagihan }}</div>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Verifikasi --}}
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Menunggu Verifikasi</div>
                    <div class="stat-value text-warning">{{ $totalMenunggu }}</div>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            @if($totalMenunggu > 0)
            <a href="{{ route('admin.pembayaran.verifikasi') }}" class="btn btn-warning btn-sm mt-2 w-100">
                <i class="bi bi-check2-circle me-1"></i>Verifikasi Sekarang
            </a>
            @endif
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Total Lunas</div>
                    <div class="stat-value text-success">{{ $totalLunas }}</div>
                </div>
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Pembayaran Ditolak</div>
                    <div class="stat-value text-danger">{{ $totalDitolak }}</div>
                </div>
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label mb-1">Belum Bayar</div>
                    <div class="stat-value text-secondary">{{ $totalBelumBayar }}</div>
                </div>
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary">
                    <i class="bi bi-exclamation-circle-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Pembayaran Terbaru --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Pembayaran Terbaru</span>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.pembayaran.verifikasi') }}" class="btn btn-sm btn-warning">
                <i class="bi bi-hourglass-split me-1"></i>Antrian Verifikasi
                @if($totalMenunggu > 0)
                <span class="badge bg-white text-warning ms-1">{{ $totalMenunggu }}</span>
                @endif
            </a>
            <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-sm btn-outline-primary">
                Lihat Semua
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Warga</th>
                        <th>Tagihan</th>
                        <th>Metode</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaranTerbaru as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-500">{{ $p->warga->nama ?? '-' }}</div>
                            <small class="text-muted">{{ $p->warga->no_hp ?? '' }}</small>
                        </td>
                        <td>{{ $p->tagihan->label ?? '-' }}</td>
                        <td>{{ $p->payment_method_label }}</td>
                        <td>{{ $p->payment_channel ?? '-' }}</td>
                        <td>{!! $p->status_badge !!}</td>
                        <td>
                            @if($p->status === \App\Models\Pembayaran::STATUS_MENUNGGU_VERIFIKASI)
                            <a href="{{ route('admin.pembayaran.verifikasi') }}" class="btn btn-xs btn-warning py-0 px-2">
                                <i class="bi bi-check2"></i> Verifikasi
                            </a>
                            @else
                            <a href="{{ route('admin.pembayaran.show', $p) }}" class="btn btn-xs btn-outline-secondary py-0 px-2">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada data pembayaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
