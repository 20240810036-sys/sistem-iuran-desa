<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Bank
            ['type' => 'bank', 'provider_name' => 'BCA',     'account_number' => '1234567890', 'account_name' => 'Bendahara RT', 'is_active' => true],
            ['type' => 'bank', 'provider_name' => 'BRI',     'account_number' => '0987654321', 'account_name' => 'Bendahara RT', 'is_active' => true],
            ['type' => 'bank', 'provider_name' => 'BNI',     'account_number' => '1122334455', 'account_name' => 'Bendahara RT', 'is_active' => false],
            ['type' => 'bank', 'provider_name' => 'Mandiri', 'account_number' => '5566778899', 'account_name' => 'Bendahara RT', 'is_active' => false],

            // E-Wallet
            ['type' => 'ewallet', 'provider_name' => 'DANA',      'account_number' => '08123456789', 'account_name' => 'Bendahara RT', 'is_active' => true],
            ['type' => 'ewallet', 'provider_name' => 'OVO',       'account_number' => '08123456789', 'account_name' => 'Bendahara RT', 'is_active' => true],
            ['type' => 'ewallet', 'provider_name' => 'GoPay',     'account_number' => '08123456789', 'account_name' => 'Bendahara RT', 'is_active' => false],
            ['type' => 'ewallet', 'provider_name' => 'ShopeePay', 'account_number' => '08123456789', 'account_name' => 'Bendahara RT', 'is_active' => false],

            // QRIS (gambar diunggah manual lewat menu Pengaturan Pembayaran)
            ['type' => 'qris', 'provider_name' => 'QRIS', 'account_number' => null, 'account_name' => null, 'qr_image' => null, 'is_active' => true],
        ];

        foreach ($data as $item) {
            PaymentSetting::firstOrCreate(
                ['type' => $item['type'], 'provider_name' => $item['provider_name']],
                $item
            );
        }
    }
}
