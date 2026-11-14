<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'warga_id',
        'tagihan_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'status',
        'payment_method',
        'payment_channel',
        'proof_of_payment',
        'verification_note',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar'  => 'integer',
    ];

    const STATUS_LUNAS               = 'lunas';
    const STATUS_BELUM_LUNAS         = 'belum_lunas';
    const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    const STATUS_DITOLAK             = 'ditolak';

    const METHOD_TRANSFER_BANK = 'transfer_bank';
    const METHOD_QRIS          = 'qris';
    const METHOD_EWALLET       = 'ewallet';
    const METHOD_TUNAI         = 'tunai';

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    /**
     * Badge status untuk tampilan
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_LUNAS               => '<span class="badge bg-success">Lunas</span>',
            self::STATUS_MENUNGGU_VERIFIKASI => '<span class="badge bg-warning text-dark">Menunggu Verifikasi</span>',
            self::STATUS_DITOLAK             => '<span class="badge bg-danger">Ditolak</span>',
            default                          => '<span class="badge bg-secondary">Belum Lunas</span>',
        };
    }

    /**
     * Label metode pembayaran
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::METHOD_TRANSFER_BANK => 'Transfer Bank',
            self::METHOD_QRIS          => 'QRIS',
            self::METHOD_EWALLET       => 'E-Wallet',
            self::METHOD_TUNAI         => 'Tunai',
            default                    => '-',
        };
    }

    /**
     * URL bukti pembayaran
     */
    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_of_payment ? asset('storage/' . $this->proof_of_payment) : null;
    }
}
