<?php

namespace App\Http\Requests\Tagihan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagihanRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'bulan'        => ['required', 'integer', 'min:1', 'max:12'],
            'tahun'        => ['required', 'integer', 'min:2020', 'max:2099'],
            'jumlah_iuran' => ['required', 'integer', 'min:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'bulan.required'        => 'Bulan wajib dipilih.',
            'tahun.required'        => 'Tahun wajib diisi.',
            'jumlah_iuran.required' => 'Jumlah iuran wajib diisi.',
            'jumlah_iuran.min'      => 'Jumlah iuran minimal Rp 1.000.',
        ];
    }
}
