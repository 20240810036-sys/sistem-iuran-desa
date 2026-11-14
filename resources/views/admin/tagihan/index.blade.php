@extends('layouts.app')

@section('title', 'Data Tagihan')
@section('page-title', 'Data Tagihan')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-receipt me-2 text-primary"></i>Data Tagihan</h4>
    <a href="{{ route('admin.tagihan.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah Tagihan
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Jumlah Iuran</th>
                        <th>Jml Pembayaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihan as $i => $t)
                    <tr>
                        <td class="text-muted">{{ $tagihan->firstItem() + $i }}</td>
                        <td>{{ $t->nama_bulan }}</td>
                        <td>{{ $t->tahun }}</td>
                        <td>Rp {{ number_format($t->jumlah_iuran, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $t->pembayaran_count }} data</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.tagihan.show', $t) }}"
                               class="btn btn-sm btn-outline-secondary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.tagihan.edit', $t) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.tagihan.destroy', $t) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Hapus tagihan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada data tagihan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tagihan->hasPages())
    <div class="card-footer bg-white">{{ $tagihan->links() }}</div>
    @endif
</div>
@endsection
