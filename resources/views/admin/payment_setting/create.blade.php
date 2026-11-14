@extends('layouts.app')

@section('title', 'Tambah Pengaturan Pembayaran')
@section('page-title', 'Tambah Pengaturan Pembayaran')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Pengaturan Pembayaran</h4>
    <a href="{{ route('admin.payment-setting.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:540px;">
    <div class="card-body">
        <form action="{{ route('admin.payment-setting.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-500">Tipe <span class="text-danger">*</span></label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror"
                        onchange="toggleFields(this.value)">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="bank"    {{ old('type') === 'bank'    ? 'selected' : '' }}>Bank</option>
                    <option value="ewallet" {{ old('type') === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                    <option value="qris"    {{ old('type') === 'qris'    ? 'selected' : '' }}>QRIS</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-500">Nama Provider <span class="text-danger">*</span></label>
                <input type="text" name="provider_name" class="form-control @error('provider_name') is-invalid @enderror"
                       value="{{ old('provider_name') }}"
                       placeholder="Contoh: BCA / DANA / QRIS">
                @error('provider_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div id="field-account">
                <div class="mb-3">
                    <label class="form-label fw-500">Nomor Rekening / No. HP</label>
                    <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror"
                           value="{{ old('account_number') }}">
                    @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-500">Atas Nama</label>
                    <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror"
                           value="{{ old('account_name') }}">
                    @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div id="field-qris" style="display:none;">
                <div class="mb-3">
                    <label class="form-label fw-500">Gambar QRIS</label>
                    <input type="file" name="qr_image" accept="image/*"
                           class="form-control @error('qr_image') is-invalid @enderror">
                    <div class="form-text">Format: JPG, PNG. Maks 2 MB.</div>
                    @error('qr_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                           id="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Simpan
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
// Init on page load
toggleFields('{{ old("type", "") }}');
</script>
@endpush
@endsection
