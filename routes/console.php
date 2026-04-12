<?php

use App\Mail\ConsultationReminderMail;
use App\Models\Consultation;
use App\Models\UserNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('consultations:send-reminders', function () {
    $now = Carbon::now('Asia/Manila');
    $upperDate = $now->copy()->addHour()->toDateString();

    $candidates = Consultation::query()
        ->with(['student', 'instructor'])
        ->where('status', 'approved')
        ->whereNull('reminder_sent_at')
        ->whereDate('consultation_date', '>=', $now->toDateString())
        ->whereDate('consultation_date', '<=', $upperDate)
        ->get();

    $sentCount = 0;

    foreach ($candidates as $consultation) {
        $time = (string) $consultation->consultation_time;
        $normalizedTime = strlen($time) === 5 ? $time . ':00' : $time;

        try {
            $startAt = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $consultation->consultation_date . ' ' . $normalizedTime,
                'Asia/Manila'
            );
        } catch (\Exception $exception) {
            continue;
        }

        $reminderAt = $startAt->copy()->subHour();

        if ($now->lt($reminderAt) || $now->gte($startAt)) {
            continue;
        }

        $student = $consultation->student;
        if (! $student || ! $student->email) {
            Log::warning('Skipping consultation reminder because student email is missing.', [
                'consultation_id' => $consultation->id,
                'student_id' => $consultation->student_id,
            ]);
            continue;
        }

        try {
            Mail::to($student->email)->send(new ConsultationReminderMail($consultation, $student));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send consultation reminder email.', [
                'consultation_id' => $consultation->id,
                'student_id' => $consultation->student_id,
                'mail_to' => $student->email,
                'error' => $exception->getMessage(),
            ]);
            continue;
        }

        UserNotification::create([
            'user_id' => $consultation->student_id,
            'title' => 'Reminder!',
            'message' => 'Reminder; Your consultation session will start in 1 hour. Please prepare in advance and make sure you are in a quiet, non-noisy environment before the session begins. Thank you!',
            'type' => 'reminder',
            'is_read' => false,
        ]);

        $consultation->forceFill([
            'reminder_sent_at' => $now,
        ])->save();

        $sentCount++;
    }

    $this->info("Consultation reminders sent: {$sentCount}");
})->purpose('Send 1-hour reminder emails and notifications for approved consultations');

Schedule::command('consultations:send-reminders')
    ->everyMinute()
    ->withoutOverlapping();

Artisan::command('consultations:mark-overdue-incompleted', function () {
    $now = Carbon::now('Asia/Manila');
    $count = \App\Services\ConsultationOverdueService::markOverdueAsIncompleted($now);
    $this->info("Overdue consultations marked as incompleted: {$count}");
})->purpose('Mark past-due consultations as incomplete (incompleted)');

Schedule::command('consultations:mark-overdue-incompleted')
    ->everyMinute()
    ->withoutOverlapping();
