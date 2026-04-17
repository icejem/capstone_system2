<?php

namespace App\Services;

use App\Mail\LoginVerificationMail;
use App\Models\LoginVerification;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class LoginVerificationService
{
    public function create(User $user, Request $request, bool $remember = false): LoginVerification
    {
        $this->invalidatePendingForUser($user, 'superseded_by_new_login');

        $plainToken = Str::random(96);
        $verification = LoginVerification::create([
            'user_id' => $user->getKey(),
            'email' => $user->email,
            'token_hash' => hash('sha256', $plainToken),
            'remember' => $remember,
            'device_label' => $this->resolveDeviceLabel($request->userAgent()),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'sent_at' => now(),
            'expires_at' => now()->addMinutes($this->expirationMinutes()),
        ]);

        Mail::to($user->email)->send(new LoginVerificationMail(
            user: $user,
            verification: $verification,
            verificationUrl: $this->verificationUrl($verification, $plainToken),
        ));

        Log::info('auth.login_verification.created', [
            'verification_id' => $verification->id,
            'user_id' => $user->getKey(),
            'email' => $user->email,
            'ip' => $request->ip(),
            'device' => $verification->device_label,
            'expires_at' => $verification->expires_at?->toIso8601String(),
        ]);

        return $verification;
    }

    public function resend(LoginVerification $verification, Request $request): LoginVerification
    {
        return $this->create($verification->user, $request, $verification->remember);
    }

    public function verify(LoginVerification $verification, string $payload, Request $request): ?User
    {
        $token = $this->extractToken($verification, $payload);

        if (! $token) {
            $this->logRejectedAttempt('payload_invalid', $verification, $request);

            return null;
        }

        if (! hash_equals($verification->token_hash, hash('sha256', $token))) {
            $this->logRejectedAttempt('token_mismatch', $verification, $request);

            return null;
        }

        if ($verification->isInvalidated()) {
            $this->logRejectedAttempt('already_invalidated', $verification, $request);

            return null;
        }

        if ($verification->isConsumed()) {
            $this->logRejectedAttempt('already_consumed', $verification, $request);

            return null;
        }

        if ($verification->isExpired()) {
            $verification->forceFill([
                'invalidated_at' => $verification->invalidated_at ?? now(),
            ])->save();

            $this->logRejectedAttempt('expired', $verification, $request);

            return null;
        }

        $verification->forceFill([
            'verified_at' => now(),
            'consumed_at' => now(),
        ])->save();

        Log::info('auth.login_verification.verified', [
            'verification_id' => $verification->id,
            'user_id' => $verification->user_id,
            'email' => $verification->email,
            'ip' => $request->ip(),
        ]);

        return $verification->user;
    }

    public function invalidatePendingForUser(User $user, string $reason = 'invalidated'): void
    {
        $updated = LoginVerification::query()
            ->where('user_id', $user->getKey())
            ->whereNull('consumed_at')
            ->whereNull('invalidated_at')
            ->update([
                'invalidated_at' => now(),
                'updated_at' => now(),
            ]);

        if ($updated > 0) {
            Log::info('auth.login_verification.invalidated', [
                'user_id' => $user->getKey(),
                'email' => $user->email,
                'reason' => $reason,
                'count' => $updated,
            ]);
        }
    }

    public function resendAvailableAt(LoginVerification $verification): Carbon
    {
        $basis = $verification->last_resent_at ?? $verification->sent_at ?? $verification->created_at;

        return $basis->copy()->addSeconds($this->resendCooldownSeconds());
    }

    public function canResend(LoginVerification $verification): bool
    {
        return now()->greaterThanOrEqualTo($this->resendAvailableAt($verification));
    }

    public function expirationMinutes(): int
    {
        return (int) config('services.auth_verification.expire_minutes', 10);
    }

    public function resendCooldownSeconds(): int
    {
        return (int) config('services.auth_verification.resend_cooldown_seconds', 60);
    }

    private function verificationUrl(LoginVerification $verification, string $plainToken): string
    {
        $payload = Crypt::encryptString(json_encode([
            'verification_id' => $verification->id,
            'token' => $plainToken,
        ], JSON_THROW_ON_ERROR));

        return URL::temporarySignedRoute(
            'login.verification.verify',
            $verification->expires_at,
            [
                'verification' => $verification->id,
                'payload' => $payload,
            ],
        );
    }

    private function extractToken(LoginVerification $verification, string $payload): ?string
    {
        try {
            $decrypted = Crypt::decryptString($payload);
            $data = json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);
        } catch (DecryptException|\JsonException) {
            return null;
        }

        if (($data['verification_id'] ?? null) !== $verification->id) {
            return null;
        }

        return is_string($data['token'] ?? null) ? $data['token'] : null;
    }

    private function resolveDeviceLabel(?string $userAgent): string
    {
        $userAgent = trim((string) $userAgent);

        if ($userAgent === '') {
            return 'Unknown device';
        }

        $browser = 'Unknown browser';
        $platform = 'Unknown OS';

        foreach ([
            'Edg/' => 'Microsoft Edge',
            'OPR/' => 'Opera',
            'Chrome/' => 'Chrome',
            'Firefox/' => 'Firefox',
            'Safari/' => 'Safari',
        ] as $needle => $label) {
            if (str_contains($userAgent, $needle)) {
                $browser = $label;
                break;
            }
        }

        foreach ([
            'Windows' => 'Windows',
            'Mac OS X' => 'macOS',
            'Android' => 'Android',
            'iPhone' => 'iPhone',
            'iPad' => 'iPad',
            'Linux' => 'Linux',
        ] as $needle => $label) {
            if (str_contains($userAgent, $needle)) {
                $platform = $label;
                break;
            }
        }

        return "{$browser} on {$platform}";
    }

    private function logRejectedAttempt(string $reason, LoginVerification $verification, Request $request): void
    {
        Log::warning('auth.login_verification.rejected', [
            'reason' => $reason,
            'verification_id' => $verification->id,
            'user_id' => $verification->user_id,
            'email' => $verification->email,
            'ip' => $request->ip(),
        ]);
    }
}
