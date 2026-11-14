@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')
@section('page-title', 'Riwayat Pembayaran')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pembayaran</h4>
</div>

@if(!$warga)
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>
    Data warga belum terdaftar. Hubungi admin.
</div>
@else
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tagihan</th>
                        <th>Jumlah Iuran</th>
                        <th>Tanggal Bayar</th>
                        <th>Jumlah Dibayar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $riwayat->firstItem() + $i }}</td>
                        <td>{{ $p->tagihan->label ?? '-' }}</td>
                        <td>Rp {{ number_format($p->tagihan->jumlah_iuran ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
                        <td class="fw-500 text-success">
                            Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                        </td>
                        <td>{!! $p->status_badge !!}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada riwayat pembayaran lunas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($riwayat, 'hasPages') && $riwayat->hasPages())
    <div class="card-footer bg-white">{{ $riwayat->links() }}</div>
    @endif
</div>
@endif
@endsection
