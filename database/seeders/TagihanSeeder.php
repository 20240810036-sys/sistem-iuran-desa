<?php

namespace Database\Seeders;

use App\Models\Tagihan;
use Illuminate\Database\Seeder;

class TagihanSeeder extends Seeder
{
    public function run(): void
    {
        // Generate tagihan 6 bulan terakhir
        $tahun = 2025;
        for ($bulan = 1; $bulan <= 6; $bulan++) {
            Tagihan::create([
                'bulan'        => $bulan,
                'tahun'        => $tahun,
                'jumlah_iuran' => 15000,
            ]);
        }
    }
}
