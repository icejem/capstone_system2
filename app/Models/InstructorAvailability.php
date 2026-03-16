<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
