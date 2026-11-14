@extends('layouts.app')

@section('title', 'Laporan Warga')
@section('page-title', 'Laporan Warga')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-people me-2 text-warning"></i>Laporan Warga</h4>
    <a href="{{ route('admin.laporan.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Warga</th>
                    <th>Alamat</th>
                    <th>No. HP</th>
                    <th>Sudah Lunas</th>
                    <th>Belum Lunas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warga as $i => $w)
                <tr>
                    <td class="text-muted">{{ $i + 1 }}</td>
                    <td>{{ $w->nama }}</td>
                    <td><small>{{ $w->alamat }}</small></td>
                    <td>{{ $w->no_hp }}</td>
                    <td><span class="badge bg-success">{{ $w->lunas_count }}</span></td>
                    <td><span class="badge bg-danger">{{ $w->belum_lunas_count }}</span></td>
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
