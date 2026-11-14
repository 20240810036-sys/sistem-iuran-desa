@extends('layouts.app')

@section('title', 'Edit Tagihan')
@section('page-title', 'Edit Tagihan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Tagihan</h4>
    <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:520px;">
    <div class="card-header">Formulir Edit Tagihan</div>
    <div class="card-body">
        <form action="{{ route('admin.tagihan.update', $tagihan) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Bulan <span class="text-danger">*</span></label>
                    <select name="bulan" class="form-select @error('bulan') is-invalid @enderror">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $bl)
                        <option value="{{ $idx+1 }}"
                            {{ old('bulan', $tagihan->bulan) == $idx+1 ? 'selected' : '' }}>
                            {{ $bl }}
                        </option>
                        @endforeach
                    </select>
                    @error('bulan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tahun <span class="text-danger">*</span></label>
                    <input type="number" name="tahun"
                           class="form-control @error('tahun') is-invalid @enderror"
                           value="{{ old('tahun', $tagihan->tahun) }}" min="2020" max="2099">
                    @error('tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Jumlah Iuran (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_iuran"
                           class="form-control @error('jumlah_iuran') is-invalid @enderror"
                           value="{{ old('jumlah_iuran', $tagihan->jumlah_iuran) }}" min="1000">
                    @error('jumlah_iuran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Perbarui
                </button>
                <a href="{{ route('admin.tagihan.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
