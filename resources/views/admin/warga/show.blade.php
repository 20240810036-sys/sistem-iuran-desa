@extends('layouts.app')

@section('title', 'Detail Warga')
@section('page-title', 'Detail Warga')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-person-lines-fill me-2 text-primary"></i>Detail Warga</h4>
    <a href="{{ route('admin.warga.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">Informasi Warga</div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.875rem;">
                    <dt class="col-5 text-muted">Nama</dt>
                    <dd class="col-7 fw-500">{{ $warga->nama }}</dd>

                    <dt class="col-5 text-muted">Alamat</dt>
                    <dd class="col-7">{{ $warga->alamat }}</dd>

                    <dt class="col-5 text-muted">No. HP</dt>
                    <dd class="col-7">{{ $warga->no_hp }}</dd>

                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7">{{ $warga->user->email ?? '-' }}</dd>

                    <dt class="col-5 text-muted">Terdaftar</dt>
                    <dd class="col-7">{{ $warga->created_at->format('d M Y') }}</dd>
                </dl>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a href="{{ route('admin.warga.edit', $warga) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <form action="{{ route('admin.warga.destroy', $warga) }}" method="POST"
                      onsubmit="return confirm('Hapus warga ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Riwayat Pembayaran</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tagihan</th>
                            <th>Tgl Bayar</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warga->pembayaran as $i => $p)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>{{ $p->tagihan->label ?? '-' }}</td>
                            <td>{{ $p->tanggal_bayar->format('d M Y') }}</td>
                            <td>Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                            <td>{!! $p->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Belum ada riwayat pembayaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
