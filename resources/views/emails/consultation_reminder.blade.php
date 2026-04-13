<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Consultation Reminder</title>
</head>
<body>
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
        <li><strong>Time:</strong> {{ substr((string) $consultation->consultation_time, 0, 5) }} - {{ substr((string) $consultation->consultation_end_time, 0, 5) }}</li>
        <li><strong>Mode:</strong> {{ $consultation->consultation_mode }}</li>
        <li><strong>Type:</strong> {{ $consultation->type_label }}</li>
        <li><strong>Recipient Role:</strong> {{ ucfirst($recipientRole) }}</li>
    </ul>

    <p>Best regards,<br>{{ config('app.name') }}</p>
</body>
</html>
