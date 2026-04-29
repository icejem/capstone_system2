<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const YEAR_LEVELS = ['1st', '2nd', '3rd', '4th'];

    public const YEAR_LEVEL_LABELS = [
        '1st' => '1st Year',
        '2nd' => '2nd Year',
        '3rd' => '3rd Year',
        '4th' => '4th Year',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone_number',
        'password',
        'user_type',
        'account_status',
        'suspension_expires_at',
        'suspension_reason',
        'profile_photo_path',
        'student_id',
        'year_level',
        'yearlevel',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspension_expires_at' => 'datetime',
        ];
    }

    public function normalizedAccountStatus(): string
    {
        $status = strtolower(trim((string) ($this->getAttribute('account_status') ?? 'active')));

        return in_array($status, ['active', 'inactive', 'suspended'], true)
            ? $status
            : 'active';
    }

    public function hasActiveAccount(): bool
    {
        // Check if suspension has expired
        $this->autoUnsuspendIfExpired();
        return $this->normalizedAccountStatus() === 'active';
    }

    public function autoUnsuspendIfExpired(): void
    {
        if (
            $this->normalizedAccountStatus() === 'suspended'
            && $this->suspension_expires_at
            && \Illuminate\Support\Carbon::now('Asia/Manila')->isAfter($this->suspension_expires_at)
        ) {
            $this->update([
                'account_status' => 'active',
                'suspension_expires_at' => null,
                'suspension_reason' => null,
            ]);
        }
    }

    public function isSuspended(): bool
    {
        return $this->normalizedAccountStatus() === 'suspended';
    }

    public function getSuspensionExpiryAttribute(): ?\Illuminate\Support\Carbon
    {
        if (!$this->suspension_expires_at) {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($this->suspension_expires_at, 'Asia/Manila');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return list<string>
     */
    public static function yearLevels(): array
    {
        return self::YEAR_LEVELS;
    }

    /**
     * @return array<string, string>
     */
    public static function yearLevelLabels(): array
    {
        return self::YEAR_LEVEL_LABELS;
    }

    public static function normalizeYearLevel(?string $value): ?string
    {
        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            '1st', '1st year' => '1st',
            '2nd', '2nd year' => '2nd',
            '3rd', '3rd year' => '3rd',
            '4th', '4th year' => '4th',
            default => null,
        };
    }

    public static function yearLevelLabel(?string $value): string
    {
        $normalized = self::normalizeYearLevel($value);

        return $normalized !== null
            ? (self::YEAR_LEVEL_LABELS[$normalized] ?? $normalized)
            : 'Not set';
    }

    public static function legacyYearLevelValue(?string $value): ?string
    {
        $normalized = self::normalizeYearLevel($value);

        return $normalized !== null
            ? (self::YEAR_LEVEL_LABELS[$normalized] ?? null)
            : null;
    }

    public function trustedDevices(): HasMany
    {
        return $this->hasMany(TrustedDevice::class);
    }

}
