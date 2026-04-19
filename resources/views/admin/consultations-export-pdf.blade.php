<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Consultations Export</title>
    <style>
        :root { color-scheme: light; --ink:#0f172a; --muted:#475569; --line:#cbd5e1; --panel:#f8fafc; --brand:#b91c1c; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:Arial,sans-serif; color:var(--ink); background:#eef2ff; }
        .page { max-width:1200px; margin:0 auto; padding:28px; }
        .toolbar { display:flex; justify-content:flex-end; gap:12px; margin-bottom:18px; }
        .toolbar button { border:0; border-radius:10px; padding:10px 14px; font-size:13px; font-weight:700; cursor:pointer; }
        .toolbar .primary { background:var(--brand); color:#fff; }
        .toolbar .secondary { background:#e2e8f0; color:var(--ink); }
        .sheet { background:#fff; border:1px solid var(--line); border-radius:18px; padding:28px; box-shadow:0 10px 30px rgba(15,23,42,.08); }
        .heading { display:flex; justify-content:space-between; gap:16px; align-items:flex-start; margin-bottom:18px; }
        .heading h1 { margin:0 0 6px; font-size:28px; }
        .heading p { margin:0; color:var(--muted); }
        .meta { text-align:right; font-size:13px; color:var(--muted); }
        .filters { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:12px; margin:18px 0 22px; }
        .filter-card { border:1px solid var(--line); border-radius:12px; padding:12px 14px; background:var(--panel); }
        .filter-label { display:block; margin-bottom:4px; color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; }
        .filter-value { font-size:14px; font-weight:700; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid var(--line); padding:10px 12px; font-size:12px; vertical-align:top; text-align:left; }
        th { background:#e2e8f0; font-size:11px; text-transform:uppercase; letter-spacing:.05em; }
        .empty { border:1px dashed var(--line); border-radius:14px; padding:22px; text-align:center; color:var(--muted); font-weight:700; background:var(--panel); }
        .footer { margin-top:18px; padding-top:14px; border-top:1px solid var(--line); color:var(--muted); font-size:12px; }
        @media print { body { background:#fff; } .page { max-width:none; padding:0; } .toolbar { display:none !important; } .sheet { border:0; border-radius:0; box-shadow:none; padding:0; } }
        @media (max-width:900px) { .filters { grid-template-columns:1fr; } .heading { flex-direction:column; } .meta { text-align:left; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="toolbar">
            <button type="button" class="secondary" onclick="window.close()">Close</button>
            <button type="button" class="primary" onclick="window.print()">Print / Save as PDF</button>
        </div>

        <div class="sheet">
            <div class="heading">
                <div>
                    <h1>All Consultations Report</h1>
                    <p>Filtered export of admin consultation records.</p>
                </div>
                <div class="meta">
                    <div>Prepared by: {{ $viewer->name ?? 'Administrator' }}</div>
                    <div>Exported at: {{ $exportedAt->format('F d, Y g:i A') }}</div>
                    <div>Total records: {{ $consultations->count() }}</div>
                </div>
            </div>

            <div class="filters">
                <div class="filter-card"><span class="filter-label">Search</span><div class="filter-value">{{ $filters['search'] !== '' ? $filters['search'] : 'All' }}</div></div>
                <div class="filter-card"><span class="filter-label">Category</span><div class="filter-value">{{ $filters['category'] !== '' ? $filters['category'] : 'All' }}</div></div>
                <div class="filter-card"><span class="filter-label">Topic</span><div class="filter-value">{{ $filters['topic'] !== '' ? $filters['topic'] : 'All' }}</div></div>
                <div class="filter-card"><span class="filter-label">Status</span><div class="filter-value">{{ $filters['status'] !== '' ? ucfirst($filters['status']) : 'All' }}</div></div>
                <div class="filter-card"><span class="filter-label">Academic Year</span><div class="filter-value">{{ $filters['academic_year'] !== '' ? $filters['academic_year'] : 'All' }}</div></div>
                <div class="filter-card"><span class="filter-label">Semester / Month</span><div class="filter-value">{{ $filters['semester'] !== 'all' && $filters['semester'] !== '' ? $filters['semester'] . ' semester' : 'All semesters' }}{{ $filters['month'] !== '' ? ' / Month ' . $filters['month'] : '' }}</div></div>
            </div>

            @if ($consultations->isEmpty())
                <div class="empty">No consultations matched the selected filters.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Student</th>
                            <th>Instructor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Category</th>
                            <th>Topic</th>
                            <th>Mode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consultations as $consultation)
                            <tr>
                                <td>{{ $consultation['code'] }}</td>
                                <td>{{ $consultation['student'] }}<br>{{ $consultation['student_id'] }}</td>
                                <td>{{ $consultation['instructor'] }}</td>
                                <td>{{ $consultation['date'] }}</td>
                                <td>{{ $consultation['time'] }}<br>{{ $consultation['duration'] }}</td>
                                <td>{{ $consultation['category'] }}</td>
                                <td>{{ $consultation['topic'] }}<br>{{ $consultation['type'] }}</td>
                                <td>{{ $consultation['mode'] }}</td>
                                <td>{{ $consultation['status'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="footer">This document reflects the consultation data currently available in the system at export time.</div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            window.setTimeout(() => window.print(), 300);
        });
    </script>
</body>
</html>
