<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'instructor_id',
        'consultation_date',
        'consultation_time',
        'consultation_end_time',
        'consultation_type',
        'consultation_category',
        'consultation_topic',
        'consultation_priority',
        'consultation_mode',
        'student_notes',
        'summary_text',
        'transcript_text',
        'status',
        'started_at',
        'ended_at',
        'duration_minutes',
        'transcript_active',
        'call_attempts',
        'reminder_sent_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_minutes' => 'integer',
        'transcript_active' => 'boolean',
        'call_attempts' => 'integer',
        'reminder_sent_at' => 'datetime',
    ];

    // Append a computed label for display
    protected $appends = ['type_label'];

    public function getTypeLabelAttribute()
    {
        $parts = [];
        if (!empty($this->consultation_category)) {
            $parts[] = $this->consultation_category;
        }
        if (!empty($this->consultation_topic)) {
            // If topic duplicates category, avoid duplication
            if (empty($parts) || strtolower($this->consultation_topic) !== strtolower($this->consultation_category)) {
                $parts[] = $this->consultation_topic;
            }
        } elseif (!empty($this->consultation_type)) {
            $parts[] = $this->consultation_type;
        }

        $label = implode(' - ', $parts);
        if (!empty($this->consultation_priority)) {
            $label .= ($label ? ' ' : '') . '(' . $this->consultation_priority . ')';
        }

        return $label ?: ($this->consultation_type ?? 'Consultation');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}


