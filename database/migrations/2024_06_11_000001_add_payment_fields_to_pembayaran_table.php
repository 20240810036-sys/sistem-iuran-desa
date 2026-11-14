<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            // Ubah enum status untuk menambah nilai baru
            $table->enum('status', ['menunggu_verifikasi', 'lunas', 'ditolak', 'belum_lunas'])
                  ->default('menunggu_verifikasi')
                  ->change();

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
