<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class LoginVerificationToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'plain_token',
        'ip_address',
        'user_agent',
        'expires_at',
        'verified_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'used' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Relationship: Token belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if token is valid (not expired, not used, exists)
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Mark token as verified
     */
    public function markAsVerified(): void
    {
        $this->update([
            'verified_at' => now(),
            'used' => true,
        ]);
    }

    /**
     * Generate a new token pair
     */
    public static function generateToken(User $user, ?string $ipAddress = null, ?string $userAgent = null): self
    {
        // Invalidate all previous tokens for this user
        self::where('user_id', $user->id)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->delete();

        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);
        $expiresAt = now()->addMinutes(10); // 10 minute expiration

        return self::create([
            'user_id' => $user->id,
            'token' => $hashedToken,
            'plain_token' => $plainToken,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Find token by plain token
     */
    public static function findByPlainToken(string $plainToken): ?self
    {
        $hashedToken = hash('sha256', $plainToken);
        return self::where('token', $hashedToken)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();
    }
}
