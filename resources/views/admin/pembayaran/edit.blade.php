@extends('layouts.app')

@section('title', 'Edit Pembayaran')
@section('page-title', 'Edit Pembayaran')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Pembayaran</h4>
    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header">Formulir Edit Pembayaran</div>
    <div class="card-body">
        <form action="{{ route('admin.pembayaran.update', $pembayaran) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Warga <span class="text-danger">*</span></label>
                    <select name="warga_id" class="form-select @error('warga_id') is-invalid @enderror">
                        <option value="">-- Pilih Warga --</option>
                        @foreach($warga as $w)
                        <option value="{{ $w->id }}"
                            {{ old('warga_id', $pembayaran->warga_id) == $w->id ? 'selected' : '' }}>
                            {{ $w->nama }} – {{ $w->alamat }}
                        </option>
                        @endforeach
                    </select>
                    @error('warga_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Tagihan <span class="text-danger">*</span></label>
                    <select name="tagihan_id" class="form-select @error('tagihan_id') is-invalid @enderror">
                        <option value="">-- Pilih Tagihan --</option>
                        @foreach($tagihan as $t)
                        <option value="{{ $t->id }}"
                            {{ old('tagihan_id', $pembayaran->tagihan_id) == $t->id ? 'selected' : '' }}>
                            {{ $t->label }} – Rp {{ number_format($t->jumlah_iuran, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    @error('tagihan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tanggal Bayar <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_bayar"
                           class="form-control @error('tanggal_bayar') is-invalid @enderror"
                           value="{{ old('tanggal_bayar', $pembayaran->tanggal_bayar->format('Y-m-d')) }}">
                    @error('tanggal_bayar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Jumlah Bayar (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_bayar"
                           class="form-control @error('jumlah_bayar') is-invalid @enderror"
                           value="{{ old('jumlah_bayar', $pembayaran->jumlah_bayar) }}" min="1000">
                    @error('jumlah_bayar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="lunas"
                            {{ old('status', $pembayaran->status) == 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="belum_lunas"
                            {{ old('status', $pembayaran->status) == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Perbarui
                </button>
                <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
