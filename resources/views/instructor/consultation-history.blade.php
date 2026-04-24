@extends('layouts.app')

@section('title', 'Consultation History')

@section('content')
@php
    $formatManilaTime = function (?string $time): string {
        if (! $time) {
            return '--:--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('h:i A');
    };
    $formatManilaTimeLower = function (?string $time): string {
        if (! $time) {
            return '--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        $formatted = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i A');
        return strtolower(str_replace(' ', '', $formatted));
    };
    $formatManilaRange = function (?string $start, ?string $end) use ($formatManilaTimeLower): string {
        if (! $start && ! $end) {
            return '--';
        }
        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');
            return $formatManilaTimeLower($start) . ' to ' . $formatManilaTimeLower($endValue);
        }
        return $formatManilaTimeLower($start) . ' to ' . $formatManilaTimeLower($end);
    };
@endphp

<style>
    :root {
        --brand: #6f42c1;
        --brand-dark: #59339d;
        --brand-soft: #efe9ff;
        --bg: #f5f7fb;
        --surface: #ffffff;
        --text: #111827;
        --muted: #6b7280;
        --border: #e5e7eb;
        --shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    body {
        background: var(--bg);
        font-family: "Segoe UI", Inter, sans-serif;
        color: var(--text);
    }

    .history-page {
        padding: 32px 28px 48px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .history-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .history-title {
        font-size: 24px;
        font-weight: 800;
        margin: 0 0 4px;
    }

    .history-subtitle {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
    }

    .export-btn {
        border: 1px solid var(--border);
        background: var(--surface);
        color: var(--text);
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .filters {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
        padding: 18px;
        display: grid;
        gap: 16px;
        margin-bottom: 18px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .filter-item label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--muted);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .filter-control {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 14px;
        background: #fff;
        color: var(--text);
    }

    .filter-search {
        display: flex;
        gap: 10px;
        align-items: center;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0 12px;
        background: #fff;
    }
.filter-search input {
        border: none;
        width: 100%;
        padding: 10px 0;
        font-size: 14px;
        outline: none;
    }

    .history-table {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .filter-toggle {
        border: 1px solid var(--border);
        background: #ffffff;
        color: #111827;
        font-weight: 700;
        font-size: 12px;
        padding: 8px 12px;
        border-radius: 10px;
        cursor: pointer;
    }
.filter-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-top: 6px;
    }

    .apply-btn {
        border: none;
        background: #4f46e5;
        color: #ffffff;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .clear-btn {
        border: 1px solid var(--border);
        background: #ffffff;
        color: #111827;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .history-row-wrap {
        position: relative;
        padding-left: 34px;
    }

    .history-row-number {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: #e0e7ff;
        color: #4338ca;
        font-weight: 800;
        font-size: 12px;
        display: grid;
        place-items: center;
    }

    .history-row {
        display: grid;
        grid-template-columns: 1.2fr 1.3fr 1.1fr 0.8fr 0.7fr 1.1fr 0.9fr;
        gap: 12px;
        align-items: center;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
    }

    .history-row.header {
        background: #f8fafc;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.4px;
        color: var(--muted);
    }

    .history-row:last-child {
        border-bottom: none;
    }

    .date-time {
        display: grid;
        gap: 4px;
    }

    .date-time span:last-child {
        color: var(--muted);
        font-size: 12px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .badge-mode {
        background: #eef2ff;
        color: #4338ca;
    }

    .badge-mode.face {
        background: #fff7ed;
        color: #c2410c;
    }

    .record-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border-radius: 8px;
        background: #ecfdf3;
        color: #047857;
        font-weight: 700;
        font-size: 11px;
        margin-right: 6px;
    }

    .record-pill.secondary {
        background: #e0f2fe;
        color: #0369a1;
    }

    .view-link {
        color: var(--brand);
        font-weight: 700;
        text-decoration: none;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 72px;
        min-height: 40px;
        padding: 10px 18px;
        border: 1px solid #2b57db;
        border-radius: 14px;
        background: linear-gradient(180deg, #4d7cff 0%, #2350de 100%);
        color: #ffffff;
        line-height: 1;
        letter-spacing: 0.01em;
        box-shadow: 0 10px 20px rgba(35, 80, 222, 0.24);
        transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, border-color 0.18s ease;
    }

    .view-link:hover,
    .view-link:focus-visible {
        color: #ffffff;
        text-decoration: none;
        transform: translateY(-1px);
        background: linear-gradient(180deg, #5b88ff 0%, #1e47cf 100%);
        border-color: #1e47cf;
        box-shadow: 0 14px 26px rgba(35, 80, 222, 0.3);
    }

    .view-link:focus-visible {
        outline: 3px solid rgba(77, 124, 255, 0.26);
        outline-offset: 2px;
    }

    .empty-state {
        padding: 30px 18px;
        text-align: center;
        color: var(--muted);
        font-size: 14px;
    }

    @media (max-width: 900px) {
        .history-row {
            grid-template-columns: 1fr;
            gap: 10px;
            align-items: flex-start;
        }
        .history-row.header {
            display: none;
        }
    }

    /* Header search responsive tweaks */
    .history-header .search-wrap { min-width: 240px; }

    @media (max-width: 720px) {
        .history-header { flex-direction: column; align-items: stretch; gap: 12px; }
        .history-header .search-wrap { width: 100%; min-width: 0; }
        .history-header .export-btn { align-self: flex-end; }
    }
</style>

<div class="history-page">
    <div class="history-header">
        <div>
            <h1 class="history-title">Consultation History</h1>
        </div>
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <div class="search-wrap">
                <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase;">Search</label>
                <div class="filter-search">
                    <span aria-hidden="true">🔍</span>
                    <input id="historySearch" type="text" name="search" placeholder="Search..." value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <button class="export-btn" type="button">Export History</button>
        </div>
    </div>

    <div class="history-table">
        <div class="history-row header">
            <div>Date & Time</div>
            <div>Instructor</div>
            <div>Type</div>
            <div>Mode</div>
            <div>Duration</div>
            <div>Records</div>
            <div>Actions</div>
        </div>

        @forelse ($consultations as $consultation)
            @php
                $modeValue = strtolower((string) $consultation->consultation_mode);
                $isFaceToFace = str_contains($modeValue, 'face');
                $duration = $consultation->duration_minutes ?? null;
            @endphp
            <div class="history-row-wrap">
                <div class="history-row-number">{{ $loop->iteration }}</div>
                <div class="history-row">
                <div class="date-time">
                    <span>{{ $consultation->consultation_date }}</span>
                    <span>{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                </div>
                <div>{{ auth()->user()->name ?? 'Instructor' }}</div>
                <div>{{ $consultation->type_label }}</div>
                <div>
                    <span class="badge badge-mode {{ $isFaceToFace ? 'face' : '' }}">
                        {{ $consultation->consultation_mode }}
                    </span>
                </div>
                <div>{{ $duration ? $duration . ' min' : '—' }}</div>
                <div>
                    @if (! $isFaceToFace)
                        <span class="record-pill secondary">Action Taken</span>
                    @endif
                    <span class="record-pill">Summary</span>
                </div>
                <div>
                    <a href="#" class="view-link">View Details</a>
                </div>
                </div>
            </div>
        @empty
            <div class="empty-state">No consultation history found.</div>
        @endforelse
    </div>
</div>
@endsection






