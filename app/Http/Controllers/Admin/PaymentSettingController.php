<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentSetting\StorePaymentSettingRequest;
use App\Http\Requests\PaymentSetting\UpdatePaymentSettingRequest;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PaymentSettingController extends Controller
{
    /**
     * Simpan gambar QRIS secara aman tanpa ketergantungan php_fileinfo (shared hosting resilient).
     */
    private function storeQrImage(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION) ?: 'png');
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            abort(422, 'Format gambar QRIS harus JPG, JPEG, atau PNG.');
        }

        $imgInfo = @getimagesize($file->getRealPath());
        if (!$imgInfo || !in_array($imgInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
            abort(422, 'Isi berkas gambar QRIS tidak valid.');
        }

        $fileName   = time() . '_' . Str::random(12) . '.' . $ext;
        $publicDir  = public_path('storage/qris');
        $storageDir = storage_path('app/public/qris');

        if (!is_dir($publicDir)) {
            @mkdir($publicDir, 0777, true);
        }
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }

        $file->move($publicDir, $fileName);
        @copy($publicDir . '/' . $fileName, $storageDir . '/' . $fileName);

        return 'qris/' . $fileName;
    }

    public function index()
    {
        $banks    = PaymentSetting::byType('bank')->get();
        $ewallets = PaymentSetting::byType('ewallet')->get();
        $qris     = PaymentSetting::byType('qris')->first();

        return view('admin.payment_setting.index', compact('banks', 'ewallets', 'qris'));
    }

    public function create()
    {
        return view('admin.payment_setting.create');
    }

    public function store(StorePaymentSettingRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('qr_image')) {
            $data['qr_image'] = $this->storeQrImage($request->file('qr_image'));
        }

        PaymentSetting::create($data);

        return redirect()->route('admin.payment-setting.index')
            ->with('success', 'Pengaturan pembayaran berhasil ditambahkan.');
    }

    public function edit(PaymentSetting $paymentSetting)
    {
        return view('admin.payment_setting.edit', compact('paymentSetting'));
    }

    public function update(UpdatePaymentSettingRequest $request, PaymentSetting $paymentSetting)
    {
        $data = $request->validated();

        if ($request->hasFile('qr_image')) {
            // Hapus QRIS lama jika ada
            if ($paymentSetting->qr_image) {
                @unlink(storage_path('app/public/' . $paymentSetting->qr_image));
                @unlink(public_path('storage/' . $paymentSetting->qr_image));
            }
            $data['qr_image'] = $this->storeQrImage($request->file('qr_image'));
        }

        $paymentSetting->update($data);

        return redirect()->route('admin.payment-setting.index')
            ->with('success', 'Pengaturan pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentSetting $paymentSetting)
    {
        if ($paymentSetting->qr_image) {
            @unlink(storage_path('app/public/' . $paymentSetting->qr_image));
            @unlink(public_path('storage/' . $paymentSetting->qr_image));
        }

        $paymentSetting->delete();

        return redirect()->route('admin.payment-setting.index')
            ->with('success', 'Pengaturan pembayaran berhasil dihapus.');
    }

    /**
     * Toggle aktif/nonaktif
     */
    public function toggleActive(PaymentSetting $paymentSetting)
    {
        $paymentSetting->update(['is_active' => !$paymentSetting->is_active]);

        return back()->with('success', 'Status berhasil diperbarui.');
    }
}
