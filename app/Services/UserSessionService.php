<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserSessionService
{
    /**
     * Create a new session when user logs in
     */
    public static function createSession(User $user): UserSession
    {
        // End any previous sessions without logout
        UserSession::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->update([
                'logout_at' => now(),
                'active_minutes' => 0,
            ]);

        // Create new session
        return UserSession::create([
            'user_id' => $user->id,
            'device_identifier' => self::getDeviceIdentifier(),
            'login_at' => now(),
            'device_type' => self::getDeviceType(),
        ]);
    }

    /**
     * End the current session and calculate active minutes
     */
    public static function endSession(User $user): ?UserSession
    {
        $session = UserSession::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first(['id', 'login_at']);

        if (!$session) {
            return null;
        }

        $logoutAt = now();
        $activeMinutes = $session->login_at->diffInMinutes($logoutAt);

        UserSession::whereKey($session->id)->update([
            'logout_at' => $logoutAt,
            'active_minutes' => $activeMinutes,
        ]);

        $session->logout_at = $logoutAt;
        $session->active_minutes = $activeMinutes;

        return $session;
    }

    /**
     * Get currently online user IDs by type
     */
    public static function getOnlineUserIds(?string $userType = null): array
    {
        $query = UserSession::whereNull('logout_at')
            ->with('user')
            ->get();

        if (!$userType) {
            return $query->pluck('user_id')->toArray();
        }

        return $query->filter(fn ($session) => $session->user?->user_type === $userType)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * Get last active minutes for a user
     */
    public static function getLastActiveMinutes(int $userId): ?int
    {
        return UserSession::where('user_id', $userId)
            ->whereNotNull('logout_at')
            ->latest('logout_at')
            ->value('active_minutes');
    }

    /**
     * Check if user is currently online
     */
    public static function isUserOnline(int $userId): bool
    {
        return UserSession::where('user_id', $userId)
            ->whereNull('logout_at')
            ->exists();
    }

    /**
     * Get device identifier (browser fingerprint)
     */
    private static function getDeviceIdentifier(): string
    {
        return request()->header('User-Agent', 'unknown');
    }

    /**
     * Get device type from User-Agent
     */
    private static function getDeviceType(): string
    {
        $userAgent = request()->header('User-Agent', '');

        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android')) {
            return 'Mobile';
        } elseif (str_contains($userAgent, 'iPad') || str_contains($userAgent, 'Tablet')) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    /**
     * Get online users with their info
     */
    public static function getOnlineUsersInfo(?string $userType = null)
    {
        $query = UserSession::whereNull('logout_at')
            ->with('user')
            ->latest('login_at');

        if ($userType) {
            $query->whereHas('user', fn ($q) => $q->where('user_type', $userType));
        }

        return $query->get()->map(fn ($session) => [
            'id' => $session->user_id,
            'name' => $session->user?->name,
            'email' => $session->user?->email,
            'user_type' => $session->user?->user_type,
            'online_since' => $session->login_at,
            'device_type' => $session->device_type,
        ]);
    }
}
