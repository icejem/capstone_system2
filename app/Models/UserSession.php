<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_identifier',
        'login_at',
        'logout_at',
        'active_minutes',
        'device_type',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate active minutes if logout_at is set
     */
    public function calculateActiveMinutes(): int
    {
        if (!$this->logout_at || !$this->login_at) {
            return 0;
        }
        return (int) $this->login_at->diffInMinutes($this->logout_at);
    }

    /**
     * Get the last active session for a user
     */
    public static function lastSessionMinutes(int $userId): ?int
    {
        $session = self::where('user_id', $userId)
            ->whereNotNull('logout_at')
            ->latest('logout_at')
            ->first();

        return $session?->active_minutes;
    }

    /**
     * Check if user is currently online (has active session without logout)
     */
    public static function isUserOnline(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->whereNull('logout_at')
            ->exists();
    }

    /**
     * Get currently active users for a given user type
     */
    public static function getOnlineUsers(string $userType = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::whereNull('logout_at')
            ->with('user');

        if ($userType) {
            $query->whereHas('user', fn ($q) => $q->where('user_type', $userType));
        }

        return $query->get();
    }
}
