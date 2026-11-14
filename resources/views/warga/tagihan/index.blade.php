@extends('layouts.app')

@section('title', 'Tagihan Saya')
@section('page-title', 'Tagihan Saya')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-receipt me-2 text-primary"></i>Tagihan Saya</h4>
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
                        <th>Jumlah Bayar</th>
                        <th>Tanggal Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihan as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $tagihan->firstItem() + $i }}</td>
                        <td>{{ $p->tagihan->label ?? '-' }}</td>
                        <td>Rp {{ number_format($p->tagihan->jumlah_iuran ?? 0, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
                        <td>{!! $p->status_badge !!}</td>
                        <td>
                            <a href="{{ route('warga.tagihan.show', $p) }}"
                               class="btn btn-sm {{ $p->status !== \App\Models\Pembayaran::STATUS_LUNAS ? 'btn-primary' : 'btn-outline-secondary' }}">
                                @if($p->status === \App\Models\Pembayaran::STATUS_BELUM_LUNAS || $p->status === \App\Models\Pembayaran::STATUS_DITOLAK)
                                <i class="bi bi-credit-card me-1"></i>Bayar
                                @elseif($p->status === \App\Models\Pembayaran::STATUS_MENUNGGU_VERIFIKASI)
                                <i class="bi bi-hourglass-split me-1"></i>Detail
                                @else
                                <i class="bi bi-eye me-1"></i>Detail
                                @endif
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada tagihan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($tagihan, 'hasPages') && $tagihan->hasPages())
    <div class="card-footer bg-white">{{ $tagihan->links() }}</div>
    @endif
</div>
@endif
@endsection
