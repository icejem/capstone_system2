<?php

namespace Tests\Feature;

use App\Models\InstructorAvailability;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StudentConsultationRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_face_to_face_requests_require_an_urgency_level(): void
    {
        [$student, $instructor, $payload] = $this->buildConsultationPayload();

        $response = $this->actingAs($student)->post(route('student.consultation.store'), [
            ...$payload,
            'consultation_mode' => 'Face-to-Face',
            'consultation_priority' => '',
        ]);

        $response->assertSessionHasErrors([
            'consultation_priority' => 'Urgency level is required for Face-to-Face consultations.',
        ]);
    }

    public function test_urgent_face_to_face_requests_require_a_discussion_brief(): void
    {
        [$student, $instructor, $payload] = $this->buildConsultationPayload();

        $response = $this->actingAs($student)->post(route('student.consultation.store'), [
            ...$payload,
            'consultation_mode' => 'Face-to-Face',
            'consultation_priority' => 'Urgent',
            'student_notes' => '   ',
        ]);

        $response->assertSessionHasErrors([
            'student_notes' => 'Description is required when urgency level is Urgent.',
        ]);
    }

    private function buildConsultationPayload(): array
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 21, 9, 0, 0, 'Asia/Manila'));

        $student = User::factory()->create([
            'user_type' => 'student',
            'account_status' => 'active',
        ]);

        $instructor = User::factory()->create([
            'user_type' => 'instructor',
            'student_id' => null,
            'account_status' => 'active',
        ]);

        InstructorAvailability::create([
            'instructor_id' => $instructor->id,
            'semester' => '2nd Semester',
            'academic_year' => '2025-2026',
            'available_day' => 'wednesday',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'is_active' => true,
        ]);

        return [
            $student,
            $instructor,
            [
                'instructor_id' => $instructor->id,
                'consultation_date' => '2026-04-22',
                'consultation_time' => '10:00',
                'consultation_category' => 'Curricular Activities',
                'consultation_type' => 'Assignment Concern',
                'consultation_mode' => 'Video Call',
                'student_notes' => 'Need guidance for the scheduled consultation.',
            ],
        ];
    }
}
