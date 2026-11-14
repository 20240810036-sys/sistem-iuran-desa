@extends('layouts.app')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-person-gear me-2 text-primary"></i>Kelola User</h4>
    <a href="{{ route('admin.user.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Tambah User
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
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $u)
                    <tr>
                        <td class="text-muted">{{ $users->firstItem() + $i }}</td>
                        <td>
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                                <span class="badge bg-primary ms-1">Saya</span>
                            @endif
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="badge bg-primary">Admin</span>
                            @else
                                <span class="badge bg-secondary">Warga</span>
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $u->created_at->format('d M Y') }}</small></td>
                        <td class="text-center">
                            <a href="{{ route('admin.user.edit', $u) }}"
                               class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('admin.user.destroy', $u) }}" method="POST"
                                  class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white">{{ $users->links() }}</div>
    @endif
</div>
@endsection
