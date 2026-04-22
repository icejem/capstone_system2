<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentRegistrationRoster extends Model
{
    protected $fillable = [
        'batch_token',
        'academic_year',
        'semester',
        'student_id',
        'first_name',
        'last_name',
        'year_level',
        'imported_by',
    ];

    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
