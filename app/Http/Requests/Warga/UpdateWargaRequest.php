<?php

namespace App\Http\Requests\Warga;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWargaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $warga = $this->route('warga');

        return [
            'nama'     => ['required', 'string', 'max:100'],
            'alamat'   => ['required', 'string', 'max:255'],
            'no_hp'    => ['required', 'string', 'max:15'],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($warga->user_id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'   => 'Nama wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'no_hp.required'  => 'Nomor HP wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.unique'    => 'Email sudah digunakan.',
            'password.min'    => 'Password minimal 6 karakter.',
        ];
    }
}
