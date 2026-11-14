<?php

namespace App\Http\Requests\Pembayaran;

use Illuminate\Foundation\Http\FormRequest;

class UploadBuktiRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'proof_of_payment' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'payment_method'   => ['required', 'in:transfer_bank,qris,ewallet,tunai'],
            'payment_channel'  => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_of_payment.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_of_payment.mimes'    => 'Format file: jpg, jpeg, png, atau pdf.',
            'proof_of_payment.max'      => 'Ukuran file maksimal 2 MB.',
            'payment_method.required'   => 'Metode pembayaran wajib dipilih.',
            'payment_channel.required'  => 'Channel pembayaran wajib dipilih.',
        ];
    }
}
