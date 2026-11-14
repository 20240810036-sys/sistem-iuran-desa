<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        User::create([
            'name'     => 'Admin Desa',
            'email'    => 'admin@desa.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Akun warga sample
        $wargaData = [
            ['name' => 'Budi Santoso',    'email' => 'budi@warga.id'],
            ['name' => 'Siti Rahayu',     'email' => 'siti@warga.id'],
            ['name' => 'Ahmad Fauzi',     'email' => 'ahmad@warga.id'],
            ['name' => 'Dewi Lestari',    'email' => 'dewi@warga.id'],
            ['name' => 'Hendra Gunawan',  'email' => 'hendra@warga.id'],
        ];

        foreach ($wargaData as $data) {
            User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'),
                'role'     => 'warga',
            ]);
        }
    }
}
