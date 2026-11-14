@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-hourglass-split me-2 text-warning"></i>Antrian Verifikasi Pembayaran</h4>
    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Warga</th>
                        <th>Tagihan</th>
                        <th>Jumlah Bayar</th>
                        <th>Metode</th>
                        <th>Channel</th>
                        <th>Bukti</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaran as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $pembayaran->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-500">{{ $p->warga->nama ?? '-' }}</div>
                            <small class="text-muted">{{ $p->warga->no_hp ?? '' }}</small>
                        </td>
                        <td>{{ $p->tagihan->label ?? '-' }}</td>
                        <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>{{ $p->payment_method_label }}</td>
                        <td>{{ $p->payment_channel ?? '-' }}</td>
                        <td>
                            @if($p->proof_of_payment)
                                @if(Str::endsWith($p->proof_of_payment, '.pdf'))
                                <a href="{{ $p->proof_url }}" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2">
                                    <i class="bi bi-file-pdf"></i> PDF
                                </a>
                                @else
                                <a href="{{ $p->proof_url }}" target="_blank">
                                    <img src="{{ $p->proof_url }}" alt="Bukti" style="width:56px;height:56px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6;">
                                </a>
                                @endif
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $p->updated_at->format('d M Y H:i') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                {{-- Setujui --}}
                                <form action="{{ route('admin.pembayaran.setujui', $p) }}" method="POST"
                                      onsubmit="return confirm('Setujui pembayaran ini?')">
                                    @csrf
                                    <button class="btn btn-sm btn-success py-0 px-2">
                                        <i class="bi bi-check-lg"></i> Setujui
                                    </button>
                                </form>

                                {{-- Tolak --}}
                                <button class="btn btn-sm btn-outline-danger py-0 px-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalTolak{{ $p->id }}">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </div>

                            {{-- Modal Tolak --}}
                            <div class="modal fade" id="modalTolak{{ $p->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.pembayaran.tolak', $p) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Pembayaran</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted mb-2">
                                                    Tolak pembayaran <strong>{{ $p->warga->nama ?? '-' }}</strong>
                                                    untuk tagihan <strong>{{ $p->tagihan->label ?? '-' }}</strong>?
                                                </p>
                                                <label class="form-label fw-500">Catatan Penolakan <span class="text-danger">*</span></label>
                                                <textarea name="verification_note" class="form-control" rows="3"
                                                          placeholder="Contoh: Bukti tidak jelas, nominal tidak sesuai, dll."
                                                          required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-check2-all fs-2 d-block mb-2 text-success"></i>
                            Tidak ada pembayaran yang menunggu verifikasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pembayaran->hasPages())
    <div class="card-footer bg-white">{{ $pembayaran->links() }}</div>
    @endif
</div>
@endsection
