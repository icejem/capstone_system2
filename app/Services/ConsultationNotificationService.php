<?php

namespace App\Services;

use App\Mail\ConsultationIncompleteNotice;
use App\Mail\ConsultationReminderMail;
use App\Models\Consultation;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ConsultationNotificationService
{
    public static function processScheduledRemindersIfDue(?Carbon $now = null): array
    {
        $now = $now ?: Carbon::now('Asia/Manila');
        $minuteBucket = $now->copy()->setSecond(0)->format('YmdHi');
        $cacheKey = 'consultation_reminders:processed:' . $minuteBucket;

        try {
            if (! Cache::add($cacheKey, $now->toIso8601String(), $now->copy()->addMinutes(2))) {
                return [
                    'events_sent' => 0,
                    'emails_sent' => 0,
                    'sms_sent' => 0,
                    'skipped' => true,
                ];
            }
        } catch (\Throwable $exception) {
            Log::warning('Reminder throttle cache unavailable. Processing reminders without cache guard.', [
                'error' => $exception->getMessage(),
            ]);
        }

        $result = self::processScheduledReminders($now);
        $result['skipped'] = false;

        return $result;
    }

    public static function processScheduledReminders(?Carbon $now = null): array
    {
        $now = $now ?: Carbon::now('Asia/Manila');

        $candidates = Consultation::query()
            ->with(['student', 'instructor'])
            ->where('status', 'approved')
            ->whereDate('consultation_date', '>=', $now->toDateString())
            ->whereDate('consultation_date', '<=', $now->copy()->addDay()->toDateString())
            ->orderBy('id')
            ->get();

        $eventsSent = 0;
        $emailsSent = 0;
        $smsSent = 0;

        foreach ($candidates as $consultation) {
            $startAt = self::resolveStartAt($consultation, $now->getTimezone()->getName());

            if (! $startAt || $now->gte($startAt)) {
                continue;
            }

            foreach ([
                ['minutes' => 30, 'column' => 'reminder_30_sent_at'],
                ['minutes' => 10, 'column' => 'reminder_10_sent_at'],
            ] as $reminder) {
                $minutesBefore = $reminder['minutes'];
                $column = $reminder['column'];

                if ($consultation->{$column}) {
                    continue;
                }

                $reminderAt = $startAt->copy()->subMinutes($minutesBefore);
                if ($now->lt($reminderAt) || $now->gte($startAt)) {
                    continue;
                }

                $result = self::sendReminderNotifications($consultation, $minutesBefore);
                $emailsSent += $result['emails_sent'];
                $smsSent += $result['sms_sent'];
                $consultation->forceFill([
                    $column => $now,
                    'reminder_sent_at' => $consultation->reminder_sent_at ?: $now,
                ])->save();
                $eventsSent++;
            }
        }

        return [
            'events_sent' => $eventsSent,
            'emails_sent' => $emailsSent,
            'sms_sent' => $smsSent,
        ];
    }

    public static function sendReminderNotifications(Consultation $consultation, int $minutesBefore): array
    {
        $consultation->loadMissing(['student', 'instructor']);

        $student = $consultation->student;
        $instructor = $consultation->instructor;
        $emailsSent = 0;
        $smsSent = 0;

        foreach ([
            ['user' => $student, 'role' => 'student', 'counterpart' => $instructor],
            ['user' => $instructor, 'role' => 'instructor', 'counterpart' => $student],
        ] as $recipientConfig) {
            /** @var User|null $recipient */
            $recipient = $recipientConfig['user'];
            /** @var User|null $counterpart */
            $counterpart = $recipientConfig['counterpart'];
            $role = $recipientConfig['role'];

            if (! $recipient) {
                continue;
            }

            $counterpartLabel = $counterpart?->name ?: ($role === 'student' ? 'your instructor' : 'your student');

            UserNotification::create([
                'user_id' => $recipient->id,
                'title' => 'Consultation Reminder',
                'message' => 'Reminder: Your consultation with ' . $counterpartLabel .
                    ' starts in ' . $minutesBefore . ' minutes.',
                'type' => 'reminder',
                'is_read' => false,
            ]);

            if (SmsNotificationService::sendReminder($consultation, $recipient, $counterpart, $minutesBefore)) {
                $smsSent++;
            }

            if (! $recipient->email) {
                continue;
            }

            try {
                Mail::to($recipient->email)->send(new ConsultationReminderMail(
                    $consultation,
                    $recipient,
                    $role,
                    $counterpart,
                    $minutesBefore
                ));
                $emailsSent++;
            } catch (\Throwable $exception) {
                Log::warning('Failed to send consultation reminder email.', [
                    'consultation_id' => $consultation->id,
                    'recipient_id' => $recipient->id,
                    'recipient_role' => $role,
                    'mail_to' => $recipient->email,
                    'minutes_before' => $minutesBefore,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return [
            'emails_sent' => $emailsSent,
            'sms_sent' => $smsSent,
        ];
    }

    public static function sendIncompleteNotifications(
        Consultation $consultation,
        int $attempts = 3,
        ?string $reasonText = null
    ): int {
        $consultation->loadMissing(['student', 'instructor']);

        $student = $consultation->student;
        $instructor = $consultation->instructor;
        $attempts = max(0, $attempts);
        $reasonText = self::normalizeIncompleteReason($reasonText, $attempts);

        if ($student) {
            UserNotification::create([
                'user_id' => $student->id,
                'title' => 'Session Marked Incomplete',
                'message' => 'Your consultation was marked as incomplete ' . $reasonText,
                'type' => 'session',
                'is_read' => false,
            ]);

            SmsNotificationService::sendIncomplete(
                $consultation,
                $student,
                'Your consultation was marked as incomplete ' . $reasonText
            );
        }

        if ($instructor) {
            UserNotification::create([
                'user_id' => $instructor->id,
                'title' => 'Session Marked Incomplete',
                'message' => 'Consultation with ' . ($student?->name ?: 'the student') .
                    ' was marked as incomplete ' . $reasonText,
                'type' => 'session',
                'is_read' => false,
            ]);

            SmsNotificationService::sendIncomplete(
                $consultation,
                $instructor,
                'Consultation with ' . ($student?->name ?: 'the student') . ' was marked as incomplete ' . $reasonText
            );
        }

        $adminIds = User::query()
            ->where('user_type', 'admin')
            ->pluck('id');

        foreach ($adminIds as $adminId) {
            UserNotification::create([
                'user_id' => $adminId,
                'title' => 'Session Marked Incomplete',
                'message' => 'The consultation between ' . ($student?->name ?: 'Student') . ' and ' .
                    ($instructor?->name ?: 'Instructor') . ' was marked as incomplete ' . $reasonText,
                'type' => 'session',
                'is_read' => false,
            ]);
        }

        if (! $student || ! $instructor) {
            return 0;
        }

        $emailsSent = 0;

        foreach ([
            ['email' => $student->email, 'role' => 'student', 'id' => $student->id],
            ['email' => $instructor->email, 'role' => 'instructor', 'id' => $instructor->id],
        ] as $recipient) {
            if (! $recipient['email']) {
                continue;
            }

            try {
                Mail::to($recipient['email'])->send(new ConsultationIncompleteNotice(
                    $consultation,
                    $student,
                    $instructor,
                    $recipient['role'],
                    $attempts,
                    $reasonText
                ));
                $emailsSent++;
            } catch (\Throwable $exception) {
                Log::warning('Failed to send consultation incomplete email.', [
                    'consultation_id' => $consultation->id,
                    'recipient_id' => $recipient['id'],
                    'recipient_role' => $recipient['role'],
                    'mail_to' => $recipient['email'],
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $admins = User::query()
            ->where('user_type', 'admin')
            ->whereNotNull('email')
            ->get(['id', 'name', 'email']);

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new ConsultationIncompleteNotice(
                    $consultation,
                    $student,
                    $instructor,
                    'admin',
                    $attempts,
                    $reasonText
                ));
                $emailsSent++;
            } catch (\Throwable $exception) {
                Log::warning('Failed to send consultation incomplete email to admin.', [
                    'consultation_id' => $consultation->id,
                    'admin_id' => $admin->id,
                    'mail_to' => $admin->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $emailsSent;
    }

    public static function resolveStartAt(Consultation $consultation, string $timezone = 'Asia/Manila'): ?Carbon
    {
        $date = (string) $consultation->consultation_date;
        $time = (string) $consultation->consultation_time;

        if ($date === '' || $time === '') {
            return null;
        }

        $normalizedTime = strlen($time) === 5 ? $time . ':00' : $time;

        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $normalizedTime, $timezone);
        } catch (\Throwable $exception) {
            Log::warning('Failed to resolve consultation start time.', [
                'consultation_id' => $consultation->id,
                'consultation_date' => $date,
                'consultation_time' => $time,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    private static function normalizeIncompleteReason(?string $reasonText, int $attempts): string
    {
        $reasonText = trim((string) $reasonText);

        if ($reasonText !== '') {
            return str_starts_with($reasonText, 'because') ? $reasonText : 'because ' . $reasonText;
        }

        if ($attempts > 0) {
            return 'because there was no answer after ' . $attempts . ' call attempts.';
        }

        return 'because the scheduled session was not completed.';
    }
}
