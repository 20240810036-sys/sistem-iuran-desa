<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'bulan',
        'tahun',
        'jumlah_iuran',
    ];

    protected $casts = [
        'jumlah_iuran' => 'integer',
    ];

    /**
     * Relasi ke Pembayaran (hasMany)
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Nama bulan dalam bahasa Indonesia
     */
    public function getNamaBulanAttribute(): string
    {
        $bulan = [
            1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
            4  => 'April',    5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',     8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',  11 => 'November',  12 => 'Desember',
        ];
        return $bulan[$this->bulan] ?? '-';
    }

    /**
     * Label lengkap: "Januari 2025"
     */
    public function getLabelAttribute(): string
    {
        return $this->nama_bulan . ' ' . $this->tahun;
    }

    /**
     * Total pembayaran lunas
     */
    public function totalLunas(): int
    {
        return $this->pembayaran()->where('status', 'lunas')->count();
    }

    /**
     * Total pembayaran belum lunas
     */
    public function totalBelumLunas(): int
    {
        return $this->pembayaran()->where('status', 'belum_lunas')->count();
    }
}
