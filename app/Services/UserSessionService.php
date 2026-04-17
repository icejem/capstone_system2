<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;

class UserSessionService
{
    /**
     * Create a new session when user logs in
     */
    public static function createSession(User $user): UserSession
    {
        self::closeExpiredSessions();

        // End any previous sessions without logout
        UserSession::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->get()
            ->each(function (UserSession $session) {
                self::endTrackedSession($session, 'timeout');
            });

        $userAgent = request()->header('User-Agent', 'unknown');

        // Create new session
        return UserSession::create([
            'user_id' => $user->id,
            'device_identifier' => $userAgent,
            'login_at' => now(),
            'last_activity_at' => now(),
            'device_type' => self::getDeviceType(),
            'browser' => self::getBrowser($userAgent),
            'operating_system' => self::getOperatingSystem($userAgent),
            'ip_address' => self::getClientIp(),
            'location' => self::getApproximateLocation(self::getClientIp()),
            'user_agent' => $userAgent,
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
            ->first();

        if (!$session) {
            return null;
        }

        return self::endTrackedSession($session, 'manual');
    }

    public static function touchCurrentSession(?User $user = null): void
    {
        self::closeExpiredSessions();

        $user ??= Auth::user();
        if (! $user) {
            return;
        }

        $session = UserSession::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first(['id']);

        if ($session) {
            UserSession::whereKey($session->id)->update(['last_activity_at' => now()]);
        }
    }

    public static function closeExpiredSessions(): void
    {
        $timeoutAt = now()->subMinutes((int) config('session.lifetime', 120));

        UserSession::whereNull('logout_at')
            ->whereNotNull('last_activity_at')
            ->where('last_activity_at', '<', $timeoutAt)
            ->get()
            ->each(function (UserSession $session) {
                self::endTrackedSession($session, 'timeout');
            });
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

    private static function getBrowser(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Microsoft Edge',
            str_contains($userAgent, 'OPR/'), str_contains($userAgent, 'Opera') => 'Opera',
            str_contains($userAgent, 'Chrome/') && ! str_contains($userAgent, 'Chromium') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') && str_contains($userAgent, 'Version/') => 'Safari',
            default => 'Unknown Browser',
        };
    }

    private static function getOperatingSystem(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Windows NT') => 'Windows',
            str_contains($userAgent, 'Mac OS X') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone'), str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown OS',
        };
    }

    private static function getClientIp(): ?string
    {
        return request()?->ip();
    }

    private static function getApproximateLocation(?string $ipAddress): string
    {
        if (! $ipAddress) {
            return 'Unknown';
        }

        if (
            $ipAddress === '127.0.0.1'
            || $ipAddress === '::1'
            || str_starts_with($ipAddress, '10.')
            || str_starts_with($ipAddress, '192.168.')
            || preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $ipAddress)
        ) {
            return 'Local network';
        }

        return 'Unavailable';
    }

    private static function endTrackedSession(UserSession $session, string $reason): UserSession
    {
        $logoutAt = $reason === 'timeout'
            ? ($session->last_activity_at ?: now())
            : now();
        $activeMinutes = $session->login_at
            ? max(0, (int) $session->login_at->diffInMinutes($logoutAt))
            : 0;

        UserSession::whereKey($session->id)->update([
            'logout_at' => $logoutAt,
            'last_activity_at' => $session->last_activity_at ?: $logoutAt,
            'active_minutes' => $activeMinutes,
            'logout_reason' => $reason,
        ]);

        $session->logout_at = $logoutAt;
        $session->active_minutes = $activeMinutes;
        $session->logout_reason = $reason;

        return $session;
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
