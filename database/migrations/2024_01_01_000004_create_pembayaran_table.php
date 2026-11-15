<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('warga')->cascadeOnDelete();
            $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->integer('jumlah_bayar')->unsigned();
            $table->enum('status', ['menunggu_verifikasi', 'lunas', 'ditolak', 'belum_lunas'])->default('menunggu_verifikasi');
            $table->timestamps();

            // Index untuk query yang sering dipakai
            $table->index(['warga_id', 'status']);
            $table->index(['tagihan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
