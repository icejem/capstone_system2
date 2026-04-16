<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        'profile_photo_path',
        'student_id',
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
        return $this->normalizedAccountStatus() === 'active';
    }
}
