<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Warga;

class DashboardController extends Controller
{
    public function index()
    {
        $totalWarga     = Warga::count();
        $totalTagihan   = Tagihan::count();
        $totalPembayaran = Pembayaran::where('status', 'lunas')->count();
        $totalBelumBayar = Pembayaran::where('status', 'belum_lunas')->count();

        // Statistik baru
        $totalMenunggu = Pembayaran::where('status', Pembayaran::STATUS_MENUNGGU_VERIFIKASI)->count();
        $totalLunas    = Pembayaran::where('status', Pembayaran::STATUS_LUNAS)->count();
        $totalDitolak  = Pembayaran::where('status', Pembayaran::STATUS_DITOLAK)->count();

        $pembayaranTerbaru = Pembayaran::with(['warga', 'tagihan'])
            ->whereIn('status', [
                Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
                Pembayaran::STATUS_LUNAS,
                Pembayaran::STATUS_DITOLAK,
            ])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalWarga',
            'totalTagihan',
            'totalPembayaran',
            'totalBelumBayar',
            'totalMenunggu',
            'totalLunas',
            'totalDitolak',
            'pembayaranTerbaru'
        ));
    }
}
