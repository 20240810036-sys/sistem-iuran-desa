<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Seeder;

class WargaSeeder extends Seeder
{
    public function run(): void
    {
        $wargaData = [
            [
                'email'  => 'budi@warga.id',
                'nama'   => 'Budi Santoso',
                'alamat' => 'Jl. Melati No. 12, RT 01/RW 02',
                'no_hp'  => '081234567801',
            ],
            [
                'email'  => 'siti@warga.id',
                'nama'   => 'Siti Rahayu',
                'alamat' => 'Jl. Mawar No. 5, RT 02/RW 01',
                'no_hp'  => '081234567802',
            ],
            [
                'email'  => 'ahmad@warga.id',
                'nama'   => 'Ahmad Fauzi',
                'alamat' => 'Jl. Kenanga No. 8, RT 03/RW 02',
                'no_hp'  => '081234567803',
            ],
            [
                'email'  => 'dewi@warga.id',
                'nama'   => 'Dewi Lestari',
                'alamat' => 'Jl. Anggrek No. 15, RT 01/RW 03',
                'no_hp'  => '081234567804',
            ],
            [
                'email'  => 'hendra@warga.id',
                'nama'   => 'Hendra Gunawan',
                'alamat' => 'Jl. Dahlia No. 3, RT 04/RW 01',
                'no_hp'  => '081234567805',
            ],
        ];

        foreach ($wargaData as $data) {
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                Warga::create([
                    'user_id' => $user->id,
                    'nama'    => $data['nama'],
                    'alamat'  => $data['alamat'],
                    'no_hp'   => $data['no_hp'],
                ]);
            }
        }
    }
}
