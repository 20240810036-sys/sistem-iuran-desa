@extends('layouts.app')

@section('title', 'Pengaturan Pembayaran')
@section('page-title', 'Pengaturan Pembayaran')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-gear me-2 text-primary"></i>Pengaturan Pembayaran</h4>
    <a href="{{ route('admin.payment-setting.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- QRIS --}}
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-qr-code me-2"></i>QRIS Statis</span>
    </div>
    <div class="card-body">
        @if($qris)
        <div class="d-flex align-items-start gap-4 flex-wrap">
            @if($qris->qr_image)
            <div>
                <img src="{{ $qris->qr_image_url }}" alt="QRIS" style="width:160px;height:160px;object-fit:contain;border:1px solid #dee2e6;border-radius:8px;">
            </div>
            @else
            <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width:160px;height:160px;">
                <span class="text-muted text-center small">Belum ada<br>gambar QRIS</span>
            </div>
            @endif
            <div>
                <p class="mb-1"><strong>Status:</strong>
                    @if($qris->is_active)
                    <span class="badge bg-success">Aktif</span>
                    @else
                    <span class="badge bg-secondary">Nonaktif</span>
                    @endif
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('admin.payment-setting.edit', $qris) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit / Ganti QRIS
                    </a>
                    <form action="{{ route('admin.payment-setting.destroy', $qris) }}" method="POST"
                          onsubmit="return confirm('Hapus QRIS ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @else
        <p class="text-muted mb-2">Belum ada QRIS. Tambahkan melalui tombol di atas dengan memilih tipe <strong>QRIS</strong>.</p>
        @endif
    </div>
</div>

{{-- Bank --}}
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-bank me-2"></i>Rekening Bank</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Bank</th>
                    <th>No. Rekening</th>
                    <th>Atas Nama</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banks as $b)
                <tr>
                    <td class="fw-500">{{ $b->provider_name }}</td>
                    <td>{{ $b->account_number }}</td>
                    <td>{{ $b->account_name }}</td>
                    <td>
                        <form action="{{ route('admin.payment-setting.toggle', $b) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-xs {{ $b->is_active ? 'btn-success' : 'btn-secondary' }} py-0 px-2">
                                {{ $b->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.payment-setting.edit', $b) }}" class="btn btn-xs btn-outline-primary py-0 px-2">Edit</a>
                        <form action="{{ route('admin.payment-setting.destroy', $b) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger py-0 px-2">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada rekening bank.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- E-Wallet --}}
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-wallet2 me-2"></i>E-Wallet</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>E-Wallet</th>
                    <th>No. Tujuan</th>
                    <th>Atas Nama</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ewallets as $e)
                <tr>
                    <td class="fw-500">{{ $e->provider_name }}</td>
                    <td>{{ $e->account_number }}</td>
                    <td>{{ $e->account_name }}</td>
                    <td>
                        <form action="{{ route('admin.payment-setting.toggle', $e) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-xs {{ $e->is_active ? 'btn-success' : 'btn-secondary' }} py-0 px-2">
                                {{ $e->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.payment-setting.edit', $e) }}" class="btn btn-xs btn-outline-primary py-0 px-2">Edit</a>
                        <form action="{{ route('admin.payment-setting.destroy', $e) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger py-0 px-2">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada e-wallet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
