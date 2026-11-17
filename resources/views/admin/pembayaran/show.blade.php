@extends('layouts.app')

@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-cash-coin me-2 text-primary"></i>Detail Pembayaran</h4>
    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:520px;">
    <div class="card-header">Informasi Pembayaran</div>
    <div class="card-body">
        <dl class="row mb-0" style="font-size:.875rem;">
            <dt class="col-5 text-muted">Nama Warga</dt>
            <dd class="col-7 fw-500">{{ $pembayaran->warga->nama ?? '-' }}</dd>

            <dt class="col-5 text-muted">Alamat</dt>
            <dd class="col-7">{{ $pembayaran->warga->alamat ?? '-' }}</dd>

            <dt class="col-5 text-muted">Tagihan</dt>
            <dd class="col-7">{{ $pembayaran->tagihan->label ?? '-' }}</dd>

            <dt class="col-5 text-muted">Jumlah Iuran</dt>
            <dd class="col-7">Rp {{ number_format($pembayaran->tagihan->jumlah_iuran ?? 0, 0, ',', '.') }}</dd>

            <dt class="col-5 text-muted">Tanggal Bayar</dt>
            <dd class="col-7">{{ $pembayaran->tanggal_bayar->format('d M Y') }}</dd>

            <dt class="col-5 text-muted">Jumlah Bayar</dt>
            <dd class="col-7 fw-500 text-success">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</dd>

            <dt class="col-5 text-muted">Status</dt>
            <dd class="col-7">{!! $pembayaran->status_badge !!}</dd>

            @if($pembayaran->payment_method)
            <dt class="col-5 text-muted">Metode Bayar</dt>
            <dd class="col-7">{{ $pembayaran->payment_method_label }} ({{ $pembayaran->payment_channel ?? '-' }})</dd>
            @endif

            @if($pembayaran->proof_of_payment)
            <dt class="col-5 text-muted">Bukti Bayar</dt>
            <dd class="col-7">
                @if(Str::endsWith(strtolower($pembayaran->proof_of_payment), '.pdf'))
                <a href="{{ $pembayaran->proof_url }}" target="_blank" class="btn btn-xs btn-outline-danger py-0 px-2">
                    <i class="bi bi-file-earmark-pdf"></i> Dokumen PDF
                </a>
                @else
                <a href="{{ $pembayaran->proof_url }}" target="_blank">
                    <img src="{{ $pembayaran->proof_url }}" alt="Bukti" style="max-height:100px;border-radius:4px;border:1px solid #dee2e6;" onerror="this.onerror=null; this.src='{{ asset('storage/' . $pembayaran->proof_of_payment) }}';">
                </a>
                @endif
            </dd>
            @endif

            @if($pembayaran->verification_note)
            <dt class="col-5 text-muted">Catatan Verifikasi</dt>
            <dd class="col-7 text-danger">{{ $pembayaran->verification_note }}</dd>
            @endif

            <dt class="col-5 text-muted">Dicatat</dt>
            <dd class="col-7">{{ $pembayaran->created_at->format('d M Y H:i') }}</dd>
        </dl>
    </div>
    <div class="card-footer bg-white d-flex gap-2">
        <a href="{{ route('admin.pembayaran.edit', $pembayaran) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <form action="{{ route('admin.pembayaran.destroy', $pembayaran) }}" method="POST"
              onsubmit="return confirm('Hapus data ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
        </form>
    </div>
</div>
@endsection
