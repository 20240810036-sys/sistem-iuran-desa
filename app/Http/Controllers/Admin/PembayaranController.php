<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pembayaran\StorePembayaranRequest;
use App\Http\Requests\Pembayaran\UpdatePembayaranRequest;
use App\Http\Requests\Pembayaran\VerifikasiPembayaranRequest;
use App\Models\ActivityLog;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Warga;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran::with(['warga', 'tagihan'])
            ->latest()
            ->paginate(10);

        return view('admin.pembayaran.index', compact('pembayaran'));
    }

    public function create()
    {
        $warga   = Warga::orderBy('nama')->get();
        $tagihan = Tagihan::orderByDesc('tahun')->orderByDesc('bulan')->get();

        return view('admin.pembayaran.create', compact('warga', 'tagihan'));
    }

    public function store(StorePembayaranRequest $request)
    {
        Pembayaran::create($request->validated());

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['warga', 'tagihan']);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function edit(Pembayaran $pembayaran)
    {
        $warga   = Warga::orderBy('nama')->get();
        $tagihan = Tagihan::orderByDesc('tahun')->orderByDesc('bulan')->get();

        return view('admin.pembayaran.edit', compact('pembayaran', 'warga', 'tagihan'));
    }

    public function update(UpdatePembayaranRequest $request, Pembayaran $pembayaran)
    {
        $pembayaran->update($request->validated());

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    /**
     * Halaman antrian verifikasi
     */
    public function verifikasi()
    {
        $pembayaran = Pembayaran::with(['warga', 'tagihan'])
            ->where('status', Pembayaran::STATUS_MENUNGGU_VERIFIKASI)
            ->latest()
            ->paginate(15);

        return view('admin.pembayaran.verifikasi', compact('pembayaran'));
    }

    /**
     * Setujui pembayaran
     */
    public function setujui(Pembayaran $pembayaran)
    {
        $pembayaran->update([
            'status'            => Pembayaran::STATUS_LUNAS,
            'verification_note' => null,
        ]);

        ActivityLog::record('pembayaran_setujui', "Admin menyetujui pembayaran #{$pembayaran->id} milik {$pembayaran->warga->nama}");

        return back()->with('success', 'Pembayaran warga ' . $pembayaran->warga->nama . ' telah disetujui.');
    }

    /**
     * Tolak pembayaran
     */
    public function tolak(VerifikasiPembayaranRequest $request, Pembayaran $pembayaran)
    {
        $pembayaran->update([
            'status'            => Pembayaran::STATUS_DITOLAK,
            'verification_note' => $request->verification_note,
        ]);

        ActivityLog::record('pembayaran_tolak', "Admin menolak pembayaran #{$pembayaran->id} milik {$pembayaran->warga->nama}");

        return back()->with('success', 'Pembayaran warga ' . $pembayaran->warga->nama . ' telah ditolak.');
    }
}
