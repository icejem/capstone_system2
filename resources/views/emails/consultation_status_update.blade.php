<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Consultation Status Update</title>
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
    <h1>Consultation Request {{ ucfirst($status) }}</h1>

    <p>Dear {{ $student->name }},</p>

    @if($status === 'approved')
    <p>Your consultation request has been <strong>approved</strong> by {{ $instructor->name }}.</p>
    @else
    <p>Your consultation request has been <strong>declined</strong> by {{ $instructor->name }}.</p>
    @endif

    <h2>Consultation Details:</h2>
    <ul>
        <li><strong>Instructor:</strong> {{ $instructor->name }}</li>
        <li><strong>Date:</strong> {{ $consultation->consultation_date }}</li>
        <li><strong>Time:</strong> {{ $formatTime($consultation->consultation_time) }} - {{ $formatTime($consultation->consultation_end_time) }}</li>
        <li><strong>Mode:</strong> {{ ucfirst($consultation->consultation_mode) }}</li>
        <li><strong>Type:</strong> {{ $consultation->consultation_type }}</li>
        @if($consultation->student_notes)
        <li><strong>Your Notes:</strong> {{ $consultation->student_notes }}</li>
        @endif
    </ul>

    @if($status === 'approved')
    <p>Please be prepared for your consultation at the scheduled time. If you need to make any changes, contact your instructor.</p>
    @else
    <p>You can submit a new consultation request if needed.</p>
    @endif

    <p>Best regards,<br>
    {{ config('app.name') }}</p>
</body>
</html>
