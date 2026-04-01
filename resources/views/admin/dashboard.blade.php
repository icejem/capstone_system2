@extends('layouts.app')

@section('title')

@section('content')
@php
    $consultations = collect($consultations ?? []);
    $students = collect($students ?? []);
    $instructors = collect($instructors ?? []);
    $onlineStudentIds = $onlineStudentIds ?? [];
    $onlineInstructorIds = $onlineInstructorIds ?? [];
    $studentActiveMinutes = $studentActiveMinutes ?? [];
    $instructorActiveMinutes = $instructorActiveMinutes ?? [];

    $notifications = collect($notifications ?? [])
        ->map(function ($notification) {
            if (is_array($notification)) {
                return [
                    'id' => $notification['id'] ?? null,
                    'title' => $notification['title'] ?? 'Notification',
                    'message' => $notification['message'] ?? '',
                    'timestamp' => $notification['timestamp'] ?? 'Just now',
                    'read' => (bool) ($notification['read'] ?? ($notification['is_read'] ?? false)),
                ];
            }

            return [
                'id' => $notification->id ?? null,
                'title' => $notification->title ?? 'Notification',
                'message' => $notification->message ?? '',
                'timestamp' => $notification->created_at?->diffForHumans() ?? 'Just now',
                'read' => (bool) ($notification->is_read ?? false),
            ];
        })
        ->values();

    $unreadCount = $notifications->where('read', false)->count();
    $authUser = auth()->user();
    $userName = $authUser?->name ?? 'Admin';
    $rawName = trim((string) ($authUser?->name ?? ''));
    $userInitial = '';
    if ($rawName !== '') {
        $firstChar = function_exists('mb_substr') ? mb_substr($rawName, 0, 1) : substr($rawName, 0, 1);
        $userInitial = function_exists('mb_strtoupper') ? mb_strtoupper($firstChar) : strtoupper($firstChar);
    }
    if ($userInitial === '') {
        $userInitial = 'U';
    }

    $totalStudents = $consultations->pluck('student_id')->filter()->unique()->count();
    $totalInstructors = $consultations->pluck('instructor_id')->filter()->unique()->count();
    $totalConsultations = $consultations->count();
    $completedSessions = $consultations->where('status', 'completed')->count();
    $pendingConsultations = $consultations->where('status', 'pending')->count();

    $recentConsultations = $consultations
        ->sortByDesc(function ($consultation) {
            return $consultation->updated_at?->timestamp
                ?? $consultation->created_at?->timestamp
                ?? 0;
        })
        ->take(3)
        ->values();
    $formatManilaTimeMeridiem = function (?string $time): string {
        if (! $time) {
            return '--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('h:i A');
    };
    $formatManilaRangeDash = function (?string $start, ?string $end) use ($formatManilaTimeMeridiem): string {
        if (! $start && ! $end) {
            return '--';
        }
        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');
            return $formatManilaTimeMeridiem($start) . ' - ' . $formatManilaTimeMeridiem($endValue);
        }
        return $formatManilaTimeMeridiem($start) . ' - ' . $formatManilaTimeMeridiem($end);
    };
    $parseManilaDate = function (?string $date): ?\Illuminate\Support\Carbon {
        if (! $date) {
            return null;
        }
        try {
            return \Illuminate\Support\Carbon::parse($date, 'Asia/Manila');
        } catch (\Exception $e) {
            return null;
        }
    };
    $formatRelativeDay = function (?string $date) use ($parseManilaDate): string {
        $dateObj = $parseManilaDate($date);
        if (! $dateObj) {
            return 'Unknown day';
        }
        $today = \Illuminate\Support\Carbon::now('Asia/Manila')->startOfDay();
        $diffDays = $dateObj->copy()->startOfDay()->diffInDays($today, false);
        if ($diffDays === 0) {
            return 'Today';
        }
        if ($diffDays === -1) {
            return 'Tomorrow';
        }
        if ($diffDays === 1) {
            return 'Yesterday';
        }
        return $dateObj->format('M d');
    };
    $studentRows = $students->map(function ($student) use ($consultations) {
        $studentConsultations = $consultations->where('student_id', $student->id);
        $consultationCount = $studentConsultations->count();
        $status = $consultationCount > 0 ? 'active' : 'inactive';
        return [
            'id' => $student->id,
            'name' => $student->name ?? 'Student',
            'email' => $student->email ?? '',
            'student_id' => $student->student_id ?? '--',
            'joined' => $student->created_at?->format('Y-m-d') ?? '—',
            'consultations' => $consultationCount,
            'status' => $status,
        ];
    });

    $instructorRows = $instructors->map(function ($instructor) use ($consultations) {
        $instructorConsultations = $consultations->where('instructor_id', $instructor->id);
        $consultationCount = $instructorConsultations->count();
        $status = $consultationCount > 0 ? 'active' : 'inactive';

        return [
            'id' => $instructor->id,
            'name' => $instructor->name ?? 'Instructor',
            'email' => $instructor->email ?? '',
            'student_id' => $instructor->student_id ?? '--',
            'joined' => $instructor->created_at?->format('Y-m-d') ?? '—',
            'consultations' => $consultationCount,
            'status' => $status,
        ];
    });

    $consultationRows = $consultations->values()->map(function ($consultation, $index) {
        $modeValue = strtolower((string) ($consultation->consultation_mode ?? ''));
        $statusValue = strtolower((string) ($consultation->status ?? ''));
        $isOnline = str_contains($modeValue, 'audio') || str_contains($modeValue, 'video') || str_contains($modeValue, 'call');

        // Build a human readable time range (e.g. "8:00 am to 9:00 am").
        $startRaw = (string) ($consultation->consultation_time ?? '');
        $endRaw = (string) ($consultation->consultation_end_time ?? '');
        $timeRange = '';
        try {
            if (trim($startRaw) !== '') {
                $start = \Carbon\Carbon::parse($startRaw)->format('g:i a');
            } else {
                $start = null;
            }

            if (trim($endRaw) !== '') {
                $end = \Carbon\Carbon::parse($endRaw)->format('g:i a');
            } elseif (!empty($start)) {
                // Fallback: assume 1 hour duration if end time missing
                $end = \Carbon\Carbon::parse($startRaw)->addHour()->format('g:i a');
            } else {
                $end = null;
            }

            if ($start && $end) {
                $timeRange = $start . ' to ' . $end;
            } elseif ($start) {
                $timeRange = $start;
            }
        } catch (\Throwable $e) {
            // If parsing fails, fall back to raw substrings
            $s = substr($startRaw, 0, 5);
            $e = substr($endRaw, 0, 5);
            $timeRange = $e ? ($s . ' to ' . $e) : $s;
        }

        $durationLabel = '—';
        try {
            if ($consultation->duration_minutes !== null && $consultation->duration_minutes !== '') {
                $durationLabel = (int) $consultation->duration_minutes . ' min';
            } elseif (trim($startRaw) !== '' && trim($endRaw) !== '') {
                $durationMinutes = \Carbon\Carbon::parse($endRaw)->diffInMinutes(\Carbon\Carbon::parse($startRaw));
                $durationLabel = $durationMinutes . ' min';
            } elseif (trim($startRaw) !== '') {
                $durationLabel = '60 min';
            }
        } catch (\Throwable $e) {
            // Keep duration fallback.
        }

        return [
            'code' => 'C' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
            'student' => $consultation->student?->name ?? 'Student',
            'student_id' => $consultation->student?->student_id ?? '--',
            'instructor' => $consultation->instructor?->name ?? 'Instructor',
            'date' => (string) ($consultation->consultation_date ?? '—'),
            'time_range' => $timeRange,
            'duration' => $durationLabel,
            'type' => $consultation->type_label ?? ($consultation->consultation_type ?? 'Consultation'),
            'mode' => $consultation->consultation_mode ?? '—',
            'status' => $statusValue ?: 'pending',
            'summary' => (string) ($consultation->summary_text ?? ''),
            'action_taken' => (string) ($consultation->transcript_text ?? ''),
        ];
    });

    $statisticsRows = $consultations->values()->map(function ($consultation) {
        return [
            'date' => (string) ($consultation->consultation_date ?? ''),
            'type' => (string) ($consultation->type_label ?? ($consultation->consultation_type ?? 'Consultation')),
            'category' => (string) ($consultation->consultation_category ?? ''),
            'topic' => (string) ($consultation->consultation_topic ?? ($consultation->consultation_type ?? '')),
            'status' => strtolower((string) ($consultation->status ?? '')),
            'mode' => (string) ($consultation->consultation_mode ?? ''),
            'student' => (string) ($consultation->student?->name ?? 'Student'),
            'instructor' => (string) ($consultation->instructor?->name ?? 'Instructor'),
        ];
    })->filter(function ($row) {
        return trim((string) ($row['date'] ?? '')) !== '';
    })->values();

    $userName = auth()->user()->name ?? 'Administrator';
    $userEmail = auth()->user()->email ?? 'admin@example.com';
@endphp

<style>
    :root {
        --brand: #1F3A8A;
        --brand-dark: #1e40af;
        --brand-soft: #dbeafe;
        --secondary: #4A90E2;
        --bg: #f0f4f9;
        --surface: #ffffff;
        --text: #1f2937;
        --muted: #6b7280;
        --border: #e5e7eb;
        --shadow: 0 14px 32px rgba(31, 58, 138, 0.12);
    }

    @keyframes slideInLeft {
        from { transform: translateX(-100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideInTop {
        from { transform: translateY(-100%); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes popIn {
        0% { transform: scale(0.95); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    * { box-sizing: border-box; }

    body {
        margin: 0;
        font-family: "Inter", "Segoe UI", Tahoma, sans-serif;
        background: var(--bg);
        color: var(--text);
    }

    .dashboard {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 250px;
        background: linear-gradient(180deg, #1F3A8A 0%, #1e40af 100%);
        box-shadow: 2px 0 14px rgba(31, 58, 138, 0.1);
        padding: 24px 0;
        position: fixed;
        inset: 0 auto 0 0;
        z-index: 20;
        display: flex;
        flex-direction: column;
        animation: slideInLeft 0.5s ease-out;
        transition: transform 0.25s ease;
    }

    .sidebar-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.34);
        backdrop-filter: blur(3px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
        z-index: 140;
    }

    .sidebar-backdrop.active {
        opacity: 1;
        pointer-events: auto;
    }

    .sidebar.collapsed {
        transform: translateX(-100%);
    }

    .sidebar.collapsed + .main {
        margin-left: 0;
    }

    .sidebar.icon-only {
        width: 86px;
        align-items: center;
        padding-top: 20px;
    }

    .sidebar.icon-only + .main {
        margin-left: 86px;
    }

    .sidebar.icon-only .sidebar-logo {
        justify-content: center;
        width: 100%;
        padding: 0 12px;
        margin-bottom: 22px;
    }

    .sidebar.icon-only .sidebar-logo .secondary-logo {
        display: none;
    }

    .sidebar.icon-only .sidebar-logo-text {
        display: none;
    }

    .sidebar.icon-only .sidebar-menu-link {
        width: 58px;
        min-height: 44px;
        justify-content: center;
        gap: 0;
        padding: 10px 0;
        margin: 8px auto;
        border-left-width: 0;
        border-radius: 12px;
        font-size: 0;
    }

    .sidebar.icon-only .sidebar-menu-link i {
        width: auto;
        margin: 0;
        font-size: 18px;
    }

    .sidebar.icon-only .sidebar-menu-link:hover,
    .sidebar.icon-only .sidebar-menu-link.active {
        padding-left: 0;
        border-left-color: transparent;
    }

    .sidebar.icon-only .sidebar-logout {
        display: none;
    }


    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 20px;
        margin-bottom: 32px;
        text-decoration: none;
        color: #ffffff;
        animation: fadeIn 0.6s ease-out 0.2s backwards;
    }

    .logo-badge {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: transparent;
        color: #fff;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 16px;
        box-shadow: none;
        animation: popIn 0.5s ease-out 0.3s backwards;
        overflow: hidden;
    }

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
        border-radius: 50%;
    }

    .sidebar-logo-text {
        display: inline-flex;
        flex-direction: column;
        line-height: 1.1;
        gap: 2px;
        font-size: 14px;
        font-weight: 700;
        color: #ffffff;
    }

    .sidebar-logo-main {
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.2px;
    }

    .sidebar-logo-sub {
        font-size: 11px;
        font-weight: 600;
        opacity: 0.95;
    }

    .sidebar-menu {
        list-style: none;
        margin: 0;
        padding: 0;
        flex: 1;
    }

    .sidebar-menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 14px;
        border-left: 4px solid transparent;
        border-radius: 0 12px 12px 0;
        margin: 4px 0;
        transition: all 0.3s ease;
        animation: slideInLeft 0.5s ease-out backwards;
    }

    .sidebar-menu-link i {
        width: 18px;
        text-align: center;
        font-size: 14px;
    }

    .sidebar-menu-link:nth-child(1) { animation-delay: 0.35s; }
    .sidebar-menu-link:nth-child(2) { animation-delay: 0.4s; }
    .sidebar-menu-link:nth-child(3) { animation-delay: 0.45s; }

    .sidebar-menu-link:hover,
    .sidebar-menu-link.active {
        background: rgba(74, 144, 226, 0.25);
        color: #ffffff;
        border-left-color: #4A90E2;
        font-weight: 700;
        padding-left: 24px;
    }

    .sidebar-logout {
        padding: 16px 20px;
        border-top: 1px solid var(--border);
    }

    .logout-btn {
        width: 100%;
        border: 2px solid #4A90E2;
        background: transparent;
        color: #4A90E2;
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .logout-btn:hover {
        background: #4A90E2;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(74, 144, 226, 0.3);
    }

    .main {
        margin-left: 250px;
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .topbar {
        background: linear-gradient(180deg, #f0f9ff, #dbeafe);
        border-bottom: 1px solid #bfdbfe;
        box-shadow: 0 6px 18px rgba(31, 58, 138, 0.08);
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 15;
        animation: slideInTop 0.5s ease-out;
        display: none;
    }

    .menu-btn {
        display: none;
        border: 1px solid var(--border);
        background: #fff;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        align-items: center;
        gap: 6px;
    }

    .menu-btn:hover {
        background: #dbeafe;
        color: #1F3A8A;
    }

    .topbar-title {
        font-size: 20px;
        font-weight: 800;
    }

    .topbar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 40;
    }

    .notification-wrap {
        position: relative;
    }

    .notification-btn {
        width: 40px;
        height: 40px;
        border: 1px solid rgba(255, 255, 255, 0.45);
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        border-radius: 12px;
        padding: 0;
        font-size: 16px;
        cursor: pointer;
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .notification-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: #fff;
        border-radius: 999px;
        min-width: 18px;
        height: 18px;
        font-size: 9px;
        font-weight: 800;
        padding: 0 5px;
        border: 2px solid #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .notification-panel {
        position: absolute;
        top: 52px;
        right: 0;
        width: 340px;
        max-height: 420px;
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 20px 40px rgba(31, 58, 138, 0.25);
        display: none;
        flex-direction: column;
        overflow: hidden;
        z-index: 120;
        animation: slideInTop 0.3s ease-out;
    }

    .notification-panel.active { display: flex; }

    .notification-header {
        padding: 12px 14px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 700;
    }

    .notification-list {
        padding: 0;
        list-style: none;
        margin: 0;
        overflow-y: auto;
        max-height: 320px;
    }

    .notification-item {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f1f1;
        font-size: 13px;
        transition: background-color 0.3s ease;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .notification-item.unread {
        background: #f0f9ff;
    }

    .notification-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: var(--brand);
        margin-top: 6px;
        flex-shrink: 0;
    }

    .admin-notif-toast {
        position: fixed;
        top: 88px;
        right: 24px;
        width: min(360px, calc(100vw - 32px));
        background: linear-gradient(135deg, #1f3a8a 0%, #1d4ed8 100%);
        color: #ffffff;
        border-radius: 18px;
        box-shadow: 0 22px 40px rgba(29, 78, 216, 0.28);
        padding: 16px 18px;
        z-index: 200;
        opacity: 0;
        pointer-events: none;
        transform: translateY(-10px);
        transition: opacity 0.25s ease, transform 0.25s ease;
    }

    .admin-notif-toast.show {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0);
    }

    .admin-notif-toast-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .admin-notif-toast-title {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
    }

    .admin-notif-toast-body {
        margin: 6px 0 0;
        font-size: 13px;
        line-height: 1.45;
        color: rgba(255, 255, 255, 0.92);
    }

    .admin-notif-toast-close {
        border: none;
        background: transparent;
        color: #ffffff;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }

    .profile {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        position: relative;
        z-index: 40;
    }

    .profile > .relative {
        position: relative;
    }

    .profile .absolute.z-50 {
        margin-top: 10px;
        min-width: 168px;
        z-index: 130;
    }

    .profile .rounded-md.ring-1 {
        background: #ffffff;
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.18);
    }

    .profile .rounded-md.ring-1 a {
        display: block;
        width: 100%;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }

    .dashboard-header-copy {
        min-width: 0;
    }

    .dashboard-header-title {
        margin: 0;
        font-size: clamp(24px, 2.3vw, 38px);
        line-height: 1.06;
        font-weight: 800;
        color: #ffffff;
        text-shadow: 0 2px 10px rgba(15, 23, 42, 0.45);
    }

    .dashboard-header-subtitle {
        margin: 6px 0 0;
        font-size: 13px;
        color: #e2e8f0;
        font-weight: 600;
    }

    .header-profile-trigger {
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 12px;
        background: #ffffff;
        color: #1F3A8A;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.18);
    }

    .header-profile-trigger:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.24);
    }

    .header-avatar {
        font-size: 16px;
        font-weight: 800;
        line-height: 1;
    }

    .profile-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        color: #fff;
        display: grid;
        place-items: center;
        font-weight: 800;
    }

    .profile-name {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
    }

    .profile-email {
        font-size: 12px;
        color: var(--muted);
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }

    .content {
        padding: 22px;
    }

    .content-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
        position: relative;
        z-index: 20;
        overflow: visible;
        background: url('{{ asset('head1.JPG') }}') center/cover no-repeat;
        border: 1px solid rgba(59, 130, 246, 0.34);
        border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.22);
    }

    .content-header::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(31, 58, 138, 0.34) 0%, rgba(30, 64, 175, 0.3) 100%);
        pointer-events: none;
    }

    .content-header > * {
        position: relative;
        z-index: 1;
    }

    .hero {
        border-radius: 16px;
        background: linear-gradient(120deg, #2e3b59, #1f2a44);
        color: #fff;
        padding: 20px;
        margin-bottom: 18px;
    }

    .hero-title {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 12px;
    }

    .hero-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .hero-tab {
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 8px 14px;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }

    .hero-tab.active {
        background: #fff;
        color: #1f2a44;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 12px 28px rgba(17, 24, 39, 0.08);
        padding: 20px;
        border-top: 4px solid #4A90E2;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: popIn 0.5s ease-out backwards;
        display: flex;
        gap: 16px;
        align-items: center;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.15s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.25s; }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px rgba(31, 58, 138, 0.14);
    }

    .stat-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 18px;
        line-height: 1;
        font-weight: 800;
        flex-shrink: 0;
    }

    .stat-icon i {
        font-size: 20px;
        line-height: 1;
    }

    .stat-chip {
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.1px;
    }

    .chip-green { background: #ecfdf5; color: #047857; }
    .chip-orange { background: #fff7ed; color: #c2410c; }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
    }

    .stat-label {
        margin-top: 4px;
        font-size: 14px;
        color: var(--muted);
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .panel-head {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        font-size: 28px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .panel-head .view-link {
        font-size: 14px;
        text-decoration: none;
        color: #0f766e;
        font-weight: 700;
    }

    .admin-recent-panel {
        background: #f3f4f6;
        border: 1px solid #d8dde6;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        padding: 14px;
        overflow: visible;
    }

    .admin-recent-panel .overview-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        gap: 12px;
    }

    .admin-recent-panel .overview-panel-title {
        margin: 0;
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        color: #111827;
    }

    .admin-recent-panel .overview-panel-link {
        border: none;
        background: none;
        padding: 0;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        color: #66b8c7;
        text-decoration: none;
        white-space: nowrap;
    }

    .admin-recent-panel .overview-panel-link:hover {
        text-decoration: underline;
    }

    .admin-recent-panel .overview-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        font-size: 13px;
        color: #64748b;
        background: #f8fafc;
    }

    .admin-recent-panel .recent-list {
        display: grid;
        gap: 12px;
    }

    .admin-recent-panel .recent-item {
        background: linear-gradient(180deg, #22408f 0%, #1f3a8a 100%);
        border: 1px solid #1f3a8a;
        border-radius: 12px;
        padding: 14px 12px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    .admin-recent-panel .recent-item-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .admin-recent-panel .recent-item-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #f8fafc;
    }

    .admin-recent-panel .recent-item-meta {
        margin-top: 6px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12px;
        color: #dbeafe;
        font-weight: 700;
    }

    .admin-recent-panel .recent-status-pill {
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: capitalize;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .admin-recent-panel .recent-status-pill.status-approved { background: #23b05f; color: #f0fdf4; border-color: #1a9a53; }
    .admin-recent-panel .recent-status-pill.status-pending { background: #f59e0b; color: #fff7ed; border-color: #ea8a00; }
    .admin-recent-panel .recent-status-pill.status-completed { background: #dbe7ff; color: #1e3a8a; border-color: #bcd0ff; }
    .admin-recent-panel .recent-status-pill.status-in_progress { background: #c7d2fe; color: #3730a3; border-color: #a8b8ff; }
    .admin-recent-panel .recent-status-pill.status-incompleted { background: #fef3c7; color: #92400e; border-color: #f59e0b; }
    .admin-recent-panel .recent-status-pill.status-declined,
    .admin-recent-panel .recent-status-pill.status-cancelled { background: #f97366; color: #fff1f2; border-color: #ef5b4b; }

    .consultation-item {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        display: flex;
        gap: 12px;
        justify-content: space-between;
    }

    .consultation-item:last-child { border-bottom: none; }

    .consultation-main {
        min-width: 0;
    }

    .consultation-student {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .consultation-sub,
    .consultation-date {
        color: var(--muted);
        font-size: 13px;
    }

    .status-pill {
        align-self: center;
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-pending { background: #fff7ed; color: #c2410c; }
    .status-approved { background: #ecfdf5; color: #047857; }
    .status-in-progress { background: #dbeafe; color: #1d4ed8; }
    .status-completed { background: #e0e7ff; color: #4338ca; }
    .status-incompleted { background: #fef3c7; color: #92400e; }
    .status-declined { background: #fee2e2; color: #b91c1c; }
    .status-default { background: #f3f4f6; color: #374151; }

    .overview-list {
        padding: 8px 12px 12px;
    }

    .overview-item {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 6px;
        border-bottom: 1px solid var(--border);
        align-items: center;
    }

    .overview-item:last-child { border-bottom: none; }

    .overview-title {
        font-weight: 700;
        margin-bottom: 2px;
    }

    .overview-sub {
        color: var(--muted);
        font-size: 13px;
    }

    .overview-state {
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .state-green { background: #ecfdf5; color: #047857; }
    .state-orange { background: #fff7ed; color: #c2410c; }
    .state-blue { background: #eff6ff; color: #1d4ed8; }

    .is-hidden {
        display: none;
    }

    #overviewSection.statistics-only > :not(#statistics) {
        display: none !important;
    }

    #overviewSection.statistics-only #statistics {
        margin-top: 0;
    }

    .students-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .students-head {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .students-title {
        font-size: 18px;
        font-weight: 800;
    }

    .students-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-close-btn {
        width: 34px;
        height: 34px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #fff;
        color: #334155;
        font-size: 20px;
        line-height: 1;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .section-close-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .consultations-head-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .consultations-head {
        padding: 18px 20px 16px;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }

    .consultations-title {
        font-size: 34px;
        line-height: 1.1;
        font-weight: 900;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.3px;
    }

    .consultations-subtitle {
        margin: 6px 0 0;
        font-size: 14px;
        color: #475569;
        font-weight: 600;
    }

    .consultations-filter-card {
        margin-top: 16px;
        padding: 14px;
        border: 1px solid #d6dbe5;
        border-radius: 12px;
        background: #f8fafc;
    }

    .consultations-filter-top {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .consultations-filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .consultation-filter-group {
        min-width: 0;
    }

    .consultation-filter-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 800;
        color: #334155;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }

    .consultation-filter-group .students-search,
    .consultation-filter-group .students-filter {
        width: 100%;
        min-width: 0;
    }

    .consultation-semester-toggle {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px;
        border-radius: 10px;
        border: 1px solid #d6dbe5;
        background: #eceff4;
    }

    .consultation-semester-btn {
        border: 1px solid transparent;
        background: transparent;
        color: #4b5563;
        padding: 9px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .consultation-semester-btn.active {
        background: #fff;
        border-color: #d6dbe5;
        color: var(--brand);
        box-shadow: 0 2px 8px rgba(31, 58, 138, 0.12);
    }

    .consultation-search-input {
        padding-left: 12px;
    }

    .stats-workspace {
        margin-top: 18px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
        padding: 14px;
    }

    .stats-filter-card {
        border: 1px solid #d6dbe5;
        border-radius: 12px;
        background: #f8fafc;
        padding: 12px;
    }

    .stats-filter-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .stats-filter-title {
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: 0.2px;
    }

    .stats-export-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .stats-export-btn {
        border: none;
        border-radius: 8px;
        padding: 7px 11px;
        font-size: 11px;
        font-weight: 800;
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stats-export-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.18);
    }

    .stats-export-pdf { background: linear-gradient(135deg, #dc2626, #ef4444); }
    .stats-export-excel { background: linear-gradient(135deg, #15803d, #22c55e); }

    .stats-filter-grid {
        display: grid;
        grid-template-columns: minmax(260px, 300px) repeat(3, minmax(180px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .stats-filter-group {
        min-width: 0;
    }

    .stats-filter-label {
        display: block;
        margin-bottom: 6px;
        font-size: 10px;
        color: #475569;
        font-weight: 800;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }

    .stats-semester-toggle {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 4px;
        background: #eceff4;
        border: 1px solid #d6dbe5;
        border-radius: 10px;
        padding: 5px;
        width: 100%;
    }

    .stats-semester-btn {
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 10px 14px;
        background: transparent;
        color: #4b5563;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s ease;
    }

    .stats-semester-btn.active {
        background: #fff;
        border-color: #d6dbe5;
        color: var(--brand);
        box-shadow: 0 2px 8px rgba(31, 58, 138, 0.12);
    }

    .stats-filter-select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        min-height: 42px;
        padding: 8px 12px;
        font-size: 12px;
        color: #1f2937;
        background: #fff;
        font-weight: 700;
    }

    .stats-metric-grid {
        margin-top: 12px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .stats-metric-card {
        border-radius: 12px;
        padding: 13px 14px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .stats-metric-card::after {
        content: "";
        position: absolute;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        right: -30px;
        top: -35px;
        background: rgba(255, 255, 255, 0.14);
    }

    .stats-metric-card.consultations { background: linear-gradient(135deg, #0ea5a4, #14b8a6); }
    .stats-metric-card.types { background: linear-gradient(135deg, #059669, #10b981); }
    .stats-metric-card.period { background: linear-gradient(135deg, #0ea5e9, #06b6d4); }

    .stats-metric-label {
        font-size: 10px;
        font-weight: 700;
        opacity: 0.92;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 7px;
    }

    .stats-metric-value {
        font-size: 26px;
        font-weight: 900;
        line-height: 1.05;
    }

    .stats-metric-subvalue {
        font-size: 18px;
        font-weight: 900;
        line-height: 1.1;
    }

    .stats-distribution {
        margin-top: 12px;
        border: 1px solid #dbe3ec;
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
    }

    .stats-distribution-head {
        padding: 12px 14px;
        border-bottom: 1px solid #e5eaf1;
        background: #f8fafc;
    }

    .stats-distribution-title {
        font-size: 14px;
        font-weight: 900;
        color: #0f172a;
    }

    .stats-distribution-subtitle {
        margin-top: 2px;
        font-size: 11px;
        color: #64748b;
        font-weight: 700;
    }

    .stats-distribution-body {
        display: grid;
        grid-template-columns: 1fr;
        gap: 18px;
        padding: 18px;
    }

    .stats-bar-summary {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid #dbe3ec;
        border-radius: 12px;
        background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%);
    }

    .stats-bar-summary-label {
        font-size: 12px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .stats-donut-total {
        font-size: 32px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
    }

    .stats-bar-chart {
        display: grid;
        gap: 16px;
    }

    .stats-bar-chart-empty {
        padding: 18px 16px;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        text-align: center;
    }

    .stats-bar-row {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr) 60px;
        gap: 14px;
        align-items: center;
    }

    .stats-bar-label {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        line-height: 1.35;
    }

    .stats-bar-track {
        position: relative;
        height: 32px;
        border-radius: 12px;
        overflow: hidden;
        background:
            repeating-linear-gradient(
                to right,
                rgba(148, 163, 184, 0.12) 0,
                rgba(148, 163, 184, 0.12) 1px,
                transparent 1px,
                transparent calc(20% - 1px)
            ),
            #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .stats-bar-fill {
        height: 100%;
        border-radius: 11px;
        min-width: 10px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22);
    }

    .stats-bar-value {
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        text-align: right;
    }

    .stats-empty {
        padding: 30px 12px;
        text-align: center;
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
    }

    .students-search,
    .students-filter {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 9px 12px;
        font-size: 14px;
        background: #fff;
        color: var(--text);
    }

    .students-btn {
        border: none;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        color: #fff;
    }

    .students-search {
        min-width: 250px;
    }

    .students-table {
        width: 100%;
        border-collapse: collapse;
    }

    .students-table thead th {
        text-align: left;
        font-size: 12px;
        color: #64748b;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        padding: 12px 20px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
    }

    .students-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid var(--border);
        font-size: 14px;
        vertical-align: middle;
    }

    .students-table tbody tr {
        transition: all 0.3s ease;
        animation: fadeIn 0.5s ease-out backwards;
    }

    .students-table tbody tr:nth-child(1) { animation-delay: 0.1s; }
    .students-table tbody tr:nth-child(2) { animation-delay: 0.15s; }
    .students-table tbody tr:nth-child(3) { animation-delay: 0.2s; }
    .students-table tbody tr:nth-child(4) { animation-delay: 0.25s; }
    .students-table tbody tr:nth-child(5) { animation-delay: 0.3s; }

    .students-table tbody tr:hover {
        background: #f0f9ff;
        box-shadow: inset 0 0 8px rgba(74, 144, 226, 0.1);
    }

    .students-table tbody tr:last-child td {
        border-bottom: none;
    }

    .student-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #d1fae5;
        color: #0f766e;
        display: grid;
        place-items: center;
        font-weight: 800;
    }

    .student-name {
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .student-email {
        font-size: 13px;
        color: var(--muted);
    }

    .status-tag {
        display: inline-block;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .status-active { background: #d1fae5; color: #047857; }
    .status-inactive { background: #e5e7eb; color: #374151; }
    .status-suspended { background: #fee2e2; color: #b91c1c; }

    .manage-link {
        color: #0f766e;
        font-weight: 700;
        text-decoration: none;
    }

    .manage-label-mobile {
        display: none;
    }

    .manage-modal {
        position: fixed;
        inset: 0;
        z-index: 90;
        background: rgba(15, 23, 42, 0.52);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .manage-modal.open { display: flex; }

    .manage-dialog {
        width: 100%;
        max-width: 430px;
        background: linear-gradient(165deg, rgba(6, 23, 52, 0.96), rgba(8, 37, 84, 0.95));
        border: 1px solid rgba(125, 211, 252, 0.34);
        border-radius: 16px;
        box-shadow: 0 28px 64px rgba(0, 10, 24, 0.6);
        color: #e7f6ff;
        overflow: hidden;
        animation: popIn 0.5s ease-out;
        backdrop-filter: blur(8px);
    }

    .manage-head {
        padding: 12px 14px;
        border-bottom: 1px solid rgba(125, 211, 252, 0.22);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(4, 16, 36, 0.32);
    }

    .manage-title {
        font-size: 17px;
        font-weight: 800;
        color: #ecf8ff;
        letter-spacing: 0.02em;
    }

    .manage-close {
        width: 30px;
        height: 30px;
        border: 1px solid rgba(125, 211, 252, 0.34);
        border-radius: 10px;
        background: rgba(7, 23, 48, 0.74);
        color: #b7dcf1;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .manage-close:hover {
        color: #ecf8ff;
        border-color: rgba(125, 211, 252, 0.62);
        background: rgba(9, 30, 61, 0.92);
    }

    .manage-body {
        padding: 14px 14px 16px;
    }

    .manage-user {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .manage-user > div {
        min-width: 0;
    }

    .manage-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: rgba(125, 211, 252, 0.22);
        color: #99e8ff;
        border: 1px solid rgba(125, 211, 252, 0.35);
        display: grid;
        place-items: center;
        font-size: 22px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .manage-name {
        font-size: 16px;
        font-weight: 700;
        color: #ecf8ff;
        letter-spacing: 0.01em;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .manage-email {
        color: #a8ccdf;
        font-size: 13px;
    }

    .manage-meta {
        color: #8fb4c9;
        font-size: 12px;
        margin-top: 2px;
    }

    .manage-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(125, 211, 252, 0.16);
        font-size: 13px;
    }

    .manage-row:last-of-type {
        border-bottom: none;
    }

    .manage-row-label {
        color: #a8ccdf;
    }

    .manage-row-value {
        font-weight: 700;
        color: #ecf8ff;
    }

    .manage-actions-label {
        font-size: 12px;
        font-weight: 700;
        margin: 10px 0 8px;
        color: #b9def2;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .manage-actions {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 6px;
    }

    .manage-dialog .status-tag {
        padding: 4px 10px;
        font-size: 11px;
        border: 1px solid transparent;
    }

    .manage-dialog .status-active {
        background: rgba(34, 197, 94, 0.18);
        color: #bbf7d0;
        border-color: rgba(74, 222, 128, 0.36);
    }

    .manage-dialog .status-inactive {
        background: rgba(148, 163, 184, 0.2);
        color: #dbeafe;
        border-color: rgba(148, 163, 184, 0.35);
    }

    .manage-dialog .status-suspended {
        background: rgba(239, 68, 68, 0.2);
        color: #fecaca;
        border-color: rgba(248, 113, 113, 0.35);
    }

    .manage-dialog .manage-status-btn {
        border: 1px solid rgba(125, 211, 252, 0.24);
        border-radius: 10px;
        padding: 9px 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        background: rgba(7, 23, 48, 0.76);
        color: #d8f2ff;
        transition: transform 0.2s ease, filter 0.2s ease, box-shadow 0.2s ease;
    }

    .manage-dialog .manage-status-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
        box-shadow: 0 10px 22px rgba(2, 132, 199, 0.28);
    }

    .manage-dialog .manage-status-btn.activate {
        background: linear-gradient(135deg, #2563eb 55%, #1d4ed8);
        color: #ffffff;
        border-color: rgba(96, 165, 250, 0.55);
    }

    .manage-dialog .manage-status-btn.deactivate {
        background: linear-gradient(135deg, #4338ca, #5b21b6);
        color: #ede9fe;
        border-color: rgba(139, 92, 246, 0.5);
    }

    .manage-dialog .manage-status-btn.suspend {
        background: linear-gradient(135deg, #991b1b, #b91c1c);
        color: #fee2e2;
        border-color: rgba(248, 113, 113, 0.5);
    }

    .add-modal {
        position: fixed;
        inset: 0;
        z-index: 95;
        background: rgba(15, 23, 42, 0.52);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .add-modal.open { display: flex; }

    .add-dialog {
        width: 100%;
        max-width: 520px;
        background: linear-gradient(165deg, rgba(6, 23, 52, 0.96), rgba(8, 37, 84, 0.95));
        border: 1px solid rgba(125, 211, 252, 0.34);
        border-radius: 16px;
        box-shadow: 0 28px 64px rgba(0, 10, 24, 0.62);
        color: #e7f6ff;
        overflow: hidden;
        animation: popIn 0.5s ease-out;
        backdrop-filter: blur(8px);
        display: flex;
        flex-direction: column;
        max-height: min(92vh, 680px);
    }

    .add-head {
        padding: 12px 14px;
        border-bottom: 1px solid rgba(125, 211, 252, 0.22);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(4, 16, 36, 0.32);
    }

    .add-title {
        font-size: 17px;
        font-weight: 800;
        color: #ecf8ff;
        letter-spacing: 0.02em;
    }

    .add-close {
        width: 30px;
        height: 30px;
        border: 1px solid rgba(125, 211, 252, 0.34);
        border-radius: 10px;
        background: rgba(7, 23, 48, 0.74);
        color: #b7dcf1;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .add-close:hover {
        color: #ecf8ff;
        border-color: rgba(125, 211, 252, 0.62);
        background: rgba(9, 30, 61, 0.92);
    }

    .add-body {
        padding: 14px 14px 16px;
        overflow-y: auto;
    }

    .add-form-grid {
        display: grid;
        gap: 10px;
    }

    .add-form-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .add-form-row.single { grid-template-columns: 1fr; }

    .add-label {
        font-size: 12px;
        font-weight: 700;
        color: #b9def2;
        display: block;
        margin-bottom: 6px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .add-input {
        width: 100%;
        padding: 11px 12px;
        border-radius: 10px;
        border: 1px solid rgba(125, 211, 252, 0.34);
        font-size: 13px;
        outline: none;
        background: rgba(7, 23, 48, 0.76);
        color: #e8f8ff;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .add-input::placeholder {
        color: #7fa5bf;
    }

    .add-input:focus {
        border-color: #38bdf8;
        background: rgba(9, 30, 61, 0.9);
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.2);
    }

    .add-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin-top: 12px;
    }

    .add-dialog .manage-status-btn {
        border: 1px solid rgba(125, 211, 252, 0.24);
        border-radius: 10px;
        padding: 10px 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        background: rgba(7, 23, 48, 0.76);
        color: #d8f2ff;
        transition: transform 0.2s ease, filter 0.2s ease, box-shadow 0.2s ease;
    }

    .add-dialog .manage-status-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
        box-shadow: 0 10px 22px rgba(2, 132, 199, 0.28);
    }

    .add-dialog .manage-status-btn.activate {
        background: linear-gradient(135deg, #2563eb 55%, #1d4ed8);
        color: #ffffff;
        border-color: rgba(96, 165, 250, 0.55);
    }

    .add-dialog .manage-status-btn.suspend {
        background: linear-gradient(135deg, #991b1b, #b91c1c);
        color: #fee2e2;
        border-color: rgba(248, 113, 113, 0.5);
    }

    .add-alert {
        background: rgba(239, 68, 68, 0.18);
        color: #fecaca;
        border: 1px solid rgba(248, 113, 113, 0.4);
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 12px;
        margin-bottom: 12px;
    }

    @media (max-width: 560px) {
        .add-form-row {
            grid-template-columns: 1fr;
        }
    }

    .mode-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .mode-audio { background: #ccfbf1; color: #0f766e; }
    .mode-video { background: #dbeafe; color: #1d4ed8; }
    .mode-face { background: #f3e8ff; color: #7e22ce; }
    .mode-default { background: #f1f5f9; color: #334155; }

    .action-cell {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .action-view {
        color: #0f766e;
        font-weight: 700;
        text-decoration: none;
    }

    .admin-consultation-shell {
        border: 1px solid #dbe1ea;
        border-radius: 14px;
        background: #ffffff;
        overflow-x: auto;
    }

    .admin-consultation-head {
        min-width: 1080px;
        display: grid;
        grid-template-columns: 1.1fr 1.1fr 1.15fr 2fr 0.9fr 0.9fr 0.8fr;
        align-items: center;
        background: #eef2f7;
        border-bottom: 1px solid #dbe1ea;
    }

    .admin-consultation-head > div {
        padding: 12px 14px;
        font-size: 11px;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #425066;
        font-weight: 800;
    }

    .admin-consultation-table {
        display: block;
        min-width: 1080px;
    }

    .admin-consultation-row {
        display: grid;
        grid-template-columns: 1.1fr 1.1fr 1.15fr 2fr 0.9fr 0.9fr 0.8fr;
        align-items: center;
        gap: 0;
        border-bottom: 1px solid #edf1f6;
        background: #ffffff;
        transition: background-color 0.2s ease;
    }

    .admin-consultation-row:hover {
        background: #f8fbff;
    }

    .admin-consultation-row > div {
        padding: 12px 14px;
        min-width: 0;
    }

    .admin-consultation-party {
        display: grid;
        gap: 4px;
    }

    .admin-consultation-primary {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
        word-break: break-word;
    }

    .admin-consultation-secondary {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
        word-break: break-word;
    }

    .admin-consultation-datetime {
        display: grid;
        gap: 4px;
    }

    .admin-consultation-date {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .admin-consultation-time {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
    }

    .admin-consultation-type-text {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.35;
        word-break: break-word;
    }

    .admin-consultation-mode,
    .admin-consultation-status,
    .admin-consultation-actions {
        display: flex;
        align-items: center;
    }

    .admin-consultation-actions .action-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 52px;
    }

    .admin-consultation-empty {
        padding: 18px 16px;
        text-align: center;
        color: var(--muted);
        font-size: 14px;
        font-weight: 600;
    }


    .details-modal {
        position: fixed;
        inset: 0;
        z-index: 95;
        background: rgba(15, 23, 42, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .details-modal.open { display: flex; }

    .details-dialog {
        width: 100%;
        max-width: 500px;
        border-radius: 18px;
        background: #ffffff;
        border: 1px solid rgba(196, 203, 214, 0.95);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: min(92vh, 760px);
    }

    .details-header {
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #d5d9e3;
        background: linear-gradient(180deg, #2f4eb2 0%, #2744a2 100%);
        color: #fff;
    }

    .details-title {
        font-size: 23px;
        font-weight: 800;
        line-height: 1.1;
    }

    .details-subtitle {
        font-size: 12px;
        opacity: 0.9;
        margin-top: 2px;
    }

    .details-close {
        border: none;
        background: transparent;
        color: rgba(255, 255, 255, 0.92);
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }

    .details-body {
        flex: 1 1 auto;
        min-height: 0;
        padding: 14px 16px 16px;
        overflow-y: auto;
        background: #f5f6f8;
    }

    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 12px;
    }

    .details-card {
        background: #ededee;
        border: 1px solid #d7dbe3;
        border-radius: 12px;
        padding: 9px 12px;
        font-size: 13px;
        color: #3d4451;
        min-height: 42px;
        display: flex;
        align-items: center;
    }

    .details-card-student {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 8px 12px;
        align-items: center;
    }

    .details-card-student > span {
        min-width: 0;
    }

    .details-card-inline-id {
        display: none;
        white-space: nowrap;
        color: #5b6472;
        font-size: 12px;
        font-weight: 600;
    }

    .details-summary {
        margin-top: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #d5dae3;
        background: #ffffff;
    }

    .details-summary-title {
        font-size: 13px;
        font-weight: 700;
        color: #151a23;
        margin-bottom: 6px;
    }

    .details-summary-text {
        color: #1f2937;
        font-size: 13px;
        line-height: 1.5;
        white-space: pre-wrap;
        max-height: 220px;
        overflow-y: auto;
        overflow-wrap: anywhere;
    }

    .detail-status-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .detail-status-pending { background: #fff7ed; color: #c2410c; }
    .detail-status-approved { background: #dcfce7; color: #047857; }
    .detail-status-in-progress { background: #dbeafe; color: #1d4ed8; }
    .detail-status-completed { background: #ccfbf1; color: #0f766e; }
    .detail-status-incompleted { background: #fef3c7; color: #92400e; }
    .detail-status-declined { background: #fee2e2; color: #b91c1c; }
    .detail-status-default { background: #e5e7eb; color: #374151; }

    @media (max-width: 1100px) {
        .stat-grid {
            grid-template-columns: 1fr 1fr;
        }

        .grid-2 {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        .sidebar {
            transform: translateX(-100%);
            width: min(84vw, 260px);
            padding: 18px 0;
            z-index: 150;
        }

        .sidebar.open { transform: translateX(0); }

        .main { margin-left: 0; }

        .menu-btn { display: inline-flex; }

        .topbar {
            padding: 12px 14px;
        }

        .content {
            padding: 16px;
        }

        .content-header {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            padding: 14px 16px;
            margin-bottom: 16px;
        }

        .topbar-actions {
            order: -1;
            width: 100%;
            justify-content: flex-start;
            align-items: flex-start;
            flex-wrap: nowrap;
        }

        .topbar-actions .notification-wrap {
            margin-left: auto;
        }

        .profile-email {
            max-width: 140px;
        }

        .stat-value {
            font-size: 36px;
        }

        .panel-head {
            font-size: 22px;
        }

        .consultations-title {
            font-size: 30px;
        }

        .students-table thead th,
        .students-table tbody td {
            padding-left: 14px;
            padding-right: 14px;
        }

    }

    @media (max-width: 640px) {
        .content { padding: 10px; }

        .content-header {
            padding: 12px;
            border-radius: 12px;
            overflow: visible;
            isolation: isolate;
        }

        .dashboard-header-title {
            font-size: 22px;
        }

        .dashboard-header-subtitle {
            font-size: 11px;
        }

        .stat-grid { grid-template-columns: 1fr; }

        .stat-card {
            padding: 14px 12px;
        }

        .stat-value {
            font-size: 30px;
        }

        .topbar-actions {
            gap: 8px;
            position: relative;
            z-index: 6;
        }

        .menu-btn span {
            display: none;
        }

        .notification-btn,
        .header-profile-trigger {
            width: 40px;
            height: 40px;
        }

        .profile {
            z-index: 80;
        }

        .profile .absolute.z-50 {
            right: 0;
            left: auto;
            min-width: 150px;
        }

        .profile .rounded-md.ring-1 a {
            padding: 10px 12px;
            font-size: 12px;
        }

        .notification-panel {
            width: min(86vw, 300px);
            right: 0;
            top: 46px;
            border-radius: 14px;
        }

        .notification-header {
            padding: 12px 14px;
            font-size: 12px;
        }

        .notification-list {
            max-height: 240px;
        }

        .notification-item {
            padding: 12px 14px;
            font-size: 12px;
            gap: 10px;
        }

        .notification-item > div {
            min-width: 0;
        }

        .hero-title {
            font-size: 17px;
        }

        .hero {
            padding: 14px 12px;
            border-radius: 12px;
        }

        .hero-tab {
            font-size: 12px;
            padding: 7px 11px;
        }

        .students-head,
        .students-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .students-search {
            min-width: 0;
            width: 100%;
        }

        .students-search,
        .students-filter,
        .students-btn {
            font-size: 13px;
            padding: 9px 10px;
        }

        .students-head,
        .consultations-head {
            padding: 14px 12px;
        }

        .consultations-title {
            font-size: 24px;
        }

        .consultations-filter-grid {
            grid-template-columns: 1fr;
        }

        .consultations-filter-top {
            justify-content: flex-start;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .stats-filter-grid {
            grid-template-columns: 1fr;
        }

        .stats-metric-grid {
            grid-template-columns: 1fr;
        }

        .stats-distribution-body {
            grid-template-columns: 1fr;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .details-card-inline-id {
            display: inline;
        }

        #detailsStudentId {
            display: none;
        }

        .students-table {
            min-width: 700px;
        }

        #studentsSection .table-scroll-shell {
            overflow: visible;
        }

        #studentsSection .students-table {
            min-width: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        #studentsSection .students-table thead {
            display: none;
        }

        #studentsSection .students-table tbody {
            display: block;
        }

        #studentsSection .students-table tbody tr[data-status] {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 14px;
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.08);
            margin-bottom: 10px;
            padding: 12px;
        }

        #studentsSection .students-table tbody td {
            padding: 0;
            border: 0;
            background: transparent;
        }

        #studentsSection .students-table tbody td:nth-child(3),
        #studentsSection .students-table tbody td:nth-child(4),
        #studentsSection .students-table tbody td:nth-child(5),
        #studentsSection .students-table tbody td:nth-child(6),
        #studentsSection .students-table tbody td:nth-child(2) {
            display: none;
        }

        #studentsSection .students-table tbody td:first-child {
            min-width: 0;
        }

        #studentsSection .student-cell {
            gap: 10px;
            min-width: 0;
        }

        #studentsSection .student-avatar {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }

        #studentsSection .student-name {
            font-size: 13px;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #studentsSection .student-email {
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #studentsSection .student-action-cell {
            justify-self: end;
        }

        #studentsSection .student-view-details-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        #studentsSection .manage-label-desktop {
            display: none;
        }

        #studentsSection .manage-label-mobile {
            display: inline;
        }

        #instructorsSection .table-scroll-shell {
            overflow: visible;
        }

        #instructorsSection .students-table {
            min-width: 0;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        #instructorsSection .students-table thead {
            display: none;
        }

        #instructorsSection .students-table tbody {
            display: block;
        }

        #instructorsSection .students-table tbody tr[data-status] {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 14px;
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.08);
            margin-bottom: 10px;
            padding: 12px;
        }

        #instructorsSection .students-table tbody td {
            padding: 0;
            border: 0;
            background: transparent;
        }

        #instructorsSection .students-table tbody td:nth-child(2),
        #instructorsSection .students-table tbody td:nth-child(3),
        #instructorsSection .students-table tbody td:nth-child(4),
        #instructorsSection .students-table tbody td:nth-child(5) {
            display: none;
        }

        #instructorsSection .students-table tbody td:first-child {
            min-width: 0;
        }

        #instructorsSection .student-cell {
            gap: 10px;
            min-width: 0;
        }

        #instructorsSection .student-avatar {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }

        #instructorsSection .student-name {
            font-size: 13px;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #instructorsSection .student-email {
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #instructorsSection .student-action-cell {
            justify-self: end;
        }

        #instructorsSection .manage-label-desktop {
            display: none;
        }

        #instructorsSection .manage-label-mobile {
            display: inline;
        }

        #studentPaginationContainer,
        #instructorPaginationContainer,
        #consultationPaginationContainer {
            padding: 0 12px !important;
            gap: 10px !important;
        }
    }

    @media (max-width: 420px) {
        .content {
            padding: 8px;
        }

        .dashboard-header-title {
            font-size: 20px;
        }

        .consultations-title {
            font-size: 22px;
        }

        .section-close-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
        }

        .stats-filter-card,
        .consultations-filter-card {
            padding: 10px;
        }
    }

    /* iPhone 12/13/14 baseline (390 x 844) */
    @media (max-width: 390px) and (max-height: 844px) {
        .sidebar {
            width: min(88vw, 322px);
        }

        .sidebar-logo {
            margin-bottom: 18px;
            padding: 0 14px;
        }

        .logo-badge {
            width: 34px;
            height: 34px;
        }

        .sidebar-menu-link {
            padding: 11px 16px;
            font-size: 13px;
        }

        .content {
            padding: 10px;
        }

        .content-header {
            padding: 12px;
            gap: 12px;
        }

        .dashboard-header-title {
            font-size: 21px;
        }

        .hero-title {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .hero-tabs {
            gap: 8px;
        }

        .hero-tab {
            font-size: 12px;
            padding: 7px 10px;
        }

        .students-head,
        .consultations-head {
            padding: 14px 12px;
        }

        .students-table {
            min-width: 660px;
        }

        #studentsSection .students-table {
            min-width: 0;
        }

        .stats-export-actions {
            width: 100%;
        }

        .stats-export-btn {
            flex: 1 1 calc(50% - 4px);
            justify-content: center;
        }

        .details-dialog,
        .manage-dialog,
        .add-dialog {
            width: calc(100vw - 18px);
            max-width: none;
            max-height: 88vh;
        }
    }

    /* Android compact baseline (360 x 800) */
    @media (max-width: 360px) and (max-height: 800px) {
        .sidebar {
            width: min(90vw, 320px);
            padding-top: 14px;
        }

        .sidebar-menu-link {
            padding: 10px 14px;
            font-size: 12px;
        }

        .content {
            padding: 8px;
        }

        .content-header {
            padding: 10px;
            margin-bottom: 12px;
        }

        .dashboard-header-title {
            font-size: 19px;
        }

        .dashboard-header-subtitle {
            font-size: 10px;
        }

        .hero {
            padding: 12px 10px;
        }

        .hero-tab {
            font-size: 11px;
            padding: 6px 9px;
        }

        .stat-card {
            padding: 12px 10px;
        }

        .stat-value {
            font-size: 27px;
        }

        .students-search,
        .students-filter,
        .students-btn {
            font-size: 12px;
            padding: 8px 9px;
        }

        .consultations-title {
            font-size: 21px;
        }

        .consultations-subtitle {
            font-size: 12px;
        }

        .consultation-semester-btn,
        .stats-semester-btn {
            padding: 7px 8px;
            font-size: 11px;
        }

        .students-table {
            min-width: 620px;
        }

        #studentsSection .students-table {
            min-width: 0;
        }

        #studentPaginationInfo,
        #instructorPaginationInfo,
        #consultationPaginationInfo {
            font-size: 12px !important;
        }

        .pagination-nav-btn,
        .pagination-page-btn {
            padding: 6px 8px;
            font-size: 11px;
        }

        .details-dialog,
        .manage-dialog,
        .add-dialog {
            width: calc(100vw - 14px);
            border-radius: 12px;
        }
    }

    .pagination-nav-btn {
        border: 1px solid var(--border);
        background: #fff;
        color: var(--text);
        padding: 6px 10px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.3s ease;
    }
    .pagination-nav-btn:hover {
        background: var(--brand);
        color: #fff;
        border-color: var(--brand);
    }
    .pagination-page-btn {
        border: 1px solid var(--border);
        background: #fff;
        color: var(--text);
        padding: 6px 10px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.3s ease;
        min-width: 32px;
    }
    .pagination-page-btn:hover {
        background: var(--brand-soft);
    }
    .pagination-page-btn.active {
        background: var(--brand);
        color: #fff;
        border-color: var(--brand);
    }

    .online-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        background: #d1fae5;
        color: #047857;
        white-space: nowrap;
    }

    .user-active-minutes-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        background: #fef3c7;
        color: #7c2d12;
        white-space: nowrap;
    }
</style>
<style>
/* Admin dashboard cyber theme (matched with student dashboard style) */
.admin-cyber-theme {
    background:
        radial-gradient(circle at 16% 22%, rgba(0, 186, 255, 0.12), transparent 36%),
        radial-gradient(circle at 86% 8%, rgba(30, 64, 175, 0.16), transparent 34%),
        linear-gradient(180deg, #f3fbff 0%, #eef6ff 100%);
}

.admin-cyber-theme .sidebar {
    background:
        linear-gradient(180deg, rgba(6, 19, 64, 0.72) 0%, rgba(9, 35, 104, 0.72) 100%),
        url('{{ asset('sidebar.JPG') }}') center/cover no-repeat;
    border: 1px solid rgba(94, 217, 255, 0.45);
    box-shadow: 0 0 0 1px rgba(103, 232, 249, 0.2), 0 0 24px rgba(8, 145, 178, 0.4);
}

.admin-cyber-theme .sidebar::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        radial-gradient(circle at 14% 10%, rgba(0, 247, 255, 0.14), transparent 35%),
        linear-gradient(130deg, transparent 0 35%, rgba(70, 207, 255, 0.09) 35% 36%, transparent 36% 100%);
}

.admin-cyber-theme .sidebar-menu-link {
    border: 1px solid rgba(96, 165, 250, 0.28);
    background: rgba(21, 46, 122, 0.7);
    border-radius: 12px;
    margin: 8px 14px;
    color: #e2edff;
    min-height: 46px;
}

.admin-cyber-theme .sidebar-menu-link:hover,
.admin-cyber-theme .sidebar-menu-link.active {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.65), rgba(20, 184, 166, 0.45));
    border-color: rgba(103, 232, 249, 0.62);
    box-shadow: 0 0 20px rgba(56, 189, 248, 0.3);
}

.admin-cyber-theme .sidebar.icon-only .sidebar-menu-link {
    margin: 8px auto;
}

.admin-cyber-theme .logout-btn {
    background: rgba(14, 34, 96, 0.9);
    border: 1px solid rgba(125, 211, 252, 0.5);
    color: #dbeafe;
}

.admin-cyber-theme .content-header {
    position: relative;
    z-index: 20;
    overflow: visible;
    background: linear-gradient(135deg, #10224e 0%, #1f3f8a 100%);
    border: 1px solid rgba(96, 165, 250, 0.22);
    border-radius: 0 0 18px 18px;
    padding: 18px 24px;
    box-shadow: 0 16px 34px rgba(15, 23, 42, 0.2);
}

.admin-cyber-theme .content-header::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0) 100%),
        radial-gradient(circle at 82% 18%, rgba(96, 165, 250, 0.16), transparent 26%);
    pointer-events: none;
    z-index: 0;
}

.admin-cyber-theme .content-header::after {
    content: none;
}

    .admin-cyber-theme .dashboard-header-copy,
    .admin-cyber-theme .topbar-actions {
        position: relative;
        z-index: 2;
    }

    .admin-cyber-theme .profile {
        z-index: 40;
    }

    .admin-cyber-theme .profile .rounded-md.ring-1 {
        background: #ffffff;
        border-color: rgba(148, 163, 184, 0.28);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.24);
    }

.admin-cyber-theme .dashboard-header-title {
    color: #ffffff;
    font-size: clamp(24px, 2.2vw, 34px);
    font-weight: 800;
    text-shadow: 0 2px 10px rgba(15, 23, 42, 0.38);
    letter-spacing: 0;
}

.admin-cyber-theme .dashboard-header-subtitle {
    color: #c8dcff;
    font-size: 15px;
}

.admin-cyber-theme .dashboard-header-name {
    color: #66a8ff;
}

.admin-cyber-theme .dashboard-header-wave {
    display: inline-block;
    margin-left: 6px;
    font-size: 0.9em;
}

.admin-cyber-theme .notification-btn {
    width: 44px;
    height: 44px;
    border-color: rgba(191, 219, 254, 0.28);
    background: rgba(15, 23, 42, 0.2);
    color: #ffffff;
    box-shadow: none;
}

.admin-cyber-theme .header-profile-trigger {
    width: 44px;
    height: 44px;
    padding: 0;
    border-radius: 50%;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(191, 219, 254, 0.28);
    background: linear-gradient(135deg, #475569, #64748b);
    color: #fff;
    box-shadow: none;
}

.admin-cyber-theme .header-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
}

.admin-cyber-theme .stat-card {
    position: relative;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-top: 4px solid #4A90E2;
    color: #111827;
    box-shadow: 0 12px 28px rgba(17, 24, 39, 0.08);
}

.admin-cyber-theme .stat-card::before {
    content: none;
}

.admin-cyber-theme .stat-icon {
    background: #dbeafe !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe;
}

.admin-cyber-theme .stat-value,
.admin-cyber-theme .stat-label {
    color: #111827;
    position: relative;
    z-index: 1;
}

.admin-cyber-theme .stat-chip {
    background: #eef2ff !important;
    color: #3730a3 !important;
    border-color: #c7d2fe !important;
}

.admin-cyber-theme .panel {
    background: rgba(245, 251, 255, 0.92);
    border: 1px solid rgba(56, 189, 248, 0.35);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.1);
}

.admin-cyber-theme .admin-recent-panel {
    background: #f3f4f6;
    border: 1px solid #d8dde6;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
}

.admin-cyber-theme .table-scroll-shell {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 1024px) {
    .admin-cyber-theme .stat-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .admin-cyber-theme,
    .admin-cyber-theme .main,
    .admin-cyber-theme .content {
        overflow-x: hidden;
    }

    .admin-cyber-theme .sidebar {
        width: min(84vw, 300px);
        z-index: 150;
    }

    .admin-cyber-theme .content {
        padding: 14px 12px 28px;
    }

    .admin-cyber-theme .content-header {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 14px 12px;
        overflow: visible;
        isolation: isolate;
    }

    .admin-cyber-theme .dashboard-header-copy {
        width: 100%;
    }

    .admin-cyber-theme .topbar-actions {
        order: -1;
        width: 100%;
        justify-content: flex-start;
        align-items: flex-start;
        flex-wrap: nowrap;
        gap: 10px;
        position: relative;
        z-index: 6;
    }

    .admin-cyber-theme .topbar-actions .notification-wrap {
        margin-left: auto;
    }

    .admin-cyber-theme .profile {
        z-index: 80;
    }

    .admin-cyber-theme .profile .absolute.z-50 {
        right: 0;
        left: auto;
        min-width: 150px;
    }

    .admin-cyber-theme .notification-panel {
        width: min(86vw, 300px);
        right: 0;
        top: 46px;
        border-radius: 14px;
    }

    .admin-cyber-theme .notification-header {
        padding: 12px 14px;
        font-size: 12px;
    }

    .admin-cyber-theme .notification-list {
        max-height: 240px;
    }

    .admin-cyber-theme .notification-item {
        padding: 12px 14px;
        font-size: 12px;
        gap: 10px;
    }

    .admin-cyber-theme .notification-item > div {
        min-width: 0;
    }

    .admin-cyber-theme .profile-email {
        max-width: 112px;
    }

    .admin-cyber-theme .hero {
        padding: 16px 14px;
    }

    .admin-cyber-theme .hero-tabs,
    .admin-cyber-theme .consultations-filter-top,
    .admin-cyber-theme .stats-export-actions {
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 4px;
    }

    .admin-cyber-theme .hero-tab,
    .admin-cyber-theme .consultation-semester-btn,
    .admin-cyber-theme .stats-export-btn {
        flex: 0 0 auto;
        white-space: nowrap;
    }

    .admin-cyber-theme .students-head,
    .admin-cyber-theme .consultations-head {
        padding: 14px 12px;
    }

    .admin-cyber-theme .students-controls {
        width: 100%;
        flex-wrap: wrap;
    }

    .admin-cyber-theme .students-search,
    .admin-cyber-theme .students-filter {
        width: 100%;
        min-width: 0;
        flex: 1 1 100%;
    }

    .admin-cyber-theme .section-close-btn {
        margin-left: auto;
    }

    .admin-cyber-theme .consultations-head-top,
    .admin-cyber-theme .stats-filter-head {
        flex-direction: column;
        align-items: stretch;
    }

    .admin-cyber-theme .consultations-filter-grid,
    .admin-cyber-theme .stats-filter-grid,
    .admin-cyber-theme .stats-metric-grid,
    .admin-cyber-theme .stats-distribution-body {
        grid-template-columns: 1fr;
    }

    .admin-cyber-theme .stats-bar-row {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .admin-cyber-theme .stats-bar-value {
        text-align: left;
    }

    .admin-cyber-theme .students-table {
        min-width: 700px;
    }

    .admin-consultation-head {
        display: none;
    }

    .admin-consultation-table {
        min-width: 0;
    }

    .admin-consultation-row {
        grid-template-columns: 1fr;
        min-width: 0;
    }

    .admin-consultation-row > div {
        padding: 10px 12px;
    }

    .admin-consultation-mode,
    .admin-consultation-status,
    .admin-consultation-actions {
        padding-top: 0;
    }

    #studentPaginationContainer,
    #instructorPaginationContainer,
    #consultationPaginationContainer {
        padding: 0 !important;
        align-items: flex-start !important;
        flex-direction: column;
    }

    #studentPaginationControls,
    #instructorPaginationControls,
    #consultationPaginationControls,
    #studentPageNumbers,
    #instructorPageNumbers,
    #consultationPageNumbers {
        flex-wrap: wrap;
    }

    .admin-cyber-theme .details-dialog,
    .admin-cyber-theme .manage-dialog,
    .admin-cyber-theme .add-dialog {
        width: calc(100vw - 16px);
        max-width: none;
        max-height: 90vh;
        border-radius: 14px;
    }
}

@media (max-width: 480px) {
    .admin-cyber-theme .content {
        padding: 10px 8px 24px;
    }

    .admin-cyber-theme .content-header {
        padding: 12px 10px;
    }

    .admin-cyber-theme .dashboard-header-title {
        font-size: 20px;
    }

    .admin-cyber-theme .dashboard-header-subtitle {
        font-size: 11px;
    }

    .admin-cyber-theme .topbar-actions {
        gap: 8px;
    }

    .admin-cyber-theme .menu-btn span {
        display: none;
    }

    .admin-cyber-theme .notification-btn,
    .admin-cyber-theme .header-profile-trigger {
        width: 40px;
        height: 40px;
    }

    .admin-cyber-theme .profile .rounded-md.ring-1 a {
        padding: 10px 12px;
        font-size: 12px;
    }

    .admin-cyber-theme .hero {
        padding: 14px 12px;
    }

    .admin-cyber-theme .hero-title,
    .admin-cyber-theme .consultations-title,
    .admin-cyber-theme .admin-recent-panel .overview-panel-title {
        font-size: 18px;
    }

    .admin-cyber-theme .stat-card,
    .admin-cyber-theme .stats-workspace,
    .admin-cyber-theme .admin-recent-panel,
    .admin-cyber-theme .students-card {
        border-radius: 12px;
    }

    .admin-cyber-theme .section-close-btn {
        width: 32px;
        height: 32px;
    }
</style>

<div class="dashboard admin-cyber-theme">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
            <span class="logo-badge">
                <img src="{{ asset('cslogo1.jpeg.png') }}" alt="CS Logo" class="logo-img">
            </span>
            <span class="sidebar-logo-text">
                <span class="sidebar-logo-main">Computer Studies</span>
                <span class="sidebar-logo-sub">Consultation Platform</span>
            </span>
            <span class="logo-badge secondary-logo">
                <img src="{{ asset('philcstlogo.png') }}" alt="PhilCST Logo" class="logo-img">
            </span>
        </a>

        <ul class="sidebar-menu">
            <li><a href="#overview" class="sidebar-menu-link active" id="overviewLink"><i class="fa-solid fa-house"></i>Dashboard</a></li>
            <li><a href="#students" class="sidebar-menu-link" id="studentsLink"><i class="fa-solid fa-user-graduate"></i>Students</a></li>
            <li><a href="#instructors" class="sidebar-menu-link" id="instructorsLink"><i class="fa-solid fa-chalkboard-user"></i>Instructors</a></li>
            <li><a href="#consultations" class="sidebar-menu-link" id="consultationsLink"><i class="fa-solid fa-clipboard-check"></i>Consultations</a></li>
            <li><a href="#statistics" class="sidebar-menu-link" id="statisticsLink"><i class="fa-solid fa-chart-pie"></i>Statistics</a></li>
        </ul>

        <div class="sidebar-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <div class="main">
        <div class="content">
            <div class="content-header" id="dashboardContentHeader">
                <div class="dashboard-header-copy">
                    <h1 class="dashboard-header-title">Welcome back, <span class="dashboard-header-name">{{ $userName }}</span><span class="dashboard-header-wave">👋</span></h1>
                    <p class="dashboard-header-subtitle">Here's what's happening with consultations today — {{ now()->format('F j, Y') }}</p>
                </div>

                <div class="topbar-actions">
                <button class="menu-btn" id="menuBtn" type="button" aria-label="Open sidebar menu">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    <span>Menu</span>
                </button>
                <div class="notification-wrap">
                    <button class="notification-btn" id="notificationBtn" type="button" aria-label="Open notifications">
                        <i class="fa-solid fa-bell" aria-hidden="true"></i>
                        <span class="notification-badge" id="notificationBadge" @if ($unreadCount <= 0) style="display:none" @endif>{{ $unreadCount }}</span>
                    </button>
                    <div class="notification-panel" id="notificationPanel">
                        <div class="notification-header">
                            <span>Notifications</span>
                            <form method="POST" action="{{ route('notifications.markAllRead') }}" id="markAllReadForm">
                                @csrf
                                <button id="markAllReadBtn" type="submit" style="border:none;background:none;color:var(--brand);font-weight:700;cursor:pointer;">Mark all read</button>
                            </form>
                        </div>
                        <ul class="notification-list" id="notificationList">
                            @forelse ($notifications as $notification)
                                <li class="notification-item {{ $notification['read'] ? '' : 'unread' }}">
                                    <span class="notification-dot"></span>
                                    <div>
                                        <div style="font-weight:700;">{{ $notification['title'] }}</div>
                                        <div style="color:var(--muted);margin-top:4px;">{{ $notification['message'] }}</div>
                                        <div style="font-size:11px;color:#9ca3af;margin-top:6px;">{{ $notification['timestamp'] }}</div>
                                    </div>
                                </li>
                            @empty
                                <li class="notification-item">
                                    <div>
                                        <div style="font-weight:700;">No notifications</div>
                                        <div style="color:var(--muted);margin-top:4px;">You're all caught up.</div>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                        <div style="padding:12px 14px;border-top:1px solid var(--border);text-align:center;">
                            <a href="{{ route('notifications.index') }}" style="color:var(--brand);font-weight:700;text-decoration:none;font-size:13px;">View all</a>
                        </div>
                    </div>
                </div>

                <div class="profile" style="position: relative;">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="header-profile-trigger" type="button" title="{{ $userName }}" aria-label="Open profile menu">
                                <span class="header-avatar">
                                    {{ $userInitial }}
                                </span>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                </div>
            </div>
            <div id="overviewSection">
            <div class="stat-grid" id="overviewStatsCards">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#e0f2fe;color:#075985;"><i class="fa-solid fa-user-graduate" aria-hidden="true"></i></div>
                    <div class="stat-value" id="adminOverviewStudents">{{ $totalStudents }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background:#ecfdf5;color:#047857;"><i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i></div>
                    <div class="stat-value" id="adminOverviewInstructors">{{ $totalInstructors }}</div>
                </div>

                <div class="stat-card" id="recent-consultations">
                    <div class="stat-icon" style="background:#fff7ed;color:#c2410c;"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i></div>
                    <div class="stat-value" id="adminOverviewConsultations">{{ $totalConsultations }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background:#ede9fe;color:#5b21b6;"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                    <div class="stat-value" id="adminOverviewCompleted">{{ $completedSessions }}</div>
                </div>
            </div>

                <div class="grid-2">
                <div class="panel admin-recent-panel">
                    <div class="overview-panel-header">
                        <h2 class="overview-panel-title">Recent Consultations</h2>
                        <a href="#" class="overview-panel-link">View All <span aria-hidden="true">→</span></a>
                    </div>

                    <div id="adminRecentConsultationsList">
                    @if ($recentConsultations->isEmpty())
                        <div class="overview-empty">No recent consultations yet.</div>
                    @else
                        <div class="recent-list">
                            @foreach ($recentConsultations as $consultation)
                                @php
                                    $statusKey = strtolower((string) ($consultation->status ?? 'pending'));
                                    $statusLabel = match ($statusKey) {
                                        'incompleted' => 'Incomplete',
                                        default => ucwords(str_replace('_', ' ', $statusKey)),
                                    };
                                    $consultationTitle = $consultation->type_label ?: 'Consultation Session';
                                @endphp
                                <div class="recent-item">
                                    <div class="recent-item-top">
                                        <p class="recent-item-title">{{ $consultationTitle }}</p>
                                        <span class="recent-status-pill status-{{ $statusKey }}">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="recent-item-meta">
                                        <span><i class="fa-solid fa-users" aria-hidden="true"></i> {{ $consultation->student?->name ?? 'Student' }} with {{ $consultation->instructor?->name ?? 'Instructor' }}</span>
                                        <span><i class="fa-solid fa-clock" aria-hidden="true"></i> {{ $formatRelativeDay($consultation->consultation_date) }}, {{ $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    </div>
                </div>

                <div class="panel" id="system-overview">
                    <div class="panel-head">System Overview</div>
                    <div class="overview-list">
                        <div class="overview-item">
                            <div>
                                <div class="overview-title">Data Integrity</div>
                                <div class="overview-sub">Records validated and available</div>
                            </div>
                            <span class="overview-state state-green">Healthy</span>
                        </div>
                        <div class="overview-item">
                            <div>
                                <div class="overview-title">Security Status</div>
                                <div class="overview-sub">No threat flags detected</div>
                            </div>
                            <span class="overview-state state-green">Secure</span>
                        </div>
                        <div class="overview-item">
                            <div>
                                <div class="overview-title">Database</div>
                                <div class="overview-sub">Connected and synchronized</div>
                            </div>
                            <span class="overview-state state-blue">Online</span>
                        </div>
                        <div class="overview-item">
                            <div>
                                <div class="overview-title">Pending Actions</div>
                                <div class="overview-sub">{{ $pendingConsultations }} consultations awaiting approval</div>
                            </div>
                            <span class="overview-state state-orange">Attention</span>
                        </div>
                    </div>
                </div>
                </div>

                <div class="stats-workspace" id="statistics">
                    <div class="stats-filter-card">
                        <div class="stats-filter-head">
                            <div class="stats-filter-title"><i class="fa-solid fa-filter"></i> Filters</div>
                            <div class="stats-export-actions">
                                <button type="button" class="stats-export-btn stats-export-pdf" id="statsExportPdfBtn">
                                    <i class="fa-solid fa-file-pdf"></i> Export PDF
                                </button>
                                <button type="button" class="stats-export-btn stats-export-excel" id="statsExportExcelBtn">
                                    <i class="fa-solid fa-file-excel"></i> Export Excel
                                </button>
                                <button type="button" class="section-close-btn section-close-trigger" data-close-section="statistics" aria-label="Close statistics section">&times;</button>
                            </div>
                        </div>
                        <div class="stats-filter-grid">
                            <div class="stats-filter-group">
                                <span class="stats-filter-label">Semester</span>
                                <div class="stats-semester-toggle" role="group" aria-label="Statistics semester filter">
                                    <button type="button" class="stats-semester-btn active" data-stats-semester="all">All</button>
                                    <button type="button" class="stats-semester-btn" data-stats-semester="first">1st Sem</button>
                                    <button type="button" class="stats-semester-btn" data-stats-semester="second">2nd Sem</button>
                                </div>
                            </div>
                            <div class="stats-filter-group">
                                <label class="stats-filter-label" for="statsAcademicYearSelect">Academic Year</label>
                                <select class="stats-filter-select" id="statsAcademicYearSelect"></select>
                            </div>
                            <div class="stats-filter-group">
                                <label class="stats-filter-label" for="statsMonthSelect">Month</label>
                                <select class="stats-filter-select" id="statsMonthSelect"></select>
                            </div>
                            <div class="stats-filter-group">
                                <label class="stats-filter-label" for="statsCategorySelect">Category</label>
                                <select class="stats-filter-select" id="statsCategorySelect"></select>
                            </div>
                            <div class="stats-filter-group">
                                <label class="stats-filter-label" for="statsTopicSelect">Topic</label>
                                <select class="stats-filter-select" id="statsTopicSelect"></select>
                            </div>
                            <div class="stats-filter-group">
                                <label class="stats-filter-label" for="statsModeSelect">Mode</label>
                                <select class="stats-filter-select" id="statsModeSelect"></select>
                            </div>
                            <div class="stats-filter-group">
                                <label class="stats-filter-label" for="statsInstructorSelect">Instructor</label>
                                <select class="stats-filter-select" id="statsInstructorSelect"></select>
                            </div>
                            <div class="stats-filter-group">
                                <span class="stats-filter-label">&nbsp;</span>
                                <button type="button" class="stats-filter-select" id="statsResetBtn">Reset Filters</button>
                            </div>
                        </div>
                    </div>

                    <div class="stats-metric-grid">
                        <div class="stats-metric-card consultations">
                            <div class="stats-metric-label">Total Consultations</div>
                            <div class="stats-metric-value" id="statsTotalConsultations">0</div>
                        </div>
                        <div class="stats-metric-card types">
                            <div class="stats-metric-label">Consultation Types</div>
                            <div class="stats-metric-value" id="statsTypeCount">0</div>
                        </div>
                        <div class="stats-metric-card period">
                            <div class="stats-metric-label">Current Period</div>
                            <div class="stats-metric-subvalue" id="statsCurrentPeriod">1st Sem</div>
                        </div>
                    </div>

                    <div class="stats-distribution">
                        <div class="stats-distribution-head">
                            <div class="stats-distribution-title"><i class="fa-solid fa-chart-bar"></i> Horizontal Bar Chart - Percentage Distribution</div>
                            <div class="stats-distribution-subtitle" id="statsDistributionSubtitle">Month - Semester Academic Year</div>
                        </div>
                        <div class="stats-distribution-body">
                            <div class="stats-bar-summary">
                                <div class="stats-bar-summary-label">Total Consultations</div>
                                <div class="stats-donut-total" id="statsDonutTotal">0</div>
                            </div>
                            <div class="stats-bar-chart" id="statsDonutChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="students-card is-hidden" id="studentsSection">
                <div class="students-head">
                    <div class="students-title">Student Accounts</div>
                    <div class="students-controls">
                        <input type="text" class="students-search" id="studentSearch" placeholder="Search by name, email, or ID...">
                        <select class="students-filter" id="studentStatusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        <button type="button" class="section-close-btn section-close-trigger" data-close-section="students" aria-label="Close students section">&times;</button>
                    </div>
                </div>

                <div class="table-scroll-shell">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Student ID</th>
                                <th>Joined</th>
                                <th>Consultations</th>
                                <th>Status</th>
                                <th>Online Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            @forelse ($studentRows as $student)
                                <tr data-status="{{ $student['status'] }}" data-search="{{ strtolower($student['name'] . ' ' . $student['email'] . ' ' . $student['student_id']) }}">
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar">{{ strtoupper(substr($student['name'], 0, 1)) }}</div>
                                            <div>
                                                <div class="student-name">{{ $student['name'] }}</div>
                                                <div class="student-email">{{ $student['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="student-id-cell">{{ $student['student_id'] }}</td>
                                    <td>{{ $student['joined'] }}</td>
                                    <td style="font-weight:700">{{ $student['consultations'] }}</td>
                                    <td><span class="status-tag status-{{ $student['status'] }}">{{ $student['status'] }}</span></td>
                                    <td>
                                        @php
                                            $studentOnline = in_array($student['id'], (array) $onlineStudentIds) || \App\Services\UserSessionService::isUserOnline($student['id']);
                                            $lastActiveMinutes = isset($studentActiveMinutes[$student['id']])
                                                ? $studentActiveMinutes[$student['id']]['last_active_minutes']
                                                : \App\Services\UserSessionService::getLastActiveMinutes($student['id']);
                                        @endphp
                                        @if ($studentOnline)
                                            <span class="online-badge">Online</span>
                                        @elseif ($lastActiveMinutes !== null)
                                            <span class="user-active-minutes-badge">Active {{ $lastActiveMinutes }}{{ $lastActiveMinutes === 1 ? ' min' : ' mins' }} ago</span>
                                        @else
                                            <span style="color:var(--muted);font-size:11px;font-weight:700;">Offline</span>
                                        @endif
                                    </td>
                                    <td class="student-action-cell">
                                        <a href="#"
                                           class="manage-link manage-user-btn student-view-details-link"
                                           data-role="Student"
                                           data-name="{{ $student['name'] }}"
                                           data-email="{{ $student['email'] }}"
                                           data-meta="Student ID: {{ $student['student_id'] }}"
                                           data-joined="{{ $student['joined'] }}"
                                           data-consultations="{{ $student['consultations'] }}"
                                           data-status="{{ $student['status'] }}"
                                        ><span class="manage-label-desktop">Manage</span><span class="manage-label-mobile">View Details</span></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="color:var(--muted);text-align:center;">No student accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Student Pagination Controls -->
                <div id="studentPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;padding:0 16px;">
                    <div id="studentPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to 10 of 0 students
                    </div>
                    <div id="studentPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevStudentBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="studentPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextStudentBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">›</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="students-card is-hidden" id="instructorsSection">
                <div class="students-head">
                    <div class="students-title">Instructor Accounts</div>
                    <div class="students-controls">
                        <input type="text" class="students-search" id="instructorSearch" placeholder="Search by name or email...">
                        <select class="students-filter" id="instructorStatusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        <button type="button" class="students-btn" id="openAddInstructor">Add Instructor</button>
                        <button type="button" class="section-close-btn section-close-trigger" data-close-section="instructors" aria-label="Close instructors section">&times;</button>
                    </div>
                </div>

                <div class="table-scroll-shell">
                    <table class="students-table">
                        <thead>
                                <tr>
                                <th>User</th>
                                <th>Joined</th>
                                <th>Consultations</th>
                                <th>Status</th>
                                <th>Online Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="instructorTableBody">
                            @forelse ($instructorRows as $instructor)
                                <tr data-status="{{ $instructor['status'] }}" data-search="{{ strtolower($instructor['name'] . ' ' . $instructor['email']) }}">
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar">{{ strtoupper(substr($instructor['name'], 0, 1)) }}</div>
                                            <div>
                                                <div class="student-name">{{ $instructor['name'] }}</div>
                                                <div class="student-email">{{ $instructor['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $instructor['joined'] }}</td>
                                    <td style="font-weight:700">{{ $instructor['consultations'] }}</td>
                                    <td><span class="status-tag status-{{ $instructor['status'] }}">{{ $instructor['status'] }}</span></td>
                                    <td>
                                        @php
                                            $instructorOnline = in_array($instructor['id'], (array) $onlineInstructorIds) || \App\Services\UserSessionService::isUserOnline($instructor['id']);
                                            $lastActiveMinutes = isset($instructorActiveMinutes[$instructor['id']])
                                                ? $instructorActiveMinutes[$instructor['id']]['last_active_minutes']
                                                : \App\Services\UserSessionService::getLastActiveMinutes($instructor['id']);
                                        @endphp
                                        @if ($instructorOnline)
                                            <span class="online-badge">Online</span>
                                        @elseif ($lastActiveMinutes !== null)
                                            <span class="user-active-minutes-badge">Active {{ $lastActiveMinutes }}{{ $lastActiveMinutes === 1 ? ' min' : ' mins' }} ago</span>
                                        @else
                                            <span style="color:var(--muted);font-size:11px;font-weight:700;">Offline</span>
                                        @endif
                                    </td>
                                    <td class="student-action-cell">
                                        <a href="#"
                                           class="manage-link manage-user-btn student-view-details-link"
                                           data-role="Instructor"
                                           data-name="{{ $instructor['name'] }}"
                                           data-email="{{ $instructor['email'] }}"
                                           data-meta="Instructor Account"
                                           data-joined="{{ $instructor['joined'] }}"
                                           data-consultations="{{ $instructor['consultations'] }}"
                                           data-status="{{ $instructor['status'] }}"
                                        ><span class="manage-label-desktop">Manage</span><span class="manage-label-mobile">View Details</span></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="color:var(--muted);text-align:center;">No instructor accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Instructor Pagination Controls -->
                <div id="instructorPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;padding:0 16px;">
                    <div id="instructorPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to 10 of 0 instructors
                    </div>
                    <div id="instructorPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevInstructorBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="instructorPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextInstructorBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">›</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="students-card is-hidden" id="consultationsSection">
                <div class="consultations-head">
                    <div class="consultations-head-top">
                        <div>
                            <h2 class="consultations-title">All Consultations</h2>
                            <p class="consultations-subtitle">Manage and track all student consultations</p>
                        </div>
                        <button type="button" class="section-close-btn section-close-trigger" data-close-section="consultations" aria-label="Close consultations section">&times;</button>
                    </div>
                    <div class="consultations-filter-card">
                        <div class="consultations-filter-top">
                            <div class="consultation-semester-toggle" role="group" aria-label="Consultation semester filter">
                                <button type="button" id="consultationSemAll" class="consultation-semester-btn active" data-sem="all">All</button>
                                <button type="button" id="consultationSem1" class="consultation-semester-btn" data-sem="1">1st Sem</button>
                                <button type="button" id="consultationSem2" class="consultation-semester-btn" data-sem="2">2nd Sem</button>
                            </div>
                        </div>
                        <div class="consultations-filter-grid">
                            <div class="consultation-filter-group" id="consultationMonthPickerContainer">
                                <label for="consultationMonthSelect">Month</label>
                                <select class="students-filter" id="consultationMonthSelect">
                                    <option value="">All months</option>
                                </select>
                            </div>
                            <div class="consultation-filter-group">
                                <label for="consultationSearch">Search</label>
                                <input
                                    type="text"
                                    class="students-search consultation-search-input"
                                    id="consultationSearch"
                                    placeholder="Search consultations..."
                                    autocomplete="off"
                                >
                            </div>
                            <div class="consultation-filter-group">
                                <label for="consultationStatusFilter">Status</label>
                                <select class="students-filter" id="consultationStatusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="consultation-filter-group">
                                <label for="consultationYearInput">Academic Year</label>
                                <input type="text" class="students-search" id="consultationYearInput" placeholder="Academic Year (e.g., 2024-2025)" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-consultation-shell">
                    <div class="admin-consultation-head" role="row">
                        <div>Student</div>
                        <div>Instructor</div>
                        <div>Date &amp; Time</div>
                        <div>Type</div>
                        <div>Mode</div>
                        <div>Status</div>
                        <div>Actions</div>
                    </div>
                    <div class="admin-consultation-table" id="consultationTableBody">
                        @forelse ($consultationRows as $row)
                            @php
                                $modeClass = str_contains(strtolower((string) $row['mode']), 'audio')
                                    ? 'mode-audio'
                                    : (str_contains(strtolower((string) $row['mode']), 'video')
                                        ? 'mode-video'
                                        : (str_contains(strtolower((string) $row['mode']), 'face')
                                            ? 'mode-face'
                                            : 'mode-default'));
                            @endphp
                            <div
                                class="admin-consultation-row"
                                data-status="{{ strtolower((string) $row['status']) }}"
                                data-date="{{ $row['date'] }}"
                                data-search-all="{{ strtolower($row['code'] . ' ' . $row['student'] . ' ' . $row['instructor'] . ' ' . $row['date'] . ' ' . $row['time_range'] . ' ' . $row['duration'] . ' ' . $row['type'] . ' ' . $row['mode'] . ' ' . $row['status'] . ' ' . $row['summary'] . ' ' . $row['action_taken']) }}"
                            >
                                <div class="admin-consultation-party">
                                    <div class="admin-consultation-primary">{{ $row['student'] }}</div>
                                    <div class="admin-consultation-secondary">ID: {{ $row['student_id'] ?: '--' }}</div>
                                </div>
                                <div class="admin-consultation-party">
                                    <div class="admin-consultation-primary">{{ $row['instructor'] }}</div>
                                    <div class="admin-consultation-secondary">Instructor</div>
                                </div>
                                <div class="admin-consultation-datetime">
                                    <div class="admin-consultation-date">{{ $row['date'] }}</div>
                                    <div class="admin-consultation-time">{{ $row['time_range'] }}</div>
                                </div>
                                <div class="admin-consultation-type">
                                    <span class="admin-consultation-type-text">{{ $row['type'] }}</span>
                                </div>
                                <div class="admin-consultation-mode">
                                    <span class="mode-pill {{ $modeClass }}">{{ $row['mode'] }}</span>
                                </div>
                                <div class="admin-consultation-status">
                                    <span class="status-tag status-{{ strtolower((string) $row['status']) }}">{{ strtoupper((string) $row['status']) }}</span>
                                </div>
                                <div class="admin-consultation-actions">
                                    <a href="#"
                                       class="action-view consultation-view-btn"
                                       data-id="{{ $row['code'] }}"
                                       data-student="{{ $row['student'] }}"
                                       data-student-id="{{ $row['student_id'] }}"
                                       data-instructor="{{ $row['instructor'] }}"
                                       data-date="{{ $row['date'] }}"
                                       data-time="{{ $row['time_range'] }}"
                                       data-duration="{{ $row['duration'] }}"
                                       data-type="{{ $row['type'] }}"
                                       data-mode="{{ $row['mode'] }}"
                                       data-status="{{ strtoupper((string) $row['status']) }}"
                                       data-summary="{{ $row['summary'] }}"
                                       data-action-taken="{{ $row['action_taken'] }}"
                                    >View</a>
                                </div>
                            </div>
                        @empty
                            <div class="admin-consultation-empty">No consultations found.</div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Consultation Pagination Controls -->
                <div id="consultationPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;padding:0 16px;">
                    <div id="consultationPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to 10 of 0 consultations
                    </div>
                    <div id="consultationPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevConsultationAdminBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="consultationPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextConsultationAdminBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">›</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="details-modal" id="consultationDetailsModal" aria-hidden="true">
    <div class="details-dialog">
        <div class="details-header">
            <div>
                <div class="details-title">Consultation Details</div>
                <div class="details-subtitle" id="detailsSubtitle">Consultation session details</div>
            </div>
            <button type="button" class="details-close" id="closeConsultationDetailsModal">x</button>
        </div>
        <div class="details-body">
            <div class="details-grid">
                <div class="details-card" id="detailsDate">Date & Time: --</div>
                <div class="details-card details-card-student" id="detailsStudent">
                    <span id="detailsStudentText">Student: --</span>
                    <span class="details-card-inline-id" id="detailsStudentInlineId">ID: --</span>
                </div>
                <div class="details-card" id="detailsStudentId">Student ID: --</div>
                <div class="details-card" id="detailsInstructor">Instructor: --</div>
                <div class="details-card" id="detailsMode">Mode: --</div>
                <div class="details-card" id="detailsType">Type: --</div>
                <div class="details-card" id="detailsDuration">Duration: --</div>
            </div>

            <div class="details-summary">
                <div class="details-summary-title">Summary</div>
                <div class="details-summary-text" id="detailsSummaryText">Summary not yet available.</div>
            </div>

            <div class="details-summary">
                <div class="details-summary-title">Action Taken</div>
                <div class="details-summary-text" id="detailsActionTakenText">Action taken not yet available.</div>
            </div>
        </div>
    </div>
</div>

<div class="manage-modal" id="manageUserModal" aria-hidden="true">
    <div class="manage-dialog">
        <div class="manage-head">
            <div class="manage-title">Manage User</div>
            <button type="button" class="manage-close" id="closeManageUserModal">x</button>
        </div>
        <div class="manage-body">
            <div class="manage-user">
                <div class="manage-avatar" id="manageAvatar">U</div>
                <div>
                    <div class="manage-name" id="manageName">—</div>
                    <div class="manage-email" id="manageEmail">—</div>
                    <div class="manage-meta" id="manageMeta">—</div>
                </div>
            </div>

            <div class="manage-row">
                <div class="manage-row-label">Role</div>
                <div class="manage-row-value" id="manageRole">—</div>
            </div>
            <div class="manage-row">
                <div class="manage-row-label">Joined Date</div>
                <div class="manage-row-value" id="manageJoined">—</div>
            </div>
            <div class="manage-row">
                <div class="manage-row-label">Total Consultations</div>
                <div class="manage-row-value" id="manageConsultations">0</div>
            </div>
            <div class="manage-row">
                <div class="manage-row-label">Current Status</div>
                <div><span class="status-tag status-active" id="manageCurrentStatus">active</span></div>
            </div>

            <div class="manage-actions-label">Change Status</div>
            <div class="manage-actions">
                <button type="button" class="manage-status-btn activate" data-status-value="active">Activate</button>
                <button type="button" class="manage-status-btn deactivate" data-status-value="inactive">Deactivate</button>
                <button type="button" class="manage-status-btn suspend" data-status-value="suspended">Suspend</button>
            </div>
        </div>
    </div>
</div>

<div class="add-modal" id="addInstructorModal" aria-hidden="true">
    <div class="add-dialog">
        <div class="add-head">
            <div class="add-title">Add Instructor</div>
            <button type="button" class="add-close" id="closeAddInstructor">x</button>
        </div>
        <div class="add-body">
            @if ($errors->any())
                <div class="add-alert">
                    <div style="font-weight:700;margin-bottom:6px;">Please fix the errors below.</div>
                    <ul style="margin:0;padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.instructors.store') }}">
                @csrf
                <div class="add-form-grid">
                    <div class="add-form-row">
                        <div>
                            <label class="add-label" for="add_first_name">First Name</label>
                            <input id="add_first_name" class="add-input" type="text" name="first_name" value="{{ old('first_name') }}" required>
                        </div>
                        <div>
                            <label class="add-label" for="add_last_name">Last Name</label>
                            <input id="add_last_name" class="add-input" type="text" name="last_name" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                    <div class="add-form-row">
                        <div>
                            <label class="add-label" for="add_middle_name">Middle Name</label>
                            <input id="add_middle_name" class="add-input" type="text" name="middle_name" value="{{ old('middle_name') }}">
                        </div>
                        <div>
                            <label class="add-label" for="add_email">Email</label>
                            <input id="add_email" class="add-input" type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="add-form-row">
                        <div>
                            <label class="add-label" for="add_password">Password</label>
                            <input id="add_password" class="add-input" type="password" name="password" required>
                        </div>
                        <div>
                            <label class="add-label" for="add_password_confirmation">Confirm Password</label>
                            <input id="add_password_confirmation" class="add-input" type="password" name="password_confirmation" required>
                        </div>
                    </div>
                </div>
                <div class="add-actions">
                    <button type="button" class="manage-status-btn suspend" id="cancelAddInstructor">Cancel</button>
                    <button type="submit" class="manage-status-btn activate">Create Instructor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="admin-notif-toast" id="adminNotifToast" aria-live="polite" aria-atomic="true">
    <div class="admin-notif-toast-head">
        <div>
            <p class="admin-notif-toast-title" id="adminNotifToastTitle">New Notification</p>
            <p class="admin-notif-toast-body" id="adminNotifToastBody">You have a new consultation update.</p>
        </div>
        <button class="admin-notif-toast-close" id="adminNotifToastClose" type="button" aria-label="Close notification">&times;</button>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menuBtn');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationPanel = document.getElementById('notificationPanel');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');
    const markAllReadForm = document.getElementById('markAllReadForm');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    const adminNotifToast = document.getElementById('adminNotifToast');
    const adminNotifToastTitle = document.getElementById('adminNotifToastTitle');
    const adminNotifToastBody = document.getElementById('adminNotifToastBody');
    const adminNotifToastClose = document.getElementById('adminNotifToastClose');
    const adminOverviewStudents = document.getElementById('adminOverviewStudents');
    const adminOverviewInstructors = document.getElementById('adminOverviewInstructors');
    const adminOverviewConsultations = document.getElementById('adminOverviewConsultations');
    const adminOverviewCompleted = document.getElementById('adminOverviewCompleted');
    const adminRecentConsultationsList = document.getElementById('adminRecentConsultationsList');
    const overviewSection = document.getElementById('overviewSection');
    const studentsSection = document.getElementById('studentsSection');
    const instructorsSection = document.getElementById('instructorsSection');
    const consultationsSection = document.getElementById('consultationsSection');
    const dashboardContentHeader = document.getElementById('dashboardContentHeader');
    const overviewLink = document.getElementById('overviewLink');
    const studentsLink = document.getElementById('studentsLink');
    const instructorsLink = document.getElementById('instructorsLink');
    const consultationsLink = document.getElementById('consultationsLink');
    const statisticsLink = document.getElementById('statisticsLink');
    const sidebarMenuLinks = Array.from(document.querySelectorAll('.sidebar-menu-link'));
    const sectionCloseTriggers = Array.from(document.querySelectorAll('.section-close-trigger'));
    const statsWorkspace = document.getElementById('statistics');
    const statsSemesterButtons = Array.from(document.querySelectorAll('.stats-semester-btn[data-stats-semester]'));
    const statsAcademicYearSelect = document.getElementById('statsAcademicYearSelect');
    const statsMonthSelect = document.getElementById('statsMonthSelect');
    const statsCategorySelect = document.getElementById('statsCategorySelect');
    const statsTopicSelect = document.getElementById('statsTopicSelect');
    const statsModeSelect = document.getElementById('statsModeSelect');
    const statsInstructorSelect = document.getElementById('statsInstructorSelect');
    const statsResetBtn = document.getElementById('statsResetBtn');
    const statsExportPdfBtn = document.getElementById('statsExportPdfBtn');
    const statsExportExcelBtn = document.getElementById('statsExportExcelBtn');
    const statsTotalConsultations = document.getElementById('statsTotalConsultations');
    const statsTypeCount = document.getElementById('statsTypeCount');
    const statsCurrentPeriod = document.getElementById('statsCurrentPeriod');
    const statsDistributionSubtitle = document.getElementById('statsDistributionSubtitle');
    const statsDonutChart = document.getElementById('statsDonutChart');
    const statsDonutTotal = document.getElementById('statsDonutTotal');
    const overviewTab = document.getElementById('overviewTab');
    const studentsTab = document.getElementById('studentsTab');
    const instructorsTab = document.getElementById('instructorsTab');
    const consultationsTab = document.getElementById('consultationsTab');
    const studentSearch = document.getElementById('studentSearch');
    const studentStatusFilter = document.getElementById('studentStatusFilter');
    const studentTableBody = document.getElementById('studentTableBody');
    const instructorSearch = document.getElementById('instructorSearch');
    const instructorStatusFilter = document.getElementById('instructorStatusFilter');
    const instructorTableBody = document.getElementById('instructorTableBody');
    const consultationSearch = document.getElementById('consultationSearch');
    const consultationStatusFilter = document.getElementById('consultationStatusFilter');
    const consultationYearInput = document.getElementById('consultationYearInput');
    const consultationSemButtons = Array.from(document.querySelectorAll('#consultationsSection .consultation-semester-btn[data-sem]'));
    const consultationMonthPickerContainer = document.getElementById('consultationMonthPickerContainer');
    const consultationMonthSelect = document.getElementById('consultationMonthSelect');
    const consultationTableBody = document.getElementById('consultationTableBody');
    const consultationDetailsModal = document.getElementById('consultationDetailsModal');
    let consultationViewButtons = Array.from(document.querySelectorAll('.consultation-view-btn'));
    let activeConsultationDetailsId = '';
    const closeConsultationDetailsModal = document.getElementById('closeConsultationDetailsModal');
    const detailsSubtitle = document.getElementById('detailsSubtitle');
    const detailsDate = document.getElementById('detailsDate');
    const detailsStudent = document.getElementById('detailsStudent');
    const detailsStudentText = document.getElementById('detailsStudentText');
    const detailsStudentInlineId = document.getElementById('detailsStudentInlineId');
    const detailsStudentId = document.getElementById('detailsStudentId');
    const detailsInstructor = document.getElementById('detailsInstructor');
    const detailsMode = document.getElementById('detailsMode');
    const detailsType = document.getElementById('detailsType');
    const detailsDuration = document.getElementById('detailsDuration');
    const detailsSummaryText = document.getElementById('detailsSummaryText');
    const detailsActionTakenText = document.getElementById('detailsActionTakenText');
    const manageUserModal = document.getElementById('manageUserModal');
    const manageUserButtons = document.querySelectorAll('.manage-user-btn');
    const closeManageUserModal = document.getElementById('closeManageUserModal');
    const manageAvatar = document.getElementById('manageAvatar');
    const manageName = document.getElementById('manageName');
    const manageEmail = document.getElementById('manageEmail');
    const manageMeta = document.getElementById('manageMeta');
    const manageRole = document.getElementById('manageRole');
    const manageJoined = document.getElementById('manageJoined');
    const manageConsultations = document.getElementById('manageConsultations');
    const manageCurrentStatus = document.getElementById('manageCurrentStatus');
    const manageStatusButtons = document.querySelectorAll('#manageUserModal .manage-status-btn');
    const openAddInstructor = document.getElementById('openAddInstructor');
    const addInstructorModal = document.getElementById('addInstructorModal');
    const closeAddInstructor = document.getElementById('closeAddInstructor');
    const cancelAddInstructor = document.getElementById('cancelAddInstructor');
    const statsSource = @json($statisticsRows ?? []);
    const latestNotification = @json($notifications->firstWhere('read', false));
    const unreadCount = @json($unreadCount);
    const adminToastUserId = @json(auth()->id());
    let activeManageRow = null;

    function syncSidebarBackdropState() {
        if (!sidebarBackdrop) return;
        const isMobile = window.innerWidth <= 900;
        const isOpen = isMobile && sidebar && sidebar.classList.contains('open');
        sidebarBackdrop.classList.toggle('active', Boolean(isOpen));
        sidebarBackdrop.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    }

    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('open');
                syncSidebarBackdropState();
                return;
            }
            sidebar.classList.toggle('open');
            syncSidebarBackdropState();
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', () => {
            if (!sidebar) return;
            sidebar.classList.remove('open');
            syncSidebarBackdropState();
        });
    }

    if (notificationBtn && notificationPanel) {
        notificationBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            notificationPanel.classList.toggle('active');
        });

        document.addEventListener('click', (event) => {
            if (!notificationPanel.contains(event.target) && !notificationBtn.contains(event.target)) {
                notificationPanel.classList.remove('active');
            }
        });
    }

    function updateAdminNotificationBadge(nextUnreadCount = 0) {
        if (!notificationBadge) return;
        const count = Number(nextUnreadCount || 0);
        notificationBadge.textContent = String(count);
        notificationBadge.style.display = count > 0 ? 'inline-flex' : 'none';
    }

    function escapeAdminNotificationHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function renderAdminNotificationList(notifications = []) {
        if (!notificationList) return;
        if (!Array.isArray(notifications) || notifications.length === 0) {
            notificationList.innerHTML = `
                <li class="notification-item">
                    <div>
                        <div style="font-weight:700">No notifications</div>
                        <div style="color:var(--muted);margin-top:4px">You're all caught up.</div>
                    </div>
                </li>
            `;
            return;
        }

        notificationList.innerHTML = notifications.map((notification) => {
            const title = escapeAdminNotificationHtml(notification?.title || 'Notification');
            const message = escapeAdminNotificationHtml(notification?.message || '');
            const timeLabel = escapeAdminNotificationHtml(
                notification?.created_at_human || notification?.timestamp || 'Just now'
            );
            const unreadClass = notification?.read || notification?.is_read ? '' : ' unread';

            return `
                <li class="notification-item${unreadClass}">
                    <span class="notification-dot"></span>
                    <div>
                        <div style="font-weight:700">${title}</div>
                        <div style="color:var(--muted);margin-top:4px">${message}</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:6px">${timeLabel}</div>
                    </div>
                </li>
            `;
        }).join('');
    }

    function formatAdminStatusLabel(status = '') {
        const normalized = String(status || '').toLowerCase();
        if (!normalized) return 'Pending';
        if (normalized === 'incompleted') return 'Incomplete';
        return normalized.replace(/_/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
    }

    function buildAdminConsultationSearchText(row = {}) {
        return String([
            row.code || '',
            row.student || '',
            row.instructor || '',
            row.date || '',
            row.time_range || '',
            row.duration || '',
            row.type || '',
            row.mode || '',
            row.status || '',
            row.summary || '',
            row.action_taken || '',
        ].join(' ')).toLowerCase();
    }

    function getAdminModeClass(mode = '') {
        const normalized = String(mode || '').toLowerCase();
        if (normalized.includes('audio')) return 'mode-audio';
        if (normalized.includes('video')) return 'mode-video';
        if (normalized.includes('face')) return 'mode-face';
        return 'mode-default';
    }

    function buildAdminConsultationRow(row = {}) {
        const student = escapeAdminNotificationHtml(row.student || 'Student');
        const instructor = escapeAdminNotificationHtml(row.instructor || 'Instructor');
        const date = escapeAdminNotificationHtml(row.date || '--');
        const timeRange = escapeAdminNotificationHtml(row.time_range || '--');
        const type = escapeAdminNotificationHtml(row.type || 'Consultation');
        const mode = escapeAdminNotificationHtml(row.mode || '--');
        const status = String(row.status || 'pending').toLowerCase();
        const statusLabel = escapeAdminNotificationHtml(formatAdminStatusLabel(status));
        const duration = escapeAdminNotificationHtml(row.duration || '--');
        const studentId = escapeAdminNotificationHtml(row.student_id || '--');
        const code = escapeAdminNotificationHtml(row.code || '--');
        const summary = escapeAdminNotificationHtml(row.summary || '');
        const actionTaken = escapeAdminNotificationHtml(row.action_taken || '');
        const modeClass = getAdminModeClass(mode);
        const searchAll = escapeAdminNotificationHtml(buildAdminConsultationSearchText(row));

        return `
            <div class="admin-consultation-row" data-status="${escapeAdminNotificationHtml(status)}" data-date="${date}" data-search-all="${searchAll}">
                <div class="admin-consultation-party">
                    <div class="admin-consultation-primary">${student}</div>
                    <div class="admin-consultation-secondary">ID: ${studentId}</div>
                </div>
                <div class="admin-consultation-party">
                    <div class="admin-consultation-primary">${instructor}</div>
                    <div class="admin-consultation-secondary">Instructor</div>
                </div>
                <div class="admin-consultation-datetime">
                    <div class="admin-consultation-date">${date}</div>
                    <div class="admin-consultation-time">${timeRange}</div>
                </div>
                <div class="admin-consultation-type">
                    <span class="admin-consultation-type-text">${type}</span>
                </div>
                <div class="admin-consultation-mode">
                    <span class="mode-pill ${modeClass}">${mode}</span>
                </div>
                <div class="admin-consultation-status">
                    <span class="status-tag status-${escapeAdminNotificationHtml(status)}">${statusLabel}</span>
                </div>
                <div class="admin-consultation-actions">
                    <a href="#"
                       class="action-view consultation-view-btn"
                       data-id="${code}"
                       data-student="${student}"
                       data-student-id="${studentId}"
                       data-instructor="${instructor}"
                       data-date="${date}"
                       data-time="${timeRange}"
                       data-duration="${duration}"
                       data-type="${type}"
                       data-mode="${mode}"
                       data-status="${statusLabel}"
                       data-summary="${summary}"
                       data-action-taken="${actionTaken}"
                    >View</a>
                </div>
            </div>
        `;
    }

    function bindConsultationViewButtons() {
        consultationViewButtons = Array.from(document.querySelectorAll('.consultation-view-btn'));
        consultationViewButtons.forEach((btn) => {
            if (btn.dataset.bound === 'true') return;
            btn.dataset.bound = 'true';
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                openConsultationDetails({
                    id: btn.dataset.id || '--',
                    status: btn.dataset.status || '--',
                    student: btn.dataset.student || '--',
                    studentId: btn.dataset.studentId || '--',
                    instructor: btn.dataset.instructor || '--',
                    date: btn.dataset.date || '--',
                    time: btn.dataset.time || '',
                    duration: btn.dataset.duration || '--',
                    mode: btn.dataset.mode || '--',
                    type: btn.dataset.type || '--',
                    summary: btn.dataset.summary || '',
                    actionTaken: btn.dataset.actionTaken || '',
                });
            });
        });
    }

    function refreshAdminConsultationTable(consultations = []) {
        const targetPage = currentConsultationPage;
        if (!consultationTableBody) return;
        if (!Array.isArray(consultations) || consultations.length === 0) {
            consultationTableBody.innerHTML = '<div class="admin-consultation-empty">No consultations found.</div>';
            consultationRowsAll = [];
            showConsultationPage(1, { scroll: false });
            bindConsultationViewButtons();
            return;
        }

        consultationTableBody.innerHTML = consultations.map((row) => buildAdminConsultationRow(row)).join('');
        consultationRowsAll = Array.from(consultationTableBody.querySelectorAll('.admin-consultation-row[data-status]'));
        showConsultationPage(targetPage, { scroll: false });
        bindConsultationViewButtons();
    }

    function syncOpenConsultationDetails(consultations = []) {
        if (!consultationDetailsModal || !consultationDetailsModal.classList.contains('open')) return;
        if (!activeConsultationDetailsId || !Array.isArray(consultations)) return;

        const matched = consultations.find((item) => String(item?.id || '') === String(activeConsultationDetailsId));
        if (!matched) return;

        openConsultationDetails({
            id: matched.id || '--',
            status: matched.status || '--',
            student: matched.student || '--',
            studentId: matched.student_id || '--',
            instructor: matched.instructor || '--',
            date: matched.date || '--',
            time: matched.time_range || '',
            duration: matched.duration || '--',
            mode: matched.mode || '--',
            type: matched.type || '--',
            summary: matched.summary || '',
            actionTaken: matched.action_taken || '',
        });
    }

    function renderAdminRecentConsultations(items = []) {
        if (!adminRecentConsultationsList) return;
        if (!Array.isArray(items) || items.length === 0) {
            adminRecentConsultationsList.innerHTML = '<div class="overview-empty">No recent consultations yet.</div>';
            return;
        }

        adminRecentConsultationsList.innerHTML = `
            <div class="recent-list">
                ${items.map((item) => {
                    const status = String(item?.status || 'pending').toLowerCase();
                    const statusLabel = escapeAdminNotificationHtml(formatAdminStatusLabel(status));
                    const title = escapeAdminNotificationHtml(item?.title || 'Consultation Session');
                    const student = escapeAdminNotificationHtml(item?.student || 'Student');
                    const instructor = escapeAdminNotificationHtml(item?.instructor || 'Instructor');
                    const dateLabel = escapeAdminNotificationHtml(item?.date_label || '--');
                    const timeLabel = escapeAdminNotificationHtml(item?.time_label || '--');

                    return `
                        <div class="recent-item">
                            <div class="recent-item-top">
                                <p class="recent-item-title">${title}</p>
                                <span class="recent-status-pill status-${escapeAdminNotificationHtml(status)}">${statusLabel}</span>
                            </div>
                            <div class="recent-item-meta">
                                <span><i class="fa-solid fa-users" aria-hidden="true"></i> ${student} with ${instructor}</span>
                                <span><i class="fa-solid fa-clock" aria-hidden="true"></i> ${dateLabel}, ${timeLabel}</span>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;
    }

    function updateAdminOverviewStats(stats = {}) {
        if (adminOverviewStudents && Object.prototype.hasOwnProperty.call(stats, 'total_students')) {
            adminOverviewStudents.textContent = String(Number(stats.total_students || 0));
        }
        if (adminOverviewInstructors && Object.prototype.hasOwnProperty.call(stats, 'total_instructors')) {
            adminOverviewInstructors.textContent = String(Number(stats.total_instructors || 0));
        }
        if (adminOverviewConsultations && Object.prototype.hasOwnProperty.call(stats, 'total_consultations')) {
            adminOverviewConsultations.textContent = String(Number(stats.total_consultations || 0));
        }
        if (adminOverviewCompleted && Object.prototype.hasOwnProperty.call(stats, 'completed_consultations')) {
            adminOverviewCompleted.textContent = String(Number(stats.completed_consultations || 0));
        }
    }

    function buildAdminNotificationToken(notification) {
        if (!notification) return '';
        const directId = notification.id ?? null;
        if (directId !== null && directId !== undefined && String(directId).trim() !== '') {
            return `admin:${directId}`;
        }

        return [
            notification.title ?? '',
            notification.message ?? '',
            notification.created_at ?? notification.timestamp ?? '',
        ].join('|');
    }

    function hasShownAdminToast(token) {
        if (!token) return false;
        try {
            return localStorage.getItem(`admin_last_toast_notification_${adminToastUserId || 'guest'}`) === token;
        } catch (_) {
            return false;
        }
    }

    function markShownAdminToast(token) {
        if (!token) return;
        try {
            localStorage.setItem(`admin_last_toast_notification_${adminToastUserId || 'guest'}`, token);
        } catch (_) {
            // ignore storage errors
        }
    }

    function showAdminNotificationToast(notification) {
        if (!notification || !adminNotifToast || !adminNotifToastTitle || !adminNotifToastBody) return;
        const token = buildAdminNotificationToken(notification);
        if (!token || hasShownAdminToast(token)) return;

        adminNotifToastTitle.textContent = notification.title ?? 'New Notification';
        adminNotifToastBody.textContent = notification.message ?? 'You have a new consultation update.';
        adminNotifToast.classList.add('show');
        markShownAdminToast(token);
        window.setTimeout(() => {
            adminNotifToast.classList.remove('show');
        }, 6000);
    }

    if (adminNotifToastClose) {
        adminNotifToastClose.addEventListener('click', () => {
            adminNotifToast?.classList.remove('show');
        });
    }

    if (markAllReadForm) {
        markAllReadForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (markAllReadBtn) {
                markAllReadBtn.disabled = true;
                markAllReadBtn.style.opacity = '0.65';
                markAllReadBtn.style.cursor = 'wait';
            }

            try {
                const response = await fetch(markAllReadForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: new FormData(markAllReadForm),
                });

                if (!response.ok) {
                    throw new Error(`Unable to mark notifications as read (${response.status})`);
                }

                const data = await response.json();
                updateAdminNotificationBadge(data?.unreadNotifications || 0);
                renderAdminNotificationList(data?.notifications || []);
                adminNotifToast?.classList.remove('show');
            } catch (error) {
                console.error('Failed to mark admin notifications as read.', error);
            } finally {
                if (markAllReadBtn) {
                    markAllReadBtn.disabled = false;
                    markAllReadBtn.style.opacity = '';
                    markAllReadBtn.style.cursor = '';
                }
            }
        });
    }

    async function pollAdminNotifications() {
        try {
            const response = await fetch('{{ route("api.admin.consultations-summary") }}', {
                cache: 'no-store',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`Admin notification poll failed (${response.status})`);
            }

            const data = await response.json();
            updateAdminNotificationBadge(data?.unreadNotifications || 0);
            renderAdminNotificationList(data?.notifications || []);
            updateAdminOverviewStats(data?.stats || {});
            refreshAdminConsultationTable(data?.consultations || []);
            syncOpenConsultationDetails(data?.consultations || []);
            renderAdminRecentConsultations(data?.recentConsultations || []);

            const latestUnreadNotification = data?.latestUnreadNotification || null;
            if (latestUnreadNotification) {
                showAdminNotificationToast(latestUnreadNotification);
            }
        } catch (error) {
            console.error('Failed to poll admin notifications.', error);
        }
    }

    if (unreadCount > 0 && latestNotification) {
        showAdminNotificationToast(latestNotification);
    }

    pollAdminNotifications();
    window.setInterval(pollAdminNotifications, 3000);

    function setActiveSidebar(linkId) {
        document.querySelectorAll('.sidebar-menu-link').forEach((link) => {
            link.classList.remove('active');
        });
        const activeLink = document.getElementById(linkId);
        if (activeLink) activeLink.classList.add('active');
    }

    function closeSidebarIfOpen() {
        if (!sidebar) return;
        sidebar.classList.remove('open');
        sidebar.classList.add('collapsed');
        syncSidebarBackdropState();
    }

    function setSidebarIconOnly(enabled) {
        if (!sidebar) return;
        const shouldEnable = Boolean(enabled) && window.innerWidth > 900;
        sidebar.classList.toggle('icon-only', shouldEnable);
        if (shouldEnable) {
            sidebar.classList.remove('collapsed');
            sidebar.classList.remove('open');
            syncSidebarBackdropState();
            return;
        }

        if (window.innerWidth > 900) {
            sidebar.classList.remove('collapsed');
            sidebar.classList.remove('open');
        }

        syncSidebarBackdropState();
    }

    window.addEventListener('resize', syncSidebarBackdropState);
    syncSidebarBackdropState();

    function scrollToOverviewTarget(targetId) {
        const target = document.getElementById(targetId);
        if (!target) return;
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function showOverview() {
        setSidebarIconOnly(false);
        if (overviewSection) overviewSection.classList.remove('statistics-only');
        if (dashboardContentHeader) dashboardContentHeader.classList.remove('is-hidden');
        if (overviewSection) overviewSection.classList.remove('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.add('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('overviewLink');
    }

    function showStudents() {
        setSidebarIconOnly(false);
        if (dashboardContentHeader) dashboardContentHeader.classList.add('is-hidden');
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.remove('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.add('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('studentsLink');
    }

    function showInstructors() {
        setSidebarIconOnly(false);
        if (dashboardContentHeader) dashboardContentHeader.classList.add('is-hidden');
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.remove('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.add('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('instructorsLink');
    }

    function showConsultations() {
        setSidebarIconOnly(false);
        if (dashboardContentHeader) dashboardContentHeader.classList.add('is-hidden');
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.remove('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.add('active');
        setActiveSidebar('consultationsLink');
    }

    function showStatistics() {
        setSidebarIconOnly(false);
        if (dashboardContentHeader) dashboardContentHeader.classList.add('is-hidden');
        if (overviewSection) {
            overviewSection.classList.remove('is-hidden');
            overviewSection.classList.add('statistics-only');
        }
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('statisticsLink');
    }

    const statsAllMonths = [
        { value: 1, label: 'January' },
        { value: 2, label: 'February' },
        { value: 3, label: 'March' },
        { value: 4, label: 'April' },
        { value: 5, label: 'May' },
        { value: 6, label: 'June' },
        { value: 7, label: 'July' },
        { value: 8, label: 'August' },
        { value: 9, label: 'September' },
        { value: 10, label: 'October' },
        { value: 11, label: 'November' },
        { value: 12, label: 'December' },
    ];

    const statsMonthsBySemester = {
        all: statsAllMonths,
        first: statsAllMonths.filter((month) => month.value >= 8),
        second: statsAllMonths.filter((month) => month.value <= 5),
    };

    const statsPalette = ['#0ea5a4', '#10b981', '#06b6d4', '#3b82f6', '#6366f1', '#14b8a6', '#0f766e', '#22d3ee'];

    function parseStatsDate(dateStr) {
        const raw = String(dateStr || '').trim();
        const match = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (!match) return null;
        return {
            year: Number(match[1]),
            month: Number(match[2]),
            day: Number(match[3]),
            raw,
        };
    }

    function getStatsSemester(month) {
        if (month >= 8 && month <= 12) return 'first';
        if (month >= 1 && month <= 5) return 'second';
        return '';
    }

    function getStatsAcademicYear(year, month) {
        if (month >= 8) return `${year}-${year + 1}`;
        if (month <= 5) return `${year - 1}-${year}`;
        return '';
    }

    function statsSemesterLabel(value) {
        if (value === 'all') return 'All Semesters';
        return value === 'second' ? '2nd Semester' : '1st Semester';
    }

    const statsNormalizedRows = Array.isArray(statsSource)
        ? statsSource
            .map((item) => {
                const parsed = parseStatsDate(item?.date);
                if (!parsed) return null;
                const semester = getStatsSemester(parsed.month);
                const academicYear = getStatsAcademicYear(parsed.year, parsed.month);
                if (!semester || !academicYear) return null;
                return {
                    date: parsed.raw,
                    month: parsed.month,
                    year: parsed.year,
                    semester,
                    academicYear,
                    type: String(item?.type || 'Consultation').trim() || 'Consultation',
                    category: String(item?.category || '').trim(),
                    topic: String(item?.topic || '').trim(),
                    status: String(item?.status || '').trim(),
                    mode: String(item?.mode || '').trim(),
                    student: String(item?.student || '').trim(),
                    instructor: String(item?.instructor || '').trim(),
                };
            })
            .filter(Boolean)
        : [];

    let selectedStatsSemester = 'all';
    let selectedStatsAcademicYear = '';
    let selectedStatsMonth = '';
    let selectedStatsCategory = '';
    let selectedStatsTopic = '';
    let selectedStatsMode = '';
    let selectedStatsInstructor = '';

    function populateStatsSelect(select, rows, key, placeholder) {
        if (!select) return;

        const values = Array.from(new Set(
            rows
                .map((row) => String(row?.[key] || '').trim())
                .filter(Boolean)
        )).sort((a, b) => a.localeCompare(b));

        const previousValue = String(select.value || '');
        select.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = placeholder;
        select.appendChild(defaultOption);

        values.forEach((value) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            select.appendChild(option);
        });

        select.value = values.includes(previousValue) ? previousValue : '';
    }

    function populateStatsAcademicYears() {
        if (!statsAcademicYearSelect) return;
        const years = Array.from(new Set(statsNormalizedRows.map((row) => row.academicYear)));
        years.sort((a, b) => Number(b.split('-')[0]) - Number(a.split('-')[0]));

        statsAcademicYearSelect.innerHTML = '';
        years.forEach((year) => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            statsAcademicYearSelect.appendChild(option);
        });

        selectedStatsAcademicYear = years[0] || '';
        statsAcademicYearSelect.value = selectedStatsAcademicYear;
    }

    function populateStatsMonths() {
        if (!statsMonthSelect) return;
        const monthOptions = statsMonthsBySemester[selectedStatsSemester] || [];
        statsMonthSelect.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'All months';
        statsMonthSelect.appendChild(defaultOption);
        monthOptions.forEach((month) => {
            const option = document.createElement('option');
            option.value = String(month.value);
            option.textContent = month.label;
            statsMonthSelect.appendChild(option);
        });

        const hasCurrent = monthOptions.some((month) => String(month.value) === String(selectedStatsMonth));
        selectedStatsMonth = hasCurrent
            ? String(selectedStatsMonth)
            : '';
        statsMonthSelect.value = selectedStatsMonth;
    }

    function getStatsRowsForAttributeFilters() {
        return statsNormalizedRows.filter((row) => {
            const matchSemester = selectedStatsSemester === 'all' || row.semester === selectedStatsSemester;
            const matchYear = !selectedStatsAcademicYear || row.academicYear === selectedStatsAcademicYear;
            const matchMonth = !selectedStatsMonth || String(row.month) === String(selectedStatsMonth);
            return matchSemester && matchYear && matchMonth;
        });
    }

    function populateStatsAttributeFilters() {
        const scopedRows = getStatsRowsForAttributeFilters();
        populateStatsSelect(statsCategorySelect, scopedRows, 'category', 'All categories');
        populateStatsSelect(statsTopicSelect, scopedRows, 'topic', 'All topics');
        populateStatsSelect(statsModeSelect, scopedRows, 'mode', 'All modes');
        populateStatsSelect(statsInstructorSelect, scopedRows, 'instructor', 'All instructors');

        selectedStatsCategory = statsCategorySelect?.value || '';
        selectedStatsTopic = statsTopicSelect?.value || '';
        selectedStatsMode = statsModeSelect?.value || '';
        selectedStatsInstructor = statsInstructorSelect?.value || '';
    }

    function getCurrentStatsRows() {
        return statsNormalizedRows.filter((row) => {
            const matchSemester = selectedStatsSemester === 'all' || row.semester === selectedStatsSemester;
            const matchYear = !selectedStatsAcademicYear || row.academicYear === selectedStatsAcademicYear;
            const matchMonth = !selectedStatsMonth || String(row.month) === String(selectedStatsMonth);
            const matchCategory = !selectedStatsCategory || row.category === selectedStatsCategory;
            const matchTopic = !selectedStatsTopic || row.topic === selectedStatsTopic;
            const matchMode = !selectedStatsMode || row.mode === selectedStatsMode;
            const matchInstructor = !selectedStatsInstructor || row.instructor === selectedStatsInstructor;
            return matchSemester
                && matchYear
                && matchMonth
                && matchCategory
                && matchTopic
                && matchMode
                && matchInstructor;
        });
    }

    function buildStatsDistribution(rows) {
        const typeMap = new Map();
        rows.forEach((row) => {
            typeMap.set(row.type, (typeMap.get(row.type) || 0) + 1);
        });
        return Array.from(typeMap.entries())
            .map(([type, count]) => ({ type, count }))
            .sort((a, b) => b.count - a.count || a.type.localeCompare(b.type));
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function renderStatsDonut(distribution, total) {
        if (!statsDonutChart) return;
        if (!total || !distribution.length) {
            statsDonutChart.innerHTML = '<div class="stats-bar-chart-empty">No consultation data for the selected period.</div>';
            return;
        }

        statsDonutChart.innerHTML = distribution.map((item, index) => {
            const percent = (item.count / total) * 100;
            const color = statsPalette[index % statsPalette.length];
            return `
                <div class="stats-bar-row">
                    <div class="stats-bar-label">${escapeHtml(item.type)}</div>
                    <div class="stats-bar-track">
                        <div class="stats-bar-fill" style="width:${percent.toFixed(1)}%;background:${color};"></div>
                    </div>
                    <div class="stats-bar-value">${percent.toFixed(1)}%</div>
                </div>
            `;
        }).join('');
    }

    function renderStatsLegend(distribution, total) {
        return;
    }

    function updateStatisticsWorkspace() {
        const rows = getCurrentStatsRows();
        const total = rows.length;
        const distribution = buildStatsDistribution(rows);
        const semesterLabel = statsSemesterLabel(selectedStatsSemester);
        const monthLabel = statsMonthsBySemester[selectedStatsSemester]
            ?.find((month) => String(month.value) === String(selectedStatsMonth))?.label || 'All months';

        if (statsTotalConsultations) statsTotalConsultations.textContent = String(total);
        if (statsTypeCount) statsTypeCount.textContent = String(distribution.length);
        if (statsCurrentPeriod) {
            const shortSemesterLabel = selectedStatsSemester === 'all'
                ? 'All Semesters'
                : (selectedStatsSemester === 'second' ? '2nd Sem' : '1st Sem');
            statsCurrentPeriod.textContent = `${shortSemesterLabel} ${selectedStatsAcademicYear || 'N/A'}`;
        }
        if (statsDistributionSubtitle) {
            statsDistributionSubtitle.textContent = `${monthLabel} - ${semesterLabel} ${selectedStatsAcademicYear || ''}`.trim();
        }
        if (statsDonutTotal) statsDonutTotal.textContent = String(total);

        renderStatsDonut(distribution, total);
        renderStatsLegend(distribution, total);
    }

    function escapeCsvCell(value) {
        const raw = String(value ?? '');
        if (/[",\n]/.test(raw)) {
            return `"${raw.replace(/"/g, '""')}"`;
        }
        return raw;
    }

    function exportStatisticsCsv() {
        const rows = getCurrentStatsRows();
        const header = ['Date', 'Student', 'Instructor', 'Type', 'Category', 'Topic', 'Status', 'Mode', 'Semester', 'Academic Year', 'Month'];
        const body = rows.map((row) => {
            const monthLabel = statsMonthsBySemester[row.semester]
                ?.find((month) => month.value === row.month)?.label || row.month;
            return [
                row.date,
                row.student,
                row.instructor,
                row.type,
                row.category || 'N/A',
                row.topic || 'N/A',
                row.status || 'N/A',
                row.mode || 'N/A',
                statsSemesterLabel(row.semester),
                row.academicYear,
                monthLabel,
            ];
        });
        const csvContent = [header, ...body]
            .map((line) => line.map((item) => escapeCsvCell(item)).join(','))
            .join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `statistics-${selectedStatsAcademicYear || 'report'}-${selectedStatsSemester}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function exportStatisticsPdf() {
        const rows = getCurrentStatsRows();
        const distribution = buildStatsDistribution(rows);
        const periodText = `${statsSemesterLabel(selectedStatsSemester)} ${selectedStatsAcademicYear || ''}`.trim();
        const monthLabel = statsMonthsBySemester[selectedStatsSemester]
            ?.find((month) => String(month.value) === String(selectedStatsMonth))?.label || 'All months';
        const filterSummary = [
            selectedStatsCategory ? `Category: ${selectedStatsCategory}` : '',
            selectedStatsTopic ? `Topic: ${selectedStatsTopic}` : '',
            selectedStatsMode ? `Mode: ${selectedStatsMode}` : '',
            selectedStatsInstructor ? `Instructor: ${selectedStatsInstructor}` : '',
        ].filter(Boolean).join(' | ');
        const safeFilterSummary = escapeHtml(filterSummary);

        const popup = window.open('', '_blank', 'width=980,height=740');
        if (!popup) return;
        popup.document.write(`
            <html>
            <head>
                <title>Statistics Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 24px; color: #0f172a; }
                    h1 { margin: 0 0 4px; font-size: 24px; }
                    .sub { margin: 0 0 20px; color: #475569; font-size: 13px; }
                    .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px; }
                    .card { border: 1px solid #cbd5e1; border-radius: 10px; padding: 12px; }
                    .label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 6px; }
                    .value { font-size: 24px; font-weight: 900; }
                    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
                    th, td { border: 1px solid #cbd5e1; padding: 8px; font-size: 12px; text-align: left; }
                    th { background: #f8fafc; }
                </style>
            </head>
            <body>
                <h1>Consultation Statistics Report</h1>
                <p class="sub">${monthLabel} - ${periodText}</p>
                ${safeFilterSummary ? `<p class="sub">${safeFilterSummary}</p>` : ''}
                <div class="cards">
                    <div class="card"><div class="label">Total Consultations</div><div class="value">${rows.length}</div></div>
                    <div class="card"><div class="label">Consultation Types</div><div class="value">${distribution.length}</div></div>
                    <div class="card"><div class="label">Current Period</div><div class="value" style="font-size:18px">${periodText}</div></div>
                </div>
                <h3>Consultation Type Distribution</h3>
                <table>
                    <thead><tr><th>Rank</th><th>Type</th><th>Count</th><th>Percent</th></tr></thead>
                    <tbody>
                        ${distribution.map((item, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.type}</td>
                                <td>${item.count}</td>
                                <td>${rows.length ? ((item.count / rows.length) * 100).toFixed(1) : '0.0'}%</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <h3 style="margin-top:20px;">Consultation Records</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Instructor</th>
                            <th>Category</th>
                            <th>Topic</th>
                            <th>Mode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rows.map((item) => `
                            <tr>
                                <td>${item.date}</td>
                                <td>${item.student || 'N/A'}</td>
                                <td>${item.instructor || 'N/A'}</td>
                                <td>${item.category || 'N/A'}</td>
                                <td>${item.topic || 'N/A'}</td>
                                <td>${item.mode || 'N/A'}</td>
                                <td>${item.status || 'N/A'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </body>
            </html>
        `);
        popup.document.close();
        popup.focus();
        popup.print();
    }

    function initializeStatisticsWorkspace() {
        if (!statsWorkspace) return;

        populateStatsAcademicYears();
        populateStatsMonths();
        populateStatsAttributeFilters();
        updateStatisticsWorkspace();

        if (statsSemesterButtons.length) {
            statsSemesterButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    statsSemesterButtons.forEach((item) => item.classList.remove('active'));
                    btn.classList.add('active');
                    selectedStatsSemester = btn.dataset.statsSemester || 'all';
                    populateStatsMonths();
                    populateStatsAttributeFilters();
                    updateStatisticsWorkspace();
                });
            });
        }

        if (statsAcademicYearSelect) {
            statsAcademicYearSelect.addEventListener('change', () => {
                selectedStatsAcademicYear = statsAcademicYearSelect.value || '';
                populateStatsAttributeFilters();
                updateStatisticsWorkspace();
            });
        }

        if (statsMonthSelect) {
            statsMonthSelect.addEventListener('change', () => {
                selectedStatsMonth = statsMonthSelect.value || '';
                populateStatsAttributeFilters();
                updateStatisticsWorkspace();
            });
        }

        if (statsCategorySelect) {
            statsCategorySelect.addEventListener('change', () => {
                selectedStatsCategory = statsCategorySelect.value || '';
                updateStatisticsWorkspace();
            });
        }

        if (statsTopicSelect) {
            statsTopicSelect.addEventListener('change', () => {
                selectedStatsTopic = statsTopicSelect.value || '';
                updateStatisticsWorkspace();
            });
        }

        if (statsModeSelect) {
            statsModeSelect.addEventListener('change', () => {
                selectedStatsMode = statsModeSelect.value || '';
                updateStatisticsWorkspace();
            });
        }

        if (statsInstructorSelect) {
            statsInstructorSelect.addEventListener('change', () => {
                selectedStatsInstructor = statsInstructorSelect.value || '';
                updateStatisticsWorkspace();
            });
        }

        if (statsResetBtn) {
            statsResetBtn.addEventListener('click', () => {
                selectedStatsSemester = 'all';
                selectedStatsCategory = '';
                selectedStatsTopic = '';
                selectedStatsMode = '';
                selectedStatsInstructor = '';
                statsSemesterButtons.forEach((btn) => btn.classList.toggle('active', btn.dataset.statsSemester === 'all'));
                populateStatsAcademicYears();
                populateStatsMonths();
                populateStatsAttributeFilters();
                updateStatisticsWorkspace();
            });
        }

        if (statsExportExcelBtn) {
            statsExportExcelBtn.addEventListener('click', exportStatisticsCsv);
        }

        if (statsExportPdfBtn) {
            statsExportPdfBtn.addEventListener('click', exportStatisticsPdf);
        }
    }

    if (overviewLink) {
        overviewLink.addEventListener('click', (event) => {
            event.preventDefault();
            showOverview();
            if (window.innerWidth <= 900) {
                closeSidebarIfOpen();
            }
        });
    }

    if (studentsLink) {
        studentsLink.addEventListener('click', (event) => {
            event.preventDefault();
            showStudents();
        });
    }

    if (instructorsLink) {
        instructorsLink.addEventListener('click', (event) => {
            event.preventDefault();
            showInstructors();
        });
    }

    if (consultationsLink) {
        consultationsLink.addEventListener('click', (event) => {
            event.preventDefault();
            showConsultations();
        });
    }

    if (statisticsLink) {
        statisticsLink.addEventListener('click', (event) => {
            event.preventDefault();
            showStatistics();
            scrollToOverviewTarget('statistics');
        });
    }

    if (sidebarMenuLinks.length) {
        sidebarMenuLinks.forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 900) {
                    closeSidebarIfOpen();
                }
            });
        });
    }

    if (sectionCloseTriggers.length) {
        sectionCloseTriggers.forEach((btn) => {
            btn.addEventListener('click', () => {
                setSidebarIconOnly(false);
                showOverview();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                if (sidebar) {
                    sidebar.classList.remove('collapsed');
                    sidebar.classList.add('open');
                }
            });
        });
    }

    if (overviewTab) {
        overviewTab.addEventListener('click', (event) => {
            event.preventDefault();
            showOverview();
        });
    }

    if (studentsTab) {
        studentsTab.addEventListener('click', (event) => {
            event.preventDefault();
            showStudents();
        });
    }

    if (instructorsTab) {
        instructorsTab.addEventListener('click', (event) => {
            event.preventDefault();
            showInstructors();
        });
    }

    if (consultationsTab) {
        consultationsTab.addEventListener('click', (event) => {
            event.preventDefault();
            showConsultations();
        });
    }

    initializeStatisticsWorkspace();

    function filterStudentsTable() {
        if (!studentTableBody) return;
        const searchValue = (studentSearch?.value || '').toLowerCase().trim();
        const selectedStatus = (studentStatusFilter?.value || '').toLowerCase().trim();

        studentTableBody.querySelectorAll('tr[data-status]').forEach((row) => {
            const rowSearch = row.dataset.search || '';
            const rowStatus = (row.dataset.status || '').toLowerCase();
            const matchSearch = !searchValue || rowSearch.includes(searchValue);
            const matchStatus = !selectedStatus || rowStatus === selectedStatus;
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });
    }

    if (studentSearch) {
        studentSearch.addEventListener('input', filterStudentsTable);
    }

    if (studentStatusFilter) {
        studentStatusFilter.addEventListener('change', filterStudentsTable);
    }

    function filterInstructorsTable() {
        if (!instructorTableBody) return;
        const searchValue = (instructorSearch?.value || '').toLowerCase().trim();
        const selectedStatus = (instructorStatusFilter?.value || '').toLowerCase().trim();

        instructorTableBody.querySelectorAll('tr[data-status]').forEach((row) => {
            const rowSearch = row.dataset.search || '';
            const rowStatus = (row.dataset.status || '').toLowerCase();
            const matchSearch = !searchValue || rowSearch.includes(searchValue);
            const matchStatus = !selectedStatus || rowStatus === selectedStatus;
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });
    }

    if (instructorSearch) {
        instructorSearch.addEventListener('input', filterInstructorsTable);
    }

    if (instructorStatusFilter) {
        instructorStatusFilter.addEventListener('change', filterInstructorsTable);
    }

    // Generate years 2026-2027, 2028-2029, ..., 9090-9091 for admin consultation filtering
    function generateYearRange(start, end, step = 1) {
        const years = [];
        for (let y = start; y <= end; y += step) {
            years.push(`${y}-${y + 1}`);
        }
        return years;
    }

    const adminGeneratedYears = generateYearRange(2026, 9090, 2);

    const consultationSemesterMonths = {
        '1': [
            { name: 'August', num: 8 },
            { name: 'September', num: 9 },
            { name: 'October', num: 10 },
            { name: 'November', num: 11 },
            { name: 'December', num: 12 },
        ],
        '2': [
            { name: 'January', num: 1 },
            { name: 'February', num: 2 },
            { name: 'March', num: 3 },
            { name: 'April', num: 4 },
            { name: 'May', num: 5 },
        ],
    };

    const consultationAllMonths = [
        { name: 'January', num: 1 },
        { name: 'February', num: 2 },
        { name: 'March', num: 3 },
        { name: 'April', num: 4 },
        { name: 'May', num: 5 },
        { name: 'June', num: 6 },
        { name: 'July', num: 7 },
        { name: 'August', num: 8 },
        { name: 'September', num: 9 },
        { name: 'October', num: 10 },
        { name: 'November', num: 11 },
        { name: 'December', num: 12 },
    ];

    let selectedConsultationMonth = null;

    function normalizeSearchText(value) {
        return String(value || '')
            .toLowerCase()
            .replace(/\s+/g, ' ')
            .trim();
    }

    function getDateParts(dateStr) {
        if (!dateStr) return null;

        const normalized = String(dateStr).trim();
        const match = normalized.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (match) {
            return {
                year: Number(match[1]),
                month: Number(match[2]),
            };
        }

        try {
            const date = new Date(normalized);
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            if (!Number.isNaN(month) && !Number.isNaN(year)) {
                return { year, month };
            }
        } catch (e) {
            return null;
        }

        return null;
    }

    function getAcademicYearFromDate(dateStr) {
        const parts = getDateParts(dateStr);
        if (!parts) return null;
        const { month, year } = parts;
        if (month >= 8) {
            return year + '-' + (year + 1);
        }
        if (month <= 5) {
            return (year - 1) + '-' + year;
        }
        return null;
    }

    function getSemesterFromDate(dateStr) {
        const parts = getDateParts(dateStr);
        if (!parts) return '';
        if (parts.month >= 8 && parts.month <= 12) return '1';
        if (parts.month >= 1 && parts.month <= 5) return '2';
        return '';
    }

    function getMonthFromDate(dateStr) {
        const parts = getDateParts(dateStr);
        return parts ? parts.month : null;
    }

    function renderConsultationMonthSelector(semester) {
        if (!consultationMonthSelect) return;

        const targetSemester = String(semester || 'all');
        const availableMonths = targetSemester === '1'
            ? consultationSemesterMonths['1']
            : (targetSemester === '2'
                ? consultationSemesterMonths['2']
                : consultationAllMonths);

        const previousMonth = selectedConsultationMonth;
        consultationMonthSelect.innerHTML = '<option value="">All months</option>';
        availableMonths.forEach((month) => {
            const option = document.createElement('option');
            option.value = month.num;
            option.textContent = month.name;
            consultationMonthSelect.appendChild(option);
        });

        const hasPreviousMonth = previousMonth !== null
            && availableMonths.some((month) => month.num === Number(previousMonth));
        selectedConsultationMonth = hasPreviousMonth ? Number(previousMonth) : null;
        consultationMonthSelect.value = selectedConsultationMonth ? String(selectedConsultationMonth) : '';

        consultationMonthSelect.onchange = () => {
            selectedConsultationMonth = consultationMonthSelect.value
                ? parseInt(consultationMonthSelect.value, 10)
                : null;
            filterConsultationsTable();
        };

        if (consultationMonthPickerContainer) {
            consultationMonthPickerContainer.style.display = '';
        }

        filterConsultationsTable();
    }

    function getFilteredConsultationRows() {
        if (!consultationTableBody) return [];

        const searchValue = normalizeSearchText(consultationSearch?.value || '');
        const selectedStatus = normalizeSearchText(consultationStatusFilter?.value || '');
        const yearValue = normalizeSearchText(consultationYearInput?.value || '');
        const selectedSemBtn = consultationSemButtons.find((btn) => btn.classList.contains('active'));
        const selectedSemester = selectedSemBtn ? (selectedSemBtn.dataset.sem || 'all') : 'all';

        return Array.from(consultationTableBody.querySelectorAll('.admin-consultation-row[data-status]')).filter((row) => {
            const rowSearch = normalizeSearchText(
                row.dataset.searchAll
                || row.dataset.search
                || row.textContent
                || ''
            );
            const rowStatus = normalizeSearchText(row.dataset.status || '');
            const rowDateStr = row.dataset.date || '';
            const rowYear = normalizeSearchText(getAcademicYearFromDate(rowDateStr));
            const rowSemester = getSemesterFromDate(rowDateStr);
            const rowMonth = getMonthFromDate(rowDateStr);

            const matchSearch = !searchValue || rowSearch.includes(searchValue);
            const matchStatus = !selectedStatus || rowStatus === selectedStatus;
            const matchYear = !yearValue || (rowYear && rowYear.includes(yearValue));
            const matchSemester = selectedSemester === 'all' || rowSemester === selectedSemester;
            const matchMonth = !selectedConsultationMonth
                || rowMonth === Number(selectedConsultationMonth);

            return matchSearch && matchStatus && matchYear && matchSemester && matchMonth;
        });
    }

    function filterConsultationsTable(options = {}) {
        if (!consultationTableBody) return;
        const { preservePage = false } = options;
        const targetPage = preservePage ? currentConsultationPage : 1;
        showConsultationPage(targetPage, { scroll: false });
    }

    if (consultationSearch) {
        consultationSearch.addEventListener('input', filterConsultationsTable);
    }

    if (consultationStatusFilter) {
        consultationStatusFilter.addEventListener('change', filterConsultationsTable);
    }

    if (consultationYearInput) {
        consultationYearInput.addEventListener('input', filterConsultationsTable);
    }

    if (consultationSemButtons.length) {
        consultationSemButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                consultationSemButtons.forEach((item) => item.classList.remove('active'));
                btn.classList.add('active');
                renderConsultationMonthSelector(btn.dataset.sem || 'all');
            });
        });
    }

    // ===== STUDENT ACCOUNTS PAGINATION =====
    const studentTableElm = document.querySelector('#studentsSection .students-table');
    const studentRowsAll = Array.from(document.querySelectorAll('#studentTableBody tr[data-status]'));
    const studentPaginationInfo = document.getElementById('studentPaginationInfo');
    const studentPageNumbers = document.getElementById('studentPageNumbers');
    const prevStudentBtn = document.getElementById('prevStudentBtn');
    const nextStudentBtn = document.getElementById('nextStudentBtn');

    const studentItemsPerPage = 10;
    let currentStudentPage = 1;
    let totalStudentItems = studentRowsAll.length;
    let totalStudentPages = Math.ceil(totalStudentItems / studentItemsPerPage);

    function createStudentPagination() {
        if (!studentPageNumbers) return;
        studentPageNumbers.innerHTML = '';
        
        for (let i = 1; i <= totalStudentPages; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pagination-page-btn' + (i === currentStudentPage ? ' active' : '');
            btn.textContent = i;
            btn.addEventListener('click', () => showStudentPage(i));
            studentPageNumbers.appendChild(btn);
        }
        
        prevStudentBtn.style.display = currentStudentPage > 1 ? 'block' : 'none';
        nextStudentBtn.style.display = currentStudentPage < totalStudentPages ? 'block' : 'none';
    }

    function showStudentPage(pageNum) {
        currentStudentPage = pageNum;
        const visibleRows = studentRowsAll.filter(row => row.style.display !== 'none');
        const start = (pageNum - 1) * studentItemsPerPage;
        const end = start + studentItemsPerPage;
        
        studentRowsAll.forEach((item, index) => {
            const isVisible = visibleRows.includes(item);
            const isInRange = isVisible && visibleRows.indexOf(item) >= start && visibleRows.indexOf(item) < end;
            item.style.display = isInRange ? '' : 'none';
        });
        
        const displayStart = visibleRows.length > 0 ? Math.min(start + 1, visibleRows.length) : 0;
        const displayEnd = Math.min(end, visibleRows.length);
        studentPaginationInfo.textContent = `Showing ${displayStart} to ${displayEnd} of ${visibleRows.length} students`;
        
        createStudentPagination();
        if (studentTableElm) {
            window.scrollTo({ top: studentTableElm.offsetTop - 100, behavior: 'smooth' });
        }
    }

    if (prevStudentBtn) {
        prevStudentBtn.addEventListener('click', () => {
            if (currentStudentPage > 1) showStudentPage(currentStudentPage - 1);
        });
    }

    if (nextStudentBtn) {
        nextStudentBtn.addEventListener('click', () => {
            if (currentStudentPage < totalStudentPages) showStudentPage(currentStudentPage + 1);
        });
    }

    // Initialize student pagination on page load
    if (totalStudentPages > 0) {
        showStudentPage(1);
    } else {
        studentPaginationInfo.textContent = 'No students found';
    }

    // Update student pagination when filters change
    const originalFilterStudentsTable = filterStudentsTable;
    filterStudentsTable = function() {
        originalFilterStudentsTable();
        
        const visibleRows = studentRowsAll.filter(row => row.style.display !== 'none');
        totalStudentItems = visibleRows.length;
        totalStudentPages = Math.ceil(totalStudentItems / studentItemsPerPage) || 1;
        currentStudentPage = 1;
        
        if (totalStudentPages > 0) {
            showStudentPage(1);
        } else {
            studentPaginationInfo.textContent = 'No students found';
            studentPageNumbers.innerHTML = '';
            prevStudentBtn.style.display = 'none';
            nextStudentBtn.style.display = 'none';
        }
    };

    // ===== INSTRUCTOR ACCOUNTS PAGINATION =====
    const instructorTableElm = document.querySelector('#instructorsSection .students-table');
    const instructorRowsAll = Array.from(document.querySelectorAll('#instructorTableBody tr[data-status]'));
    const instructorPaginationInfo = document.getElementById('instructorPaginationInfo');
    const instructorPageNumbers = document.getElementById('instructorPageNumbers');
    const prevInstructorBtn = document.getElementById('prevInstructorBtn');
    const nextInstructorBtn = document.getElementById('nextInstructorBtn');

    const instructorItemsPerPage = 10;
    let currentInstructorPage = 1;
    let totalInstructorItems = instructorRowsAll.length;
    let totalInstructorPages = Math.ceil(totalInstructorItems / instructorItemsPerPage);

    function createInstructorPagination() {
        if (!instructorPageNumbers) return;
        instructorPageNumbers.innerHTML = '';
        
        for (let i = 1; i <= totalInstructorPages; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pagination-page-btn' + (i === currentInstructorPage ? ' active' : '');
            btn.textContent = i;
            btn.addEventListener('click', () => showInstructorPage(i));
            instructorPageNumbers.appendChild(btn);
        }
        
        prevInstructorBtn.style.display = currentInstructorPage > 1 ? 'block' : 'none';
        nextInstructorBtn.style.display = currentInstructorPage < totalInstructorPages ? 'block' : 'none';
    }

    function showInstructorPage(pageNum) {
        currentInstructorPage = pageNum;
        const visibleRows = instructorRowsAll.filter(row => row.style.display !== 'none');
        const start = (pageNum - 1) * instructorItemsPerPage;
        const end = start + instructorItemsPerPage;
        
        instructorRowsAll.forEach((item, index) => {
            const isVisible = visibleRows.includes(item);
            const isInRange = isVisible && visibleRows.indexOf(item) >= start && visibleRows.indexOf(item) < end;
            item.style.display = isInRange ? '' : 'none';
        });
        
        const displayStart = visibleRows.length > 0 ? Math.min(start + 1, visibleRows.length) : 0;
        const displayEnd = Math.min(end, visibleRows.length);
        instructorPaginationInfo.textContent = `Showing ${displayStart} to ${displayEnd} of ${visibleRows.length} instructors`;
        
        createInstructorPagination();
        if (instructorTableElm) {
            window.scrollTo({ top: instructorTableElm.offsetTop - 100, behavior: 'smooth' });
        }
    }

    if (prevInstructorBtn) {
        prevInstructorBtn.addEventListener('click', () => {
            if (currentInstructorPage > 1) showInstructorPage(currentInstructorPage - 1);
        });
    }

    if (nextInstructorBtn) {
        nextInstructorBtn.addEventListener('click', () => {
            if (currentInstructorPage < totalInstructorPages) showInstructorPage(currentInstructorPage + 1);
        });
    }

    // Initialize instructor pagination on page load
    if (totalInstructorPages > 0) {
        showInstructorPage(1);
    } else {
        instructorPaginationInfo.textContent = 'No instructors found';
    }

    // Update instructor pagination when filters change
    const originalFilterInstructorsTable = filterInstructorsTable;
    filterInstructorsTable = function() {
        originalFilterInstructorsTable();
        
        const visibleRows = instructorRowsAll.filter(row => row.style.display !== 'none');
        totalInstructorItems = visibleRows.length;
        totalInstructorPages = Math.ceil(totalInstructorItems / instructorItemsPerPage) || 1;
        currentInstructorPage = 1;
        
        if (totalInstructorPages > 0) {
            showInstructorPage(1);
        } else {
            instructorPaginationInfo.textContent = 'No instructors found';
            instructorPageNumbers.innerHTML = '';
            prevInstructorBtn.style.display = 'none';
            nextInstructorBtn.style.display = 'none';
        }
    };

    // ===== CONSULTATION PAGINATION =====
    const consultationTable = document.querySelector('#consultationsSection .admin-consultation-shell');
    let consultationRowsAll = Array.from(document.querySelectorAll('#consultationTableBody .admin-consultation-row[data-status]'));
    const consultationPaginationInfo = document.getElementById('consultationPaginationInfo');
    const consultationPageNumbers = document.getElementById('consultationPageNumbers');
    const prevConsultationAdminBtn = document.getElementById('prevConsultationAdminBtn');
    const nextConsultationAdminBtn = document.getElementById('nextConsultationAdminBtn');

    const consultationItemsPerPage = 10;
    let currentConsultationPage = 1;
    let totalConsultationItems = consultationRowsAll.length;
    let totalConsultationPages = totalConsultationItems > 0
        ? Math.ceil(totalConsultationItems / consultationItemsPerPage)
        : 0;

    function createConsultationPagination() {
        if (!consultationPageNumbers) return;
        consultationPageNumbers.innerHTML = '';
        
        for (let i = 1; i <= totalConsultationPages; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pagination-page-btn' + (i === currentConsultationPage ? ' active' : '');
            btn.textContent = i;
            btn.addEventListener('click', () => showConsultationPage(i));
            consultationPageNumbers.appendChild(btn);
        }

        if (prevConsultationAdminBtn) {
            prevConsultationAdminBtn.style.display = currentConsultationPage > 1 ? 'block' : 'none';
        }
        if (nextConsultationAdminBtn) {
            nextConsultationAdminBtn.style.display = currentConsultationPage < totalConsultationPages ? 'block' : 'none';
        }
    }

    function showConsultationPage(pageNum, options = {}) {
        const { scroll = true } = options;
        const filteredRows = getFilteredConsultationRows();

        totalConsultationItems = filteredRows.length;
        totalConsultationPages = totalConsultationItems > 0
            ? Math.ceil(totalConsultationItems / consultationItemsPerPage)
            : 0;

        if (totalConsultationItems === 0) {
            consultationRowsAll.forEach((row) => {
                row.style.display = 'none';
            });
            currentConsultationPage = 1;
            if (consultationPaginationInfo) {
                consultationPaginationInfo.textContent = 'No consultations found';
            }
            createConsultationPagination();
            return;
        }

        currentConsultationPage = Math.min(Math.max(1, pageNum), totalConsultationPages);
        const start = (currentConsultationPage - 1) * consultationItemsPerPage;
        const end = start + consultationItemsPerPage;

        const filteredIndexMap = new Map(filteredRows.map((row, index) => [row, index]));
        consultationRowsAll.forEach((item) => {
            const index = filteredIndexMap.get(item);
            item.style.display = (index !== undefined && index >= start && index < end) ? 'grid' : 'none';
        });

        const displayStart = Math.min(start + 1, totalConsultationItems);
        const displayEnd = Math.min(end, totalConsultationItems);
        if (consultationPaginationInfo) {
            consultationPaginationInfo.textContent = `Showing ${displayStart} to ${displayEnd} of ${totalConsultationItems} consultations`;
        }
        
        createConsultationPagination();
        if (scroll && consultationTable && consultationsSection && !consultationsSection.classList.contains('is-hidden')) {
            window.scrollTo({ top: consultationTable.offsetTop - 100, behavior: 'smooth' });
        }
    }

    if (prevConsultationAdminBtn) {
        prevConsultationAdminBtn.addEventListener('click', () => {
            if (currentConsultationPage > 1) showConsultationPage(currentConsultationPage - 1);
        });
    }

    if (nextConsultationAdminBtn) {
        nextConsultationAdminBtn.addEventListener('click', () => {
            if (currentConsultationPage < totalConsultationPages) showConsultationPage(currentConsultationPage + 1);
        });
    }

    // Initialize consultation filters + pagination on page load
    renderConsultationMonthSelector('all');

    function openConsultationDetails(data) {
        if (!consultationDetailsModal) return;
        activeConsultationDetailsId = String(data.id || '');

        const typeText = data.type || '--';
        const modeText = data.mode || '--';
        const dateText = data.date || '--';
        const timeText = data.time || '--';
        const studentText = data.student || '--';
        const studentIdText = data.studentId || '--';
        const instructorText = data.instructor || '--';
        const durationText = data.duration || '--';

        if (detailsSubtitle) detailsSubtitle.textContent = `${typeText} - ${modeText} Session`;
        if (detailsDate) detailsDate.textContent = `Date & Time: ${dateText} at ${timeText}`;
        if (detailsStudentText) {
            detailsStudentText.textContent = `Student: ${studentText}`;
        } else if (detailsStudent) {
            detailsStudent.textContent = `Student: ${studentText}`;
        }
        if (detailsStudentInlineId) detailsStudentInlineId.textContent = `ID: ${studentIdText}`;
        if (detailsStudentId) detailsStudentId.textContent = `Student ID: ${studentIdText}`;
        if (detailsInstructor) detailsInstructor.textContent = `Instructor: ${instructorText}`;
        if (detailsMode) detailsMode.textContent = `Mode: ${modeText}`;
        if (detailsType) detailsType.textContent = `Type: ${typeText}`;
        if (detailsDuration) detailsDuration.textContent = `Duration: ${durationText}`;
        if (detailsSummaryText) detailsSummaryText.textContent = data.summary || 'Summary not yet available.';
        if (detailsActionTakenText) detailsActionTakenText.textContent = data.actionTaken || 'Action taken not yet available.';
        consultationDetailsModal.classList.add('open');
        consultationDetailsModal.setAttribute('aria-hidden', 'false');
    }

    function closeConsultationDetails() {
        if (!consultationDetailsModal) return;
        activeConsultationDetailsId = '';
        consultationDetailsModal.classList.remove('open');
        consultationDetailsModal.setAttribute('aria-hidden', 'true');
    }

    if (consultationViewButtons.length) {
        consultationViewButtons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                openConsultationDetails({
                    id: btn.dataset.id || '—',
                    status: btn.dataset.status || '—',
                    student: btn.dataset.student || '—',
                    studentId: btn.dataset.studentId || '--',
                    instructor: btn.dataset.instructor || '—',
                    date: btn.dataset.date || '—',
                    time: btn.dataset.time || '',
                    duration: btn.dataset.duration || '--',
                    mode: btn.dataset.mode || '—',
                    type: btn.dataset.type || '—',
                    summary: btn.dataset.summary || '',
                    actionTaken: btn.dataset.actionTaken || '',
                });
            });
        });
    }

    bindConsultationViewButtons();

    if (closeConsultationDetailsModal) {
        closeConsultationDetailsModal.addEventListener('click', closeConsultationDetails);
    }

    if (consultationDetailsModal) {
        consultationDetailsModal.addEventListener('click', (event) => {
            if (event.target === consultationDetailsModal) {
                closeConsultationDetails();
            }
        });
    }

    function applyStatusPill(el, status) {
        if (!el) return;
        const normalized = String(status || '').toLowerCase();
        const pillClass = normalized === 'active'
            ? 'status-active'
            : (normalized === 'inactive'
                ? 'status-inactive'
                : (normalized === 'suspended' ? 'status-suspended' : 'status-inactive'));
        el.className = `status-tag ${pillClass}`;
        el.textContent = normalized || 'inactive';
    }

    function openManageModal(data, row) {
        if (!manageUserModal) return;
        activeManageRow = row || null;
        if (manageAvatar) manageAvatar.textContent = (data.name || 'U').charAt(0).toUpperCase();
        if (manageName) manageName.textContent = data.name || '—';
        if (manageEmail) manageEmail.textContent = data.email || '—';
        if (manageMeta) manageMeta.textContent = data.meta || '—';
        if (manageRole) manageRole.textContent = data.role || '—';
        if (manageJoined) manageJoined.textContent = data.joined || '—';
        if (manageConsultations) manageConsultations.textContent = data.consultations || '0';
        applyStatusPill(manageCurrentStatus, data.status || 'inactive');
        manageUserModal.classList.add('open');
        manageUserModal.setAttribute('aria-hidden', 'false');
    }

    function closeManageModal() {
        if (!manageUserModal) return;
        manageUserModal.classList.remove('open');
        manageUserModal.setAttribute('aria-hidden', 'true');
        activeManageRow = null;
    }

    function openAddInstructorModal() {
        if (!addInstructorModal) return;
        addInstructorModal.classList.add('open');
        addInstructorModal.setAttribute('aria-hidden', 'false');
    }

    function closeAddInstructorModal() {
        if (!addInstructorModal) return;
        addInstructorModal.classList.remove('open');
        addInstructorModal.setAttribute('aria-hidden', 'true');
    }

    if (openAddInstructor) {
        openAddInstructor.addEventListener('click', openAddInstructorModal);
    }

    if (closeAddInstructor) {
        closeAddInstructor.addEventListener('click', closeAddInstructorModal);
    }

    if (cancelAddInstructor) {
        cancelAddInstructor.addEventListener('click', closeAddInstructorModal);
    }

    if (addInstructorModal) {
        addInstructorModal.addEventListener('click', (event) => {
            if (event.target === addInstructorModal) {
                closeAddInstructorModal();
            }
        });
    }

    const hasAddInstructorErrors = @json($errors->any());
    if (hasAddInstructorErrors) {
        openAddInstructorModal();
    }

    if (manageUserButtons.length) {
        manageUserButtons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                const row = btn.closest('tr');
                openManageModal({
                    role: btn.dataset.role || '—',
                    name: btn.dataset.name || '—',
                    email: btn.dataset.email || '—',
                    meta: btn.dataset.meta || '—',
                    joined: btn.dataset.joined || '—',
                    consultations: btn.dataset.consultations || '0',
                    status: btn.dataset.status || 'inactive',
                }, row);
            });
        });
    }

    if (manageStatusButtons.length) {
        manageStatusButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const nextStatus = btn.dataset.statusValue || 'inactive';
                applyStatusPill(manageCurrentStatus, nextStatus);

                if (activeManageRow) {
                    activeManageRow.dataset.status = nextStatus;
                    const rowPill = activeManageRow.querySelector('.status-tag');
                    applyStatusPill(rowPill, nextStatus);
                }
            });
        });
    }

    if (closeManageUserModal) {
        closeManageUserModal.addEventListener('click', closeManageModal);
    }

    if (manageUserModal) {
        manageUserModal.addEventListener('click', (event) => {
            if (event.target === manageUserModal) {
                closeManageModal();
            }
        });
    }
</script>
@endsection
