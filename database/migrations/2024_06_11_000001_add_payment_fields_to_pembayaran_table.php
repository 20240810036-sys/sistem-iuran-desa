<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum status (tanpa doctrine/dbal). Untuk driver lain enum sudah lengkap di migrasi create.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pembayaran MODIFY status ENUM('menunggu_verifikasi','lunas','ditolak','belum_lunas') NOT NULL DEFAULT 'menunggu_verifikasi'");
        }

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->enum('payment_method', ['transfer_bank', 'qris', 'ewallet', 'tunai'])
                  ->nullable()
                  ->after('status');

            $table->string('payment_channel')->nullable()->after('payment_method');

            $table->string('proof_of_payment')->nullable()->after('payment_channel');

            $table->text('verification_note')->nullable()->after('proof_of_payment');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_channel',
                'proof_of_payment',
                'verification_note',
            ]);
        });
    }
};
