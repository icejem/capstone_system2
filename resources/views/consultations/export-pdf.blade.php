<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consultation {{ $consultation->id }} PDF Export</title>
    <style>
        :root { color-scheme: light; --ink:#0f172a; --muted:#475569; --line:#cbd5e1; --panel:#f8fafc; --brand:#1d4ed8; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:Arial,sans-serif; color:var(--ink); background:#eef2ff; }
        .page { max-width:900px; margin:0 auto; padding:28px; }
        .toolbar { display:flex; justify-content:flex-end; gap:12px; margin-bottom:18px; }
        .toolbar button { border:0; border-radius:10px; padding:10px 14px; font-size:13px; font-weight:700; cursor:pointer; }
        .toolbar .primary { background:var(--brand); color:#fff; }
        .toolbar .secondary { background:#e2e8f0; color:var(--ink); }
        .sheet { background:#fff; border:1px solid var(--line); border-radius:18px; padding:28px; box-shadow:0 10px 30px rgba(15,23,42,.08); }
        .header { display:flex; justify-content:space-between; gap:24px; align-items:flex-start; border-bottom:2px solid var(--line); padding-bottom:18px; margin-bottom:20px; }
        .title { margin:0 0 8px; font-size:28px; font-weight:800; }
        .subtitle,.meta-note { margin:0; color:var(--muted); font-size:13px; line-height:1.6; }
        .badge { display:inline-block; padding:7px 12px; border-radius:999px; background:#dbeafe; color:#1e40af; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.08em; }
        .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; margin-bottom:22px; }
        .card { border:1px solid var(--line); border-radius:14px; padding:14px 16px; background:var(--panel); }
        .label { display:block; margin-bottom:6px; color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; }
        .value { font-size:15px; font-weight:700; line-height:1.5; white-space:pre-wrap; word-break:break-word; }
        .section { margin-top:20px; }
        .section h2 { margin:0 0 10px; font-size:17px; }
        .section-box { border:1px solid var(--line); border-radius:14px; padding:16px; background:#fff; min-height:74px; line-height:1.7; white-space:pre-wrap; word-break:break-word; }
        .footer { margin-top:22px; padding-top:14px; border-top:1px solid var(--line); color:var(--muted); font-size:12px; }
        @media print { body { background:#fff; } .page { max-width:none; padding:0; } .toolbar { display:none !important; } .sheet { border:0; border-radius:0; box-shadow:none; padding:0; } }
        @media (max-width:720px) { .grid { grid-template-columns:1fr; } .header { flex-direction:column; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="toolbar">
            <button type="button" class="secondary" onclick="window.close()">Close</button>
            <button type="button" class="primary" onclick="window.print()">Print / Save as PDF</button>
        </div>
        <div class="sheet">
            <div class="header">
                <div>
                    <h1 class="title">Consultation Details Export</h1>
                    <p class="subtitle">Consultation ID: {{ $consultation->id }}</p>
                    <p class="meta-note">Generated for {{ ucfirst((string) ($viewer->user_type ?? 'user')) }} on {{ $exportedAt->format('F d, Y g:i A') }} (Asia/Manila)</p>
                </div>
                <div class="badge">{{ $statusLabel }}</div>
            </div>
            <div class="grid">
                <div class="card"><span class="label">Student</span><div class="value">{{ $consultation->student?->name ?? 'Student' }}</div></div>
                <div class="card"><span class="label">Student ID</span><div class="value">{{ $consultation->student?->student_id ?? '--' }}</div></div>
                <div class="card"><span class="label">Instructor</span><div class="value">{{ $consultation->instructor?->name ?? 'Instructor' }}</div></div>
                <div class="card"><span class="label">Mode</span><div class="value">{{ $consultation->consultation_mode ?? '--' }}</div></div>
                <div class="card"><span class="label">Consultation Type</span><div class="value">{{ $typeLabel }}</div></div>
                <div class="card"><span class="label">Priority</span><div class="value">{{ $consultation->consultation_priority ?? '--' }}</div></div>
                <div class="card"><span class="label">Date</span><div class="value">{{ $formattedDate }}</div></div>
                <div class="card"><span class="label">Time</span><div class="value">{{ $formattedTime }}</div></div>
                <div class="card"><span class="label">Duration</span><div class="value">{{ $durationLabel }}</div></div>
                <div class="card"><span class="label">Category / Topic</span><div class="value">{{ $consultation->consultation_category ?: '--' }}@if (!empty($consultation->consultation_topic)) / {{ $consultation->consultation_topic }}@endif</div></div>
            </div>
            <div class="section">
                <h2>Student Notes</h2>
                <div class="section-box">{{ trim((string) ($consultation->student_notes ?? '')) !== '' ? $consultation->student_notes : 'No notes provided.' }}</div>
            </div>
            <div class="section">
                <h2>Consultation Summary</h2>
                <div class="section-box">{{ trim((string) ($consultation->summary_text ?? '')) !== '' ? $consultation->summary_text : 'Summary not yet available.' }}</div>
            </div>
            <div class="section">
                <h2>Action Taken</h2>
                <div class="section-box">{{ trim((string) ($consultation->transcript_text ?? '')) !== '' ? $consultation->transcript_text : 'Action taken not yet available.' }}</div>
            </div>
            <div class="footer">This document was prepared from the consultation details currently stored in the system.</div>
        </div>
    </div>
    <script>
        window.addEventListener('load', () => {
            window.setTimeout(() => window.print(), 300);
        });
    </script>
</body>
</html>
