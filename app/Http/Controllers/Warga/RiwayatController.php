<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $warga = Auth::user()->warga;

        $riwayat = $warga
            ? Pembayaran::with('tagihan')
                ->where('warga_id', $warga->id)
                ->where('status', 'lunas')
                ->latest('tanggal_bayar')
                ->paginate(10)
            : collect();

        return view('warga.pembayaran.riwayat', compact('riwayat', 'warga'));
    }
}
