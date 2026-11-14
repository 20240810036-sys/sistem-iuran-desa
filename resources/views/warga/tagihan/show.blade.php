@extends('layouts.app')

@section('title', 'Detail Tagihan')
@section('page-title', 'Detail Tagihan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-receipt me-2 text-primary"></i>Detail Tagihan</h4>
    <a href="{{ route('warga.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    {{-- Info Tagihan --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">Informasi Tagihan</div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-5 text-muted">Tagihan</dt>
                    <dd class="col-7 fw-500">{{ $pembayaran->tagihan->label ?? '-' }}</dd>

                    <dt class="col-5 text-muted">Jumlah Iuran</dt>
                    <dd class="col-7 fw-500 text-primary fs-5">Rp {{ number_format($pembayaran->tagihan->jumlah_iuran ?? 0, 0, ',', '.') }}</dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">{!! $pembayaran->status_badge !!}</dd>

                    @if($pembayaran->payment_method)
                    <dt class="col-5 text-muted">Metode</dt>
                    <dd class="col-7">{{ $pembayaran->payment_method_label }}</dd>

                    <dt class="col-5 text-muted">Channel</dt>
                    <dd class="col-7">{{ $pembayaran->payment_channel ?? '-' }}</dd>
                    @endif

                    @if($pembayaran->verification_note)
                    <dt class="col-5 text-muted">Catatan Admin</dt>
                    <dd class="col-7 text-danger">{{ $pembayaran->verification_note }}</dd>
                    @endif
                </dl>

                {{-- Bukti yang sudah diunggah --}}
                @if($pembayaran->proof_of_payment)
                <hr>
                <p class="fw-500 mb-1" style="font-size:.875rem;">Bukti Pembayaran:</p>
                @if(Str::endsWith($pembayaran->proof_of_payment, '.pdf'))
                <a href="{{ $pembayaran->proof_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-file-pdf me-1"></i>Lihat PDF
                </a>
                @else
                <a href="{{ $pembayaran->proof_url }}" target="_blank">
                    <img src="{{ $pembayaran->proof_url }}" alt="Bukti"
                         style="max-width:100%;border-radius:8px;border:1px solid #dee2e6;">
                </a>
                @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Panel Pembayaran --}}
    <div class="col-md-7">
        @if($pembayaran->status === \App\Models\Pembayaran::STATUS_LUNAS)
        <div class="alert alert-success d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-4"></i>
            <div>Pembayaran ini sudah <strong>lunas</strong>. Terima kasih!</div>
        </div>

        @elseif($pembayaran->status === \App\Models\Pembayaran::STATUS_MENUNGGU_VERIFIKASI)
        <div class="alert alert-warning d-flex align-items-center gap-2">
            <i class="bi bi-hourglass-split fs-4"></i>
            <div>Bukti pembayaran sedang <strong>menunggu verifikasi</strong> admin.</div>
        </div>

        @else
        {{-- Pilih Metode Pembayaran --}}
        <div class="card">
            <div class="card-header">
                <i class="bi bi-credit-card me-2"></i>Pilih Metode Pembayaran
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-500">Metode</label>
                    <div class="d-flex gap-2 flex-wrap" id="method-btns">
                        @if($banks->count())
                        <button type="button" class="btn btn-outline-primary method-btn" data-method="transfer_bank">
                            <i class="bi bi-bank me-1"></i>Transfer Bank
                        </button>
                        @endif
                        @if($qris && $qris->qr_image)
                        <button type="button" class="btn btn-outline-primary method-btn" data-method="qris">
                            <i class="bi bi-qr-code me-1"></i>QRIS
                        </button>
                        @endif
                        @if($ewallets->count())
                        <button type="button" class="btn btn-outline-primary method-btn" data-method="ewallet">
                            <i class="bi bi-wallet2 me-1"></i>E-Wallet
                        </button>
                        @endif
                        <button type="button" class="btn btn-outline-primary method-btn" data-method="tunai">
                            <i class="bi bi-cash-coin me-1"></i>Tunai
                        </button>
                    </div>
                </div>

                {{-- Panel Transfer Bank --}}
                <div id="panel-transfer_bank" class="payment-panel d-none">
                    <label class="form-label fw-500 mb-2">Pilih Bank Tujuan</label>
                    <div class="row g-2">
                        @foreach($banks as $bank)
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 channel-card" data-channel="{{ $bank->provider_name }}" style="cursor:pointer;">
                                <div class="fw-600">{{ $bank->provider_name }}</div>
                                <div class="text-primary fw-500">{{ $bank->account_number }}</div>
                                <small class="text-muted">a.n. {{ $bank->account_name }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Panel QRIS --}}
                <div id="panel-qris" class="payment-panel d-none">
                    @if($qris && $qris->qr_image)
                    <div class="text-center">
                        <p class="text-muted mb-2">Scan QR Code berikut menggunakan aplikasi pembayaran QRIS:</p>
                        <img src="{{ $qris->qr_image_url }}" alt="QRIS"
                             id="qris-img"
                             style="max-width:220px;border:2px solid #dee2e6;border-radius:12px;padding:8px;">
                        <div class="mt-3 d-flex justify-content-center gap-2">
                            <a href="{{ $qris->qr_image_url }}" download="QRIS-Iuran-Sampah.png"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i>Unduh PNG
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printQRIS()">
                                <i class="bi bi-printer me-1"></i>Cetak
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Panel E-Wallet --}}
                <div id="panel-ewallet" class="payment-panel d-none">
                    <label class="form-label fw-500 mb-2">Pilih E-Wallet</label>
                    <div class="row g-2">
                        @foreach($ewallets as $ew)
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 channel-card" data-channel="{{ $ew->provider_name }}" style="cursor:pointer;">
                                <div class="fw-600">{{ $ew->provider_name }}</div>
                                <div class="text-primary fw-500">{{ $ew->account_number }}</div>
                                <small class="text-muted">a.n. {{ $ew->account_name }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Panel Tunai --}}
                <div id="panel-tunai" class="payment-panel d-none">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Silakan bayar <strong>tunai</strong> langsung ke <strong>Bendahara RT</strong>.
                        Setelah membayar, minta struk dan unggah foto struk sebagai bukti di bawah.
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Upload Bukti --}}
        <div class="card mt-3" id="upload-section" style="display:none!important;">
            <div class="card-header">
                <i class="bi bi-upload me-2"></i>Upload Bukti Pembayaran
            </div>
            <div class="card-body">
                <form action="{{ route('warga.tagihan.upload-bukti', $pembayaran) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_method" id="input-payment-method">
                    <input type="hidden" name="payment_channel" id="input-payment-channel">

                    <div class="mb-3">
                        <label class="form-label fw-500">Bukti Pembayaran <span class="text-danger">*</span></label>
                        <input type="file" name="proof_of_payment" accept=".jpg,.jpeg,.png,.pdf"
                               class="form-control @error('proof_of_payment') is-invalid @enderror">
                        <div class="form-text">Format: JPG, PNG, atau PDF. Maks 2 MB.</div>
                        @error('proof_of_payment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Kirim Bukti
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const methodBtns = document.querySelectorAll('.method-btn');
const panels     = document.querySelectorAll('.payment-panel');
const channelCards = () => document.querySelectorAll('.channel-card');

let selectedMethod  = null;
let selectedChannel = null;

function updateUploadSection() {
    const showUpload = selectedMethod && (selectedChannel || selectedMethod === 'tunai' || selectedMethod === 'qris');
    document.getElementById('upload-section').style.setProperty('display', showUpload ? 'block' : 'none', 'important');
    document.getElementById('input-payment-method').value  = selectedMethod ?? '';
    document.getElementById('input-payment-channel').value = selectedChannel ?? selectedMethod ?? '';
}

methodBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        selectedMethod  = this.dataset.method;
        selectedChannel = null;

        // Reset btn styles
        methodBtns.forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
        this.classList.replace('btn-outline-primary', 'btn-primary');

        // Show correct panel
        panels.forEach(p => p.classList.add('d-none'));
        const panel = document.getElementById('panel-' + selectedMethod);
        if (panel) panel.classList.remove('d-none');

        // Auto-set channel for QRIS and Tunai
        if (selectedMethod === 'qris')  selectedChannel = 'QRIS';
        if (selectedMethod === 'tunai') selectedChannel = 'Tunai';

        updateUploadSection();
    });
});

document.addEventListener('click', function(e) {
    const card = e.target.closest('.channel-card');
    if (!card) return;

    selectedChannel = card.dataset.channel;

    // Highlight selected card
    channelCards().forEach(c => c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10'));
    card.classList.add('border-primary');

    updateUploadSection();
});

function printQRIS() {
    const img = document.getElementById('qris-img');
    if (!img) return;
    const w = window.open('', '_blank');
    w.document.write(`<html><body style="text-align:center;padding:40px;">
        <h3>QRIS Iuran Sampah</h3>
        <img src="${img.src}" style="max-width:300px;">
        <p style="margin-top:16px;font-size:14px;">Scan menggunakan aplikasi pembayaran QRIS manapun.</p>
        </body></html>`);
    w.document.close();
    w.print();
}
</script>

<style>
.channel-card.border-primary { border-color: #2563EB !important; background: #eff6ff; }
</style>
@endpush
@endsection
