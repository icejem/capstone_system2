<html>
<head>
    <meta charset="utf-8">
    <title>Consultation Incomplete Notice</title>
</head>
<body>
    <h1>Consultation Marked as Incomplete</h1>

    <p>
        Dear {{ $recipientRole === 'instructor' ? $instructor->name : $student->name }},
    </p>

    @if ($recipientRole === 'instructor')
        <p>
            The consultation with <strong>{{ $student->name }}</strong> was automatically marked as
            <strong>incomplete</strong> because there was no answer after {{ $attempts }} call attempts.
        </p>
    @else
        <p>
            Your consultation with <strong>{{ $instructor->name }}</strong> was automatically marked as
            <strong>incomplete</strong> because there was no answer after {{ $attempts }} call attempts.
        </p>
    @endif

    <h2>Consultation Details:</h2>
    <ul>
        <li><strong>Date:</strong> {{ $consultation->consultation_date }}</li>
        <li><strong>Time:</strong> {{ substr((string) $consultation->consultation_time, 0, 5) }} - {{ substr((string) $consultation->consultation_end_time, 0, 5) }}</li>
        <li><strong>Mode:</strong> {{ $consultation->consultation_mode }}</li>
        <li><strong>Type:</strong> {{ $consultation->type_label ?? $consultation->consultation_type }}</li>
        <li><strong>Call Attempts:</strong> {{ $attempts }}</li>
    </ul>

    <p>
        Best regards,<br>
        {{ config('app.name') }}
    </p>
</body>
</html>

