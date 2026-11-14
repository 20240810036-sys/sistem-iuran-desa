<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['bank', 'ewallet', 'qris']);
            $table->string('provider_name');          // Nama Bank / E-Wallet / "QRIS"
            $table->string('account_number')->nullable(); // Nomor rekening / no. HP
            $table->string('account_name')->nullable();   // Atas nama
            $table->string('qr_image')->nullable();       // Path gambar QRIS
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
