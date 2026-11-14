@extends('layouts.app')

@section('title', 'Laporan Tagihan')
@section('page-title', 'Laporan Tagihan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-receipt me-2 text-primary"></i>Laporan Tagihan</h4>
    <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('admin.laporan.tagihan') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" class="form-control form-control-sm"
                       value="{{ request('tahun', date('Y')) }}" min="2020" max="2099">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('admin.laporan.tagihan') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tagihan</th>
                    <th>Jumlah Iuran</th>
                    <th>Lunas</th>
                    <th>Belum Lunas</th>
                    <th>Total Terkumpul</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tagihan as $i => $t)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>{{ $t->label }}</td>
                    <td>Rp {{ number_format($t->jumlah_iuran, 0, ',', '.') }}</td>
                    <td><span class="badge bg-success">{{ $t->lunas_count }}</span></td>
                    <td><span class="badge bg-danger">{{ $t->belum_lunas_count }}</span></td>
                    <td class="fw-500">
                        Rp {{ number_format($t->lunas_count * $t->jumlah_iuran, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
