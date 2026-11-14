<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Auth::user();
        $warga = $user->warga;

        if (! $warga) {
            return view('warga.dashboard.index', [
                'warga'              => null,
                'totalTagihanAktif'  => 0,
                'totalLunas'         => 0,
                'totalBelumLunas'    => 0,
                'riwayatPembayaran'  => collect(),
            ]);
        }

        $totalTagihanAktif = Pembayaran::where('warga_id', $warga->id)->count();
        $totalLunas        = Pembayaran::where('warga_id', $warga->id)->where('status', 'lunas')->count();
        $totalBelumLunas   = Pembayaran::where('warga_id', $warga->id)->where('status', 'belum_lunas')->count();

        $riwayatPembayaran = Pembayaran::with('tagihan')
            ->where('warga_id', $warga->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('warga.dashboard.index', compact(
            'warga',
            'totalTagihanAktif',
            'totalLunas',
            'totalBelumLunas',
            'riwayatPembayaran'
        ));
    }
}
