@extends('layouts.app')

@section('title', 'Laporan Pembayaran')
@section('page-title', 'Laporan Pembayaran')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-cash-coin me-2 text-success"></i>Laporan Pembayaran</h4>
    <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

{{-- Filter --}}
<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('admin.laporan.pembayaran') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">Semua Bulan</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $idx => $bl)
                    <option value="{{ $idx+1 }}" {{ request('bulan') == $idx+1 ? 'selected' : '' }}>{{ $bl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" class="form-control form-control-sm"
                       value="{{ request('tahun', date('Y')) }}" min="2020" max="2099">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('admin.laporan.pembayaran') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
            <div class="col-auto ms-auto">
                <a href="{{ route('admin.laporan.export.pdf', request()->all()) }}"
                   class="btn btn-danger btn-sm" target="_blank">
                    <i class="bi bi-filetype-pdf me-1"></i>Export PDF
                </a>
                <a href="{{ route('admin.laporan.export.excel', request()->all()) }}"
                   class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Hasil Laporan</span>
        <span class="badge bg-secondary">{{ $pembayaran->count() }} data</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Warga</th>
                    <th>Tagihan</th>
                    <th>Tanggal Bayar</th>
                    <th>Jumlah Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayaran as $i => $p)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>{{ $p->warga->nama ?? '-' }}</td>
                    <td>{{ $p->tagihan->label ?? '-' }}</td>
                    <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
                    <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                    <td>{!! $p->status_badge !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
            @if($pembayaran->count())
            <tfoot>
                <tr class="table-light fw-600">
                    <td colspan="4" class="text-end">Total Terkumpul:</td>
                    <td>Rp {{ number_format($pembayaran->where('status','lunas')->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
