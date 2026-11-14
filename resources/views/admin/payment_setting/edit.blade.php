@extends('layouts.app')

@section('title', 'Edit Pengaturan Pembayaran')
@section('page-title', 'Edit Pengaturan Pembayaran')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-pencil me-2 text-primary"></i>Edit Pengaturan Pembayaran</h4>
    <a href="{{ route('admin.payment-setting.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:540px;">
    <div class="card-body">
        <form action="{{ route('admin.payment-setting.update', $paymentSetting) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-500">Tipe <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror"
                        onchange="toggleFields(this.value)">
                    <option value="bank"    {{ old('type', $paymentSetting->type) === 'bank'    ? 'selected' : '' }}>Bank</option>
                    <option value="ewallet" {{ old('type', $paymentSetting->type) === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="qris"    {{ old('type', $paymentSetting->type) === 'qris'    ? 'selected' : '' }}>QRIS</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-500">Nama Provider <span class="text-danger">*</span></label>
                <input type="text" name="provider_name" class="form-control @error('provider_name') is-invalid @enderror"
                       value="{{ old('provider_name', $paymentSetting->provider_name) }}">
                @error('provider_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div id="field-account">
                <div class="mb-3">
                    <label class="form-label fw-500">Nomor Rekening / No. HP</label>
                    <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror"
                           value="{{ old('account_number', $paymentSetting->account_number) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500">Atas Nama</label>
                    <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror"
                           value="{{ old('account_name', $paymentSetting->account_name) }}">
                </div>
            </div>

            <div id="field-qris" style="display:none;">
                @if($paymentSetting->qr_image)
                <div class="mb-2">
                    <label class="form-label fw-500">QRIS Saat Ini</label><br>
                    <img src="{{ $paymentSetting->qr_image_url }}" alt="QRIS"
                         style="width:140px;height:140px;object-fit:contain;border:1px solid #dee2e6;border-radius:8px;">
                </div>
                @endif
                <div class="mb-3">
                    <label class="form-label fw-500">Ganti Gambar QRIS</label>
                    <input type="file" name="qr_image" accept="image/*"
                           class="form-control @error('qr_image') is-invalid @enderror">
                    <div class="form-text">Kosongkan jika tidak ingin mengganti. Format: JPG, PNG. Maks 2 MB.</div>
                    @error('qr_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                           id="is_active" {{ old('is_active', $paymentSetting->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Perbarui
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleFields(type) {
    document.getElementById('field-account').style.display = type === 'qris' ? 'none' : '';
    document.getElementById('field-qris').style.display = type === 'qris' ? '' : 'none';
}
toggleFields('{{ old("type", $paymentSetting->type) }}');
</script>
@endpush
@endsection
