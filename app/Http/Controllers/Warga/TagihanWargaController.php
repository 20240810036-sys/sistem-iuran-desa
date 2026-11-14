<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pembayaran\UploadBuktiRequest;
use App\Models\Pembayaran;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TagihanWargaController extends Controller
{
    public function index()
    {
        $warga = Auth::user()->warga;

        $tagihan = $warga
            ? Pembayaran::with('tagihan')
                ->where('warga_id', $warga->id)
                ->latest()
                ->paginate(10)
            : collect();

        return view('warga.tagihan.index', compact('tagihan', 'warga'));
    }

    /**
     * Detail tagihan + pilihan metode pembayaran
     */
    public function show(Pembayaran $pembayaran)
    {
        // Pastikan warga hanya bisa lihat miliknya sendiri
        $warga = Auth::user()->warga;
        abort_if(!$warga || $pembayaran->warga_id !== $warga->id, 403);

        $pembayaran->load('tagihan');

        $banks    = PaymentSetting::byType('bank')->active()->get();
        $ewallets = PaymentSetting::byType('ewallet')->active()->get();
        $qris     = PaymentSetting::byType('qris')->active()->first();

        return view('warga.tagihan.show', compact('pembayaran', 'banks', 'ewallets', 'qris'));
    }

    /**
     * Upload bukti pembayaran
     */
    public function uploadBukti(UploadBuktiRequest $request, Pembayaran $pembayaran)
    {
        $warga = Auth::user()->warga;
        abort_if(!$warga || $pembayaran->warga_id !== $warga->id, 403);

        // Hanya bisa upload jika belum lunas / ditolak
        abort_if($pembayaran->status === Pembayaran::STATUS_LUNAS, 422, 'Pembayaran sudah lunas.');

        // Hapus bukti lama jika ada
        if ($pembayaran->proof_of_payment) {
            Storage::disk('public')->delete($pembayaran->proof_of_payment);
        }

        $path = $request->file('proof_of_payment')->store('proofs', 'public');

        $pembayaran->update([
            'proof_of_payment' => $path,
            'payment_method'   => $request->payment_method,
            'payment_channel'  => $request->payment_channel,
            'status'           => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }
}
