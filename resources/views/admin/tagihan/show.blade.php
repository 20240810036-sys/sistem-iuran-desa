@extends('layouts.app')

@section('title', 'Detail Tagihan')
@section('page-title', 'Detail Tagihan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-receipt me-2 text-primary"></i>Detail Tagihan – {{ $tagihan->label }}</h4>
    <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-label mb-1">Jumlah Iuran</div>
            <div class="stat-value text-primary">Rp {{ number_format($tagihan->jumlah_iuran, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-label mb-1">Sudah Lunas</div>
            <div class="stat-value text-success">{{ $tagihan->totalLunas() }}</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-label mb-1">Belum Lunas</div>
            <div class="stat-value text-danger">{{ $tagihan->totalBelumLunas() }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Daftar Pembayaran</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Warga</th>
                    <th>Tanggal Bayar</th>
                    <th>Jumlah Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihan->pembayaran as $i => $p)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>{{ $p->warga->nama ?? '-' }}</td>
                    <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
                    <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                    <td>{!! $p->status_badge !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-3 text-muted">Belum ada pembayaran.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
