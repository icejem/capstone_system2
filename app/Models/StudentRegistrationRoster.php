<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentRegistrationRoster extends Model
{
    protected $fillable = [
        'batch_token',
        'academic_year',
        'semester',
        'student_id',
        'first_name',
        'last_name',
        'imported_by',
    ];
}
