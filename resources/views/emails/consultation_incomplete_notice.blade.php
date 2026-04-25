<html>
<head>
    <meta charset="utf-8">
    <title>Consultation Incomplete Notice</title>
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
    <h1>Consultation Marked as Incomplete</h1>

    <p>
        Dear
        @if ($recipientRole === 'instructor')
            {{ $instructor->name }}
        @elseif ($recipientRole === 'admin')
            Admin
        @else
            {{ $student->name }}
        @endif
        ,
    </p>

    @if ($recipientRole === 'admin')
        <p>
            The consultation between <strong>{{ $student->name }}</strong> and
            <strong>{{ $instructor->name }}</strong> was marked as <strong>incomplete</strong>
            {{ $reasonText }}
        </p>
    @elseif ($recipientRole === 'instructor')
        <p>
            The consultation with <strong>{{ $student->name }}</strong> was marked as
            <strong>incomplete</strong> {{ $reasonText }}
        </p>
    @else
        <p>
            Your consultation with <strong>{{ $instructor->name }}</strong> was marked as
            <strong>incomplete</strong> {{ $reasonText }}
        </p>
    @endif

    <h2>Consultation Details:</h2>
    <ul>
        <li><strong>Date:</strong> {{ $consultation->consultation_date }}</li>
        <li><strong>Time:</strong> {{ $formatTime($consultation->consultation_time) }} - {{ $formatTime($consultation->consultation_end_time) }}</li>
        <li><strong>Mode:</strong> {{ $consultation->consultation_mode }}</li>
        <li><strong>Type:</strong> {{ $consultation->type_label ?? $consultation->consultation_type }}</li>
        @if ($attempts > 0)
            <li><strong>Call Attempts:</strong> {{ $attempts }}</li>
        @endif
    </ul>

    <p>
        Best regards,<br>
        ONLINE FACULTY-STUDENT CONSULTATION FOR CCS
    </p>
</body>
</html>
