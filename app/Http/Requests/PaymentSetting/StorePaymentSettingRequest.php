<?php

namespace App\Http\Requests\PaymentSetting;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentSettingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type'           => ['required', 'in:bank,ewallet,qris'],
            'provider_name'  => ['required', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'account_name'   => ['nullable', 'string', 'max:100'],
            'qr_image'       => ['nullable', 'file', 'max:2048'],
            'is_active'      => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'          => 'Tipe wajib dipilih.',
            'provider_name.required' => 'Nama provider wajib diisi.',
            'qr_image.image'         => 'File QRIS harus berupa gambar.',
            'qr_image.max'           => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}
