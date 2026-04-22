<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'semester',
        'academic_year',
        'available_day',
        'available_date',
        'start_time',
        'end_time',
        'is_active',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
