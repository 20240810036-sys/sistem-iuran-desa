<?php

namespace App\Http\Requests\Pembayaran;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePembayaranRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'warga_id'       => ['required', 'exists:warga,id'],
            'tagihan_id'     => ['required', 'exists:tagihan,id'],
            'tanggal_bayar'  => ['required', 'date'],
            'jumlah_bayar'   => ['required', 'integer', 'min:1000'],
            'status'         => ['required', 'in:lunas,belum_lunas,menunggu_verifikasi,ditolak'],
            'payment_method' => ['nullable', 'in:transfer_bank,qris,ewallet,tunai'],
            'payment_channel'=> ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'warga_id.required'      => 'Warga wajib dipilih.',
            'tagihan_id.required'    => 'Tagihan wajib dipilih.',
            'tanggal_bayar.required' => 'Tanggal bayar wajib diisi.',
            'jumlah_bayar.required'  => 'Jumlah bayar wajib diisi.',
            'status.required'        => 'Status wajib dipilih.',
        ];
    }
}
