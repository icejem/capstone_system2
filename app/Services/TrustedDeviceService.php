<?php

namespace App\Services;

use App\Models\LoginVerification;
use App\Models\TrustedDevice;
use App\Models\User;
use Illuminate\Http\Request;

class TrustedDeviceService
{
    public function findTrustedDevice(User $user, Request $request): ?TrustedDevice
    {
        $fingerprintHash = $this->fingerprintHashFromRequest($request);

        if (! $fingerprintHash) {
            return null;
        }

        return TrustedDevice::query()
            ->where('user_id', $user->getKey())
            ->where('fingerprint_hash', $fingerprintHash)
            ->whereNull('revoked_at')
            ->first();
    }

    public function hasTrustedDevice(User $user, Request $request): bool
    {
        return $this->findTrustedDevice($user, $request) !== null;
    }

    public function touchTrustedDevice(TrustedDevice $device, Request $request): void
    {
        $context = $this->contextFromRequest($request);

        $device->forceFill([
            'device_label' => $context['device_label'],
            'device_type' => $context['device_type'],
            'browser' => $context['browser'],
            'operating_system' => $context['operating_system'],
            'ip_address' => $context['ip_address'],
            'location' => $context['location'],
            'user_agent' => $context['user_agent'],
            'last_used_at' => now(),
        ])->save();
    }

    public function createOrRefreshFromVerification(LoginVerification $verification): ?TrustedDevice
    {
        $fingerprintHash = trim((string) $verification->device_fingerprint_hash);

        if ($fingerprintHash === '' || ! $verification->user_id) {
            return null;
        }

        $device = TrustedDevice::query()->firstOrNew([
            'user_id' => $verification->user_id,
            'fingerprint_hash' => $fingerprintHash,
        ]);

        $device->forceFill([
            'device_label' => $verification->device_label,
            'device_type' => $this->resolveDeviceType($verification->user_agent),
            'browser' => $this->resolveBrowser($verification->user_agent),
            'operating_system' => $this->resolveOperatingSystem($verification->user_agent),
            'ip_address' => $verification->ip_address,
            'location' => $this->resolveLocation($verification->ip_address),
            'user_agent' => $verification->user_agent,
            'trusted_at' => $device->trusted_at ?? now(),
            'last_used_at' => now(),
            'revoked_at' => null,
        ])->save();

        if ((int) $verification->trusted_device_id !== (int) $device->getKey()) {
            $verification->forceFill([
                'trusted_device_id' => $device->getKey(),
            ])->save();
        }

        return $device;
    }

    public function contextFromRequest(Request $request): array
    {
        $userAgent = trim((string) $request->userAgent());
        $browser = $this->resolveBrowser($userAgent);
        $operatingSystem = $this->resolveOperatingSystem($userAgent);

        return [
            'fingerprint_hash' => $this->fingerprintHashFromRequest($request),
            'device_type' => $this->resolveDeviceType($userAgent),
            'browser' => $browser,
            'operating_system' => $operatingSystem,
            'device_label' => $this->resolveDeviceLabel($browser, $operatingSystem),
            'ip_address' => $request->ip(),
            'location' => $this->resolveLocation($request->ip()),
            'user_agent' => $userAgent !== '' ? $userAgent : null,
        ];
    }

    private function fingerprintHashFromRequest(Request $request): ?string
    {
        $rawFingerprint = trim((string) $request->input('device_fingerprint'));

        if ($rawFingerprint === '') {
            return null;
        }

        return hash('sha256', $rawFingerprint);
    }

    private function resolveDeviceType(?string $userAgent): string
    {
        $userAgent = (string) $userAgent;

        if (str_contains($userAgent, 'iPad') || str_contains($userAgent, 'Tablet')) {
            return 'Tablet';
        }

        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone')) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    private function resolveBrowser(?string $userAgent): string
    {
        $userAgent = (string) $userAgent;

        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Microsoft Edge',
            str_contains($userAgent, 'OPR/'), str_contains($userAgent, 'Opera') => 'Opera',
            str_contains($userAgent, 'Chrome/') && ! str_contains($userAgent, 'Chromium') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') && str_contains($userAgent, 'Version/') => 'Safari',
            default => 'Unknown browser',
        };
    }

    private function resolveOperatingSystem(?string $userAgent): string
    {
        $userAgent = (string) $userAgent;

        return match (true) {
            str_contains($userAgent, 'Windows NT') => 'Windows',
            str_contains($userAgent, 'Mac OS X') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone'), str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown OS',
        };
    }

    private function resolveDeviceLabel(string $browser, string $operatingSystem): string
    {
        return "{$browser} on {$operatingSystem}";
    }

    private function resolveLocation(?string $ipAddress): string
    {
        $ipAddress = trim((string) $ipAddress);

        if ($ipAddress === '') {
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
}
