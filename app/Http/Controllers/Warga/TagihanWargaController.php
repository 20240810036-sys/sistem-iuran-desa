<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pembayaran;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        abort_if(!$warga || $pembayaran->warga_id !== $warga->id, 403, 'Akses ditolak.');

        $pembayaran->load('tagihan');

        $banks    = PaymentSetting::byType('bank')->active()->get();
        $ewallets = PaymentSetting::byType('ewallet')->active()->get();
        $qris     = PaymentSetting::byType('qris')->active()->first();

        return view('warga.tagihan.show', compact('pembayaran', 'banks', 'ewallets', 'qris'));
    }

    /**
     * Upload bukti pembayaran (Crash-proof & Shared Hosting Resilient)
     */
    public function uploadBukti(Request $request, Pembayaran $pembayaran)
    {
        try {
            $warga = Auth::user()->warga;
            if (!$warga || $pembayaran->warga_id !== $warga->id) {
                return back()->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk tagihan ini.');
            }

            // Hanya bisa upload jika belum lunas
            if ($pembayaran->status === Pembayaran::STATUS_LUNAS) {
                return back()->with('error', 'Tagihan pembayaran ini sudah lunas.');
            }

            // Validasi Input manual yang aman dari dependensi PHP fileinfo
            $request->validate([
                'payment_method'   => ['required', 'in:transfer_bank,qris,ewallet,tunai'],
                'payment_channel'  => ['nullable', 'string', 'max:100'],
                'proof_of_payment' => ['required', 'file', 'max:2048'],
            ], [
                'payment_method.required'   => 'Metode pembayaran wajib dipilih.',
                'proof_of_payment.required' => 'Bukti pembayaran wajib diunggah.',
                'proof_of_payment.max'      => 'Ukuran berkas maksimal 2 MB.',
            ]);

            $file = $request->file('proof_of_payment');
            if (!$file || !$file->isValid()) {
                return back()->with('error', 'Berkas bukti bayar tidak valid atau gagal diproses oleh server.')->withInput();
            }

            // Validasi ekstensi & magic bytes secara langsung (tanpa ketergantungan php_fileinfo)
            $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION) ?: 'png');
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            if (!in_array($ext, $allowedExtensions)) {
                return back()->with('error', 'Format berkas harus berupa JPG, JPEG, PNG, atau PDF.')->withInput();
            }

            $realPath = $file->getRealPath();
            if ($ext === 'pdf') {
                $header = @file_get_contents($realPath, false, null, 0, 5);
                if ($header !== '%PDF-') {
                    return back()->with('error', 'Isi berkas PDF tidak valid atau rusak.')->withInput();
                }
            } else {
                $imgInfo = @getimagesize($realPath);
                if (!$imgInfo || !in_array($imgInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                    return back()->with('error', 'Isi berkas gambar tidak valid (harus JPG atau PNG asli).')->withInput();
                }
            }

            $fileName = time() . '_' . Str::random(12) . '.' . $ext;

            // Siapkan direktori public/storage/proofs
            $publicDir = public_path('storage/proofs');
            if (!is_dir($publicDir)) {
                @mkdir($publicDir, 0777, true);
            }

            $storageDir = storage_path('app/public/proofs');
            if (!is_dir($storageDir)) {
                @mkdir($storageDir, 0777, true);
            }

            // Hapus berkas bukti lama jika ada
            if ($pembayaran->proof_of_payment) {
                @unlink(public_path('storage/' . $pembayaran->proof_of_payment));
                @unlink(storage_path('app/public/' . $pembayaran->proof_of_payment));
            }

            // Simpan berkas fisik ke public/storage/proofs terlebih dahulu
            try {
                $file->move($publicDir, $fileName);
                @copy($publicDir . '/' . $fileName, $storageDir . '/' . $fileName);
            } catch (\Throwable $moveErr) {
                // Jika public_path gagal, coba ke storage_path
                try {
                    $file->move($storageDir, $fileName);
                    @copy($storageDir . '/' . $fileName, $publicDir . '/' . $fileName);
                } catch (\Throwable $moveErr2) {
                    return back()->with('error', 'Gagal menyimpan berkas ke server: ' . $moveErr2->getMessage())->withInput();
                }
            }

            $path = 'proofs/' . $fileName;

            // Siapkan data pembaruan database
            $channel = $request->payment_channel ?: ($request->payment_method === 'qris' ? 'QRIS' : ($request->payment_method === 'tunai' ? 'Tunai' : '-'));

            $updateData = [
                'status'           => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
                'payment_method'   => $request->payment_method,
                'payment_channel'  => $channel,
                'proof_of_payment' => $path,
            ];

            // Eksekusi update database secara aman
            try {
                $pembayaran->update($updateData);
            } catch (\Throwable $dbErr) {
                // Coba query langsung jika ada kendala model Eloquent
                try {
                    DB::table('pembayaran')
                        ->where('id', $pembayaran->id)
                        ->update($updateData);
                } catch (\Throwable $dbErr2) {
                    return back()->with('error', 'Gagal menyimpan ke database (Kemungkinan kolom tabel belum lengkap): ' . $dbErr2->getMessage())->withInput();
                }
            }

            ActivityLog::record('upload_bukti', "Warga {$warga->nama} mengunggah bukti pembayaran #{$pembayaran->id} ({$request->payment_method})");

            return back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi dari admin desa.');

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return back()->withErrors($ve->validator)->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan pada sistem: ' . $e->getMessage())->withInput();
        }
    }
}
