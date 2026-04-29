<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'stage',
        'status',
        'phone_number_input',
        'phone_number_normalized',
        'message',
        'context',
        'provider_http_status',
        'provider_response',
        'provider_error',
        'result_message',
    ];

    protected $casts = [
        'context' => 'array',
        'provider_http_status' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
