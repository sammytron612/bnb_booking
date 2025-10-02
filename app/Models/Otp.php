<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'verified_at',
        'used',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'used' => 'boolean'
    ];

    /**
     * Get the user that owns the OTP
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is valid (not used and not expired)
     */
    public function isValid(): bool
    {
        return !$this->used && !$this->isExpired();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update([
            'used' => true,
            'verified_at' => now()
        ]);
    }

    /**
     * Generate a random 6-digit OTP code
     */
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP for a user
     */
    public static function createForUser(\App\Models\User $user, ?string $ipAddress = null, ?string $userAgent = null): self
    {
        // Delete any existing unused OTPs for this user
        static::where('user_id', $user->id)
              ->where('used', false)
              ->delete();

        return static::create([
            'user_id' => $user->id,
            'code' => static::generateCode(),
            'expires_at' => now()->addMinutes(10), // OTP expires in 10 minutes
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }
}
