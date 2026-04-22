<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotificationService
{
    public static function debugSend(?string $phoneNumber, string $message, array $context = []): array
    {
        $normalized = self::normalizePhoneNumber($phoneNumber);
        $provider = (string) config('services.sms.provider', 'log');
        $enabled = (bool) config('services.sms.enabled', false);

        if ($normalized === null) {
            return [
                'ok' => false,
                'stage' => 'normalize',
                'provider' => $provider,
                'enabled' => $enabled,
                'phone_number' => $phoneNumber,
                'normalized_phone_number' => null,
                'message' => 'Invalid or unsupported phone number format.',
            ];
        }

        if (! $enabled) {
            return [
                'ok' => false,
                'stage' => 'config',
                'provider' => $provider,
                'enabled' => false,
                'phone_number' => $phoneNumber,
                'normalized_phone_number' => $normalized,
                'message' => 'SMS service is disabled.',
            ];
        }

        if ($provider === 'log') {
            Log::info('SMS log delivery.', $context + [
                'phone_number' => $normalized,
                'message' => $message,
            ]);

            return [
                'ok' => true,
                'stage' => 'log',
                'provider' => 'log',
                'enabled' => true,
                'phone_number' => $phoneNumber,
                'normalized_phone_number' => $normalized,
                'message' => 'SMS logged only. No real provider request was sent.',
            ];
        }

        if ($provider === 'twilio') {
            return self::sendViaTwilio($normalized, $message, $context, true);
        }

        if ($provider === 'unisms') {
            return self::sendViaUnisms($normalized, $message, $context, true);
        }

        return [
            'ok' => false,
            'stage' => 'config',
            'provider' => $provider,
            'enabled' => true,
            'phone_number' => $phoneNumber,
            'normalized_phone_number' => $normalized,
            'message' => 'Unsupported SMS provider configured.',
        ];
    }

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
        $result = self::debugSend($phoneNumber, $message, $context);
        return (bool) ($result['ok'] ?? false);
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

    private static function sendViaTwilio(string $phoneNumber, string $message, array $context = [], bool $debug = false): array|bool
    {
        $accountSid = (string) config('services.sms.twilio.account_sid');
        $authToken = (string) config('services.sms.twilio.auth_token');
        $fromNumber = (string) config('services.sms.twilio.from_number');
        $messagingServiceSid = (string) config('services.sms.twilio.messaging_service_sid');

        if ($accountSid === '' || $authToken === '') {
            Log::warning('SMS skipped: missing Twilio credentials.', $context);
            return $debug ? [
                'ok' => false,
                'stage' => 'provider',
                'provider' => 'twilio',
                'enabled' => true,
                'normalized_phone_number' => $phoneNumber,
                'message' => 'Missing Twilio Account SID or Auth Token.',
            ] : false;
        }

        if ($fromNumber === '' && $messagingServiceSid === '') {
            Log::warning('SMS skipped: missing Twilio sender configuration.', $context);
            return $debug ? [
                'ok' => false,
                'stage' => 'provider',
                'provider' => 'twilio',
                'enabled' => true,
                'normalized_phone_number' => $phoneNumber,
                'message' => 'Missing Twilio From Number or Messaging Service SID.',
            ] : false;
        }

        try {
            $payload = [
                'To' => $phoneNumber,
                'Body' => $message,
            ];
            if ($messagingServiceSid !== '') {
                $payload['MessagingServiceSid'] = $messagingServiceSid;
            } else {
                $payload['From'] = $fromNumber;
            }

            $response = Http::asForm()
                ->timeout((int) config('services.sms.timeout', 10))
                ->withBasicAuth($accountSid, $authToken)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", $payload);

            if ($response->successful() || $response->status() === 201) {
                Log::info('SMS sent successfully.', $context + [
                    'phone_number' => $phoneNumber,
                    'response' => $response->body(),
                ]);

                return $debug ? [
                    'ok' => true,
                    'stage' => 'provider',
                    'provider' => 'twilio',
                    'enabled' => true,
                    'normalized_phone_number' => $phoneNumber,
                    'from_number' => $fromNumber,
                    'messaging_service_sid' => $messagingServiceSid,
                    'http_status' => $response->status(),
                    'response_body' => $response->body(),
                    'message' => 'SMS request accepted by Twilio.',
                ] : true;
            }

            Log::warning('SMS send failed.', $context + [
                'phone_number' => $phoneNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return $debug ? [
                'ok' => false,
                'stage' => 'provider',
                'provider' => 'twilio',
                'enabled' => true,
                'normalized_phone_number' => $phoneNumber,
                'from_number' => $fromNumber,
                'messaging_service_sid' => $messagingServiceSid,
                'http_status' => $response->status(),
                'response_body' => $response->body(),
                'message' => 'Twilio rejected the SMS request.',
            ] : false;
        } catch (\Throwable $exception) {
            Log::warning('SMS send exception.', $context + [
                'phone_number' => $phoneNumber,
                'error' => $exception->getMessage(),
            ]);
            return $debug ? [
                'ok' => false,
                'stage' => 'provider',
                'provider' => 'twilio',
                'enabled' => true,
                'normalized_phone_number' => $phoneNumber,
                'from_number' => $fromNumber,
                'messaging_service_sid' => $messagingServiceSid,
                'message' => 'SMS request threw an exception.',
                'error' => $exception->getMessage(),
            ] : false;
        }
    }

    private static function sendViaUnisms(string $phoneNumber, string $message, array $context = [], bool $debug = false): array|bool
    {
        $apiKey = (string) config('services.sms.unisms.api_key');
        $fromNumber = (string) config('services.sms.unisms.from_number');
        $apiUrl = (string) config('services.sms.unisms.api_url');

        if ($apiKey === '') {
            Log::warning('SMS skipped: missing UNIsms API key.', $context);
            return $debug ? [
                'ok' => false,
                'stage' => 'provider',
                'provider' => 'unisms',
                'enabled' => true,
                'normalized_phone_number' => $phoneNumber,
                'message' => 'Missing UNIsms API Key.',
            ] : false;
        }

        try {
            $payload = [
                'recipient' => $phoneNumber,
                'content' => $message,
            ];

            // Inference from UniSMS official examples: basic auth + JSON body is the current API shape.
            $response = Http::timeout((int) config('services.sms.timeout', 10))
                ->withBasicAuth($apiKey, '')
                ->acceptJson()
                ->asJson()
                ->post($apiUrl, $payload);

            if ($response->successful() || $response->status() === 200 || $response->status() === 201) {
                Log::info('SMS sent successfully via UNIsms.', $context + [
                    'phone_number' => $phoneNumber,
                    'response' => $response->body(),
                ]);

                return $debug ? [
                    'ok' => true,
                    'stage' => 'provider',
                    'provider' => 'unisms',
                    'enabled' => true,
                    'normalized_phone_number' => $phoneNumber,
                    'from_number' => $fromNumber !== '' ? $fromNumber : null,
                    'http_status' => $response->status(),
                    'response_body' => $response->body(),
                    'message' => 'SMS request accepted by UNIsms.',
                ] : true;
            }

            Log::warning('SMS send failed via UNIsms.', $context + [
                'phone_number' => $phoneNumber,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return $debug ? [
                'ok' => false,
                'stage' => 'provider',
                'provider' => 'unisms',
                'enabled' => true,
                'normalized_phone_number' => $phoneNumber,
                'from_number' => $fromNumber !== '' ? $fromNumber : null,
                'http_status' => $response->status(),
                'response_body' => $response->body(),
                'message' => 'UNIsms rejected the SMS request.',
            ] : false;
        } catch (\Throwable $exception) {
            Log::warning('SMS send exception via UNIsms.', $context + [
                'phone_number' => $phoneNumber,
                'error' => $exception->getMessage(),
            ]);
            return $debug ? [
                'ok' => false,
                'stage' => 'provider',
                'provider' => 'unisms',
                'enabled' => true,
                'normalized_phone_number' => $phoneNumber,
                'from_number' => $fromNumber !== '' ? $fromNumber : null,
                'message' => 'SMS request threw an exception.',
                'error' => $exception->getMessage(),
            ] : false;
        }
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

        try {
            $startLabel = Carbon::createFromFormat('H:i:s', strlen($start) === 5 ? $start . ':00' : $start, 'Asia/Manila')
                ->setTimezone('Asia/Manila')
                ->format('g:i A');
        } catch (\Throwable $exception) {
            $startLabel = $start;
        }

        if ($end !== '') {
            try {
                $endLabel = Carbon::createFromFormat('H:i:s', strlen($end) === 5 ? $end . ':00' : $end, 'Asia/Manila')
                    ->setTimezone('Asia/Manila')
                    ->format('g:i A');
            } catch (\Throwable $exception) {
                $endLabel = $end;
            }
        } else {
            $endLabel = '';
        }

        return trim($dateLabel . ' ' . $startLabel . ($endLabel !== '' ? '-' . $endLabel : ''));
    }
}
