<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): User
    {
        $this->ensureIsNotRateLimited();

        $email = Str::lower(trim((string) $this->input('email')));
        $password = (string) $this->input('password');
        $user = User::where('email', $email)->first();

        if ($user && ! $user->hasActiveAccount()) {
            Log::warning('auth.login.rejected_inactive_account', [
                'user_id' => $user->getKey(),
                'email' => $email,
                'ip' => $this->ip(),
            ]);

            throw ValidationException::withMessages([
                'email' => $user->normalizedAccountStatus() === 'suspended'
                    ? 'Access denied. Your account is suspended. Please contact the administrator.'
                    : 'Access denied. Your account is deactivated. Please contact the administrator.',
            ]);
        }

        if ($user) {
            $storedPassword = (string) $user->getAuthPassword();

            if (Hash::check($password, $storedPassword)) {
                RateLimiter::clear($this->throttleKey());
                Log::info('auth.login.credentials_valid', [
                    'user_id' => $user->getKey(),
                    'email' => $email,
                    'ip' => $this->ip(),
                ]);

                return $user;
            }

            if ($storedPassword !== '' && hash_equals($storedPassword, $password)) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                RateLimiter::clear($this->throttleKey());
                Log::warning('auth.login.legacy_password_rehashed', [
                    'user_id' => $user->getKey(),
                    'email' => $email,
                    'ip' => $this->ip(),
                ]);

                return $user;
            }
        }

        RateLimiter::hit($this->throttleKey());
        Log::warning('auth.login.failed', [
            'email' => $email,
            'ip' => $this->ip(),
        ]);

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
