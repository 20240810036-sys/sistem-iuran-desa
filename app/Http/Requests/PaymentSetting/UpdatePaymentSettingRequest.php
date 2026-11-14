<?php

namespace App\Http\Requests\PaymentSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentSettingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type'           => ['required', 'in:bank,ewallet,qris'],
            'provider_name'  => ['required', 'string', 'max:100'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'account_name'   => ['nullable', 'string', 'max:100'],
            'qr_image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'is_active'      => ['boolean'],
        ];
    }
}
