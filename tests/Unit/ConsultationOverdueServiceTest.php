<?php

namespace Tests\Unit;

use App\Models\Consultation;
use App\Models\User;
use App\Services\ConsultationOverdueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ConsultationOverdueServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_marks_overdue_consultations_as_incompleted(): void
    {
        $now = Carbon::create(2026, 4, 12, 12, 0, 0, 'Asia/Manila');

        $student = User::factory()->create(['user_type' => 'student']);
        $instructor = User::factory()->create([
            'user_type' => 'instructor',
            'student_id' => null,
        ]);

        $overdue = Consultation::create([
            'student_id' => $student->id,
            'instructor_id' => $instructor->id,
            'consultation_date' => $now->copy()->subDay()->toDateString(),
            'consultation_time' => '10:00:00',
            'consultation_end_time' => '11:00:00',
            'consultation_type' => 'Video Call',
            'consultation_mode' => 'Online',
            'status' => 'approved',
        ]);

        $future = Consultation::create([
            'student_id' => $student->id,
            'instructor_id' => $instructor->id,
            'consultation_date' => $now->copy()->addDay()->toDateString(),
            'consultation_time' => '10:00:00',
            'consultation_end_time' => '11:00:00',
            'consultation_type' => 'Video Call',
            'consultation_mode' => 'Online',
            'status' => 'approved',
        ]);

        $updated = ConsultationOverdueService::markOverdueAsIncompleted($now);

        $this->assertSame(1, $updated);
        $this->assertSame('incompleted', $overdue->fresh()->status);
        $this->assertSame('approved', $future->fresh()->status);
    }
}

