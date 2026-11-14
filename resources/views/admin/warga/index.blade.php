@extends('layouts.app')

@section('title', 'Data Warga')
@section('page-title', 'Data Warga')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-people me-2 text-primary"></i>Data Warga</h4>
    <a href="{{ route('admin.warga.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah Warga
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No. HP</th>
                        <th>Email</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warga as $i => $w)
                    <tr>
                        <td class="text-muted">{{ $warga->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-500">{{ $w->nama }}</div>
                        </td>
                        <td><small>{{ $w->alamat }}</small></td>
                        <td>{{ $w->no_hp }}</td>
                        <td><small class="text-muted">{{ $w->user->email ?? '-' }}</small></td>
                        <td class="text-center">
                            <a href="{{ route('admin.warga.show', $w) }}"
                               class="btn btn-sm btn-outline-secondary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.warga.edit', $w) }}"
                               class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.warga.destroy', $w) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Hapus warga ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada data warga.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($warga->hasPages())
    <div class="card-footer bg-white">
        {{ $warga->links() }}
    </div>
    @endif
</div>
@endsection
