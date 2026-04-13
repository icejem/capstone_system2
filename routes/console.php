<?php

use App\Services\ConsultationNotificationService;
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
    $result = ConsultationNotificationService::processScheduledReminders($now);

    $this->info(
        'Consultation reminder events sent: ' . $result['events_sent'] .
        ' | Emails delivered: ' . $result['emails_sent']
    );
})->purpose('Send 30-minute and 10-minute reminder emails and notifications for approved consultations');

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
