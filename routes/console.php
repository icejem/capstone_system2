<?php

use App\Services\ConsultationNotificationService;
use App\Services\SmsNotificationService;
use App\Services\StudentSemesterAccountService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Carbon;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('user:make-admin {email : Gmail address} {--password= : Set a new password}', function () {
    $email = mb_strtolower(trim((string) $this->argument('email')));

    if ($email === '' || ! str_ends_with($email, '@gmail.com')) {
        $this->error('Email must be a valid @gmail.com address.');
        return self::FAILURE;
    }

    $password = (string) ($this->option('password') ?: '');
    if ($password === '') {
        $this->error('Missing --password option.');
        return self::FAILURE;
    }

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => 'Admin User',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'user_type' => 'admin',
            'account_status' => 'active',
            'email_verified_at' => Carbon::now(),
        ]
    );

    $user->forceFill([
        'password' => $password,
        'user_type' => 'admin',
        'account_status' => 'active',
    ])->save();

    $this->info("Admin account ready: {$user->email}");
    return self::SUCCESS;
})->purpose('Create/update an admin user and set password');

Artisan::command('consultations:send-reminders', function () {
    $now = Carbon::now('Asia/Manila');
    $result = ConsultationNotificationService::processScheduledRemindersIfDue($now);

    $this->info(
        'Consultation reminder events sent: ' . $result['events_sent'] .
        ' | Emails delivered: ' . $result['emails_sent'] .
        ' | SMS delivered: ' . ($result['sms_sent'] ?? 0) .
        ' | Skipped: ' . (($result['skipped'] ?? false) ? 'yes' : 'no')
    );
})->purpose('Send 30-minute and 10-minute reminder emails, SMS alerts, and notifications for approved consultations');

Artisan::command('sms:test {number} {message?}', function (string $number, ?string $message = null) {
    $message = $message ?: 'Test SMS from Consultation Platform.';
    $result = SmsNotificationService::debugSend($number, $message, [
        'source' => 'artisan_sms_test',
    ]);

    $this->line('Provider: ' . ($result['provider'] ?? 'unknown'));
    $this->line('Enabled: ' . ((bool) ($result['enabled'] ?? false) ? 'true' : 'false'));
    $this->line('Input Number: ' . $number);
    $this->line('Normalized Number: ' . ($result['normalized_phone_number'] ?? 'n/a'));
    if (array_key_exists('from_number', $result)) {
        $this->line('From Number: ' . (($result['from_number'] ?? '') !== '' ? $result['from_number'] : '[blank]'));
    }
    if (array_key_exists('messaging_service_sid', $result)) {
        $this->line('Messaging Service SID: ' . (($result['messaging_service_sid'] ?? '') !== '' ? $result['messaging_service_sid'] : '[blank]'));
    }
    if (array_key_exists('http_status', $result)) {
        $this->line('HTTP Status: ' . $result['http_status']);
    }
    if (! empty($result['response_body'])) {
        $this->line('Provider Response: ' . $result['response_body']);
    }
    if (! empty($result['error'])) {
        $this->error('Provider Error: ' . $result['error']);
    }

    if ($result['ok'] ?? false) {
        $this->info($result['message'] ?? 'SMS request sent successfully.');
        return self::SUCCESS;
    }

    $this->error($result['message'] ?? 'SMS request was not sent.');
    return self::FAILURE;
})->purpose('Send a direct test SMS without requiring a consultation record');

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

Schedule::call(function (): void {
    StudentSemesterAccountService::syncCurrentTermAccounts();
})
    ->name('students.sync-current-term-accounts')
    ->dailyAt('00:05')
    ->timezone('Asia/Manila')
    ->withoutOverlapping();

Schedule::command('users:unsuspend-expired')
    ->everyMinute()
    ->timezone('Asia/Manila')
    ->withoutOverlapping();
