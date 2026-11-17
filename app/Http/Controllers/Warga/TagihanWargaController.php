<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
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
     * Upload bukti pembayaran (Self-healing & Shared Hosting Resilient)
     */
    public function uploadBukti(Request $request, Pembayaran $pembayaran)
    {
        try {
            $warga = Auth::user()->warga;
            abort_if(!$warga || $pembayaran->warga_id !== $warga->id, 403, 'Akses ditolak.');

            // Hanya bisa upload jika belum lunas / ditolak
            if ($pembayaran->status === Pembayaran::STATUS_LUNAS) {
                return back()->with('error', 'Tagihan pembayaran ini sudah lunas.');
            }

            // Validasi Input
            $request->validate([
                'payment_method'   => ['required', 'in:transfer_bank,qris,ewallet,tunai'],
                'payment_channel'  => ['nullable', 'string', 'max:100'],
                'proof_of_payment' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
            ], [
                'payment_method.required'   => 'Metode pembayaran wajib dipilih.',
                'proof_of_payment.required' => 'Bukti pembayaran wajib diunggah.',
                'proof_of_payment.mimes'    => 'Format file harus berupa JPG, PNG, atau PDF.',
                'proof_of_payment.max'      => 'Ukuran file maksimal 2 MB.',
            ]);

            $file = $request->file('proof_of_payment');
            if (!$file || !$file->isValid()) {
                return back()->with('error', 'Berkas bukti bayar tidak valid atau gagal diproses oleh server.');
            }

            // 1. Simpan berkas fisik menggunakan metode direct move (kompatibel penuh dengan shared hosting)
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $fileName = time() . '_' . Str::random(12) . '.' . strtolower($ext);

            $storageDir = storage_path('app/public/proofs');
            if (!is_dir($storageDir)) {
                @mkdir($storageDir, 0777, true);
            }

            $publicDir = public_path('storage/proofs');
            if (!is_dir($publicDir)) {
                @mkdir($publicDir, 0777, true);
            }

            // Hapus berkas lama jika ada
            if ($pembayaran->proof_of_payment) {
                @unlink(storage_path('app/public/' . $pembayaran->proof_of_payment));
                @unlink(public_path('storage/' . $pembayaran->proof_of_payment));
            }

            $file->move($storageDir, $fileName);
            @copy($storageDir . '/' . $fileName, $publicDir . '/' . $fileName);

            $path = 'proofs/' . $fileName;

            // 2. Siapkan data update database
            $channel = $request->payment_channel ?: ($request->payment_method === 'qris' ? 'QRIS' : ($request->payment_method === 'tunai' ? 'Tunai' : '-'));

            $updateData = [
                'status'           => Pembayaran::STATUS_MENUNGGU_VERIFIKASI,
                'payment_method'   => $request->payment_method,
                'payment_channel'  => $channel,
                'proof_of_payment' => $path,
            ];

            // 3. Eksekusi update database dengan mekanisme self-repair otomatis
            try {
                $pembayaran->update($updateData);
            } catch (\Illuminate\Database\QueryException $qe) {
                // Perbaiki struktur tabel otomatis jika kolom atau enum di database hosting belum lengkap
                $this->autoRepairPembayaranTable();
                
                // Coba update ulang setelah perbaikan skema
                $pembayaran->update($updateData);
            }

            return back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi dari admin desa.');

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return back()->withErrors($ve->validator)->withInput();
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kendala saat memproses: ' . $e->getMessage());
        }
    }

    /**
     * Memperbaiki skema database tabel pembayaran secara otomatis jika belum lengkap
     */
    private function autoRepairPembayaranTable(): void
    {
        try {
            DB::statement("ALTER TABLE `pembayaran` MODIFY COLUMN `status` ENUM('menunggu_verifikasi','lunas','ditolak','belum_lunas') NOT NULL DEFAULT 'menunggu_verifikasi'");
        } catch (\Throwable $t) {}

        if (!Schema::hasColumn('pembayaran', 'payment_method')) {
            try {
                DB::statement("ALTER TABLE `pembayaran` ADD COLUMN `payment_method` ENUM('transfer_bank','qris','ewallet','tunai') NULL AFTER `status`");
            } catch (\Throwable $t) {}
        }

        if (!Schema::hasColumn('pembayaran', 'payment_channel')) {
            try {
                DB::statement("ALTER TABLE `pembayaran` ADD COLUMN `payment_channel` VARCHAR(100) NULL AFTER `payment_method`");
            } catch (\Throwable $t) {}
        }

        if (!Schema::hasColumn('pembayaran', 'proof_of_payment')) {
            try {
                DB::statement("ALTER TABLE `pembayaran` ADD COLUMN `proof_of_payment` VARCHAR(255) NULL AFTER `payment_channel`");
            } catch (\Throwable $t) {}
        }

        if (!Schema::hasColumn('pembayaran', 'verification_note')) {
            try {
                DB::statement("ALTER TABLE `pembayaran` ADD COLUMN `verification_note` TEXT NULL AFTER `proof_of_payment`");
            } catch (\Throwable $t) {}
        }
    }
}
