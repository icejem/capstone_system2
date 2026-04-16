<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotificationService
{
    public static function sendReminder(Consultation $consultation, User $recipient, ?User $counterpart, int $minutesBefore): bool
    {
        $dateLabel = self::formatConsultationDateTime($consultation);
        $counterpartLabel = $counterpart?->name ?: 'the other participant';

        return self::sendToUser(
            $recipient,
            "Consultation reminder: Your session with {$counterpartLabel} starts in {$minutesBefore} minutes ({$dateLabel})."
        );
    }

    public static function sendConsultationRequest(Consultation $consultation, User $student, User $instructor): bool
    {
        $dateLabel = self::formatConsultationDateTime($consultation);

        return self::sendToUser(
            $instructor,
            "New consultation request from {$student->name}. Schedule: {$dateLabel}. Please check your dashboard."
        );
    }

    public static function sendStatusUpdate(Consultation $consultation, User $student, User $instructor, string $status): bool
    {
        $dateLabel = self::formatConsultationDateTime($consultation);
        $statusLabel = $status === 'approved' ? 'approved' : 'declined';

        return self::sendToUser(
            $student,
            "Your consultation request with {$instructor->name} was {$statusLabel}. Schedule: {$dateLabel}."
        );
    }

    public static function sendStudentCancellation(
        User $instructor,
        string $studentName,
        string $consultationDate,
        string $consultationTime,
        ?string $consultationEndTime,
        ?string $consultationType
    ): bool {
        $timeLabel = trim($consultationTime . ($consultationEndTime ? ' - ' . $consultationEndTime : ''));
        $typeLabel = $consultationType ?: 'consultation';

        return self::sendToUser(
            $instructor,
            "{$studentName} cancelled the {$typeLabel} scheduled on {$consultationDate} {$timeLabel}."
        );
    }

    public static function sendInstructorCalling(Consultation $consultation, User $student, ?User $instructor, int $callAttempt): bool
    {
        $dateLabel = self::formatConsultationDateTime($consultation);
        $instructorName = $instructor?->name ?: 'Your instructor';

        return self::sendToUser(
            $student,
            "{$instructorName} is calling for your consultation now. Attempt {$callAttempt}. Schedule: {$dateLabel}."
        );
    }

    public static function sendIncomplete(Consultation $consultation, User $recipient, string $message): bool
    {
        $dateLabel = self::formatConsultationDateTime($consultation);

        return self::sendToUser(
            $recipient,
            "Consultation update: {$message} Schedule: {$dateLabel}."
        );
    }

    public static function sendToUser(User $user, string $message): bool
    {
        return self::send($user->phone_number, $message, [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
        ]);
    }

    public static function send(?string $phoneNumber, string $message, array $context = []): bool
    {
        $normalized = self::normalizePhoneNumber($phoneNumber);
        $provider = (string) config('services.sms.provider', 'log');
        $enabled = (bool) config('services.sms.enabled', false);

        if ($normalized === null) {
            Log::info('SMS skipped: no valid phone number.', $context + [
                'phone_number' => $phoneNumber,
            ]);

            return false;
        }

        if (! $enabled) {
            Log::info('SMS skipped: service disabled.', $context + [
                'phone_number' => $normalized,
            ]);

            return false;
        }

        if ($provider === 'log') {
            Log::info('SMS log delivery.', $context + [
                'phone_number' => $normalized,
                'message' => $message,
            ]);

            return true;
        }

        if ($provider === 'semaphore') {
            return self::sendViaSemaphore($normalized, $message, $context);
        }

        Log::warning('SMS skipped: unsupported provider.', $context + [
            'provider' => $provider,
        ]);

        return false;
    }

    public static function normalizePhoneNumber(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '63') && strlen($digits) === 12) {
            return '+' . $digits;
        }

        if (str_starts_with($digits, '09') && strlen($digits) === 11) {
            return '+63' . substr($digits, 1);
        }

        if (str_starts_with($digits, '9') && strlen($digits) === 10) {
            return '+63' . $digits;
        }

        return null;
    }

    private static function sendViaSemaphore(string $phoneNumber, string $message, array $context = []): bool
    {
        $apiKey = (string) config('services.sms.semaphore.key');
        $sender = (string) config('services.sms.semaphore.sender_name');

        if ($apiKey === '') {
            Log::warning('SMS skipped: missing Semaphore API key.', $context);
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout((int) config('services.sms.timeout', 10))
                ->post('https://api.semaphore.co/api/v4/messages', [
                    'apikey' => $apiKey,
                    'number' => $phoneNumber,
                    'message' => $message,
                    'sendername' => $sender !== '' ? $sender : null,
                ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully.', $context + [
                    'phone_number' => $phoneNumber,
                ]);

                return true;
            }

            Log::warning('SMS send failed.', $context + [
                'phone_number' => $phoneNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('SMS send exception.', $context + [
                'phone_number' => $phoneNumber,
                'error' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    private static function formatConsultationDateTime(Consultation $consultation): string
    {
        $date = (string) $consultation->consultation_date;
        $start = (string) $consultation->consultation_time;
        $end = (string) ($consultation->consultation_end_time ?? '');

        try {
            $dateLabel = Carbon::parse($date)->format('M d, Y');
        } catch (\Throwable $exception) {
            $dateLabel = $date;
        }

        return trim($dateLabel . ' ' . $start . ($end !== '' ? '-' . $end : ''));
    }
}
