<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Consultation Reminder</title>
</head>
<body>
    @php
        $formatTime = function ($time) {
            $value = trim((string) $time);
            if ($value === '') {
                return '--';
            }

            try {
                return \Illuminate\Support\Carbon::createFromFormat('H:i:s', strlen($value) === 5 ? $value . ':00' : $value, 'Asia/Manila')
                    ->setTimezone('Asia/Manila')
                    ->format('g:i A');
            } catch (\Throwable $e) {
                return $value;
            }
        };
    @endphp
    <h1>Consultation Reminder</h1>

    <p>Dear {{ $recipient->name }},</p>

    <p>Your consultation session will start in {{ $minutesBefore }} minutes.</p>
    <p>
        This reminder is for your upcoming consultation
        @if ($counterpart)
            with <strong>{{ $counterpart->name }}</strong>
        @endif
        .
    </p>
    <p>Please prepare in advance and make sure you are ready before the session begins.</p>
    <p>Thank you!</p>

    <h2>Session Details:</h2>
    <ul>
        <li><strong>Date:</strong> {{ $consultation->consultation_date }}</li>
        <li><strong>Time:</strong> {{ $formatTime($consultation->consultation_time) }} - {{ $formatTime($consultation->consultation_end_time) }}</li>
        <li><strong>Mode:</strong> {{ $consultation->consultation_mode }}</li>
        <li><strong>Type:</strong> {{ $consultation->type_label }}</li>
        <li><strong>Recipient Role:</strong> {{ ucfirst($recipientRole) }}</li>
    </ul>

    <p>Best regards,<br>{{ config('app.name') }}</p>
</body>
</html>
