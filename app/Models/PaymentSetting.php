<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $table = 'payment_settings';

    protected $fillable = [
        'type',
        'provider_name',
        'account_number',
        'account_name',
        'qr_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    const TYPE_BANK    = 'bank';
    const TYPE_EWALLET = 'ewallet';
    const TYPE_QRIS    = 'qris';

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * URL gambar QRIS (jika ada)
     */
    public function getQrImageUrlAttribute(): ?string
    {
        return $this->qr_image ? asset('storage/' . $this->qr_image) : null;
    }
}
