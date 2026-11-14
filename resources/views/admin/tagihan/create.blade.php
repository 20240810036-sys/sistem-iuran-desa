@extends('layouts.app')

@section('title', 'Tambah Tagihan')
@section('page-title', 'Tambah Tagihan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-receipt me-2 text-primary"></i>Tambah Tagihan</h4>
    <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:520px;">
    <div class="card-header">Formulir Tagihan</div>
    <div class="card-body">
        <form action="{{ route('admin.tagihan.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Bulan <span class="text-danger">*</span></label>
                    <select name="bulan" class="form-select @error('bulan') is-invalid @enderror">
                        <option value="">-- Pilih Bulan --</option>
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $bl)
                        <option value="{{ $idx+1 }}" {{ old('bulan') == $idx+1 ? 'selected' : '' }}>
                            {{ $bl }}
                        </option>
                        @endforeach
                    </select>
                    @error('bulan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                    <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror"
                           value="{{ old('tahun', date('Y')) }}" min="2020" max="2099">
                    @error('tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Jumlah Iuran (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_iuran"
                           class="form-control @error('jumlah_iuran') is-invalid @enderror"
                           value="{{ old('jumlah_iuran', 15000) }}" min="1000">
                    @error('jumlah_iuran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan
                </button>
                <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
