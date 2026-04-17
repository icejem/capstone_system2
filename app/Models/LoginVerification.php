<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginVerification extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'token_hash',
        'remember',
        'device_label',
        'ip_address',
        'user_agent',
        'sent_at',
        'expires_at',
        'last_resent_at',
        'verified_at',
        'consumed_at',
        'invalidated_at',
    ];

    protected function casts(): array
    {
        return [
            'remember' => 'boolean',
            'sent_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_resent_at' => 'datetime',
            'verified_at' => 'datetime',
            'consumed_at' => 'datetime',
            'invalidated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function isInvalidated(): bool
    {
        return $this->invalidated_at !== null;
    }

    public function isPending(): bool
    {
        return ! $this->isExpired() && ! $this->isConsumed() && ! $this->isInvalidated();
    }
}
