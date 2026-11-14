<?php

namespace Database\Seeders;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Warga;
use Illuminate\Database\Seeder;

class PembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $wargaList = Warga::all();
        $tagihanList = Tagihan::all();

        foreach ($wargaList as $warga) {
            foreach ($tagihanList as $tagihan) {
                // Simulasi: 70% lunas, 30% belum lunas
                $status = rand(1, 10) <= 7 ? 'lunas' : 'belum_lunas';

                Pembayaran::create([
                    'warga_id'     => $warga->id,
                    'tagihan_id'   => $tagihan->id,
                    'tanggal_bayar'=> now()->subMonths(rand(0, 5))->format('Y-m-d'),
                    'jumlah_bayar' => $tagihan->jumlah_iuran,
                    'status'       => $status,
                ]);
            }
        }
    }
}
