<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentSetting\StorePaymentSettingRequest;
use App\Http\Requests\PaymentSetting\UpdatePaymentSettingRequest;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentSettingController extends Controller
{
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
            $data['qr_image'] = $request->file('qr_image')->store('qris', 'public');
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
                Storage::disk('public')->delete($paymentSetting->qr_image);
            }
            $data['qr_image'] = $request->file('qr_image')->store('qris', 'public');
        }

        $paymentSetting->update($data);

        return redirect()->route('admin.payment-setting.index')
            ->with('success', 'Pengaturan pembayaran berhasil diperbarui.');
    }

    public function destroy(PaymentSetting $paymentSetting)
    {
        if ($paymentSetting->qr_image) {
            Storage::disk('public')->delete($paymentSetting->qr_image);
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
