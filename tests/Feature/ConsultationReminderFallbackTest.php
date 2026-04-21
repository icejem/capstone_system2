<?php

namespace Tests\Feature;

use App\Mail\ConsultationReminderMail;
use App\Models\Consultation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ConsultationReminderFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_summary_poll_triggers_due_30_minute_reminders_once(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 21, 9, 0, 0, 'Asia/Manila'));
        Mail::fake();
        Http::fake([
            'https://unismsapi.com/api/sms' => Http::response(['ok' => true], 200),
        ]);
        config()->set('services.sms.enabled', true);
        config()->set('services.sms.provider', 'unisms');

        $student = User::factory()->create([
            'user_type' => 'student',
            'account_status' => 'active',
        ]);

        $instructor = User::factory()->create([
            'user_type' => 'instructor',
            'student_id' => null,
            'account_status' => 'active',
        ]);

        $consultation = Consultation::create([
            'student_id' => $student->id,
            'instructor_id' => $instructor->id,
            'consultation_date' => '2026-04-21',
            'consultation_time' => '09:30:00',
            'consultation_end_time' => '10:30:00',
            'consultation_type' => 'Curricular Activities - Assignment Concern',
            'consultation_category' => 'Curricular Activities',
            'consultation_topic' => 'Assignment Concern',
            'consultation_priority' => 'Normal',
            'consultation_mode' => 'Face-to-Face',
            'student_notes' => 'Please remind me before the meeting.',
            'status' => 'approved',
        ]);

        $this->actingAs($student)
            ->get(route('api.student.consultations-summary'))
            ->assertOk();

        Mail::assertSent(ConsultationReminderMail::class, 2);
        Http::assertSentCount(2);
        $this->assertNotNull($consultation->fresh()->reminder_30_sent_at);

        $this->actingAs($student)
            ->get(route('api.student.consultations-summary'))
            ->assertOk();

        Mail::assertSent(ConsultationReminderMail::class, 2);
        Http::assertSentCount(2);
    }

    public function test_student_summary_poll_triggers_due_10_minute_reminders_once(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 21, 9, 20, 0, 'Asia/Manila'));
        Mail::fake();
        Http::fake([
            'https://unismsapi.com/api/sms' => Http::response(['ok' => true], 200),
        ]);
        config()->set('services.sms.enabled', true);
        config()->set('services.sms.provider', 'unisms');

        $student = User::factory()->create([
            'user_type' => 'student',
            'account_status' => 'active',
        ]);

        $instructor = User::factory()->create([
            'user_type' => 'instructor',
            'student_id' => null,
            'account_status' => 'active',
        ]);

        $consultation = Consultation::create([
            'student_id' => $student->id,
            'instructor_id' => $instructor->id,
            'consultation_date' => '2026-04-21',
            'consultation_time' => '09:30:00',
            'consultation_end_time' => '10:30:00',
            'consultation_type' => 'Curricular Activities - Assignment Concern',
            'consultation_category' => 'Curricular Activities',
            'consultation_topic' => 'Assignment Concern',
            'consultation_priority' => 'Normal',
            'consultation_mode' => 'Face-to-Face',
            'student_notes' => 'Please remind me before the meeting.',
            'status' => 'approved',
            'reminder_30_sent_at' => Carbon::create(2026, 4, 21, 9, 0, 0, 'Asia/Manila'),
        ]);

        $this->actingAs($student)
            ->get(route('api.student.consultations-summary'))
            ->assertOk();

        Mail::assertSent(ConsultationReminderMail::class, 2);
        Http::assertSentCount(2);
        $this->assertNotNull($consultation->fresh()->reminder_10_sent_at);

        $this->actingAs($student)
            ->get(route('api.student.consultations-summary'))
            ->assertOk();

        Mail::assertSent(ConsultationReminderMail::class, 2);
        Http::assertSentCount(2);
    }
}
