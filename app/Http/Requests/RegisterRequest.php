<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100', 'regex:/^[\pL\s\.\'-]+$/u'],
            'email'    => ['required', 'email:rfc', 'max:150', 'unique:users,email'],
            'alamat'   => ['required', 'string', 'max:255'],
            'no_hp'    => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,11}$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama wajib diisi.',
            'name.regex'         => 'Nama hanya boleh berisi huruf, spasi, titik, dan tanda petik.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'alamat.required'    => 'Alamat wajib diisi.',
            'no_hp.required'     => 'No. HP wajib diisi.',
            'no_hp.regex'        => 'Format No. HP tidak valid (contoh: 081234567890).',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}
