<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';

    protected $fillable = [
        'user_id',
        'nama',
        'alamat',
        'no_hp',
    ];

    /**
     * Relasi ke User (belongsTo)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Pembayaran (hasMany)
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Hitung total sudah bayar
     */
    public function totalBayar(): int
    {
        return $this->pembayaran()->where('status', 'lunas')->count();
    }

    /**
     * Hitung total belum bayar
     */
    public function totalBelumBayar(): int
    {
        return $this->pembayaran()->where('status', 'belum_lunas')->count();
    }
}
