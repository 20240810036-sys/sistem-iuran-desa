<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('bulan')->unsigned()->comment('1-12');
            $table->smallInteger('tahun')->unsigned();
            $table->integer('jumlah_iuran')->unsigned()->comment('Dalam rupiah');
            $table->timestamps();

            // Satu tagihan per bulan per tahun
            $table->unique(['bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
