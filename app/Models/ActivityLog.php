<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'action', 'description', 'ip_address', 'user_agent'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Catat aktivitas user (login, logout, CRUD, dsb).
     */
    public static function record(string $action, string $description, ?int $userId = null): void
    {
        $request = request();
        static::create([
            'user_id'     => $userId ?? auth()->id(),
            'action'      => $action,
            'description' => Str::limit($description, 250),
            'ip_address'  => $request?->ip(),
            'user_agent'  => Str::limit((string) $request?->userAgent(), 250),
        ]);
    }
}
