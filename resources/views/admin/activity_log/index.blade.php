@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-shield-check me-2 text-primary"></i>Log Aktivitas</h4>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="action" class="form-select form-select-sm">
                    <option value="">Semua aksi</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}" @selected(request('action') === $a)>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <input type="text" name="q" value="{{ request('q') }}" maxlength="100"
                       class="form-control form-control-sm" placeholder="Cari deskripsi...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $color = str_contains($log->action, 'failed') || str_contains($log->action, 'blocked') || str_contains($log->action, 'delete')
                            ? 'danger' : (str_starts_with($log->action, 'login') ? 'success' : 'secondary');
                    @endphp
                    <tr>
                        <td><small class="text-muted">{{ $log->created_at?->format('d M Y H:i:s') }}</small></td>
                        <td>{{ $log->user->name ?? '-' }}</td>
                        <td><span class="badge bg-{{ $color }}">{{ $log->action }}</span></td>
                        <td>{{ $log->description }}</td>
                        <td><small class="text-muted">{{ $log->ip_address }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada log aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
