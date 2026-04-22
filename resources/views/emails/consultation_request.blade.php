<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Consultation Request</title>
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
    <h1>New Consultation Request</h1>

    <p>Dear {{ $instructor->name }},</p>

    <p>You have received a new consultation request from <strong>{{ $student->name }}</strong>.</p>

    <h2>Consultation Details:</h2>
    <ul>
        <li><strong>Date:</strong> {{ $consultation->consultation_date }}</li>
        <li><strong>Time:</strong> {{ $formatTime($consultation->consultation_time) }} - {{ $formatTime($consultation->consultation_end_time) }}</li>
        <li><strong>Mode:</strong> {{ ucfirst($consultation->consultation_mode) }}</li>
        <li><strong>Type:</strong> {{ $consultation->consultation_type }}</li>
        @if($consultation->student_notes)
        <li><strong>Student Notes:</strong> {{ $consultation->student_notes }}</li>
        @endif
    </ul>

    <p>Please log in to your dashboard to review and respond to this request.</p>

    <p>Best regards,<br>
    {{ config('app.name') }}</p>
</body>
</html>
