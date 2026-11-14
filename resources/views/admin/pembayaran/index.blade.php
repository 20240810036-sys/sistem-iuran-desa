@extends('layouts.app')

@section('title', 'Data Pembayaran')
@section('page-title', 'Data Pembayaran')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-cash-coin me-2 text-primary"></i>Data Pembayaran</h4>
    <a href="{{ route('admin.pembayaran.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Catat Pembayaran
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
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
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaran as $i => $p)
                    <tr>
                        <td class="text-muted">{{ $pembayaran->firstItem() + $i }}</td>
                        <td>{{ $p->warga->nama ?? '-' }}</td>
                        <td>{{ $p->tagihan->label ?? '-' }}</td>
                        <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
                        <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>{!! $p->status_badge !!}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.pembayaran.show', $p) }}"
                               class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.pembayaran.edit', $p) }}"
                               class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.pembayaran.destroy', $p) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada data pembayaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pembayaran->hasPages())
    <div class="card-footer bg-white">{{ $pembayaran->links() }}</div>
    @endif
</div>
@endsection
