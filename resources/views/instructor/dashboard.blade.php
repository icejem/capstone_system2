@extends('layouts.app')

@section('title')

@section('content')
@php
    $consultations = $consultations ?? collect();
    $notifications = $notifications ?? collect();
    $feedbacks = $feedbacks ?? collect();
    $feedbackStats = $feedbackStats ?? [
        'average_rating' => 0,
        'total_feedback' => 0,
        'positive_rate' => 0,
        'this_month' => 0,
    ];
    $unreadCount = $notifications->where('is_read', false)->count();
    $userName = auth()->user()->name ?? 'Instructor';
    $userEmail = auth()->user()->email ?? '';
    $formatManilaTime = function (?string $time): string {
        if (! $time) {
            return '--:--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('H:i');
    };
    $formatManilaTime12 = function (?string $time): string {
        if (! $time) {
            return '--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        $formatted = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i A');
        return strtolower(str_replace(' ', '', $formatted));
    };
    $formatManilaRange = function (?string $start, ?string $end) use ($formatManilaTime12): string {
        if (! $start && ! $end) {
            return '--';
        }
        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');
            return $formatManilaTime12($start) . ' to ' . $formatManilaTime12($endValue);
        }
        return $formatManilaTime12($start) . ' to ' . $formatManilaTime12($end);
    };
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
    $onlineStudentIds = $onlineStudentIds ?? [];
    $todayManila = \Illuminate\Support\Carbon::now('Asia/Manila')->toDateString();
    $nowManila = \Illuminate\Support\Carbon::now('Asia/Manila');
    $isUpcomingStatus = function (?string $status): bool {
        $value = strtolower((string) $status);
        return in_array($value, ['pending', 'approved', 'in_progress'], true);
    };
    $isUpcomingConsultation = function ($consultation) use ($todayManila, $nowManila, $isUpcomingStatus): bool {
        $status = strtolower((string) ($consultation->status ?? ''));
        if (! $isUpcomingStatus($status)) {
            return false;
        }

        $date = (string) ($consultation->consultation_date ?? '');
        if ($date === '') {
            return false;
        }
        if ($date > $todayManila) {
            return true;
        }
        if ($date < $todayManila) {
            return false;
        }

        if ($status === 'in_progress') {
            return true;
        }

        $timeRaw = (string) ($consultation->consultation_time ?? '');
        if ($timeRaw === '') {
            return true;
        }

        try {
            $timeValue = strlen($timeRaw) === 5 ? $timeRaw . ':00' : $timeRaw;
            $startAt = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timeValue, 'Asia/Manila');
            return $startAt->greaterThanOrEqualTo($nowManila);
        } catch (\Throwable $e) {
            return true;
        }
    };
    $recentConsultations = $consultations
        ->sortByDesc(function ($consultation) {
            return sprintf(
                '%s %s',
                (string) ($consultation->consultation_date ?? '0000-00-00'),
                (string) ($consultation->consultation_time ?? '00:00:00')
            );
        })
        ->take(3)
        ->values();
    $upcomingConsultations = $consultations
        ->filter(function ($consultation) use ($isUpcomingConsultation) {
            return $isUpcomingConsultation($consultation);
        })
        ->sortBy(function ($consultation) {
            return sprintf(
                '%s %s',
                (string) ($consultation->consultation_date ?? '9999-12-31'),
                (string) ($consultation->consultation_time ?? '23:59:59')
            );
        })
        ->take(3)
        ->values();
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

    body {
        background: var(--bg);
        font-family: "Segoe UI", Inter, sans-serif;
    }

    .dashboard {
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        background: linear-gradient(180deg, #1F3A8A 0%, #1e40af 100%);
        box-shadow: 2px 0 16px rgba(31, 58, 138, 0.1);
        padding: 28px 0;
        position: fixed;
        inset: 0 auto 0 0;
        z-index: 20;
        display: flex;
        flex-direction: column;
        animation: slideInLeft 0.5s ease-out;
        transition: transform 0.25s ease, width 0.25s ease;
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
        font-weight: 700;
        font-size: 14px;
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
        padding: 0;
        margin: 0;
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
        border-left: 3px solid transparent;
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
        font-weight: 600;
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
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        background: #4A90E2;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(74, 144, 226, 0.3);
    }

    /* Main */
    .main {
        margin-left: 250px;
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .topbar {
        background: linear-gradient(180deg, #f0f9ff, #dbeafe);
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #bfdbfe;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 6px 18px rgba(31, 58, 138, 0.08);
        animation: slideInTop 0.5s ease-out;
        display: none;
    }
    .topbar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        z-index: 40;
    }
    .notification-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #e5e7eb;
        background: #f1f5f9;
        color: #334155;
        font-size: 16px;
        cursor: pointer;
        position: relative;
        padding: 0;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .notification-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
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
        padding: 0 5px;
        font-size: 9px;
        font-weight: 800;
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
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 20px 40px rgba(31, 58, 138, 0.25);
        border: 1px solid var(--border);
        display: none;
        flex-direction: column;
        max-height: 420px;
        overflow: hidden;
        z-index: 30;
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
        list-style: none;
        margin: 0;
        padding: 0;
        overflow-y: auto;
        max-height: 320px;
    }
    .notification-item {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f1f1;
        font-size: 13px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        background: #ffffff;
        transition: background-color 0.3s ease;
    }
    .notification-item.unread { background: #f0f9ff; }
    .notification-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--brand);
        margin-top: 6px;
        flex-shrink: 0;
    }
    .toast {
        position: fixed;
        top: 16px;
        right: 16px;
        background: #ffffff;
        border: 1px solid var(--border);
        box-shadow: 0 12px 30px rgba(31, 58, 138, 0.18);
        padding: 12px 14px;
        border-radius: 12px;
        display: none;
        z-index: 60;
        min-width: 240px;
        animation: popIn 0.4s ease-out;
    }
    .toast.show { display: block; }
    .toast-title { font-weight: 700; margin-bottom: 4px; }
    .toast-body { font-size: 13px; color: var(--muted); }
    .toast-close {
        position: absolute;
        top: 6px;
        right: 8px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .menu-btn {
        display: none;
        background: none;
        border: 1px solid var(--border);
        padding: 6px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
    }

    .topbar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .profile {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
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
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.3);
    }

    .header-profile-trigger:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.35);
    }

    .header-avatar {
        font-size: 16px;
        font-weight: 800;
        line-height: 1;
    }

    .content {
        padding: 28px 24px 40px;
    }

    .content-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
        position: relative;
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

    /* Banner */
    .banner {
        background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        color: #fff;
        padding: 26px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 28px;
    }

    .banner-title {
        font-size: 18px;
        font-weight: 700;
    }

    .btn {
        background: #fff;
        color: var(--brand);
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
    }

    /* Stats */
    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 18px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--surface);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--shadow);
        display: flex;
        gap: 16px;
        align-items: center;
        border-top: 4px solid var(--brand);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: popIn 0.5s ease-out backwards;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.15s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.25s; }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 44px rgba(31, 58, 138, 0.18);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: var(--brand-soft);
        display: grid;
        place-items: center;
        color: var(--brand);
        font-weight: 800;
        font-size: 18px;
    }

    .stat-count {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .overview-panels {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 28px;
    }

    .overview-panel {
        background: #f3f4f6;
        border: 1px solid #d8dde6;
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }

    .overview-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        gap: 12px;
    }

    .overview-panel-title {
        margin: 0;
        font-size: 24px;
        line-height: 1.1;
        font-weight: 800;
        color: #111827;
    }

    .overview-panel-link {
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

    .overview-panel-link:hover {
        text-decoration: underline;
    }

    .overview-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        font-size: 13px;
        color: #64748b;
        background: #f8fafc;
    }

    .recent-list,
    .schedule-list {
        display: grid;
        gap: 12px;
    }

    .recent-item {
        background: linear-gradient(180deg, #22408f 0%, #1f3a8a 100%);
        border: 1px solid #1f3a8a;
        border-radius: 12px;
        padding: 14px 12px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    .recent-item-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .recent-item-title {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #f8fafc;
    }

    .recent-item-meta {
        margin-top: 6px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: 12px;
        color: #dbeafe;
        font-weight: 700;
    }

    .recent-status-pill {
        border-radius: 999px;
        padding: 5px 12px;
        font-size: 11px;
        font-weight: 800;
        text-transform: capitalize;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .recent-status-pill.status-approved { background: #23b05f; color: #f0fdf4; border-color: #1a9a53; }
    .recent-status-pill.status-pending { background: #f59e0b; color: #fff7ed; border-color: #ea8a00; }
    .recent-status-pill.status-completed { background: #dbe7ff; color: #1e3a8a; border-color: #bcd0ff; }
    .recent-status-pill.status-in_progress { background: #c7d2fe; color: #3730a3; border-color: #a8b8ff; }
    .recent-status-pill.status-incompleted { background: #fef3c7; color: #92400e; border-color: #f59e0b; }
    .recent-status-pill.status-declined,
    .recent-status-pill.status-cancelled { background: #f97366; color: #fff1f2; border-color: #ef5b4b; }

    .schedule-item {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 12px;
        align-items: center;
        background: linear-gradient(180deg, #22408f 0%, #1f3a8a 100%);
        border: 1px solid #1f3a8a;
        border-radius: 12px;
        padding: 12px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .schedule-date-chip {
        width: 58px;
        border: 1px solid #c7d2fe;
        border-radius: 10px;
        padding: 6px 4px;
        text-align: center;
        background: #fff;
    }

    .schedule-date-day {
        display: block;
        font-size: 30px;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }

    .schedule-date-month {
        display: block;
        margin-top: 2px;
        font-size: 10px;
        letter-spacing: 0.4px;
        font-weight: 800;
        color: #64748b;
    }

    .schedule-title {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 800;
        color: #f8fafc;
    }

    .schedule-time {
        margin: 0;
        font-size: 12px;
        color: #dbeafe;
        font-weight: 700;
    }

    /* Sections */
    .section {
        background: var(--surface);
        border-radius: 16px;
        padding: 22px;
        box-shadow: var(--shadow);
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 14px;
    }

    .consultation-list {
        display: grid;
        gap: 12px;
    }

    .request-table {
        display: grid;
        gap: 12px;
        background: transparent;
        border: none;
        box-shadow: none;
        align-items: stretch;
    }

    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .section-close {
        border: 1px solid var(--border);
        background: #fff;
        color: var(--text);
        border-radius: 10px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .section-close:hover {
        background: #f9fafb;
    }

    .section.is-hidden {
        display: none;
    }

    .feedback-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 16px;
    }

    .feedback-stat-card {
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #ffffff;
        padding: 14px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: popIn 0.5s ease-out backwards;
    }

    .feedback-stat-card:nth-child(1) { animation-delay: 0.15s; }
    .feedback-stat-card:nth-child(2) { animation-delay: 0.2s; }
    .feedback-stat-card:nth-child(3) { animation-delay: 0.25s; }
    .feedback-stat-card:nth-child(4) { animation-delay: 0.3s; }

    .feedback-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 44px rgba(31, 58, 138, 0.18);
    }

    .feedback-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        margin-bottom: 10px;
        font-size: 18px;
    }

    .feedback-stat-value {
        font-size: 34px;
        line-height: 1;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .feedback-stat-label {
        font-size: 13px;
        color: var(--muted);
    }

    .feedback-list {
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
    }

    .feedback-list-head {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        font-weight: 800;
        background: #fbfaff;
    }

    .feedback-item {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
        background: #ffffff;
        transition: all 0.3s ease;
        animation: fadeIn 0.5s ease-out backwards;
    }

    .feedback-item:nth-child(1) { animation-delay: 0.15s; }
    .feedback-item:nth-child(2) { animation-delay: 0.2s; }
    .feedback-item:nth-child(3) { animation-delay: 0.25s; }
    .feedback-item:nth-child(4) { animation-delay: 0.3s; }

    .feedback-item:hover {
        background: #f0f9ff;
    }

    .feedback-item:last-child {
        border-bottom: none;
    }

    .feedback-item-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
    }

    .feedback-student {
        font-weight: 700;
    }

    .feedback-meta {
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 8px;
    }

    .feedback-stars {
        color: #f59e0b;
        letter-spacing: 1px;
        font-size: 16px;
        margin-bottom: 6px;
    }

    .feedback-comment {
        font-size: 13px;
        color: #334155;
    }

    .request-row {
        display: grid;
        grid-template-columns: 1.3fr 1fr 1.2fr 1.2fr auto;
        gap: 16px;
        align-items: start;
        padding: 16px 18px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: #faf9ff;
        transition: all 0.3s ease;
        animation: popIn 0.5s ease-out backwards;
        flex: 1;
    }

    .request-row:nth-child(1) { animation-delay: 0.1s; }
    .request-row:nth-child(2) { animation-delay: 0.15s; }
    .request-row:nth-child(3) { animation-delay: 0.2s; }
    .request-row:nth-child(4) { animation-delay: 0.25s; }
    .request-row:nth-child(5) { animation-delay: 0.3s; }

    .request-row:hover {
        border-color: var(--brand);
        box-shadow: 0 12px 26px rgba(31, 58, 138, 0.14);
        background: #f0f9ff;
        transform: translateY(-2px);
    }

    .request-row-wrap {
        display: flex;
        flex-direction: column;
    }

    .history-row-wrap {
        position: relative;
    }

    .request-user {
        display: grid;
        gap: 4px;
    }

    .request-user-name {
        font-weight: 800;
        font-size: 13px;
    }

    .request-user-email {
        color: var(--muted);
        font-size: 12px;
    }
    .request-user-id {
        color: var(--muted);
        font-size: 12px;
    }

    .request-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        width: max-content;
    }

    .request-status.pending { background: #fff7ed; color: #9a3412; }
    .request-status.approved { background: #ecfdf5; color: #065f46; }
    .request-status.in_progress { background: #ede9fe; color: #5b21b6; }
    .request-status.completed { background: #f1f5f9; color: #334155; }
    .request-status.incompleted { background: #fef3c7; color: #92400e; }
    .request-status.declined { background: #fef2f2; color: #b91c1c; }

    .request-meta {
        display: grid;
        gap: 4px;
        font-size: 12px;
        color: var(--muted);
        line-height: 1.4;
    }

    .request-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .request-note-preview {
        display: block;
        margin-top: 4px;
        color: #4b5563;
        font-size: 11.5px;
        line-height: 1.4;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        word-break: break-word;
    }

    .request-note-label {
        font-weight: 700;
        color: #1f2937;
    }

    .request-tags {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .request-tag {
        font-size: 11px;
        font-weight: 700;
        border-radius: 999px;
        padding: 3px 8px;
        background: #eef2ff;
        color: #4338ca;
    }

    .request-tag.face { background: #fff7ed; color: #c2410c; }

    .request-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
        align-self: start;
    }
    .request-actions form {
        display: inline-flex;
    }

    .request-filter-row {
        margin: 8px 0 14px;
        display: flex;
        gap: 14px;
        align-items: flex-end;
        flex-wrap: wrap;
        max-width: 100%;
    }

    .request-filter-group {
        display: grid;
        gap: 8px;
        min-width: 260px;
        flex: 1 1 300px;
        max-width: 360px;
    }

    .request-filter-label {
        font-size: 14px;
        font-weight: 700;
        color: var(--muted);
    }

    .request-status-filter {
        position: relative;
        width: 100%;
    }

    .request-status-filter-btn {
        width: 100%;
        border: 2px solid #5b6bff;
        border-radius: 10px;
        background: #fff;
        color: #6b7280;
        font-size: 14px;
        font-weight: 700;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
    }

    .request-status-filter-caret {
        color: #111827;
        font-size: 13px;
        line-height: 1;
    }

    .request-status-filter-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        border: 2px solid #5b6bff;
        border-radius: 10px;
        background: #fff;
        padding: 10px 12px;
        display: none;
        z-index: 35;
        box-shadow: 0 14px 28px rgba(31, 58, 138, 0.12);
    }

    .request-status-filter-menu.open {
        display: grid;
        gap: 10px;
    }

    .request-status-filter-option {
        border: none;
        background: transparent;
        text-align: left;
        padding: 0;
        cursor: pointer;
    }

    .request-status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .request-status-pill.all { background: #eef2ff; color: #4338ca; }
    .request-status-pill.pending { background: #fef3c7; color: #92400e; }
    .request-status-pill.approved { background: #d1fae5; color: #166534; }
    .request-status-pill.in_progress { background: #ede9fe; color: #5b21b6; }
    .request-status-pill.completed { background: #cfeef6; color: #155e75; }
    .request-status-pill.incompleted { background: #fef3c7; color: #92400e; }
    .request-status-pill.decline { background: #fee2e2; color: #991b1b; }

    .request-search-wrap {
        display: grid;
        gap: 8px;
    }

    .request-search-input {
        width: 100%;
        border: 2px solid #d1d5db;
        border-radius: 10px;
        background: #fff;
        color: #111827;
        font-size: 14px;
        font-weight: 600;
        padding: 12px 14px;
    }

    .request-search-input:focus {
        outline: none;
        border-color: #5b6bff;
        box-shadow: 0 0 0 3px rgba(91, 107, 255, 0.15);
    }

    .request-table-shell {
        border: 1px solid #dbe1ea;
        border-radius: 14px;
        background: #ffffff;
        overflow-x: auto;
    }

    .request-table-head {
        min-width: 1080px;
        display: grid;
        grid-template-columns: 1.45fr 1.25fr 2fr 1fr 1fr 0.9fr 1.1fr;
        align-items: center;
        background: #eef2f7;
        border-bottom: 1px solid #dbe1ea;
    }

    .request-table-head > div {
        padding: 12px 14px;
        font-size: 11px;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #425066;
        font-weight: 800;
    }

    .request-table {
        display: block;
    }

    .request-row-wrap {
        display: block;
    }

    .request-row {
        min-width: 1080px;
        width: 100%;
        grid-template-columns: 1.45fr 1.25fr 2fr 1fr 1fr 0.9fr 1.1fr;
        gap: 0;
        align-items: center;
        padding: 0;
        border: 0;
        border-bottom: 1px solid #edf1f6;
        border-radius: 0;
        background: #ffffff;
        box-shadow: none;
        transform: none !important;
    }

    .request-row:hover {
        background: #f8fbff;
        box-shadow: none;
        border-color: transparent;
        transform: none;
    }

    .request-user,
    .request-meta,
    .request-status-col,
    .request-updated-col,
    .request-actions {
        padding: 12px 14px;
    }

    .request-user {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .request-user-main {
        width: 100%;
        min-width: 0;
    }

    .request-avatar {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        background: linear-gradient(135deg, #7489ff 0%, #5b6bff 100%);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        flex: 0 0 auto;
    }

    .request-user-top {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .request-user-name {
        font-weight: 800;
        font-size: 14px;
        line-height: 1.15;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .request-user-id {
        font-size: 11px;
        color: #64748b;
        line-height: 1.35;
    }

    .request-meta.request-datetime {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        gap: 4px;
    }

    .request-meta.request-datetime i {
        color: #0f172a;
        font-size: 11px;
        width: 14px;
        text-align: center;
    }

    .request-meta.request-type .request-type-title {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.2;
    }

    .request-meta.request-type .request-tag {
        margin-top: 4px;
    }

    .request-meta.request-mode .request-tag {
        font-size: 12px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 999px;
        border: 1px solid #bfd3f5;
        background: #eaf1ff;
        color: #214a93;
    }

    .request-meta.request-mode .request-tag.face {
        border-color: #fbcfe8;
        background: #fdf2f8;
        color: #9d174d;
    }

    .request-updated-col {
        font-size: 12px;
        color: #64748b;
        font-style: italic;
        white-space: nowrap;
    }

    .request-user .online-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        font-weight: 700;
        color: #22c55e;
        background: #f0fdf4;
        border: 1.5px solid #bbf7d0;
        border-radius: 20px;
        padding: 2px 9px;
        letter-spacing: 0.01em;
    }

    .request-user .instructor-active-minutes-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 11px;
        font-weight: 600;
        color: #888;
        background: #f5f5f8;
        border: 1.5px solid #e0e0e8;
        border-radius: 20px;
        padding: 2px 8px;
        margin-top: 2px;
        width: fit-content;
    }

    .request-actions {
        justify-content: flex-start;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 7px;
    }

    .request-actions .request-tag {
        font-size: 12px;
        font-weight: 700;
        padding: 7px 12px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
    }

    @media (max-width: 768px) {
        .request-filter-row {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .request-filter-group {
            min-width: 0;
            max-width: none;
            width: 100%;
        }
    }

    .request-btn {
        border: none;
        border-radius: 8px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        line-height: 1.15;
        box-shadow: none;
        min-width: 95px;
        text-align: center;
    }

    .request-btn.approve { background: #10b981; color: #fff; }
    .request-btn.decline { background: #ef4444; color: #fff; }
    .request-btn.start { background: linear-gradient(135deg, var(--brand), var(--brand-dark)); color: #fff; }
    .request-btn.view { background: #ffffff; color: var(--brand); border: 1px solid var(--border); box-shadow: none; }
    .request-btn.delete { background: #111827; color: #fff; }
    .request-btn.summary { background: #0ea5e9; color: #fff; }

    .request-status {
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        padding: 6px 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .request-status.pending {
        color: #9a3412;
        background: #fffbeb;
        border: 1px solid #f5a623;
    }
    .request-status.approved {
        color: #1a60bb;
        background: #eef4ff;
        border: 1px solid #4a90e2;
    }
    .request-status.in_progress {
        color: #2d7a00;
        background: #f0fff0;
        border: 1px solid #7ed321;
    }
    .request-status.completed {
        color: #555;
        background: #f5f5f5;
        border: 1px solid #bbb;
    }
    .request-status.incompleted {
        color: #92400e;
        background: #fffbeb;
        border: 1px solid #f59e0b;
    }
    .request-status.declined {
        color: #b00020;
        background: #fff0f0;
        border: 1px solid #d0021b;
    }

    .consultation-card {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px;
        display: grid;
        grid-template-columns: 1.4fr 1fr 1fr auto;
        gap: 14px;
        align-items: center;
        transition: border 0.2s ease, box-shadow 0.2s ease;
        background: #fff;
    }

    .consultation-card:hover {
        border-color: rgba(74, 144, 226, 0.4);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .status {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fff7ed;
        color: #9a3412;
    }

    .status-approved {
        background: #ecfdf5;
        color: #065f46;
    }

    .status-completed {
        background: #f1f5f9;
        color: #334155;
    }

    .meta {
        color: var(--muted);
        font-size: 13px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-btn {
        background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        color: #fff;
        border: none;
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .action-btn.secondary {
        background: #fff;
        color: var(--brand);
        border: 1px solid var(--brand);
    }

    .availability-head {
        display: flex;
        animation: slideInLeft 0.5s ease-out;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .availability-open-btn {
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }
    .availability-open-btn:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .availability-grid {
        display: none;
    }

    .availability-pill {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 14px;
        background: #f9fafb;
        display: grid;
        gap: 6px;
        transition: all 0.3s ease;
        animation: popIn 0.5s ease-out backwards;
    }

    .availability-pill:nth-child(1) { animation-delay: 0.1s; }
    .availability-pill:nth-child(2) { animation-delay: 0.15s; }
    .availability-pill:nth-child(3) { animation-delay: 0.2s; }
    .availability-pill:nth-child(4) { animation-delay: 0.25s; }
    .availability-pill:nth-child(5) { animation-delay: 0.3s; }
    .availability-pill:nth-child(6) { animation-delay: 0.35s; }

    .availability-pill:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(31, 58, 138, 0.12);
        border-color: #4A90E2;
    }

    .availability-pill-day {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        text-transform: capitalize;
    }

    .availability-pill-time {
        font-size: 13px;
        color: #4b5563;
    }

    .schedule-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 10px 14px;
        align-items: start;
        animation: fadeIn 0.5s ease-out 0.08s backwards;
    }

    .schedule-day {
        font-size: 12px;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        text-align: center;
        padding-bottom: 6px;
        border-bottom: 1px solid #e5e7eb;
        animation: slideInTop 0.5s ease-out backwards;
    }

    .schedule-day:nth-child(1) { animation-delay: 0.1s; }
    .schedule-day:nth-child(2) { animation-delay: 0.12s; }
    .schedule-day:nth-child(3) { animation-delay: 0.14s; }
    .schedule-day:nth-child(4) { animation-delay: 0.16s; }
    .schedule-day:nth-child(5) { animation-delay: 0.18s; }
    .schedule-day:nth-child(6) { animation-delay: 0.2s; }

    .schedule-cell {
        display: flex;
        justify-content: center;
        min-height: 64px;
        animation: popIn 0.5s ease-out backwards;
    }

    .schedule-cell:nth-child(7) { animation-delay: 0.2s; }
    .schedule-cell:nth-child(8) { animation-delay: 0.25s; }
    .schedule-cell:nth-child(9) { animation-delay: 0.3s; }
    .schedule-cell:nth-child(10) { animation-delay: 0.35s; }
    .schedule-cell:nth-child(11) { animation-delay: 0.4s; }
    .schedule-cell:nth-child(12) { animation-delay: 0.45s; }

    .schedule-slot {
        border: 1px solid #9fe3d9;
        background: #ecfeff;
        color: #0f766e;
        border-radius: 10px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        line-height: 1.2;
        min-width: 86px;
        transition: all 0.3s ease;
        animation: popIn 0.5s ease-out backwards;
    }

    .schedule-slot:nth-child(1) { animation-delay: 0.15s; }
    .schedule-slot:nth-child(2) { animation-delay: 0.2s; }
    .schedule-slot:nth-child(3) { animation-delay: 0.25s; }
    .schedule-slot:nth-child(4) { animation-delay: 0.3s; }
    .schedule-slot:nth-child(5) { animation-delay: 0.35s; }
    .schedule-slot:nth-child(6) { animation-delay: 0.4s; }

    .schedule-slot:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 16px rgba(20, 184, 166, 0.2);
        border-color: #14b8a6;
    }

    .schedule-slot span {
        display: block;
        font-weight: 600;
        color: #14b8a6;
        margin: 4px 0;
    }

    .schedule-empty {
        font-size: 16px;
        color: #cbd5f5;
        font-weight: 700;
        padding-top: 8px;
        animation: fadeIn 0.4s ease-out backwards;
    }

    .schedule-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 240px;
        gap: 16px;
        align-items: start;
        animation: fadeIn 0.6s ease-out 0.1s backwards;
    }

    .schedule-meta {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 14px 16px;
        background: #fbfaff;
        display: grid;
        gap: 10px;
        align-content: start;
    }

    .schedule-meta-title {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        font-weight: 800;
        color: var(--brand);
        animation: slideInLeft 0.5s ease-out 0.05s backwards;
    }

    .schedule-meta-item {
        border: 1px solid #e7defc;
        border-radius: 12px;
        padding: 10px 12px;
        background: #ffffff;
        display: grid;
        gap: 4px;
        transition: all 0.3s ease;
        animation: popIn 0.5s ease-out backwards;
    }

    .schedule-meta-item:nth-child(1) { animation-delay: 0.2s; }
    .schedule-meta-item:nth-child(2) { animation-delay: 0.25s; }
    .schedule-meta-item:nth-child(3) { animation-delay: 0.3s; }

    .schedule-meta-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(31, 58, 138, 0.1);
        border-color: #4A90E2;
    }

    .schedule-meta-label {
        font-size: 12px;
        color: var(--muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .schedule-meta-value {
        font-size: 14px;
        font-weight: 800;
        color: #1f2937;
    }

    .schedule-meta-inline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid #e7defc;
        background: #fbfaff;
        font-size: 12px;
    }

    .schedule-meta-inline-label {
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .schedule-meta-inline-value {
        font-weight: 800;
        color: #1f2937;
    }

   /* ===== Improved Availability Modal Styling ===== */

.availability-modal {
    position: fixed;
    inset: 0;
    z-index: 80;
    background: rgba(15, 23, 42, 0.55);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.availability-modal.open {
    display: flex;
}

.availability-dialog {
    width: 100%;
    max-width: 860px;
    border-radius: 18px;
    background: var(--surface);
    border: 1px solid var(--border);
    overflow: hidden;
    box-shadow: 0 32px 80px rgba(31, 58, 138, 0.25);
    display: flex;
    flex-direction: column;
    max-height: 92vh;
    animation: popIn 0.5s ease-out;
}

.availability-modal-header {
    padding: 18px 26px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color: #fff;
}

.availability-modal-title {
    font-size: 22px;
    font-weight: 800;
    margin: 0;
}

.availability-close {
    border: none;
    background: transparent;
    color: #fff;
    font-size: 28px;
    cursor: pointer;
    opacity: 0.8;
}

.availability-close:hover {
    opacity: 1;
}

.availability-modal-body {
    padding: 24px 26px;
    overflow-y: auto;
    flex: 1 1 auto;
}

.availability-help {
    margin: 0 0 18px;
    color: var(--muted);
    font-size: 14px;
}

.availability-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    margin-bottom: 18px;
}

.semester-toggle {
    display: inline-flex;
    gap: 8px;
    background: #f0f9ff;
    border: 1px solid #bfdbfe;
    padding: 6px;
    border-radius: 12px;
}

.semester-btn {
    border: 1px solid transparent;
    background: transparent;
    color: #4b5563;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
}

.semester-btn.active {
    background: #ffffff;
    color: var(--brand);
    border-color: #d1d5db;
    box-shadow: 0 6px 14px rgba(31, 58, 138, 0.1);
}

.availability-filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.availability-filter-group label {
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
}

.availability-filter-group select {
    border: 1px solid #d1d5db;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 12px;
    background: #ffffff;
}

.history-inline-filters {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    width: 100%;
    margin-top: 4px;
}

.history-inline-filter {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
    min-width: 0;
}

.history-inline-filter label {
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.history-inline-filter select {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 13px;
    background: #ffffff;
    color: var(--text);
    font-weight: 600;
}

.history-inline-filter input {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 13px;
    background: #ffffff;
    color: var(--text);
    font-weight: 600;
}

.year-picker {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    padding: 6px 8px;
    background: #ffffff;
}

.year-btn {
    border: none;
    background: transparent;
    color: #1F3A8A;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    padding: 0 6px;
    border-radius: 6px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
}

.year-btn:hover {
    background: #f0f9ff;
    color: #4A90E2;
}

.year-display {
    font-size: 12px;
    font-weight: 700;
    color: #1F3A8A;
    min-width: 80px;
    text-align: center;
}

    .online-badge, .student-online-badge {
        font-size: 11px;
        font-weight: 700;
        color: #065f46;
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        padding: 4px 8px;
        border-radius: 999px;
        display: inline-block;
    }

    .student-active-minutes-badge {
        font-size: 11px;
        font-weight: 700;
        color: #7c2d12;
        background: #fef3c7;
        border: 1px solid #fcd34d;
        padding: 4px 8px;
        border-radius: 999px;
        display: inline-block;
    }

/* ===== Availability Layout (Image Match) ===== */

.availability-table {
    display: grid;
    gap: 12px;
}

.availability-row {
    display: flex;
    align-items: center;
    gap: 16px;
    border: 1px solid #9fe3d9;
    border-radius: 12px;
    padding: 12px 16px;
    background: #ffffff;
    transition: border-color 0.2s ease, background 0.2s ease;
}

.availability-row.is-disabled {
    border-color: #e5e7eb;
    background: #f8fafc;
}

.availability-day {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: #111827;
    font-weight: 700;
    min-width: 160px;
}

.availability-day-name {
    text-transform: capitalize;
}

.availability-toggle {
    position: relative;
    width: 42px;
    height: 22px;
    flex-shrink: 0;
}

.availability-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.availability-toggle-slider {
    position: absolute;
    inset: 0;
    background: #d1d5db;
    border-radius: 999px;
    transition: background 0.2s ease;
}

.availability-toggle-slider::before {
    content: "";
    position: absolute;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #ffffff;
    top: 2px;
    left: 2px;
    transition: transform 0.2s ease;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
}

.availability-toggle input:checked + .availability-toggle-slider {
    background: #10b981;
}

.availability-toggle input:checked + .availability-toggle-slider::before {
    transform: translateX(20px);
}

.availability-slots {
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.availability-slot-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.availability-slot {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.availability-time {
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    height: 36px;
    min-width: 110px;
    padding: 0 10px;
    font-size: 13px;
    color: #111827;
    background: #ffffff;
}

.availability-time:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15);
}

.availability-time:disabled {
    background: #eef2f7;
    color: #9ca3af;
    cursor: not-allowed;
}

.availability-time-end {
    background: #ffffff;
}

.availability-to {
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    text-transform: lowercase;
}

.availability-unavailable {
    font-size: 12px;
    color: #9ca3af;
    font-style: italic;
    display: none;
}

.availability-row.is-disabled .availability-slot-list {
    display: none;
}

.availability-row.is-disabled .availability-unavailable {
    display: block;
}

/* ===== Footer Actions ===== */

.availability-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 18px 26px 22px;
    border-top: 1px solid var(--border);
    background: #f9fafb;
}

.availability-btn {
    border-radius: 12px;
    border: 1px solid #d1d5db;
    background: #fff;
    color: #374151;
    font-size: 14px;
    font-weight: 700;
    min-width: 110px;
    padding: 10px 20px;
    cursor: pointer;
}

.availability-btn:hover {
    background: #f3f4f6;
}

.availability-btn.primary {
    border-color: transparent;
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color: #fff;
}

.availability-btn.primary:hover {
    background: linear-gradient(135deg, var(--brand-dark), #4b2b8c);
}

/* Compact size for Set Availability modal */
.availability-modal .availability-dialog {
    max-width: 560px;
    border-radius: 12px;
    max-height: min(88vh, 620px);
}

.availability-modal .availability-modal-header {
    padding: 10px 14px;
}

.availability-modal .availability-modal-title {
    font-size: 17px;
}

.availability-modal .availability-close {
    font-size: 20px;
}

.availability-modal .availability-modal-body {
    padding: 12px 14px;
}

.availability-modal .availability-help {
    margin: 0 0 10px;
    font-size: 11px;
}

.availability-modal .availability-filters {
    gap: 6px;
    margin-bottom: 10px;
}

.availability-modal .semester-toggle {
    gap: 4px;
    padding: 3px;
    border-radius: 10px;
}

.availability-modal .semester-btn {
    padding: 5px 9px;
    border-radius: 8px;
    font-size: 10px;
}

.availability-modal .availability-filter-group {
    gap: 5px;
}

.availability-modal .availability-filter-group label {
    font-size: 10px;
}

.availability-modal .year-picker {
    gap: 5px;
    padding: 3px 5px;
    border-radius: 8px;
}

.availability-modal .year-btn {
    min-width: 22px;
    height: 22px;
    padding: 0 3px;
    font-size: 14px;
}

.availability-modal .year-display {
    min-width: 64px;
    font-size: 10px;
}

.availability-modal .availability-table {
    gap: 6px;
}

.availability-modal .availability-row {
    gap: 8px;
    border-radius: 9px;
    padding: 8px 10px;
}

.availability-modal .availability-day {
    gap: 8px;
    min-width: 124px;
    font-size: 12px;
}

.availability-modal .availability-toggle {
    width: 34px;
    height: 18px;
}

.availability-modal .availability-toggle-slider::before {
    width: 14px;
    height: 14px;
    top: 2px;
    left: 2px;
}

.availability-modal .availability-toggle input:checked + .availability-toggle-slider::before {
    transform: translateX(16px);
}

.availability-modal .availability-slots,
.availability-modal .availability-slot-list {
    gap: 5px;
}

.availability-modal .availability-time {
    height: 30px;
    min-width: 92px;
    padding: 0 7px;
    font-size: 11px;
    border-radius: 7px;
}

.availability-modal .availability-to,
.availability-modal .availability-unavailable {
    font-size: 10px;
}

.availability-modal .availability-modal-actions {
    gap: 6px;
    padding: 10px 14px 12px;
}

.availability-modal .availability-btn {
    min-width: 86px;
    border-radius: 9px;
    padding: 7px 12px;
    font-size: 12px;
}

.availability-modal .availability-slot {
    flex-wrap: nowrap;
}

@media (max-width: 620px) {
    .availability-modal .availability-dialog {
        max-width: 100%;
    }

    .availability-modal .availability-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .availability-modal .availability-day {
        min-width: 0;
    }

    .availability-modal .availability-slot {
        flex-wrap: wrap;
    }
}

/* ===== Consultation History Modal ===== */

.history-modal {
    position: fixed;
    inset: 0;
    z-index: 85;
    background: rgba(15, 23, 42, 0.55);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.history-modal.open {
    display: flex;
}

.history-dialog {
    width: 100%;
    max-width: 980px;
    border-radius: 18px;
    background: var(--surface);
    border: 1px solid var(--border);
    box-shadow: 0 32px 80px rgba(79, 70, 229, 0.25);
    display: flex;
    flex-direction: column;
    max-height: 92vh;
    overflow: hidden;
    animation: popIn 0.5s ease-out;
}

.history-modal-header {
    padding: 18px 20px 16px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    background: transparent;
    color: #0f172a;
}

.history-modal-title {
    font-size: 34px;
    line-height: 1.1;
    font-weight: 900;
    letter-spacing: -0.3px;
    margin: 0;
}

.history-title-wrap {
    display: grid;
    gap: 6px;
}

.history-modal-subtitle {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #475569;
}

.history-close {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    background: #f8fafc;
    color: #64748b;
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.history-close:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.history-modal-body {
    padding: 24px 26px;
    overflow-y: auto;
    flex: 1 1 auto;
}

#history.section {
    padding: 0;
    overflow: hidden;
    margin-top: 0;
}

#history .history-modal-body {
    padding: 24px 26px;
    overflow: visible;
}

.content.history-only > :not(#history) {
    display: none !important;
}

.call-modal {
    position: fixed;
    inset: 0;
    z-index: 95;
    background: rgba(15, 23, 42, 0.6);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.call-modal.open { display: flex; }

.call-dialog {
    width: 100%;
    max-width: 980px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 18px;
    box-shadow: 0 32px 80px rgba(31, 58, 138, 0.3);
    overflow: hidden;
    animation: popIn 0.5s ease-out;
}

.call-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.call-title { font-size: 16px; font-weight: 800; }
.call-timer {
    font-size: 13px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.18);
    padding: 6px 10px;
    border-radius: 999px;
}

.call-close {
    border: none;
    background: transparent;
    color: #fff;
    font-size: 22px;
    cursor: pointer;
}

.call-body { padding: 18px 20px 20px; }

.call-videos {
    display: grid;
    gap: 14px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.call-video {
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #111827;
    border-radius: 14px;
    object-fit: cover;
}

.call-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 16px;
}

.call-btn {
    border-radius: 10px;
    border: none;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.call-btn.end {
    background: #ef4444;
    color: #fff;
}

.call-btn-icon {
    width: 16px;
    height: 16px;
    display: inline-flex;
}

@media (max-width: 860px) {
    .call-videos {
        grid-template-columns: 1fr;
    }
}

.history-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}

.history-title {
    font-size: 22px;
    font-weight: 800;
    margin: 0 0 4px;
}

    .history-subtitle {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
    }
    .history-student-id {
        color: var(--muted);
        font-size: 12px;
        margin-top: 4px;
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
    display: flex;
    gap: 18px;
    margin-bottom: 18px;
    align-items: center;
    flex-wrap: wrap;
}

.filters-grid {
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
    flex: 1;
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
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 14px;
    background: #fff;
    color: var(--text);
    min-width: 160px;
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
    padding: 20px 0;
    font-size: 14px;
    outline: none;
}

.search-wrap {
    flex: 1;
    min-width: 200px;
}

#history .history-header {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 14px;
    align-items: end;
}

#history .history-filter-layout {
    display: grid;
    gap: 12px;
    min-width: 0;
}

#history .history-filter-row-top {
    display: grid;
    grid-template-columns: auto minmax(180px, 220px) minmax(220px, 1fr);
    gap: 12px;
    align-items: end;
}

#history .history-month-group,
#history .history-year-group,
#history .history-inline-filter {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 0;
}

#history .history-month-group label,
#history .history-year-group label,
#history .history-inline-filter label {
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

#history .history-month-group select,
#history .history-year-group input,
#history .history-inline-filter select,
#history .history-inline-filter input {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 13px;
    background: #fff;
    color: var(--text);
    font-weight: 600;
}

#history .history-inline-filters {
    display: grid;
    grid-template-columns: repeat(4, minmax(160px, 1fr));
    gap: 12px;
    width: 100%;
}

#history .history-right {
    display: flex;
    align-items: end;
}

#history .history-right .export-btn {
    min-height: 42px;
    white-space: nowrap;
}

@media (max-width: 720px) {
    #history .history-header {
        grid-template-columns: 1fr;
        align-items: stretch;
    }
    #history .history-filter-row-top {
        grid-template-columns: 1fr;
    }
    #history .history-inline-filters { grid-template-columns: 1fr; }
    #history .history-inline-filter { min-width: 100%; }
    #history .history-right .export-btn { align-self: flex-start; }
}

.history-table {
    background: #f3f4f6;
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.history-row {
    display: grid;
    grid-template-columns: 1.2fr 1.3fr 1.1fr 0.8fr 0.7fr 1.1fr 0.9fr;
    gap: 12px;
    align-items: center;
    padding: 16px 18px;
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
}

.status-tag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    border: 1px solid transparent;
    white-space: nowrap;
}

.status-tag.status-pending { background: #fff7ed; color: #c2410c; border-color: #fed7aa; }
.status-tag.status-approved { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
.status-tag.status-in_progress { background: #ede9fe; color: #5b21b6; border-color: #c4b5fd; }
.status-tag.status-completed { background: #e0e7ff; color: #4338ca; border-color: #c7d2fe; }
.status-tag.status-incompleted { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
.status-tag.status-declined { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }

.empty-state {
    padding: 30px 18px;
    text-align: center;
    color: var(--muted);
    font-size: 14px;
}

.details-modal {
    position: fixed;
    inset: 0;
    z-index: 95;
    background: rgba(15, 23, 42, 0.6);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.details-modal.open { display: flex; }

.details-dialog {
    width: 100%;
    max-width: 720px;
    border-radius: 18px;
    background: var(--surface);
    border: 1px solid var(--border);
    box-shadow: 0 32px 80px rgba(31, 58, 138, 0.28);
    overflow: hidden;
}

.details-header {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color: #fff;
}

.details-title {
    font-size: 18px;
    font-weight: 800;
}

.details-subtitle {
    font-size: 12px;
    opacity: 0.9;
    margin-top: 2px;
}

.details-close {
    border: none;
    background: transparent;
    color: #fff;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
}

.details-body {
    padding: 18px 20px 20px;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 14px;
}

.details-card {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 10px 12px;
    font-size: 13px;
}

.details-summary {
    margin-top: 12px;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #dbeafe;
    background: #eff6ff;
}

.details-summary-title {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    font-weight: 800;
    color: #1e40af;
    margin-bottom: 6px;
}

/* ===== Responsive ===== */

@media (max-width: 780px) {
    .availability-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .availability-day {
        min-width: 0;
    }

    .availability-slot {
        width: 100%;
    }

    .availability-modal-title {
        font-size: 18px;
    }
}

@media (max-width: 860px) {
    .schedule-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .overview-panels {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 900px) {
    .content-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .topbar-actions {
        width: 100%;
        justify-content: flex-end;
    }
}


@media (max-width: 520px) {
    .schedule-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .content-header {
        padding: 14px 16px;
        border-radius: 12px;
    }

    .content {
        padding: 16px 16px 36px;
    }

    .dashboard-header-title {
        font-size: 24px;
    }

    .dashboard-header-subtitle {
        font-size: 12px;
    }

    .notification-panel {
        width: min(94vw, 360px);
        right: -12px;
    }
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

    .details-grid {
        grid-template-columns: 1fr;
    }

    .feedback-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 560px) {
    .feedback-grid {
        grid-template-columns: 1fr;
    }
}

.summary-modal {
    position: fixed;
    inset: 0;
    z-index: 90;
    background: rgba(15, 23, 42, 0.55);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.summary-modal.open { display: flex; }

.summary-dialog {
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

#summaryForm {
    display: flex;
    flex-direction: column;
    min-height: 0;
    flex: 1 1 auto;
}

.summary-header {
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #d5d9e3;
    background: linear-gradient(180deg, #2f4eb2 0%, #2744a2 100%);
    color: #fff;
}

.summary-title {
    font-size: 23px;
    font-weight: 800;
    line-height: 1.1;
}

.summary-close {
    border: none;
    background: transparent;
    color: rgba(255, 255, 255, 0.92);
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    padding: 0;
}

.summary-close:hover {
    color: #ffffff;
}

.summary-body {
    flex: 1 1 auto;
    min-height: 0;
    padding: 14px 16px 16px;
    overflow-y: auto;
    background: #f5f6f8;
}

.summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 12px;
}

.summary-card {
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

.summary-card.summary-card-wide {
    grid-column: 1 / span 1;
}

.summary-label {
    display: block;
    margin: 14px 0 8px;
    font-size: 13px;
    font-weight: 700;
    color: #151a23;
}

.summary-textarea {
    width: 100%;
    min-height: 122px;
    border: 1px solid #d5dae3;
    border-radius: 12px;
    padding: 13px 14px;
    font-size: 13px;
    resize: vertical;
    background: #ffffff;
    color: #1f2937;
    outline: none;
    box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
}

.summary-textarea:focus {
    border-color: #3f67ff;
    box-shadow: 0 0 0 2px rgba(63, 103, 255, 0.12);
}

.summary-textarea.summary-textarea-lg {
    min-height: 108px;
}

.summary-actions {
    flex: 0 0 auto;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 14px 16px 16px;
    border-top: 1px solid #d9dde5;
    background: #f5f6f8;
}

.summary-actions .availability-btn {
    min-width: 86px;
    border-radius: 12px;
    padding: 9px 18px;
}

@media (max-width: 560px) {
    .summary-dialog {
        max-width: 100%;
        border-radius: 16px;
    }

    .summary-grid {
        grid-template-columns: 1fr;
    }

    .summary-card.summary-card-wide {
        grid-column: auto;
    }
}
</style>
<style>
/* Instructor dashboard cyber theme (matched with student dashboard style) */
.instructor-cyber-theme {
    background:
        radial-gradient(circle at 16% 22%, rgba(0, 186, 255, 0.12), transparent 36%),
        radial-gradient(circle at 86% 8%, rgba(30, 64, 175, 0.16), transparent 34%),
        linear-gradient(180deg, #f3fbff 0%, #eef6ff 100%);
}

.instructor-cyber-theme .sidebar {
    background:
        linear-gradient(180deg, rgba(6, 19, 64, 0.72) 0%, rgba(9, 35, 104, 0.72) 100%),
        url('{{ asset('sidebar.JPG') }}') center/cover no-repeat;
    border: 1px solid rgba(94, 217, 255, 0.45);
    box-shadow: 0 0 0 1px rgba(103, 232, 249, 0.2), 0 0 24px rgba(8, 145, 178, 0.4);
}

.instructor-cyber-theme .sidebar::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        radial-gradient(circle at 14% 10%, rgba(0, 247, 255, 0.14), transparent 35%),
        linear-gradient(130deg, transparent 0 35%, rgba(70, 207, 255, 0.09) 35% 36%, transparent 36% 100%);
}

.instructor-cyber-theme .sidebar-menu-link {
    border: 1px solid rgba(96, 165, 250, 0.28);
    background: rgba(21, 46, 122, 0.7);
    border-radius: 12px;
    margin: 8px 14px;
    color: #e2edff;
    min-height: 46px;
}

.instructor-cyber-theme .sidebar-menu-link:hover,
.instructor-cyber-theme .sidebar-menu-link.active {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.65), rgba(20, 184, 166, 0.45));
    border-color: rgba(103, 232, 249, 0.62);
    box-shadow: 0 0 20px rgba(56, 189, 248, 0.3);
}

.instructor-cyber-theme .sidebar.icon-only .sidebar-menu-link {
    margin: 8px auto;
}

.instructor-cyber-theme .logout-btn {
    background: rgba(14, 34, 96, 0.9);
    border: 1px solid rgba(125, 211, 252, 0.5);
    color: #dbeafe;
}

.instructor-cyber-theme .content-header {
    position: relative;
    overflow: visible;
    background: url('{{ asset('head1.JPG') }}') center/cover no-repeat;
    border: 1px solid rgba(59, 130, 246, 0.34);
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.22);
}

.instructor-cyber-theme .content-header::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(31, 58, 138, 0.34) 0%, rgba(30, 64, 175, 0.3) 100%);
    pointer-events: none;
    z-index: 0;
}

.instructor-cyber-theme .content-header::after {
    content: none;
}

.instructor-cyber-theme .dashboard-header-copy,
.instructor-cyber-theme .topbar-actions {
    position: relative;
    z-index: 2;
}

.instructor-cyber-theme .dashboard-header-title {
    color: #ffffff;
    text-shadow: 0 2px 10px rgba(15, 23, 42, 0.45);
    letter-spacing: 0;
}

.instructor-cyber-theme .dashboard-header-subtitle {
    color: #e2e8f0;
}

.instructor-cyber-theme .notification-btn {
    border-color: rgba(125, 211, 252, 0.7);
    background: rgba(20, 58, 138, 0.45);
    color: #ffffff;
}

.instructor-cyber-theme .header-profile-trigger {
    width: 46px;
    height: 46px;
    padding: 0;
    border-radius: 50%;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: linear-gradient(135deg, #1d4ed8, #4f46e5);
    color: #fff;
    box-shadow: 0 0 0 3px rgba(125, 211, 252, 0.35), 0 0 24px rgba(59, 130, 246, 0.45);
}

.instructor-cyber-theme .header-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.instructor-cyber-theme .stat-card {
    position: relative;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-top: 4px solid #4A90E2;
    color: #111827;
    box-shadow: 0 12px 28px rgba(17, 24, 39, 0.08);
}

.instructor-cyber-theme .stat-card::before {
    content: none;
}

.instructor-cyber-theme .stat-card .stat-count,
.instructor-cyber-theme .stat-card [data-stat],
.instructor-cyber-theme .stat-card [style*="font-size: 13px"] {
    color: #111827 !important;
    position: relative;
    z-index: 1;
}

.instructor-cyber-theme .stat-icon {
    background: #dbeafe !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe;
}

.instructor-cyber-theme .overview-panel {
    background: rgba(245, 251, 255, 0.92);
    border: 1px solid rgba(56, 189, 248, 0.35);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.1);
}

.instructor-cyber-theme .recent-item,
.instructor-cyber-theme .schedule-item {
    background:
        linear-gradient(145deg, #0f2e7a 0%, #173f94 55%, #0b2662 100%),
        repeating-linear-gradient(165deg, rgba(56, 189, 248, 0.16) 0, rgba(56, 189, 248, 0.16) 1px, transparent 1px, transparent 16px);
    border: 1px solid rgba(56, 189, 248, 0.65);
    box-shadow: 0 0 0 1px rgba(186, 230, 253, 0.16), 0 10px 20px rgba(15, 23, 42, 0.25);
}

.instructor-cyber-theme .recent-item-title,
.instructor-cyber-theme .schedule-title {
    color: #f8fdff;
}

.instructor-cyber-theme .recent-item-meta,
.instructor-cyber-theme .schedule-time {
    color: #d7f4ff;
}

.instructor-cyber-theme .schedule-date-chip {
    border: 1px solid rgba(125, 211, 252, 0.6);
    box-shadow: inset 0 0 12px rgba(186, 230, 253, 0.5);
}

@media (max-width: 1024px) {
    .instructor-cyber-theme .stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .instructor-cyber-theme,
    .instructor-cyber-theme .main,
    .instructor-cyber-theme .content {
        overflow-x: hidden;
    }

    .instructor-cyber-theme .sidebar {
        width: min(84vw, 300px);
    }

    .instructor-cyber-theme .content {
        padding: 14px 12px 28px;
    }

    .instructor-cyber-theme .content-header {
        align-items: stretch;
        gap: 12px;
        padding: 14px 12px;
    }

    .instructor-cyber-theme .dashboard-header-copy {
        width: 100%;
    }

    .instructor-cyber-theme .topbar-actions {
        width: 100%;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .instructor-cyber-theme .menu-btn {
        display: inline-flex;
    }

    .instructor-cyber-theme .notification-panel {
        width: min(92vw, 360px);
        right: 0;
    }

    .instructor-cyber-theme .stats {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .instructor-cyber-theme .overview-panels,
    .instructor-cyber-theme .feedback-grid,
    .instructor-cyber-theme .details-grid {
        grid-template-columns: 1fr;
    }

    .instructor-cyber-theme .schedule-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .instructor-cyber-theme .request-table-shell {
        border: none;
        background: transparent;
        overflow: visible;
    }

    .instructor-cyber-theme .request-table-head {
        display: none;
    }

    .instructor-cyber-theme .request-row-wrap {
        display: block;
        margin-bottom: 12px;
    }

    .instructor-cyber-theme .request-row {
        min-width: 0;
        grid-template-columns: 1fr;
        gap: 10px;
        align-items: stretch;
        padding: 14px;
        border: 1px solid #dbe1ea;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    }

    .instructor-cyber-theme .request-user,
    .instructor-cyber-theme .request-meta,
    .instructor-cyber-theme .request-status-col,
    .instructor-cyber-theme .request-updated-col,
    .instructor-cyber-theme .request-actions {
        padding: 0;
    }

    .instructor-cyber-theme .request-user-top {
        align-items: flex-start;
    }

    .instructor-cyber-theme .request-status-col,
    .instructor-cyber-theme .request-updated-col {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 6px;
        border-top: 1px solid #edf1f6;
    }

    .instructor-cyber-theme .request-status-col::before {
        content: "Status";
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .instructor-cyber-theme .request-updated-col::before {
        content: "Updated";
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        font-style: normal;
    }

    .instructor-cyber-theme .request-actions {
        gap: 8px;
        padding-top: 8px;
        border-top: 1px solid #edf1f6;
    }

    .instructor-cyber-theme .request-actions > *,
    .instructor-cyber-theme .request-actions form {
        flex: 1 1 calc(50% - 4px);
    }

    .instructor-cyber-theme .request-actions .request-btn,
    .instructor-cyber-theme .request-actions .view-link {
        display: inline-flex;
        align-items: center;
        width: 100%;
        justify-content: center;
        text-align: center;
    }

    .instructor-cyber-theme .history-row-wrap {
        margin-bottom: 12px;
    }

    .instructor-cyber-theme .history-row {
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #ffffff;
    }

    .instructor-cyber-theme .history-row.header {
        display: none;
    }

    .instructor-cyber-theme .call-modal,
    .instructor-cyber-theme .summary-modal,
    .instructor-cyber-theme .availability-modal {
        padding: 10px;
    }

    .instructor-cyber-theme .call-dialog,
    .instructor-cyber-theme .details-dialog,
    .instructor-cyber-theme .summary-dialog,
    .instructor-cyber-theme .availability-modal .availability-dialog {
        width: calc(100vw - 20px);
        max-width: none;
        max-height: 90vh;
        border-radius: 16px;
    }

    .instructor-cyber-theme .call-header,
    .instructor-cyber-theme .call-body {
        padding-left: 14px;
        padding-right: 14px;
    }

    .instructor-cyber-theme .call-actions {
        flex-wrap: wrap;
    }

    .instructor-cyber-theme .call-btn {
        flex: 1 1 calc(50% - 5px);
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .instructor-cyber-theme .content {
        padding: 10px 8px 24px;
    }

    .instructor-cyber-theme .content-header {
        padding: 12px 10px;
    }

    .instructor-cyber-theme .dashboard-header-title {
        font-size: 20px;
    }

    .instructor-cyber-theme .dashboard-header-subtitle {
        font-size: 11px;
    }

    .instructor-cyber-theme .schedule-grid {
        grid-template-columns: 1fr;
    }

    .instructor-cyber-theme .request-actions > *,
    .instructor-cyber-theme .request-actions form,
    .instructor-cyber-theme .call-btn {
        flex-basis: 100%;
    }

    .instructor-cyber-theme .summary-body {
        padding: 12px;
    }
}
</style>

@php
    $userName = auth()->user()->name ?? 'Instructor';
    $userEmail = auth()->user()->email ?? 'instructor@example.com';
    $authUser = auth()->user();
    $rawName = trim((string) ($authUser?->name ?? ''));
    $userInitial = '';
    if ($rawName !== '') {
        $firstChar = function_exists('mb_substr') ? mb_substr($rawName, 0, 1) : substr($rawName, 0, 1);
        $userInitial = function_exists('mb_strtoupper') ? mb_strtoupper($firstChar) : strtoupper($firstChar);
    }
    if ($userInitial === '') {
        $userInitial = 'U';
    }
@endphp

<div class="dashboard instructor-cyber-theme">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('instructor.dashboard') }}" class="sidebar-logo">
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
            <li>
                <a href="#dashboard" class="sidebar-menu-link" id="dashboardLink"><i class="fa-solid fa-house"></i>Dashboard</a>
            </li>
            <li>
                <a href="#requests" class="sidebar-menu-link" id="requestsLink"><i class="fa-solid fa-inbox"></i>Requests</a>
            </li>
            <li>
                <a href="#schedule" class="sidebar-menu-link" id="scheduleLink"><i class="fa-solid fa-calendar-days"></i>Schedule</a>
            </li>
            <li>
                <a href="#set-availability" class="sidebar-menu-link" id="setAvailabilityLink"><i class="fa-solid fa-sliders"></i>Set Availability</a>
            </li>
            <li>
                <a href="#history" class="sidebar-menu-link" id="historyLink"><i class="fa-solid fa-clock-rotate-left"></i>History</a>
            </li>
            <li>
                <a href="#feedback" class="sidebar-menu-link" id="feedbackLink"><i class="fa-solid fa-comments"></i>Feedback</a>
            </li>
        </ul>

        <div class="sidebar-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <div class="main">
        <div class="content">
            <div class="content-header">
                <button class="menu-btn" id="menuBtn" type="button" aria-label="Open sidebar menu">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    <span>Menu</span>
                </button>
                <div class="dashboard-header-copy">
                    <h1 class="dashboard-header-title">Welcome back, {{ $userName }}!</h1>
                    <p class="dashboard-header-subtitle">Here's what's happening with your consultations today</p>
                </div>

                <div class="topbar-actions">
                <div style="position: relative;">
                    <button class="notification-btn" id="notificationBtn" type="button" aria-label="Open notifications">
                        <i class="fa-solid fa-bell" aria-hidden="true"></i>
                        <span class="notification-badge" id="notificationBadge" @if ($unreadCount <= 0) style="display:none" @endif>{{ $unreadCount }}</span>
                    </button>
                    <div class="notification-panel" id="notificationPanel">
                        <div class="notification-header">
                            <span>Notifications</span>
                            <form method="POST" action="{{ route('notifications.markAllRead') }}" id="markAllReadForm">
                                @csrf
                                <button id="markAllReadBtn" type="submit" style="background:none;border:none;color:var(--brand);font-weight:700;cursor:pointer">Mark all read</button>
                            </form>
                        </div>
                        <ul class="notification-list" id="notificationList">
                            @forelse ($notifications as $notification)
                                <li class="notification-item {{ $notification->is_read ? '' : 'unread' }}">
                                    <span class="notification-dot"></span>
                                    <div>
                                        <div style="font-weight:700">{{ $notification->title }}</div>
                                        <div style="color:var(--muted);margin-top:4px">{{ $notification->message }}</div>
                                        <div style="color:#9ca3af;font-size:11px;margin-top:6px">{{ $notification->created_at?->diffForHumans() }}</div>
                                    </div>
                                </li>
                            @empty
                                <li class="notification-item">
                                    <div>
                                        <div style="font-weight:700">No notifications</div>
                                        <div style="color:var(--muted);margin-top:4px">You're all caught up.</div>
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

            {{-- CONSULTATION STATS --}}
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i></div>
                    <div>
                        <div class="stat-count" data-stat="total">{{ $stats['total'] ?? 0 }}</div>
                        <div style="font-size: 13px; color: var(--muted); margin-top: 2px;">Total</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fef3c7; color: #c2410c;"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i></div>
                    <div>
                        <div class="stat-count" data-stat="pending">{{ $stats['pending'] ?? 0 }}</div>
                        <div style="font-size: 13px; color: var(--muted); margin-top: 2px;">Pending</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #d1fae5; color: #065f46;"><i class="fa-solid fa-check" aria-hidden="true"></i></div>
                    <div>
                        <div class="stat-count" data-stat="approved">{{ $stats['approved'] ?? 0 }}</div>
                        <div style="font-size: 13px; color: var(--muted); margin-top: 2px;">Approved</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #cfeef6; color: #155e75;"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                    <div>
                        <div class="stat-count" data-stat="completed">{{ $stats['completed'] ?? 0 }}</div>
                        <div style="font-size: 13px; color: var(--muted); margin-top: 2px;">Completed</div>
                    </div>
                </div>
            </div>

            <div class="overview-panels">
                <article class="overview-panel">
                    <div class="overview-panel-header">
                        <h2 class="overview-panel-title">Recent Consultations</h2>
                        <button type="button" class="overview-panel-link" id="overviewViewAllBtn">View All <span aria-hidden="true">→</span></button>
                    </div>
                    @if ($recentConsultations->isEmpty())
                        <div class="overview-empty">No recent consultations yet.</div>
                    @else
                        <div class="recent-list">
                            @foreach ($recentConsultations as $consultation)
                                @php
                                    $statusKey = strtolower((string) ($consultation->status ?? 'pending'));
                                    $statusLabel = ucwords(str_replace('_', ' ', $statusKey));
                                    $consultationTitle = $consultation->type_label ?: 'Consultation Session';
                                @endphp
                                <div class="recent-item">
                                    <div class="recent-item-top">
                                        <p class="recent-item-title">{{ $consultationTitle }}</p>
                                        <span class="recent-status-pill status-{{ $statusKey }}">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="recent-item-meta">
                                        <span><i class="fa-solid fa-user" aria-hidden="true"></i> {{ $consultation->student?->name ?? 'Student' }}</span>
                                        <span><i class="fa-solid fa-clock" aria-hidden="true"></i> {{ $formatRelativeDay($consultation->consultation_date) }}, {{ $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>

                <article class="overview-panel" id="instructorUpcomingPanel">
                    <div class="overview-panel-header">
                        <h2 class="overview-panel-title">Upcoming Schedule</h2>
                        <button type="button" class="overview-panel-link history-open-btn">View Calendar <span aria-hidden="true">→</span></button>
                    </div>
                    <div id="instructorUpcomingContent">
                        @if ($upcomingConsultations->isEmpty())
                            <div class="overview-empty">No upcoming consultations scheduled.</div>
                        @else
                            <div class="schedule-list">
                                @foreach ($upcomingConsultations as $consultation)
                                    @php
                                        $consultationDate = $parseManilaDate($consultation->consultation_date);
                                        $consultationTitle = $consultation->type_label ?: 'Consultation Session';
                                    @endphp
                                    <div class="schedule-item">
                                        <div class="schedule-date-chip">
                                            <span class="schedule-date-day">{{ $consultationDate ? $consultationDate->format('d') : '--' }}</span>
                                            <span class="schedule-date-month">{{ $consultationDate ? strtoupper($consultationDate->format('M')) : '---' }}</span>
                                        </div>
                                        <div>
                                            <p class="schedule-title">{{ $consultationTitle }}</p>
                                            <p class="schedule-time"><i class="fa-solid fa-clock" aria-hidden="true"></i> {{ $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            </div>

            {{-- AVAILABILITY --}}
            <div class="section {{ $errors->has('days') ? '' : 'is-hidden' }}" id="schedule">
                @php
                    $weeklyDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    $availabilityByDay = $availabilities
                        ->groupBy(fn ($slot) => strtolower($slot->available_day ?? ''))
                        ->map(fn ($slots) => $slots->sortBy('start_time')->values());
                @endphp

                @php
                    $semesterLabel = ($selectedSemester ?? 'first') === 'second' ? 'Second Sem' : 'First Sem';
                    $academicYearLabel = $selectedAcademicYear ?: '--';
                @endphp

                <div class="availability-head">
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <div class="section-title" style="margin-bottom:0;">Schedule</div>
                        <div class="schedule-meta-inline">
                            <span class="schedule-meta-inline-label">Semester:</span>
                            <span class="schedule-meta-inline-value">{{ $semesterLabel }}</span>
                        </div>
                        <div class="schedule-meta-inline">
                            <span class="schedule-meta-inline-label">Academic Year:</span>
                            <span class="schedule-meta-inline-value">{{ $academicYearLabel }}</span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <button type="button" class="section-close" id="closeScheduleSection">Exit</button>
                        <button type="button" class="availability-open-btn" id="openAvailabilityModal">Configure Weekly Schedule</button>
                    </div>
                </div>

                @if ($errors->has('days'))
                    <div style="margin-bottom:12px;padding:10px 12px;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;border-radius:10px;font-size:13px;">
                        {{ $errors->first('days') }}
                    </div>
                @endif

                <div class="schedule-layout">
                    <div class="schedule-grid">
                        @foreach ($weeklyDays as $day)
                            <div class="schedule-day">{{ ucfirst(substr($day, 0, 3)) }}</div>
                        @endforeach

                        @foreach ($weeklyDays as $day)
                            @php
                                $daySlots = $availabilityByDay->get($day, collect());
                                $slot = $daySlots->first();
                            @endphp
                            <div class="schedule-cell">
                                @if ($slot)
                                    <div class="schedule-slot">
                                        {{ $formatManilaTime12($slot->start_time) }}
                                        <span>to</span>
                                        {{ $formatManilaTime12($slot->end_time) }}
                                    </div>
                                @else
                                <div class="schedule-empty">--</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
            <div class="section is-hidden" id="feedback">
                <div class="section-head">
                    <div class="section-title" style="margin-bottom:0;">Feedback</div>
                    <button type="button" class="section-close" id="closeFeedbackSection">Exit</button>
                </div>
                <div style="color:var(--muted);font-size:14px;margin:-6px 0 14px;">View feedback from your students</div>

                <div class="feedback-grid">
                    <div class="feedback-stat-card">
                        <div class="feedback-stat-icon" style="background:#fff7ed;color:#c2410c;">★</div>
                        <div class="feedback-stat-value">{{ number_format((float) ($feedbackStats['average_rating'] ?? 0), 1) }}</div>
                        <div class="feedback-stat-label">Average Rating</div>
                    </div>
                    <div class="feedback-stat-card">
                        <div class="feedback-stat-icon" style="background:#ecfeff;color:#0f766e;">💬</div>
                        <div class="feedback-stat-value">{{ $feedbackStats['total_feedback'] ?? 0 }}</div>
                        <div class="feedback-stat-label">Total Feedback</div>
                    </div>
                    <div class="feedback-stat-card">
                        <div class="feedback-stat-icon" style="background:#ecfdf5;color:#047857;">👍</div>
                        <div class="feedback-stat-value">{{ $feedbackStats['positive_rate'] ?? 0 }}%</div>
                        <div class="feedback-stat-label">Positive Rate</div>
                    </div>
                    <div class="feedback-stat-card">
                        <div class="feedback-stat-icon" style="background:#f1f5f9;color:#475569;">📅</div>
                        <div class="feedback-stat-value">{{ $feedbackStats['this_month'] ?? 0 }}</div>
                        <div class="feedback-stat-label">This Month</div>
                    </div>
                </div>

                <div class="feedback-list">
                    <div class="feedback-list-head">Student Feedback</div>
                    @forelse ($feedbacks as $feedback)
                        @php
                            $rating = max(1, min(5, (int) $feedback->rating));
                            $consultationType = $feedback->consultation?->type_label ?? ($feedback->consultation?->consultation_type ?? 'Consultation');
                        @endphp
                        <div class="feedback-item">
                            <div class="feedback-item-top">
                                <div class="feedback-student">{{ $feedback->student?->name ?? 'Student' }}</div>
                                <div class="request-tag">{{ $consultationType }}</div>
                            </div>
                            <div class="feedback-meta">{{ $feedback->created_at?->format('Y-m-d h:i A') ?? '—' }}</div>
                            <div class="feedback-stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</div>
                            <div class="feedback-comment">{{ $feedback->comments ?: 'No comment provided.' }}</div>
                        </div>
                    @empty
                        <div class="feedback-item">
                            <div class="feedback-comment">No feedback yet from students.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- CONSULTATION REQUESTS --}}
            <div class="section is-hidden" id="requests">
                <div class="section-head">
                    <div class="section-title" style="margin-bottom:0;">Consultation Requests</div>
                    <button type="button" class="section-close" id="closeRequestsSection">Exit</button>
                </div>
                <div class="request-filter-row">
                    <div class="request-filter-group">
                        <label class="request-filter-label" for="requestStatusFilterBtn">Select Status:</label>
                        <div class="request-status-filter" id="requestStatusFilterDropdown">
                            <button type="button" id="requestStatusFilterBtn" class="request-status-filter-btn" aria-expanded="false" aria-controls="requestStatusFilterMenu">
                                <span id="requestStatusFilterLabel">Choose a status...</span>
                                <span class="request-status-filter-caret">&#9650;</span>
                            </button>
                            <div id="requestStatusFilterMenu" class="request-status-filter-menu" aria-hidden="true">
                                <button type="button" class="request-status-filter-option" data-status="all" data-label="All">
                                    <span class="request-status-pill all">All</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="pending" data-label="Pending">
                                    <span class="request-status-pill pending">Pending</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="approved" data-label="Approved">
                                    <span class="request-status-pill approved">Approved</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="completed" data-label="Completed">
                                    <span class="request-status-pill completed">Completed</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="incompleted" data-label="Incomplete">
                                    <span class="request-status-pill incompleted">Incomplete</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="decline" data-label="Decline">
                                    <span class="request-status-pill decline">Decline</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="in_progress" data-label="In Progress">
                                    <span class="request-status-pill in_progress">In Progress</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="request-filter-group request-search-wrap">
                        <label class="request-filter-label" for="requestSearchInput">Search:</label>
                        <input
                            type="search"
                            id="requestSearchInput"
                            class="request-search-input"
                            placeholder="Search student, date, type, mode, status..."
                            autocomplete="off"
                        >
                    </div>
                </div>
                <div class="request-table-shell">
                    <div class="request-table-head" role="row">
                        <div>Name</div>
                        <div>Date &amp; Time</div>
                        <div>Session Type</div>
                        <div>Mode</div>
                        <div>Status</div>
                        <div>Updated</div>
                        <div>Action</div>
                    </div>
                <div class="request-table">
                    @forelse ($consultations as $consultation)
                        @php
                            $status = strtolower($consultation->status ?? '');
                            $modeValue = strtolower((string) $consultation->consultation_mode);
                            $isFace = str_contains($modeValue, 'face');
                            $hasSummary = !empty($consultation->summary_text);
                            $updatedLabel = $consultation->updated_at?->diffForHumans() ?? '--';
                            $studentName = $consultation->student?->name ?? 'Student';
                            $nameParts = preg_split('/\s+/', trim((string) $studentName)) ?: [];
                            $initials = collect($nameParts)->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->implode('');
                            if ($initials === '') {
                                $initials = 'ST';
                            }
                        @endphp
                        <div class="request-row-wrap">
                         <div class="request-row"
                                 data-consultation-id="{{ $consultation->id }}"
                                 data-status="{{ strtolower((string) $consultation->status) }}"
                                 data-mode="{{ strtolower((string) $consultation->consultation_mode) }}"
                                 data-mode-label="{{ $consultation->consultation_mode }}"
                                 data-call-attempts="{{ (int) ($consultation->call_attempts ?? 0) }}"
                                 data-started-at="{{ $consultation->started_at?->toIso8601String() ?? '' }}"
                                  data-summary="{{ e((string) ($consultation->summary_text ?? '')) }}"
                                  data-transcript="{{ e((string) ($consultation->transcript_text ?? '')) }}"
                                 data-notes="{{ e((string) ($consultation->student_notes ?? '')) }}"
                            >
                            <div class="request-user">
                                        <div class="request-avatar">{{ $initials }}</div>
                                        <div class="request-user-main">
                                            <div class="request-user-top">
                                            <div class="request-user-name">{{ $studentName }}</div>
                                        </div>
                                <div class="request-user-id">ID: {{ $consultation->student?->student_id ?? '--' }}</div>
                                            @php
                                                $studentOnline = in_array($consultation->student?->id ?? 0, (array) ($onlineStudentIds ?? []));
                                                $studentId = $consultation->student?->id;
                                                $lastActiveMinutes = $studentId && isset($consultationActiveMinutes[$studentId]) ? $consultationActiveMinutes[$studentId]['last_active_minutes'] : null;
                                            @endphp
                                            @if ($studentOnline)
                                                <span class="online-badge" aria-hidden="true">● Online</span>
                                            @elseif ($lastActiveMinutes !== null)
                                                <span class="instructor-active-minutes-badge">Active {{ $lastActiveMinutes }}{{ $lastActiveMinutes === 1 ? ' min' : ' mins' }} ago</span>
                                            @else
                                                <span class="instructor-active-minutes-badge">Active —</span>
                                            @endif
                                        </div>
                            </div>
                            <div class="request-meta request-datetime">
                                <span><i class="fa-regular fa-calendar"></i> {{ $consultation->consultation_date }}</span>
                                <span><i class="fa-regular fa-clock"></i> {{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                            </div>
                            <div class="request-meta request-type">
                                <span class="request-type-title">{{ $consultation->type_label }}</span>
                                @if (!empty($consultation->student_notes))
                                    <div class="request-note-preview" title="{{ $consultation->student_notes }}">
                                        <span class="request-note-label">Note:</span> {{ $consultation->student_notes }}
                                    </div>
                                @endif
                            </div>
                            <div class="request-meta request-mode">
                                <span class="request-tag {{ $isFace ? 'face' : '' }}">{{ $consultation->consultation_mode }}</span>
                            </div>
                            <div class="request-status-col">
                                <span class="request-status {{ $status }}">{{ strtoupper($consultation->status) }}</span>
                            </div>
                            <div class="request-updated-col">{{ $updatedLabel }}</div>
                            <div class="request-actions">
                                @if ($consultation->status === 'pending')
                                    <form method="POST" action="{{ route('instructor.consultations.approve', $consultation->id) }}">
                                        @csrf
                                        <button type="submit" class="request-btn approve">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('instructor.consultations.decline', $consultation->id) }}">
                                        @csrf
                                        <button type="submit" class="request-btn decline">Decline</button>
                                    </form>
                                @elseif ($consultation->status === 'approved')
                                    @if ($isFace)
                                        <button type="button"
                                                class="request-btn summary summary-open-btn"
                                                data-id="{{ $consultation->id }}"
                                                data-student="{{ $consultation->student?->name ?? 'Student' }}"
                                                data-student-id="{{ $consultation->student?->student_id ?? '' }}"
                                                data-type="{{ $consultation->type_label }}"
                                                data-mode="{{ $consultation->consultation_mode }}"
                                                data-date="{{ $consultation->consultation_date }}"
                                                data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                                data-summary="{{ e((string) ($consultation->summary_text ?? '')) }}"
                                                data-transcript="{{ e((string) ($consultation->transcript_text ?? '')) }}"
                                        >
                                            {{ $hasSummary ? 'View / Edit Summary' : 'Add Summary' }}
                                        </button>
                                    @else
                                        @php
                                            $callAttempts = (int) ($consultation->call_attempts ?? 0);
                                            $canMarkIncomplete = $callAttempts >= 3;
                                        @endphp
                                        @if (! $canMarkIncomplete)
                                            <form method="POST" action="{{ route('instructor.consultations.start', $consultation->id) }}" class="start-session-form">
                                                @csrf
                                                <button type="submit"
                                                        class="request-btn start start-session-btn"
                                                        data-consultation-id="{{ $consultation->id }}">
                                                    {{ $callAttempts > 0 ? 'Call Again' : 'Video Call' }} (Attempt {{ min($callAttempts + 1, 3) }}/3)
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST"
                                              action="{{ route('instructor.consultations.mark-incomplete', $consultation->id) }}"
                                              class="mark-incomplete-form"
                                              style="{{ $canMarkIncomplete ? '' : 'display:none;' }}"
                                              data-consultation-id="{{ $consultation->id }}">
                                            @csrf
                                            <button type="submit" class="request-btn decline mark-incomplete-btn">Mark as Incompleted</button>
                                        </form>
                                    @endif
                                @elseif ($consultation->status === 'in_progress')
                                    <span class="request-tag">Video call in progress</span>
                                @elseif (in_array($consultation->status, ['completed', 'incompleted'], true))
                                    <button type="button"
                                            class="request-btn summary summary-open-btn"
                                            data-id="{{ $consultation->id }}"
                                            data-student="{{ $consultation->student?->name ?? 'Student' }}"
                                            data-student-id="{{ $consultation->student?->student_id ?? '' }}"
                                            data-type="{{ $consultation->type_label }}"
                                            data-mode="{{ $consultation->consultation_mode }}"
                                            data-date="{{ $consultation->consultation_date }}"
                                            data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                            data-summary="{{ e((string) ($consultation->summary_text ?? '')) }}"
                                            data-transcript="{{ e((string) ($consultation->transcript_text ?? '')) }}"
                                    >
                                        {{ $hasSummary ? 'View / Edit Summary' : 'Add Summary' }}
                                    </button>
                                @elseif ($consultation->status === 'declined')
                                    <button type="button"
                                            class="request-btn summary summary-open-btn"
                                            data-id="{{ $consultation->id }}"
                                            data-student="{{ $consultation->student?->name ?? 'Student' }}"
                                            data-student-id="{{ $consultation->student?->student_id ?? '' }}"
                                            data-type="{{ $consultation->type_label }}"
                                            data-mode="{{ $consultation->consultation_mode }}"
                                            data-date="{{ $consultation->consultation_date }}"
                                            data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                            data-summary="{{ e((string) ($consultation->summary_text ?? '')) }}"
                                            data-transcript="{{ e((string) ($consultation->transcript_text ?? '')) }}"
                                    >
                                        {{ $hasSummary ? 'View / Edit Summary' : 'Add Summary' }}
                                    </button>
                                @else
                                    <span class="request-tag">No Action</span>
                                @endif
                            </div>
                            </div>
                        </div>
                    @empty
                        <div class="request-row-wrap">
                        <div class="request-row">
                            <div class="request-user" style="grid-column:1 / -1;padding:18px 14px;">
                                <div class="request-user-name">No consultation requests</div>
                                <div class="request-user-email">New requests will appear here.</div>
                            </div>
                        </div>
                        </div>
                    @endforelse
                </div>
                </div>

                <!-- Pagination Controls -->
                <div id="requestPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;">
                    <div id="requestPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to {{ min(10, $consultations->count()) }} of {{ $consultations->count() }} requests
                    </div>
                    <div id="requestPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevRequestBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="requestPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextRequestBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">›</span>
                        </button>
                    </div>
                </div>

                <style>
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
                </style>
            </div>

            {{-- APPROVED / UPCOMING --}}

                        </div>
    </div>
</div>

<div class="availability-modal" id="availabilityModal" aria-hidden="true">
    <div class="availability-dialog">
        <div class="availability-modal-header">
            <h2 class="availability-modal-title">Set Availability</h2>
            <button type="button" class="availability-close" id="closeAvailabilityModal" aria-label="Close">x</button>
        </div>
        <div class="availability-modal-body">
            <p class="availability-help">Configure your available time slots for consultations (Philippine Time, 24-hour format)</p>

            <form method="POST" action="{{ route('instructor.availability.store') }}" id="availabilityForm">
                @csrf
                @php
                    $currentYear = (int) now()->format('Y');
                    $academicYears = [];
                    for ($i = -50; $i <= 50; $i++) {
                        $year = $currentYear + $i;
                        $academicYears[] = $year . '-' . ($year + 1);
                    }
                    $selectedSemester = old('semester', $selectedSemester ?? 'first');
                    $selectedAcademicYear = old('academic_year', $selectedAcademicYear ?? ($currentYear . '-' . ($currentYear + 1)));
                @endphp
                                <div class="availability-filters">
                    <div class="semester-toggle" role="group" aria-label="Semester choices">
                        <button type="button" class="semester-btn {{ $selectedSemester === 'first' ? 'active' : '' }}" data-semester="first">First Sem</button>
                        <button type="button" class="semester-btn {{ $selectedSemester === 'second' ? 'active' : '' }}" data-semester="second">Second Sem</button>
                    </div>
                    <input type="hidden" name="semester" id="availabilitySemester" value="{{ $selectedSemester }}">
                    <div class="availability-filter-group">
                        <label for="academicYear">Academic Year</label>
                        <div class="year-picker">
                            <button type="button" class="year-btn year-prev" id="yearPrev" aria-label="Previous year">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="11" stroke="#1F3A8A" stroke-width="1.2" fill="#f8fafc"/><path d="M15 12H9" stroke="#1F3A8A" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <input type="hidden" name="academic_year" id="academicYear" value="{{ $selectedAcademicYear }}">
                            <span class="year-display" id="yearDisplay">{{ $selectedAcademicYear }}</span>
                            <button type="button" class="year-btn year-next" id="yearNext" aria-label="Next year">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="11" stroke="#1F3A8A" stroke-width="1.2" fill="#f8fafc"/><path d="M12 9V15M9 12H15" stroke="#1F3A8A" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @php
                    $weeklyDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    $oldDays = old('days');
                @endphp
                <div class="availability-table">
                    @foreach ($weeklyDays as $day)
                        @php
                            $daySlots = $availabilityByDay->get($day, collect())->pluck('start_time')->map(fn ($time) => substr((string) $time, 0, 5))->values();
                            $dayEnds = $availabilityByDay->get($day, collect())->pluck('end_time')->map(fn ($time) => substr((string) $time, 0, 5))->values();
                            $checked = is_array($oldDays)
                                ? in_array($day, $oldDays, true)
                                : $daySlots->isNotEmpty();
                            $oldSlots = collect(old("slot_times.$day", []))->filter();
                            $oldEnds = collect(old("end_times.$day", []))->filter();
                            $defaultSlot = '08:00';
                            $slotValue = $oldSlots->first()
                                ?? $daySlots->first()
                                ?? $defaultSlot;
                            $endValue = $oldEnds->first()
                                ?? $dayEnds->first()
                                ?? '';
                        @endphp
                        <div class="availability-row {{ $checked ? '' : 'is-disabled' }}" data-day="{{ $day }}">
                            <label class="availability-day">
                                <span class="availability-toggle">
                                    <input type="checkbox" name="days[]" value="{{ $day }}" class="day-check" data-day="{{ $day }}" @checked($checked)>
                                    <span class="availability-toggle-slider"></span>
                                </span>
                                <span class="availability-day-name">{{ ucfirst($day) }}</span>
                            </label>
                            <div class="availability-slots">
                                <div class="availability-slot-list">
                                    <div class="availability-slot">
                                        <input
                                            type="time"
                                            name="slot_times[{{ $day }}][0]"
                                            class="availability-time day-time availability-time-start"
                                            data-day="{{ $day }}"
                                            value="{{ $slotValue }}"
                                            @disabled(!$checked)
                                        >
                                        <span class="availability-to">to</span>
                                        <input
                                            type="time"
                                            name="end_times[{{ $day }}][0]"
                                            class="availability-time day-time availability-time-end"
                                            data-day="{{ $day }}"
                                            value="{{ $endValue }}"
                                            data-auto="{{ $endValue ? '0' : '1' }}"
                                            @disabled(!$checked)
                                        >
                                    </div>
                                </div>
                                <div class="availability-unavailable">Not available</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="availability-modal-actions">
                    <button type="button" class="availability-btn" id="cancelAvailabilityModal">Cancel</button>
                    <button type="submit" class="availability-btn primary">Save Availability</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="history" class="section is-hidden" aria-hidden="true">
        <div class="history-modal-header">
            <div class="history-title-wrap">
                <h2 class="history-modal-title">Consultation History</h2>
                <p class="history-modal-subtitle">Manage and track all completed consultations</p>
            </div>
            <button type="button" class="history-close" id="closeHistoryModal" aria-label="Close history">x</button>
        </div>
        <div class="history-modal-body">
            @php
                $completedConsultations = $consultations->where('status', 'completed');
                $historyTypes = $completedConsultations
                    ->pluck('type_label')
                    ->filter()
                    ->unique()
                    ->values();
                $historyAcademicYears = $completedConsultations
                    ->pluck('consultation_date')
                    ->filter()
                    ->map(function ($date) {
                        try {
                            $parsed = \Illuminate\Support\Carbon::parse($date);
                        } catch (\Exception $e) {
                            return null;
                        }
                        $month = (int) $parsed->format('n');
                        $year = (int) $parsed->format('Y');
                        if ($month >= 8) {
                            return $year . '-' . ($year + 1);
                        }
                        if ($month <= 5) {
                            return ($year - 1) . '-' . $year;
                        }
                        return null;
                    })
                    ->filter()
                    ->unique()
                    ->values();
            @endphp

            <div class="history-header">
                <div class="history-filter-layout">
                    <div class="history-filter-row-top">
                        <div class="semester-toggle">
                            <button type="button" id="instructorSemAll" class="semester-btn" data-sem="all">All</button>
                            <button type="button" id="instructorSem1" class="semester-btn" data-sem="1">1st Sem</button>
                            <button type="button" id="instructorSem2" class="semester-btn" data-sem="2">2nd Sem</button>
                        </div>
                        <div class="history-month-group" id="instructorMonthPickerContainer" style="display:none;">
                            <label for="instructorMonthSelect">Month</label>
                            <select id="instructorMonthSelect">
                                <option value="">All months</option>
                            </select>
                        </div>
                        <div class="history-year-group">
                            <label for="instructorHistoryYearInput">Academic Year</label>
                            <input type="text" id="instructorHistoryYearInput" placeholder="e.g., 2026-2027">
                        </div>
                    </div>
                    <div class="history-inline-filters">
                        <div class="availability-filter-group history-inline-filter">
                            <label for="instructorHistoryCategoryFilter">Category</label>
                            <select id="instructorHistoryCategoryFilter">
                                <option value="">All Categories</option>
                                <option value="Curricular Activities">Curricular Activities</option>
                                <option value="Behavior-Related">Behavior-Related</option>
                                <option value="Co-curricular activities">Co-curricular activities</option>
                            </select>
                        </div>
                        <div class="availability-filter-group history-inline-filter">
                            <label for="instructorHistoryTopicFilter">Topic</label>
                            <select id="instructorHistoryTopicFilter">
                                <option value="">All Topics</option>
                            </select>
                        </div>
                        <div class="availability-filter-group history-inline-filter">
                            <label for="instructorHistoryModeFilter">Mode</label>
                            <select id="instructorHistoryModeFilter">
                                <option value="">All Modes</option>
                                <option value="Video Call">Video Call</option>
                                <option value="Face-to-Face">Face-to-Face</option>
                            </select>
                        </div>
                        <div class="availability-filter-group history-inline-filter">
                            <label for="historySearch">Search</label>
                            <input type="search" id="historySearch" placeholder="Search history...">
                        </div>
                    </div>
                </div>
                <div class="history-right">
                    <button class="export-btn" type="button" id="historyExport">Export History</button>
                </div>
            </div>
            <div class="filters" aria-hidden="true" style="display:none;">
                <div class="filters-grid">
                    <!-- kept for spacing parity with student history -->
                </div>
            </div>

            <div class="section" id="consultationHistoryInline" style="margin-top:0;">
                <div class="history-table">
                    <div class="history-row header">
                        <div>Date & Time</div>
                        <div>Student</div>
                        <div>Type</div>
                        <div>Mode</div>
                        <div>Duration</div>
                        <div>Records</div>
                        <div>Actions</div>
                    </div>

                    @forelse ($completedConsultations as $consultation)
                        @php
                            $modeValue = strtolower((string) $consultation->consultation_mode);
                            $isFaceToFace = str_contains($modeValue, 'face');
                            $duration = $consultation->duration_minutes ?? null;
                            $dateObj = \Illuminate\Support\Carbon::parse($consultation->consultation_date);
                            $month = (int) $dateObj->format('n');
                            $year = (int) $dateObj->format('Y');
                            $academicYear = $month >= 8 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
                            $semester = $month >= 8 || $month <= 5 ? ($month >= 8 ? 'first' : 'second') : '';
                            $timeRange = $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time);
                        @endphp
                        <div class="history-row-wrap">
                            <div class="history-row history-row-item"
                                 data-category="{{ strtolower((string) ($consultation->consultation_category ?? '')) }}"
                                 data-topic="{{ strtolower((string) ($consultation->consultation_topic ?? $consultation->consultation_type ?? '')) }}"
                                 data-date="{{ $consultation->consultation_date }}"
                                 data-month="{{ $dateObj->format('F') }}"
                                 data-year="{{ $year }}"
                                 data-academic-year="{{ $academicYear }}"
                                 data-semester="{{ $semester }}"
                                 data-type="{{ strtolower((string) ($consultation->type_label ?? $consultation->consultation_type ?? '')) }}"
                                 data-mode="{{ strtolower((string) $consultation->consultation_mode) }}"
                                 data-searchable="{{ strtolower(($consultation->type_label ?? '') . ' ' . ($consultation->student?->name ?? '') . ' ' . ($consultation->student?->student_id ?? '') . ' ' . $consultation->consultation_mode . ' ' . $dateObj->format('F') . ' ' . $year) }}"
                            >
                                <div class="date-time">
                                    <span>{{ $consultation->consultation_date }}</span>
                                    <span>{{ $timeRange }}</span>
                                </div>
                                <div>{{ $consultation->student?->name ?? 'Student' }}<br><span style="color:var(--muted);font-size:12px;">ID: {{ $consultation->student?->student_id ?? '--' }}</span></div>
                                <div>{{ $consultation->type_label ?? $consultation->consultation_type }}</div>
                                <div>
                                    <span class="badge badge-mode {{ $isFaceToFace ? 'face' : '' }}">
                                        {{ $consultation->consultation_mode }}
                                    </span>
                                </div>
                                <div>{{ $duration !== null ? $duration . ' min' : '—' }}</div>
                                <div>
                                    @if (! $isFaceToFace)
                                        <span class="record-pill secondary">Action Taken</span>
                                    @endif
                                    <span class="record-pill">Summary</span>
                                </div>
                                <div>
                                    <a href="#"
                                       class="view-link details-open-btn"
                                       data-id="{{ $consultation->id }}"
                                       data-student="{{ $consultation->student?->name ?? 'Student' }}"
                                       data-student-id="{{ $consultation->student?->student_id ?? '--' }}"
                                       data-date="{{ $consultation->consultation_date }}"
                                       data-time="{{ $timeRange }}"
                                       data-type="{{ $consultation->type_label ?? $consultation->consultation_type }}"
                                       data-mode="{{ $consultation->consultation_mode }}"
                                       data-duration="{{ $consultation->duration_minutes !== null ? $consultation->duration_minutes . ' min' : '—' }}"
                                       data-notes="{{ e((string) ($consultation->student_notes ?? '')) }}"
                                       data-summary="{{ e($consultation->summary_text) }}"
                                       data-transcript="{{ e($consultation->transcript_text) }}"
                                    >View Details</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No consultation history found.</div>
                    @endforelse

                    <div class="empty-state" id="instructorHistoryEmptyState" style="display:none;">No matching results.</div>
                </div>

                <div id="instructorHistoryPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;">
                    <div id="instructorHistoryPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to {{ min(10, $completedConsultations->count()) }} of {{ $completedConsultations->count() }} consultations
                    </div>
                    <div id="instructorHistoryPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevInstructorHistoryBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="instructorHistoryPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextInstructorHistoryBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">›</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
</div>

<!-- End Call Confirmation Modal -->
<div id="endCallConfirmModal" style="display:none;position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);z-index:1300;background:#fff;border-radius:12px;padding:28px;box-shadow:0 20px 50px rgba(0,0,0,0.3);width:340px;max-width:90%;text-align:center;">
    <div style="font-weight:700;font-size:16px;color:#111827;margin-bottom:12px;">Leave Call?</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:24px;">Are you sure you want to leave this call?</div>
    <div style="display:flex;gap:12px;justify-content:center;">
        <button id="endCallConfirmNo" type="button" style="background:#e5e7eb;color:#111827;border:none;border-radius:8px;padding:10px 20px;font-weight:600;cursor:pointer;">No</button>
        <button id="endCallConfirmYes" type="button" style="background:#ef4444;color:#fff;border:none;border-radius:8px;padding:10px 20px;font-weight:600;cursor:pointer;">Yes, Leave</button>
    </div>
</div>

<!-- End Call Confirmation Overlay -->
<div id="endCallConfirmOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1299;"></div>

<div class="call-modal" id="callModal" aria-hidden="true">
    <div class="call-dialog">
        <div class="call-header">
            <div class="call-title" id="callStatusLabel">Video Session</div>
            <div class="call-timer" id="callTimer">00:00</div>
            <button type="button" class="call-close" id="closeCallModal" aria-label="Close">x</button>
        </div>
        <div class="call-body">
            <div class="call-videos">
                <video class="call-video" id="localVideo" autoplay muted playsinline></video>
                <video class="call-video" id="remoteVideo" autoplay playsinline></video>
            </div>
            <div class="call-actions">
                <button type="button" class="call-btn" id="toggleCameraBtn">
                    <span class="call-btn-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 7l-7 5 7 5V7z"></path>
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                        </svg>
                    </span>
                    <span class="call-btn-text">Camera Off</span>
                </button>
                <button type="button" class="call-btn" id="toggleTranscriptBtn">
                    <span class="call-btn-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16v16H4z"></path>
                            <path d="M7 8h10M7 12h10M7 16h6"></path>
                        </svg>
                    </span>
                    <span class="call-btn-text">Transcript</span>
                </button>
                <button type="button" class="call-btn" id="toggleMicBtn">
                    <span class="call-btn-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 1a3 3 0 0 1 3 3v8a3 3 0 0 1-6 0V4a3 3 0 0 1 3-3z"></path>
                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                            <line x1="12" y1="19" x2="12" y2="23"></line>
                            <line x1="8" y1="23" x2="16" y2="23"></line>
                        </svg>
                    </span>
                    <span class="call-btn-text">Mic Off</span>
                </button>
                <button type="button" class="call-btn end" id="endCallBtn">End Call</button>
            </div>
        </div>
    </div>
</div>

<div class="details-modal" id="detailsModal" aria-hidden="true">
    <div class="details-dialog">
        <div class="details-header">
            <div>
                <div class="details-title">Consultation Details</div>
                <div class="details-subtitle" id="detailsSubtitle">Completed session</div>
            </div>
            <button type="button" class="details-close" id="closeDetailsModal" aria-label="Close">x</button>
        </div>
        <div class="details-body">
                        <div class="details-grid">
                            <div class="details-card" id="detailsDate">Date & Time: --</div>
                            <div class="details-card" id="detailsStudent">Student: --</div>
                            <div class="details-card" id="detailsStudentId">Student ID: --</div>
                            <div class="details-card" id="detailsMode">Mode: --</div>
                            <div class="details-card" id="detailsType">Type: --</div>
                            <div class="details-card" id="detailsDuration">Duration: --</div>
                        </div>
            <div class="details-summary">
                <div class="details-summary-title">Consultation Summary</div>
                <div id="detailsSummaryText">Summary not yet available.</div>
            </div>
            <div class="details-summary" id="detailsTranscriptWrap">
                <div class="details-summary-title">Action Taken</div>
                <div id="detailsTranscriptText">Action taken not yet available.</div>
            </div>
        </div>
    </div>
</div>

<div class="summary-modal" id="summaryModal" aria-hidden="true">
    <div class="summary-dialog">
        <div class="summary-header">
            <div class="summary-title">Consultation Summary</div>
            <button type="button" class="summary-close" id="closeSummaryModal" aria-label="Close">x</button>
        </div>
        <form method="POST" id="summaryForm">
            @csrf
            <div class="summary-body">
                <div class="summary-grid">
                    <div class="summary-card" id="summaryStudent">Student: --</div>
                    <div class="summary-card" id="summaryStudentId">Student ID: --</div>
                    <div class="summary-card" id="summaryDate">Date & Time: --</div>
                    <div class="summary-card" id="summaryType">Type: --</div>
                    <div class="summary-card summary-card-wide" id="summaryMode">Mode: --</div>
                </div>
                <label class="summary-label" for="summaryText">Summary</label>
                <textarea class="summary-textarea" name="summary_text" id="summaryText" placeholder="Write the summary of the discussion..." required></textarea>
                <label class="summary-label" for="summaryActionTaken">Action Taken</label>
                <textarea class="summary-textarea summary-textarea-lg" name="action_taken_text" id="summaryActionTaken" placeholder="Write how you resolved or handled the consultation..." required></textarea>
            </div>
            <div class="summary-actions">
                <button type="button" class="availability-btn" id="cancelSummaryModal">Cancel</button>
                <button type="submit" class="availability-btn primary">Save Summary</button>
            </div>
        </form>
    </div>
</div>

<div class="toast" id="notifToast">
    <button class="toast-close" id="closeToast">x</button>
    <div class="toast-title" id="toastTitle">New Notification</div>
    <div class="toast-body" id="toastBody">You have a new notification.</div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menuBtn');
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationPanel = document.getElementById('notificationPanel');
    const markAllReadForm = document.getElementById('markAllReadForm');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    const notificationList = document.getElementById('notificationList');
    const notifToast = document.getElementById('notifToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastBody = document.getElementById('toastBody');
    const closeToast = document.getElementById('closeToast');
    const openAvailabilityModal = document.getElementById('openAvailabilityModal');
    const availabilityModal = document.getElementById('availabilityModal');
    const closeAvailabilityModal = document.getElementById('closeAvailabilityModal');
    const cancelAvailabilityModal = document.getElementById('cancelAvailabilityModal');
    const dayChecks = document.querySelectorAll('.day-check');
    const semesterButtons = document.querySelectorAll('.semester-btn[data-semester]');
    const availabilitySemester = document.getElementById('availabilitySemester');
    const yearPrev = document.getElementById('yearPrev');
    const yearNext = document.getElementById('yearNext');
    const academicYearInput = document.getElementById('academicYear');
    const yearDisplay = document.getElementById('yearDisplay');
    const historySection = document.getElementById('history');
    const contentContainer = document.querySelector('.main .content');
    const contentHeaderBlock = document.querySelector('.content > .content-header');
    const bannerBlock = document.querySelector('.content > .banner');
    const statsBlock = document.querySelector('.content > .stats');
    const overviewPanelsBlock = document.querySelector('.content > .overview-panels');
    const historyOpenBtns = document.querySelectorAll('.history-open-btn');
    const closeHistoryModal = document.getElementById('closeHistoryModal');
    const historyLink = document.getElementById('historyLink');
    const historyDateRange = document.getElementById('historyDateRange');
    const historyType = document.getElementById('historyType');
    const historyMode = document.getElementById('historyMode');
    const historySearch = document.getElementById('historySearch');
    const historyExport = document.getElementById('historyExport');
    const detailsModal = document.getElementById('detailsModal');
    const detailsOpenBtns = document.querySelectorAll('.details-open-btn');
    const closeDetailsModal = document.getElementById('closeDetailsModal');
    const detailsSubtitle = document.getElementById('detailsSubtitle');
    const detailsDate = document.getElementById('detailsDate');
    const detailsStudent = document.getElementById('detailsStudent');
    const detailsStudentId = document.getElementById('detailsStudentId');
    const detailsMode = document.getElementById('detailsMode');
    const detailsType = document.getElementById('detailsType');
    const detailsDuration = document.getElementById('detailsDuration');
    const detailsSummaryText = document.getElementById('detailsSummaryText');
    const detailsTranscriptWrap = document.getElementById('detailsTranscriptWrap');
    const detailsTranscriptText = document.getElementById('detailsTranscriptText');

    const requestsSection = document.getElementById('requests');
    const scheduleSection = document.getElementById('schedule');
    const feedbackSection = document.getElementById('feedback');
    const overviewViewAllBtn = document.getElementById('overviewViewAllBtn');
    const dashboardLink = document.getElementById('dashboardLink');
    const requestsLink = document.getElementById('requestsLink');
    const scheduleLink = document.getElementById('scheduleLink');
    const setAvailabilityLink = document.getElementById('setAvailabilityLink');
    const feedbackLink = document.getElementById('feedbackLink');
    const instructorUpcomingContent = document.getElementById('instructorUpcomingContent');
    const closeRequestsSection = document.getElementById('closeRequestsSection');
    const closeScheduleSection = document.getElementById('closeScheduleSection');
    const closeFeedbackSection = document.getElementById('closeFeedbackSection');
    const summaryModal = document.getElementById('summaryModal');
    const summaryForm = document.getElementById('summaryForm');
    const summaryOpenBtns = document.querySelectorAll('.summary-open-btn');
    const closeSummaryModal = document.getElementById('closeSummaryModal');
    const cancelSummaryModal = document.getElementById('cancelSummaryModal');
    const summaryStudent = document.getElementById('summaryStudent');
    const summaryStudentId = document.getElementById('summaryStudentId');
    const summaryDate = document.getElementById('summaryDate');
    const summaryType = document.getElementById('summaryType');
    const summaryMode = document.getElementById('summaryMode');
    const summaryText = document.getElementById('summaryText');
    const summaryActionTaken = document.getElementById('summaryActionTaken');
    const summaryActionBase = @json(url('/instructor/consultations'));

    const callModal = document.getElementById('callModal');
    const localVideo = document.getElementById('localVideo');
    const remoteVideo = document.getElementById('remoteVideo');
    const toggleCameraBtn = document.getElementById('toggleCameraBtn');
    const toggleMicBtn = document.getElementById('toggleMicBtn');
    const toggleTranscriptBtn = document.getElementById('toggleTranscriptBtn');
    const endCallBtn = document.getElementById('endCallBtn');
    const closeCallModal = document.getElementById('closeCallModal');
    const callTimer = document.getElementById('callTimer');
    const callStatusLabel = document.getElementById('callStatusLabel');

    const latestNotification = @json($notifications->firstWhere('is_read', false));
    const unreadCount = @json($unreadCount);
    const instructorToastUserId = @json(auth()->id());

    if (menuBtn) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    if (historySection && contentContainer && !contentContainer.contains(historySection)) {
        contentContainer.appendChild(historySection);
    }

    function setHistorySidebarIconOnly(enabled) {
        if (!sidebar) return;
        const shouldEnable = Boolean(enabled) && window.innerWidth > 768;
        sidebar.classList.toggle('icon-only', shouldEnable);
        if (shouldEnable) {
            sidebar.classList.remove('open');
            return;
        }
        if (window.innerWidth > 768) {
            sidebar.classList.remove('open');
        }
    }

    function setHistoryOnlyMode(enabled) {
        if (!contentContainer) return;
        contentContainer.classList.toggle('history-only', Boolean(enabled));
    }

    function setPrimaryDashboardVisible(visible) {
        const shouldShow = Boolean(visible);
        [contentHeaderBlock, bannerBlock, statsBlock, overviewPanelsBlock].forEach((block) => {
            if (!block) return;
            block.style.display = shouldShow ? '' : 'none';
        });
    }

    const hasOpenOverlaySection = [requestsSection, scheduleSection, feedbackSection, historySection]
        .some((section) => section && !section.classList.contains('is-hidden'));
    setPrimaryDashboardVisible(!hasOpenOverlaySection);

    if (notificationBtn && notificationPanel) {
        notificationBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            notificationPanel.classList.toggle('active');
        });
    }



    document.addEventListener('click', (event) => {
        if (!notificationPanel || !notificationBtn) return;
        if (notificationPanel.contains(event.target) || notificationBtn.contains(event.target)) return;
        notificationPanel.classList.remove('active');
    });

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
                updateInstructorNotificationBadge(data?.unreadNotifications || 0);
                renderInstructorNotificationList(data?.notifications || []);
                if (notifToast) {
                    notifToast.classList.remove('show');
                }
            } catch (error) {
                console.error('Failed to mark all notifications as read.', error);
            } finally {
                if (markAllReadBtn) {
                    markAllReadBtn.disabled = false;
                    markAllReadBtn.style.opacity = '';
                    markAllReadBtn.style.cursor = '';
                }
            }
        });
    }

    function _buildInstructorNotificationToken(notification) {
        if (!notification) return '';
        const directId = notification.id ?? notification.notification_id ?? null;
        if (directId !== null && directId !== undefined && String(directId).trim() !== '') {
            return `id:${directId}`;
        }
        const title = String(notification.title ?? '');
        const message = String(notification.message ?? '');
        const createdAt = String(notification.created_at ?? notification.createdAt ?? '');
        return `fallback:${title}|${message}|${createdAt}`;
    }

    function _hasShownInstructorToast(token) {
        if (!token) return false;
        try {
            const key = `instructor_last_toast_notification_${instructorToastUserId || 'guest'}`;
            return localStorage.getItem(key) === token;
        } catch (_) {
            return false;
        }
    }

    function _markShownInstructorToast(token) {
        if (!token) return;
        try {
            const key = `instructor_last_toast_notification_${instructorToastUserId || 'guest'}`;
            localStorage.setItem(key, token);
        } catch (_) {
            // ignore storage errors
        }
    }

    if (unreadCount > 0 && latestNotification && notifToast) {
        const notificationToken = _buildInstructorNotificationToken(latestNotification);
        if (!_hasShownInstructorToast(notificationToken)) {
            toastTitle.textContent = latestNotification.title ?? 'New Notification';
            toastBody.textContent = latestNotification.message ?? 'You have a new notification.';
            notifToast.classList.add('show');
            _markShownInstructorToast(notificationToken);
            setTimeout(() => notifToast.classList.remove('show'), 6000);
        }
    }

    if (closeToast) {
        closeToast.addEventListener('click', () => {
            notifToast.classList.remove('show');
        });
    }

    function updateEndTime(startInput) {
        const endInput = startInput.closest('.availability-slot')?.querySelector('.availability-time-end');
        if (!endInput) return;
        const value = startInput.value;
        if (!value) {
            if (endInput.dataset.auto === '1') endInput.value = '';
            return;
        }
        if (endInput.dataset.auto !== '1' && endInput.value) return;
        const parts = value.split(':');
        const hour = Number(parts[0]);
        const minute = Number(parts[1] || 0);
        if (Number.isNaN(hour) || Number.isNaN(minute)) {
            if (endInput.dataset.auto === '1') endInput.value = '';
            return;
        }
        const endHour = (hour + 1) % 24;
        endInput.value = `${String(endHour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
    }

    function setDayInputsState(day, checked) {
        const row = document.querySelector(`.availability-row[data-day="${day}"]`);
        if (!row) return;
        row.classList.toggle('is-disabled', !checked);
        const dayInputs = row.querySelectorAll(`.day-time[data-day="${day}"]`);
        dayInputs.forEach((input) => {
            input.disabled = !checked;
        });
    }

    dayChecks.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            setDayInputsState(checkbox.dataset.day, checkbox.checked);
        });
    });

    if (semesterButtons.length && availabilitySemester) {
        semesterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                semesterButtons.forEach((btn) => btn.classList.remove('active'));
                button.classList.add('active');
                availabilitySemester.value = button.dataset.semester || 'first';
            });
        });
    }

    document.querySelectorAll('.availability-time-start').forEach((input) => {
        updateEndTime(input);
    });

    document.querySelectorAll('.availability-time-end').forEach((input) => {
        input.addEventListener('input', () => {
            input.dataset.auto = input.value ? '0' : '1';
        });
    });

    document.addEventListener('input', (event) => {
        const target = event.target;
        if (target instanceof HTMLInputElement && target.classList.contains('availability-time-start')) {
            updateEndTime(target);
        }
    });


    function showAvailabilityModal() {
        if (!availabilityModal) return;
        availabilityModal.classList.add('open');
        availabilityModal.setAttribute('aria-hidden', 'false');
    }

    function hideAvailabilityModal() {
        if (!availabilityModal) return;
        availabilityModal.classList.remove('open');
        availabilityModal.setAttribute('aria-hidden', 'true');
    }

    if (openAvailabilityModal) {
        openAvailabilityModal.addEventListener('click', showAvailabilityModal);
    }
    if (setAvailabilityLink) {
        setAvailabilityLink.addEventListener('click', (event) => {
            event.preventDefault();
            showAvailabilityModal();
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }
    if (closeAvailabilityModal) {
        closeAvailabilityModal.addEventListener('click', hideAvailabilityModal);
    }
    if (cancelAvailabilityModal) {
        cancelAvailabilityModal.addEventListener('click', hideAvailabilityModal);
    }

    if (availabilityModal) {
        availabilityModal.addEventListener('click', (event) => {
            if (event.target === availabilityModal) {
                hideAvailabilityModal();
            }
        });
    }

    // Year Picker Logic
    if (yearPrev && yearNext && academicYearInput && yearDisplay) {
        function updateYear(change) {
            const current = academicYearInput.value;
            const [startYear] = current.split('-').map(Number);
            const newYear = startYear + change;
            const newAcademicYear = newYear + '-' + (newYear + 1);
            academicYearInput.value = newAcademicYear;
            yearDisplay.textContent = newAcademicYear;
        }

        yearPrev.addEventListener('click', (e) => {
            e.preventDefault();
            updateYear(-1);
        });

        yearNext.addEventListener('click', (e) => {
            e.preventDefault();
            updateYear(1);
        });
    }

    function showHistoryModal() {
        if (!historySection) return;
        setHistorySidebarIconOnly(true);
        setHistoryOnlyMode(true);
        setPrimaryDashboardVisible(false);
        historySection.classList.remove('is-hidden');
        historySection.setAttribute('aria-hidden', 'false');
        const historyYearEl = document.getElementById('instructorHistoryYearInput');
        if (historyYearEl) {
            historyYearEl.disabled = false;
            historyYearEl.readOnly = false;
        }
        historySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function hideHistoryModal() {
        if (!historySection) return;
        setHistorySidebarIconOnly(false);
        setHistoryOnlyMode(false);
        setPrimaryDashboardVisible(true);
        historySection.classList.add('is-hidden');
        historySection.setAttribute('aria-hidden', 'true');
    }

    if (historyOpenBtns.length) {
        historyOpenBtns.forEach((btn) => {
            btn.addEventListener('click', showHistoryModal);
        });
    }

    if (historyLink) {
        historyLink.addEventListener('click', (event) => {
            event.preventDefault();
            showHistoryModal();
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }

    if (closeHistoryModal) {
        closeHistoryModal.addEventListener('click', hideHistoryModal);
    }

    const historyRows = Array.from(document.querySelectorAll('.history-row-item'));
    const instructorHistoryRowWraps = Array.from(document.querySelectorAll('#consultationHistoryInline .history-row-wrap'));
    const historySemButtons = Array.from(document.querySelectorAll('#history [data-sem]'));
    const instructorMonthPickerContainer = document.getElementById('instructorMonthPickerContainer');
    const instructorMonthSelect = document.getElementById('instructorMonthSelect');
    const instructorHistoryYearInput = document.getElementById('instructorHistoryYearInput');
    const instructorHistoryCategoryFilter = document.getElementById('instructorHistoryCategoryFilter');
    const instructorHistoryTopicFilter = document.getElementById('instructorHistoryTopicFilter');
    const instructorHistoryModeFilter = document.getElementById('instructorHistoryModeFilter');
    const instructorHistoryEmptyState = document.getElementById('instructorHistoryEmptyState');
    const instructorHistoryTable = document.querySelector('#consultationHistoryInline .history-table');
    const instructorHistoryPaginationInfo = document.getElementById('instructorHistoryPaginationInfo');
    const instructorHistoryPageNumbers = document.getElementById('instructorHistoryPageNumbers');
    const prevInstructorHistoryBtn = document.getElementById('prevInstructorHistoryBtn');
    const nextInstructorHistoryBtn = document.getElementById('nextInstructorHistoryBtn');

    if (instructorHistoryYearInput) {
        instructorHistoryYearInput.disabled = false;
        instructorHistoryYearInput.readOnly = false;
        instructorHistoryYearInput.addEventListener('keydown', (event) => {
            event.stopPropagation();
        });
    }

    const instructorHistoryItemsPerPage = 10;
    let currentInstructorHistoryPage = 1;
    let selectedInstructorMonth = null;

    function escapeHistoryHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getInstructorHistoryAcademicYear(dateValue) {
        const parts = String(dateValue || '').split('-');
        const year = Number(parts[0]);
        const month = Number(parts[1]);
        if (!Number.isFinite(year) || !Number.isFinite(month)) return '';
        return month >= 8 ? `${year}-${year + 1}` : `${year - 1}-${year}`;
    }

    function getInstructorHistorySemester(dateValue) {
        const month = Number(String(dateValue || '').split('-')[1]);
        if (!Number.isFinite(month)) return '';
        if (month >= 8) return 'first';
        if (month <= 5) return 'second';
        return '';
    }

    function bindDetailsOpenButton(btn) {
        if (!btn || btn.dataset.detailsBound === '1') return;
        btn.dataset.detailsBound = '1';
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            openDetailsModal({
                date: btn.dataset.date || '--',
                time: btn.dataset.time || '--',
                student: btn.dataset.student || 'Student',
                studentId: btn.dataset.studentId || '--',
                mode: btn.dataset.mode || '--',
                type: btn.dataset.type || '--',
                duration: btn.dataset.duration || '--',
                summary: btn.dataset.summary || '',
                transcript: btn.dataset.transcript || '',
            });
        });
    }

    function createInstructorHistoryRowWrap(data) {
        const wrap = document.createElement('div');
        wrap.className = 'history-row-wrap';

        const modeValue = String(data.mode || '');
        const modeLower = modeValue.toLowerCase();
        const isFaceToFace = modeLower.includes('face');
        const dateObj = new Date(`${data.date || ''}T00:00:00`);
        const monthLabel = Number.isNaN(dateObj.getTime()) ? '' : dateObj.toLocaleDateString('en-US', { month: 'long' });
        const yearLabel = Number.isNaN(dateObj.getTime()) ? '' : String(dateObj.getFullYear());
        const academicYear = getInstructorHistoryAcademicYear(data.date);
        const semester = getInstructorHistorySemester(data.date);
        const typeValue = String(data.type || '--');
        const searchValue = `${typeValue} ${data.student || ''} ${data.studentId || ''} ${modeValue} ${monthLabel} ${yearLabel}`.toLowerCase();

        wrap.innerHTML = `
            <div class="history-row history-row-item"
                 data-category=""
                 data-topic=""
                 data-date="${escapeHistoryHtml(data.date || '')}"
                 data-month="${escapeHistoryHtml(monthLabel)}"
                 data-year="${escapeHistoryHtml(yearLabel)}"
                 data-academic-year="${escapeHistoryHtml(academicYear)}"
                 data-semester="${escapeHistoryHtml(semester)}"
                 data-type="${escapeHistoryHtml(typeValue.toLowerCase())}"
                 data-mode="${escapeHistoryHtml(modeLower)}"
                 data-searchable="${escapeHistoryHtml(searchValue)}"
            >
                <div class="date-time">
                    <span>${escapeHistoryHtml(data.date || '--')}</span>
                    <span>${escapeHistoryHtml(data.time || '--')}</span>
                </div>
                <div>${escapeHistoryHtml(data.student || 'Student')}<br><span style="color:var(--muted);font-size:12px;">ID: ${escapeHistoryHtml(data.studentId || '--')}</span></div>
                <div>${escapeHistoryHtml(typeValue)}</div>
                <div>
                    <span class="badge badge-mode ${isFaceToFace ? 'face' : ''}">
                        ${escapeHistoryHtml(modeValue || '--')}
                    </span>
                </div>
                <div>${escapeHistoryHtml(data.duration || '--')}</div>
                <div>
                    ${isFaceToFace ? '' : '<span class="record-pill secondary">Action Taken</span>'}
                    <span class="record-pill">Summary</span>
                </div>
                <div>
                    <a href="#"
                       class="view-link details-open-btn"
                       data-id="${escapeHistoryHtml(data.id || '')}"
                       data-student="${escapeHistoryHtml(data.student || 'Student')}"
                       data-student-id="${escapeHistoryHtml(data.studentId || '--')}"
                       data-date="${escapeHistoryHtml(data.date || '--')}"
                       data-time="${escapeHistoryHtml(data.time || '--')}"
                       data-type="${escapeHistoryHtml(typeValue)}"
                       data-mode="${escapeHistoryHtml(modeValue || '--')}"
                       data-duration="${escapeHistoryHtml(data.duration || '--')}"
                       data-summary="${escapeHistoryHtml(data.summary || '')}"
                       data-transcript="${escapeHistoryHtml(data.transcript || '')}">View Details</a>
                </div>
            </div>
        `;

        wrap.dataset.match = '1';
        const btn = wrap.querySelector('.details-open-btn');
        bindDetailsOpenButton(btn);
        return wrap;
    }

    function upsertInstructorHistoryRow(data) {
        if (!instructorHistoryTable || !data?.id) return;

        const staticEmptyState = instructorHistoryTable.querySelector('.empty-state:not(#instructorHistoryEmptyState)');
        if (staticEmptyState) {
            staticEmptyState.remove();
        }

        const existingWrap = instructorHistoryRowWraps.find((wrap) => {
            const btn = wrap.querySelector('.details-open-btn');
            return btn?.dataset.id === String(data.id);
        });

        if (existingWrap) {
            const replacementWrap = createInstructorHistoryRowWrap(data);
            existingWrap.replaceWith(replacementWrap);

            const wrapIndex = instructorHistoryRowWraps.indexOf(existingWrap);
            if (wrapIndex >= 0) {
                instructorHistoryRowWraps[wrapIndex] = replacementWrap;
            }

            const existingRow = existingWrap.querySelector('.history-row-item');
            const replacementRow = replacementWrap.querySelector('.history-row-item');
            const rowIndex = historyRows.indexOf(existingRow);
            if (rowIndex >= 0 && replacementRow) {
                historyRows[rowIndex] = replacementRow;
            }
        } else {
            const newWrap = createInstructorHistoryRowWrap(data);
            const headerRow = instructorHistoryTable.querySelector('.history-row.header');
            if (headerRow) {
                headerRow.insertAdjacentElement('afterend', newWrap);
            } else {
                instructorHistoryTable.prepend(newWrap);
            }
            instructorHistoryRowWraps.push(newWrap);
            const newRow = newWrap.querySelector('.history-row-item');
            if (newRow) {
                historyRows.push(newRow);
            }
        }

        updateInstructorHistoryTopicFilterOptions();
        applyHistoryFilters();
    }

    const semesterMonths = {
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

    const monthNameToNumber = {
        january: 1,
        february: 2,
        march: 3,
        april: 4,
        may: 5,
        june: 6,
        july: 7,
        august: 8,
        september: 9,
        october: 10,
        november: 11,
        december: 12,
    };

    function normalizeHistoryValue(value) {
        return String(value || '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, ' ')
            .trim();
    }

    function titleCase(value) {
        return String(value || '')
            .split(' ')
            .filter(Boolean)
            .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
            .join(' ');
    }

    function getAcademicYearFromDate(dateStr) {
        if (!dateStr) return '';
        try {
            const date = new Date(dateStr);
            const month = date.getMonth() + 1;
            const year = date.getFullYear();
            if (month >= 8) return `${year}-${year + 1}`;
            if (month <= 5) return `${year - 1}-${year}`;
            return '';
        } catch (e) {
            return '';
        }
    }

    function getSemesterFromDate(dateStr) {
        if (!dateStr) return '';
        try {
            const date = new Date(dateStr);
            const month = date.getMonth() + 1;
            if (month >= 8 && month <= 12) return '1';
            if (month >= 1 && month <= 5) return '2';
            return '';
        } catch (e) {
            return '';
        }
    }

    function getMonthFromDate(dateStr) {
        if (!dateStr) return null;
        try {
            const date = new Date(dateStr);
            return date.getMonth() + 1;
        } catch (e) {
            return null;
        }
    }

    function getRowSemesterCode(row) {
        const sem = normalizeHistoryValue(row.dataset.semester);
        if (sem === 'first') return '1';
        if (sem === 'second') return '2';
        return getSemesterFromDate(row.dataset.date || '');
    }

    function getRowAcademicYear(row) {
        return normalizeHistoryValue(row.dataset.academicYear || getAcademicYearFromDate(row.dataset.date || ''));
    }

    function getRowMonthNumber(row) {
        const monthName = normalizeHistoryValue(row.dataset.month);
        if (monthName && monthNameToNumber[monthName]) {
            return monthNameToNumber[monthName];
        }
        return getMonthFromDate(row.dataset.date || '');
    }

    const consultationTopicsByCategory = {
        'Curricular Activities': [
            'Thesis/Project',
            'Grades',
            'Requirements not submitted',
            'Lack of quizzes/assignments',
            'Other curricular concern',
        ],
        'Behavior-Related': [
            'Tardiness/Absences',
            'Rowdy behavior',
            'Dialogue with the party in conflict',
            'Family Problem',
        ],
        'Co-curricular activities': [
            'Make-up activities',
            'Reschedule of graded requirement',
            'Rehearsal',
        ],
    };

    const normalizedCategoryByTopic = new Map();
    const normalizedCategoryKeys = [];
    const normalizedTopicKeys = [];
    Object.entries(consultationTopicsByCategory).forEach(([category, topics]) => {
        const normalizedCategory = normalizeHistoryValue(category);
        normalizedCategoryKeys.push(normalizedCategory);
        topics.forEach((topic) => {
            const normalizedTopic = normalizeHistoryValue(topic);
            normalizedCategoryByTopic.set(normalizedTopic, normalizedCategory);
            normalizedTopicKeys.push(normalizedTopic);
        });
    });
    normalizedTopicKeys.sort((a, b) => b.length - a.length);

    function deriveInstructorHistoryCategoryAndTopic(row) {
        let rowCategory = normalizeHistoryValue(row.dataset.category);
        let rowTopic = normalizeHistoryValue(row.dataset.topic);
        const rowType = normalizeHistoryValue(row.dataset.type);

        if (!rowTopic && rowType) {
            if (normalizedCategoryByTopic.has(rowType)) {
                rowTopic = rowType;
            } else {
                const matchedTopic = normalizedTopicKeys.find((topic) => rowType.includes(topic));
                if (matchedTopic) rowTopic = matchedTopic;
            }
        }

        if (!rowCategory) {
            if (rowTopic && normalizedCategoryByTopic.has(rowTopic)) {
                rowCategory = normalizedCategoryByTopic.get(rowTopic) || '';
            } else if (rowType) {
                const matchedCategory = normalizedCategoryKeys.find((category) => rowType.includes(category));
                if (matchedCategory) rowCategory = matchedCategory;
            }
        }

        return { rowCategory, rowTopic };
    }

    function populateInstructorTopicFilter() {
        if (!instructorHistoryTopicFilter) return;
        const selectedCategoryRaw = instructorHistoryCategoryFilter?.value || '';
        const previousValue = normalizeHistoryValue(instructorHistoryTopicFilter.value);

        const topicSource = selectedCategoryRaw
            ? (consultationTopicsByCategory[selectedCategoryRaw] || [])
            : Object.values(consultationTopicsByCategory).flat();

        const uniqueSortedTopics = Array.from(new Set(topicSource))
            .sort((a, b) => a.localeCompare(b));

        instructorHistoryTopicFilter.innerHTML = '<option value="">All Topics</option>';

        uniqueSortedTopics.forEach((topic) => {
            const option = document.createElement('option');
            option.value = topic;
            option.textContent = topic;
            instructorHistoryTopicFilter.appendChild(option);
        });

        const previousRaw = topicSource.find((topic) => normalizeHistoryValue(topic) === previousValue);
        if (previousValue && previousRaw) {
            instructorHistoryTopicFilter.value = previousRaw;
        } else {
            instructorHistoryTopicFilter.value = '';
        }
    }

    function renderInstructorMonthSelector(semester) {
        if (!instructorMonthSelect) return;
        instructorMonthSelect.innerHTML = '<option value="">All months</option>';
        selectedInstructorMonth = null;

        if (!semester || semester === 'all') {
            if (instructorMonthPickerContainer) {
                instructorMonthPickerContainer.style.display = 'none';
            }
            applyHistoryFilters();
            return;
        }

        const months = semesterMonths[semester] || [];
        months.forEach((month) => {
            const option = document.createElement('option');
            option.value = month.num;
            option.textContent = month.name;
            instructorMonthSelect.appendChild(option);
        });

        instructorMonthSelect.value = '';
        instructorMonthSelect.onchange = () => {
            selectedInstructorMonth = instructorMonthSelect.value ? parseInt(instructorMonthSelect.value, 10) : null;
            applyHistoryFilters();
        };

        if (instructorMonthPickerContainer) {
            instructorMonthPickerContainer.style.display = 'block';
        }
    }

    function renderInstructorHistoryPage(page = currentInstructorHistoryPage) {
        const matchedWraps = instructorHistoryRowWraps.filter((wrap) => wrap.dataset.match === '1');
        const totalMatched = matchedWraps.length;
        const totalPages = Math.max(1, Math.ceil(totalMatched / instructorHistoryItemsPerPage));
        currentInstructorHistoryPage = Math.min(Math.max(1, page), totalPages);

        instructorHistoryRowWraps.forEach((wrap) => {
            wrap.style.display = 'none';
        });

        if (totalMatched > 0) {
            const start = (currentInstructorHistoryPage - 1) * instructorHistoryItemsPerPage;
            const end = start + instructorHistoryItemsPerPage;
            matchedWraps.forEach((wrap, index) => {
                if (index >= start && index < end) {
                    wrap.style.display = 'flex';
                }
            });

            const displayStart = start + 1;
            const displayEnd = Math.min(end, totalMatched);
            if (instructorHistoryPaginationInfo) {
                instructorHistoryPaginationInfo.textContent = `Showing ${displayStart} to ${displayEnd} of ${totalMatched} consultations`;
                instructorHistoryPaginationInfo.style.display = 'block';
            }
            if (instructorHistoryEmptyState) {
                instructorHistoryEmptyState.style.display = 'none';
            }
        } else {
            if (instructorHistoryPaginationInfo) {
                instructorHistoryPaginationInfo.textContent = 'No consultations found';
                instructorHistoryPaginationInfo.style.display = 'block';
            }
            if (instructorHistoryEmptyState) {
                instructorHistoryEmptyState.style.display = historyRows.length > 0 ? 'block' : 'none';
            }
        }

        if (instructorHistoryPageNumbers) {
            instructorHistoryPageNumbers.innerHTML = '';
            if (totalMatched > 0) {
                for (let i = 1; i <= totalPages; i += 1) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'pagination-page-btn' + (i === currentInstructorHistoryPage ? ' active' : '');
                    btn.textContent = i;
                    btn.addEventListener('click', () => renderInstructorHistoryPage(i));
                    instructorHistoryPageNumbers.appendChild(btn);
                }
            }
        }

        if (prevInstructorHistoryBtn) {
            prevInstructorHistoryBtn.style.display = totalMatched > 0 && currentInstructorHistoryPage > 1 ? 'block' : 'none';
        }
        if (nextInstructorHistoryBtn) {
            nextInstructorHistoryBtn.style.display = totalMatched > 0 && currentInstructorHistoryPage < totalPages ? 'block' : 'none';
        }
    }

    function filterInstructorHistoryRows() {
        const selectedSemBtn = historySemButtons.find((btn) => btn.classList.contains('active'));
        const selectedSem = selectedSemBtn ? normalizeHistoryValue(selectedSemBtn.dataset.sem) : 'all';
        const selectedAcademicYear = normalizeHistoryValue(instructorHistoryYearInput?.value);
        const selectedCategory = normalizeHistoryValue(instructorHistoryCategoryFilter?.value);
        const selectedTopic = normalizeHistoryValue(instructorHistoryTopicFilter?.value);
        const selectedMode = normalizeHistoryValue(instructorHistoryModeFilter?.value);
        const selectedSearch = normalizeHistoryValue(historySearch?.value);

        instructorHistoryRowWraps.forEach((wrap) => {
            const row = wrap.querySelector('.history-row-item');
            if (!row) {
                wrap.dataset.match = '0';
                return;
            }

            const rowSem = getRowSemesterCode(row);
            const rowMonth = getRowMonthNumber(row);
            const rowAcademicYear = getRowAcademicYear(row);
            const { rowCategory, rowTopic } = deriveInstructorHistoryCategoryAndTopic(row);
            const rowMode = normalizeHistoryValue(row.dataset.mode);
            const rowSearchable = normalizeHistoryValue(row.dataset.searchable || row.textContent || '');
            const compactSelectedYear = selectedAcademicYear.replace(/\s+/g, '');
            const compactRowYear = rowAcademicYear.replace(/\s+/g, '');

            let matches = true;

            if (selectedSem !== 'all') {
                matches = matches && rowSem === selectedSem;
            }
            if (matches && selectedSem !== 'all' && selectedInstructorMonth) {
                matches = rowMonth === Number(selectedInstructorMonth);
            }
            if (matches && selectedAcademicYear) {
                matches = rowAcademicYear === selectedAcademicYear
                    || rowAcademicYear.includes(selectedAcademicYear)
                    || compactRowYear.includes(compactSelectedYear);
            }
            if (matches && selectedCategory) {
                matches = rowCategory === selectedCategory;
            }
            if (matches && selectedTopic) {
                const rowType = normalizeHistoryValue(row.dataset.type || '');
                const hasRowTopic = Boolean(rowTopic);
                matches = (hasRowTopic && (rowTopic === selectedTopic || rowTopic.includes(selectedTopic) || selectedTopic.includes(rowTopic)))
                    || rowType.includes(selectedTopic);
            }
            if (matches && selectedMode) {
                matches = rowMode.includes(selectedMode);
            }
            if (matches && selectedSearch) {
                matches = rowSearchable.includes(selectedSearch);
            }

            wrap.dataset.match = matches ? '1' : '0';
        });

        currentInstructorHistoryPage = 1;
        renderInstructorHistoryPage();
    }

    function applyHistoryFilters() {
        filterInstructorHistoryRows();
    }

    if (instructorHistoryYearInput) {
        instructorHistoryYearInput.addEventListener('input', applyHistoryFilters);
    }

    if (instructorHistoryCategoryFilter) {
        instructorHistoryCategoryFilter.addEventListener('change', () => {
            populateInstructorTopicFilter();
            applyHistoryFilters();
        });
    }

    if (instructorHistoryTopicFilter) {
        instructorHistoryTopicFilter.addEventListener('change', applyHistoryFilters);
    }

    if (instructorHistoryModeFilter) {
        instructorHistoryModeFilter.addEventListener('change', applyHistoryFilters);
    }

    if (historySearch) {
        historySearch.addEventListener('input', applyHistoryFilters);
    }

    historySemButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            historySemButtons.forEach((item) => item.classList.remove('active'));
            btn.classList.add('active');
            renderInstructorMonthSelector(btn.dataset.sem);
        });
    });

    if (prevInstructorHistoryBtn) {
        prevInstructorHistoryBtn.addEventListener('click', () => {
            if (currentInstructorHistoryPage > 1) {
                renderInstructorHistoryPage(currentInstructorHistoryPage - 1);
            }
        });
    }

    if (nextInstructorHistoryBtn) {
        nextInstructorHistoryBtn.addEventListener('click', () => {
            const matchedCount = instructorHistoryRowWraps.filter((wrap) => wrap.dataset.match === '1').length;
            const totalPages = Math.max(1, Math.ceil(matchedCount / instructorHistoryItemsPerPage));
            if (currentInstructorHistoryPage < totalPages) {
                renderInstructorHistoryPage(currentInstructorHistoryPage + 1);
            }
        });
    }

    // default select 'All' semester
    const semAllBtn = document.getElementById('instructorSemAll');
    if (semAllBtn) semAllBtn.classList.add('active');
    renderInstructorMonthSelector('all');
    populateInstructorTopicFilter();
    applyHistoryFilters();


    if (historyExport) {
        historyExport.addEventListener('click', () => {
            const visibleRows = historyRows.filter((row) => row.closest('.history-row-wrap')?.dataset.match === '1');
            const exportRows = visibleRows.length ? visibleRows : historyRows;
            const rowsHtml = exportRows.map((row) => {
                const cells = Array.from(row.children).map((cell) => cell.textContent.replace(/\s+/g, ' ').trim());
                const dateTime = cells[0] || '';
                const student = cells[1] || '';
                const type = cells[2] || '';
                const mode = cells[3] || '';
                const duration = cells[4] || '';
                const records = cells[5] || '';
                return `
                    <tr>
                        <td>${dateTime}</td>
                        <td>${student}</td>
                        <td>${type}</td>
                        <td>${mode}</td>
                        <td>${duration}</td>
                        <td>${records}</td>
                    </tr>`;
            }).join('');

            const exportHtml = `
                <html>
                <head>
                    <title>Consultation History</title>
                    <style>
                        body { font-family: "Segoe UI", Arial, sans-serif; margin: 24px; color: #111827; }
                        h1 { font-size: 20px; margin: 0 0 12px; }
                        table { width: 100%; border-collapse: collapse; font-size: 12px; }
                        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; vertical-align: top; }
                        th { background: #f3f4f6; font-weight: 700; }
                        .meta { color: #6b7280; font-size: 12px; margin-bottom: 12px; }
                    </style>
                </head>
                <body>
                    <h1>Consultation History</h1>
                    <div class="meta">Exported on ${new Date().toLocaleString()}</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Student</th>
                                <th>Type</th>
                                <th>Mode</th>
                                <th>Duration</th>
                                <th>Records</th>
                            </tr>
                        </thead>
                        <tbody>${rowsHtml}</tbody>
                    </table>
                </body>
                </html>`;

            const win = window.open('', '_blank');
            if (!win) return;
            win.document.open();
            win.document.write(exportHtml);
            win.document.close();
            win.focus();
            win.print();
            win.onafterprint = () => win.close();
        });
    }

    // Generate unique device session ID for multi-device auto-disconnect
    function getOrCreateDeviceSessionId() {
        let sessionId = localStorage.getItem('deviceSessionId');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('deviceSessionId', sessionId);
        }
        return sessionId;
    }

    const DEVICE_SESSION_ID = getOrCreateDeviceSessionId();

    let currentConsultationId = null;
    let peerConnection = null;
    let localStream = null;
    let pollTimer = null;
    let lastSignalId = 0;
    let callTimerInterval = null;
    let callStartAt = null;
    let transcriptActive = false;
    let transcriptText = '';
    let speechRecognizer = null;
    let currentDeviceSessionId = null;
    let callAnswered = false;
    let outgoingCountdownSeconds = 0;
    let outgoingCountdownInterval = null;
    let isEndingCall = false;
    let activeCallRole = 'instructor';
    const WEBRTC_ICE_SERVERS = [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' },
        { urls: 'stun:stun2.l.google.com:19302' },
        {
            urls: 'turn:openrelay.metered.ca:80',
            username: 'openrelayproject',
            credential: 'openrelayproject',
        },
        {
            urls: 'turn:openrelay.metered.ca:443',
            username: 'openrelayproject',
            credential: 'openrelayproject',
        },
        {
            urls: 'turn:openrelay.metered.ca:443?transport=tcp',
            username: 'openrelayproject',
            credential: 'openrelayproject',
        },
    ];

    function openCallModal() {
        if (!callModal) return;
        callModal.classList.add('open');
        callModal.setAttribute('aria-hidden', 'false');
    }

    function setCallStatusLabel(text) {
        if (!callStatusLabel) return;
        callStatusLabel.textContent = text;
    }

    function clearOutgoingCountdown() {
        if (outgoingCountdownInterval) {
            clearInterval(outgoingCountdownInterval);
            outgoingCountdownInterval = null;
        }
        outgoingCountdownSeconds = 0;
    }

    function closeCallModalUI() {
        if (!callModal) return;
        callModal.classList.remove('open');
        callModal.setAttribute('aria-hidden', 'true');
    }

    function actuallyStopCall() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
        stopTranscript();
        saveTranscript();
        if (callTimerInterval) {
            clearInterval(callTimerInterval);
            callTimerInterval = null;
        }
        clearOutgoingCountdown();
        if (peerConnection) {
            peerConnection.close();
            peerConnection = null;
        }
        if (localStream) {
            localStream.getTracks().forEach((track) => track.stop());
            localStream = null;
        }
        if (localVideo) localVideo.srcObject = null;
        if (remoteVideo) remoteVideo.srcObject = null;
        currentConsultationId = null;
        lastSignalId = 0;
        callStartAt = null;
        transcriptText = '';
        callAnswered = false;
        activeCallRole = 'instructor';
        if (callTimer) callTimer.textContent = '00:00';
        setCallStatusLabel('Video Session');
        closeCallModalUI();
    }

    function stopCall() {
        // Show confirmation dialog
        showEndCallConfirmation();
    }

    function initSpeechRecognition() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) return null;
        const recognizer = new SpeechRecognition();
        recognizer.continuous = true;
        recognizer.interimResults = false;
        recognizer.lang = 'en-US';
        recognizer.onresult = (event) => {
            for (let i = event.resultIndex; i < event.results.length; i += 1) {
                if (event.results[i].isFinal) {
                    const chunk = event.results[i][0].transcript.trim();
                    transcriptText += `${chunk}\n`;
                    appendTranscriptChunk('instructor', chunk);
                }
            }
        };
        recognizer.onerror = () => {
            // ignore errors; user might block mic permission
        };
        return recognizer;
    }

    async function startTranscript() {
        if (transcriptActive) return;
        if (!speechRecognizer) {
            speechRecognizer = initSpeechRecognition();
        }
        if (!speechRecognizer) {
            alert('Speech recognition is not supported in this browser.');
            return;
        }
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        await fetch(`{{ url('/instructor/consultations') }}/${currentConsultationId}/transcript-toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ active: true }),
        });
        transcriptActive = true;
        toggleTranscriptBtn.textContent = 'Stop Transcript';
        speechRecognizer.start();
    }

    async function stopTranscript() {
        if (!transcriptActive) return;
        transcriptActive = false;
        if (toggleTranscriptBtn) toggleTranscriptBtn.textContent = 'Transcript';
        try {
            speechRecognizer?.stop();
        } catch (_) {
            // ignore
        }
        if (currentConsultationId) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            await fetch(`{{ url('/instructor/consultations') }}/${currentConsultationId}/transcript-toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ active: false }),
            });
        }
    }

    async function saveTranscript() {
        if (!currentConsultationId || !transcriptText.trim()) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        await fetch(`{{ url('/instructor/consultations') }}/${currentConsultationId}/transcript`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ transcript: transcriptText.trim() }),
        });
    }

    async function appendTranscriptChunk(role, text) {
        if (!currentConsultationId || !text) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        await fetch(`{{ url('/consultations') }}/${currentConsultationId}/transcript-append`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ role, text }),
        });
    }

    function startCallTimer() {
        callStartAt = Date.now();
        if (callTimer) callTimer.textContent = '00:00';
        if (callTimerInterval) clearInterval(callTimerInterval);
        callTimerInterval = setInterval(() => {
            if (!callStartAt) return;
            const diff = Date.now() - callStartAt;
            const totalSeconds = Math.floor(diff / 1000);
            const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
            const seconds = String(totalSeconds % 60).padStart(2, '0');
            if (callTimer) callTimer.textContent = `${minutes}:${seconds}`;
        }, 1000);
    }

    async function markNoAnswer(consultationId) {
        if (!consultationId) return { can_mark_incomplete: false };
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch(`{{ url('/instructor/consultations') }}/${consultationId}/no-answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({}),
        });
        if (!response.ok) {
            return { can_mark_incomplete: false };
        }
        return response.json();
    }

    async function finalizeCall(consultationId) {
        if (!consultationId) return null;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch(`{{ url('/consultations') }}/${consultationId}/end-call`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({}),
        });

        if (!response.ok) return null;
        const ct = response.headers.get('content-type') || '';
        return ct.includes('application/json') ? response.json() : null;
    }

    function syncRequestRowStatus(consultationId, nextStatus, options = {}) {
        if (!consultationId || !nextStatus) return;
        const requestRow = document.querySelector(`.request-row[data-consultation-id="${consultationId}"]`);
        if (!requestRow) return;

        const callAttempts = Number(options.callAttempts);
        if (Number.isFinite(callAttempts) && callAttempts >= 0) {
            requestRow.dataset.callAttempts = String(callAttempts);
        }

        updateRequestRowState(requestRow, nextStatus);
        updateIncompleteButtonVisibility();
    }

    function startOutgoingCountdown(seconds = 20) {
        clearOutgoingCountdown();
        outgoingCountdownSeconds = seconds;
        setCallStatusLabel('Calling Student...');
        if (callTimer) callTimer.textContent = `${outgoingCountdownSeconds}s`;
        outgoingCountdownInterval = setInterval(async () => {
            if (callAnswered) {
                clearOutgoingCountdown();
                return;
            }
            outgoingCountdownSeconds -= 1;
            if (outgoingCountdownSeconds <= 0) {
                clearOutgoingCountdown();
                const consultationId = currentConsultationId;
                let noAnswerResponse = null;
                if (consultationId) {
                    try {
                        await sendSignal('disconnect', { reason: 'no_answer' });
                    } catch (_) {
                        // ignore
                    }
                    try {
                        noAnswerResponse = await markNoAnswer(consultationId);
                    } catch (_) {
                        // ignore
                    }
                }
                actuallyStopCall();
                if (consultationId) {
                    syncRequestRowStatus(
                        consultationId,
                        String(noAnswerResponse?.status || 'approved').toLowerCase(),
                        {
                        callAttempts: noAnswerResponse?.call_attempts,
                        }
                    );
                }
                return;
            }
            if (callTimer) callTimer.textContent = `${outgoingCountdownSeconds}s`;
        }, 1000);
    }

    async function sendSignal(type, payload) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        await fetch("{{ url('/webrtc/signal') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                consultation_id: currentConsultationId,
                type,
                payload,
                device_session_id: type === 'disconnect' ? null : DEVICE_SESSION_ID,
            }),
        });
    }

    async function pollSignals() {
        if (!currentConsultationId) return;
        const response = await fetch(`{{ url('/webrtc/poll') }}?consultation_id=${currentConsultationId}&after=${lastSignalId}&device_session_id=${encodeURIComponent(DEVICE_SESSION_ID)}`);
        if (!response.ok) return;
        const data = await response.json();
        if (!data?.signals?.length) return;
        data.signals.forEach((signal) => {
            lastSignalId = Math.max(lastSignalId, signal.id);
            handleSignal(signal.type, signal.payload);
        });
    }

    function createPeerConnection() {
        peerConnection = new RTCPeerConnection({
            iceServers: WEBRTC_ICE_SERVERS,
            iceCandidatePoolSize: 10,
        });

        peerConnection.onicecandidate = (event) => {
            if (event.candidate) {
                sendSignal('ice', { candidate: event.candidate });
            }
        };

        peerConnection.ontrack = (event) => {
            if (remoteVideo) {
                remoteVideo.srcObject = event.streams[0];
                remoteVideo.muted = false;
                const playPromise = remoteVideo.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(() => {
                        // Browser may require user gesture; stream is still attached.
                    });
                }
            }
        };

        if (localStream) {
            localStream.getTracks().forEach((track) => {
                peerConnection.addTrack(track, localStream);
            });
        }
    }

    async function handleSignal(type, payload) {
        // Handle forced disconnect from another device
        if (type === 'disconnect') {
            const consultationId = Number(currentConsultationId || 0);
            const reason = String(payload?.reason || '');
            const requestRow = consultationId > 0
                ? document.querySelector(`.request-row[data-consultation-id="${consultationId}"]`)
                : null;
            const attempts = Number(requestRow?.dataset.callAttempts || 0);
            const reachedMaxAttempts = attempts >= 3;
            const message = reason === 'no_answer'
                ? (reachedMaxAttempts
                    ? 'No answer after 3 attempts. Consultation marked as incomplete.'
                    : 'Student did not answer this attempt.')
                : reason === 'declined'
                    ? (reachedMaxAttempts
                        ? 'Student declined after 3 attempts. Consultation marked as incomplete.'
                        : 'Student declined this call. You can call again.')
                    : 'Call ended by the other participant.';
            actuallyStopCall();
            if (consultationId > 0) {
                if (reason === 'call_ended') {
                    syncRequestRowStatus(consultationId, 'completed');
                } else if (reason === 'declined' || reason === 'no_answer') {
                    syncRequestRowStatus(consultationId, attempts >= 3 ? 'incompleted' : 'approved');
                }
            }
            const toastMsg = document.createElement('div');
            toastMsg.style.cssText = 'position:fixed;top:16px;right:16px;background:#fff3cd;border:1px solid #ffc107;color:#856404;padding:12px 16px;border-radius:8px;z-index:9999;font-weight:600;';
            toastMsg.textContent = message;
            document.body.appendChild(toastMsg);
            setTimeout(() => toastMsg.remove(), 5000);
            return;
        }

        if (!peerConnection) {
            createPeerConnection();
        }

        if (type === 'offer') {
            await peerConnection.setRemoteDescription(new RTCSessionDescription(payload));
            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);
            await sendSignal('answer', answer);
        }

        if (type === 'answer') {
            await peerConnection.setRemoteDescription(new RTCSessionDescription(payload));
            callAnswered = true;
            clearOutgoingCountdown();
            setCallStatusLabel('Video Session');
            if (!callStartAt) {
                startCallTimer();
            }
        }

        if (type === 'ice' && payload?.candidate) {
            try {
                await peerConnection.addIceCandidate(new RTCIceCandidate(payload.candidate));
            } catch (_) {
                // ignore
            }
        }
    }

    async function startVideoCall(consultationId, role, options = {}) {
        if (!consultationId) return;
        if (currentConsultationId && currentConsultationId !== consultationId) {
            actuallyStopCall();
        }
        currentConsultationId = consultationId;
        currentDeviceSessionId = DEVICE_SESSION_ID;
        activeCallRole = role || 'instructor';
        callAnswered = !!options.alreadyAnswered;
        openCallModal();

        if (!window.isSecureContext && location.hostname !== 'localhost') {
            actuallyStopCall();
            alert('Camera/Mic requires HTTPS on other devices. Open this site via https:// to start video calls.');
            return;
        }

        try {
            localStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                },
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true,
                },
            });
            if (localVideo) localVideo.srcObject = localStream;
            createPeerConnection();
        } catch (error) {
            actuallyStopCall();
            alert('Camera/Mic access is required for video call.');
            return;
        }

        if (role === 'instructor') {
            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            await sendSignal('offer', offer);
            if (callAnswered) {
                setCallStatusLabel('Video Session');
                startCallTimer();
            } else {
                startOutgoingCountdown(20);
            }
        } else {
            setCallStatusLabel('Video Session');
            startCallTimer();
        }

        pollTimer = setInterval(pollSignals, 2000);
    }

    // Confirmation modal handlers
    const endCallConfirmModal = document.getElementById('endCallConfirmModal');
    const endCallConfirmOverlay = document.getElementById('endCallConfirmOverlay');
    const endCallConfirmYes = document.getElementById('endCallConfirmYes');
    const endCallConfirmNo = document.getElementById('endCallConfirmNo');

    function showEndCallConfirmation() {
        if (endCallConfirmModal && endCallConfirmOverlay) {
            endCallConfirmModal.style.display = 'block';
            endCallConfirmOverlay.style.display = 'block';
        }
    }

    function hideEndCallConfirmation() {
        if (endCallConfirmModal && endCallConfirmOverlay) {
            endCallConfirmModal.style.display = 'none';
            endCallConfirmOverlay.style.display = 'none';
        }
    }

    if (endCallConfirmYes) {
        endCallConfirmYes.addEventListener('click', async () => {
            hideEndCallConfirmation();
            const consultationId = currentConsultationId;
            if (!consultationId || isEndingCall) {
                actuallyStopCall();
                return;
            }

            isEndingCall = true;
            try {
                if (!callAnswered && activeCallRole === 'instructor') {
                    let noAnswerResponse = null;
                    try {
                        await sendSignal('disconnect', { reason: 'no_answer' });
                    } catch (_) {
                        // ignore
                    }
                    noAnswerResponse = await markNoAnswer(consultationId);
                    syncRequestRowStatus(
                        consultationId,
                        String(noAnswerResponse?.status || 'approved').toLowerCase(),
                        {
                        callAttempts: noAnswerResponse?.call_attempts,
                        }
                    );
                } else {
                    try {
                        await sendSignal('disconnect', { reason: 'call_ended' });
                    } catch (_) {
                        // ignore
                    }
                    await finalizeCall(consultationId);
                    syncRequestRowStatus(consultationId, 'completed');
                }
            } catch (_) {
                // ignore
            } finally {
                isEndingCall = false;
                actuallyStopCall();
            }
        });
    }

    if (endCallConfirmNo) {
        endCallConfirmNo.addEventListener('click', () => {
            hideEndCallConfirmation();
        });
    }

    function updateIncompleteButtonVisibility() {
        const requestRows = document.querySelectorAll('.request-row');
        requestRows.forEach((requestRow) => {
            const attempts = Number(requestRow.dataset.callAttempts || 0);
            const actionsWrap = requestRow.querySelector('.request-actions');
            if (!actionsWrap) return;

            const startForm = actionsWrap.querySelector('.start-session-form');
            const incompleteForm = actionsWrap.querySelector('.mark-incomplete-form');
            if (attempts >= 3) {
                if (startForm) startForm.style.display = 'none';
                if (incompleteForm) incompleteForm.style.display = 'block';
            } else {
                if (startForm) startForm.style.display = '';
                if (incompleteForm) incompleteForm.style.display = 'none';
            }
        });
    }

    // Initial check for button visibility from server-side attempts
    updateIncompleteButtonVisibility();

    if (closeCallModal) closeCallModal.addEventListener('click', stopCall);
    if (endCallBtn) endCallBtn.addEventListener('click', stopCall);
    if (toggleCameraBtn) {
        toggleCameraBtn.addEventListener('click', () => {
            if (!localStream) return;
            const videoTrack = localStream.getVideoTracks()[0];
            if (!videoTrack) return;
            videoTrack.enabled = !videoTrack.enabled;
            toggleCameraBtn.querySelector('.call-btn-text').textContent = videoTrack.enabled ? 'Camera Off' : 'Camera On';
        });
    }
    if (toggleMicBtn) {
        toggleMicBtn.addEventListener('click', () => {
            if (!localStream) return;
            const audioTrack = localStream.getAudioTracks()[0];
            if (!audioTrack) return;
            audioTrack.enabled = !audioTrack.enabled;
            toggleMicBtn.querySelector('.call-btn-text').textContent = audioTrack.enabled ? 'Mic Off' : 'Mic On';
        });
    }
    if (toggleTranscriptBtn) {
        toggleTranscriptBtn.addEventListener('click', () => {
            if (!transcriptActive) {
                startTranscript();
            } else {
                stopTranscript();
            }
        });
    }
    if (callModal) {
        callModal.addEventListener('click', (event) => {
            if (event.target === callModal) {
                stopCall();
            }
        });
    }

    const autoCallRow = document.querySelector('.request-row[data-status="in_progress"][data-mode*="video"]');
    if (autoCallRow) {
        startVideoCall(autoCallRow.dataset.consultationId, 'instructor', {
            alreadyAnswered: Boolean(autoCallRow.dataset.startedAt),
        });
    }

    if (closeRequestsSection && requestsSection) {
        closeRequestsSection.addEventListener('click', () => {
            setHistorySidebarIconOnly(false);
            setHistoryOnlyMode(false);
            setPrimaryDashboardVisible(true);
            if (historySection) historySection.classList.add('is-hidden');
            requestsSection.classList.add('is-hidden');
        });
    }

    if (overviewViewAllBtn && requestsSection) {
        overviewViewAllBtn.addEventListener('click', () => {
            setHistorySidebarIconOnly(true);
            setHistoryOnlyMode(false);
            setPrimaryDashboardVisible(false);
            if (historySection) historySection.classList.add('is-hidden');
            requestsSection.classList.remove('is-hidden');
            requestsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            if (scheduleSection) scheduleSection.classList.add('is-hidden');
            if (feedbackSection) feedbackSection.classList.add('is-hidden');
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }

    if (requestsLink && requestsSection) {
        requestsLink.addEventListener('click', (event) => {
            event.preventDefault();
            setHistorySidebarIconOnly(true);
            setHistoryOnlyMode(false);
            setPrimaryDashboardVisible(false);
            if (historySection) historySection.classList.add('is-hidden');
            requestsSection.classList.remove('is-hidden');
            requestsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            if (scheduleSection) scheduleSection.classList.add('is-hidden');
            if (feedbackSection) feedbackSection.classList.add('is-hidden');
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }

    if (dashboardLink) {
        dashboardLink.addEventListener('click', (event) => {
            event.preventDefault();
            setHistorySidebarIconOnly(false);
            setHistoryOnlyMode(false);
            setPrimaryDashboardVisible(true);
            if (historySection) historySection.classList.add('is-hidden');
            if (requestsSection) requestsSection.classList.add('is-hidden');
            if (scheduleSection) scheduleSection.classList.add('is-hidden');
            if (feedbackSection) feedbackSection.classList.add('is-hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }

    if (scheduleLink && scheduleSection) {
        scheduleLink.addEventListener('click', (event) => {
            event.preventDefault();
            setHistorySidebarIconOnly(false);
            setHistoryOnlyMode(false);
            setPrimaryDashboardVisible(false);
            if (historySection) historySection.classList.add('is-hidden');
            scheduleSection.classList.remove('is-hidden');
            scheduleSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            if (requestsSection) requestsSection.classList.add('is-hidden');
            if (feedbackSection) feedbackSection.classList.add('is-hidden');
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }

    if (closeScheduleSection && scheduleSection) {
        closeScheduleSection.addEventListener('click', () => {
            setHistorySidebarIconOnly(false);
            setHistoryOnlyMode(false);
            setPrimaryDashboardVisible(true);
            if (historySection) historySection.classList.add('is-hidden');
            scheduleSection.classList.add('is-hidden');
        });
    }

    if (feedbackLink && feedbackSection) {
        feedbackLink.addEventListener('click', (event) => {
            event.preventDefault();
            setHistorySidebarIconOnly(false);
            setHistoryOnlyMode(false);
            setPrimaryDashboardVisible(false);
            if (historySection) historySection.classList.add('is-hidden');
            feedbackSection.classList.remove('is-hidden');
            feedbackSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            if (requestsSection) requestsSection.classList.add('is-hidden');
            if (scheduleSection) scheduleSection.classList.add('is-hidden');
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }

    if (closeFeedbackSection && feedbackSection) {
        closeFeedbackSection.addEventListener('click', () => {
            setHistorySidebarIconOnly(false);
            setHistoryOnlyMode(false);
            setPrimaryDashboardVisible(true);
            if (historySection) historySection.classList.add('is-hidden');
            feedbackSection.classList.add('is-hidden');
        });
    }

    function openSummaryModal(data) {
        if (!summaryModal || !summaryForm) return;
        summaryForm.action = `${summaryActionBase}/${data.id}/summary`;
        summaryForm.dataset.consultationId = String(data.id || '');
        summaryForm.dataset.student = String(data.student || 'Student');
        summaryForm.dataset.studentId = String(data.studentId || '--');
        summaryForm.dataset.date = String(data.date || '--');
        summaryForm.dataset.time = String(data.time || '--');
        summaryForm.dataset.type = String(data.type || '--');
        summaryForm.dataset.mode = String(data.mode || '--');
        summaryForm.dataset.duration = String(data.duration || '--');
        if (summaryStudent) summaryStudent.textContent = `Student: ${data.student}`;
        if (summaryStudentId) summaryStudentId.textContent = `Student ID: ${data.studentId || '--'}`;
        if (summaryDate) summaryDate.textContent = `Date & Time: ${data.date} ${data.time}`;
        if (summaryType) summaryType.textContent = `Type: ${data.type}`;
        if (summaryMode) summaryMode.textContent = `Mode: ${data.mode}`;
        if (summaryText) summaryText.value = data.summary || '';
        if (summaryActionTaken) summaryActionTaken.value = data.actionTaken || '';
        summaryModal.classList.add('open');
        summaryModal.setAttribute('aria-hidden', 'false');
    }

    function closeSummary() {
        if (!summaryModal) return;
        summaryModal.classList.remove('open');
        summaryModal.setAttribute('aria-hidden', 'true');
    }

    if (summaryOpenBtns.length) {
        summaryOpenBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                openSummaryModal({
                    id: btn.dataset.id,
                    student: btn.dataset.student || 'Student',
                    studentId: btn.dataset.studentId || '--',
                    date: btn.dataset.date || '--',
                    time: btn.dataset.time || '--',
                    type: btn.dataset.type || '--',
                    mode: btn.dataset.mode || '--',
                    duration: btn.dataset.duration || '--',
                    summary: btn.dataset.summary || '',
                    actionTaken: btn.dataset.transcript || '',
                });
            });
        });
    }

    if (closeSummaryModal) {
        closeSummaryModal.addEventListener('click', closeSummary);
    }

    if (cancelSummaryModal) {
        cancelSummaryModal.addEventListener('click', closeSummary);
    }

    if (summaryModal) {
        summaryModal.addEventListener('click', (event) => {
            if (event.target === summaryModal) {
                closeSummary();
            }
        });
    }

    if (summaryForm) {
        summaryForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!summaryForm.reportValidity()) {
                return;
            }

            const submitBtn = summaryForm.querySelector('button[type="submit"]');
            const consultationId = String(summaryForm.dataset.consultationId || '');
            const modeValue = String(summaryForm.dataset.mode || '');
            const isFaceToFace = modeValue.toLowerCase().includes('face');
            const requestRow = consultationId
                ? document.querySelector(`.request-row[data-consultation-id="${consultationId}"]`)
                : null;

            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                }

                const formData = new FormData(summaryForm);
                const response = await fetch(summaryForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getRequestCsrfToken(),
                    },
                    body: formData,
                });

                if (!response.ok) {
                    throw new Error(`Summary save failed: ${response.status}`);
                }

                const payload = await response.json().catch(() => ({}));
                const savedSummary = String(payload?.summary_text ?? formData.get('summary_text') ?? '');
                const savedActionTaken = String(payload?.action_taken_text ?? formData.get('action_taken_text') ?? '');

                if (requestRow) {
                    requestRow.dataset.summary = savedSummary;
                    requestRow.dataset.transcript = savedActionTaken;
                    const currentStatus = String(requestRow.dataset.status || '').toLowerCase();
                    const nextStatus = isFaceToFace ? 'completed' : (currentStatus || 'completed');
                    updateRequestRowState(requestRow, nextStatus);
                }

                if (isFaceToFace) {
                    upsertInstructorHistoryRow({
                        id: consultationId,
                        student: summaryForm.dataset.student || 'Student',
                        studentId: summaryForm.dataset.studentId || '--',
                        date: summaryForm.dataset.date || '--',
                        time: summaryForm.dataset.time || '--',
                        type: summaryForm.dataset.type || '--',
                        mode: summaryForm.dataset.mode || '--',
                        duration: summaryForm.dataset.duration || '--',
                        summary: savedSummary,
                        transcript: savedActionTaken,
                    });
                }

                closeSummary();
            } catch (_) {
                summaryForm.submit();
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        });
    }

    function openDetailsModal(data) {
        if (!detailsModal) return;

        if (detailsSubtitle) detailsSubtitle.textContent = `${data.type} - ${data.mode} Session`;
        if (detailsDate) detailsDate.textContent = `Date & Time: ${data.date} at ${data.time}`;
        if (detailsStudent) detailsStudent.textContent = `Student: ${data.student}`;
        if (detailsStudentId) detailsStudentId.textContent = `Student ID: ${data.studentId || '--'}`;
        if (detailsMode) detailsMode.textContent = `Mode: ${data.mode}`;
        if (detailsType) detailsType.textContent = `Type: ${data.type}`;
        if (detailsDuration) detailsDuration.textContent = `Duration: ${data.duration || '--'}`;
        if (detailsSummaryText) {
            detailsSummaryText.textContent = data.summary || 'Summary not yet available.';
        }
        if (detailsTranscriptWrap) {
            detailsTranscriptWrap.style.display = 'block';
        }
        if (detailsTranscriptText) {
            detailsTranscriptText.textContent = data.transcript || 'Action taken not yet available.';
        }

        detailsModal.classList.add('open');
        detailsModal.setAttribute('aria-hidden', 'false');
    }

    function closeDetails() {
        if (!detailsModal) return;
        detailsModal.classList.remove('open');
        detailsModal.setAttribute('aria-hidden', 'true');
    }

    if (detailsOpenBtns.length) {
        detailsOpenBtns.forEach((btn) => {
            bindDetailsOpenButton(btn);
        });
    }

    if (closeDetailsModal) {
        closeDetailsModal.addEventListener('click', closeDetails);
    }

    if (detailsModal) {
        detailsModal.addEventListener('click', (event) => {
            if (event.target === detailsModal) {
                closeDetails();
            }
        });
    }

    function getRequestCsrfToken() {
        const fromMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        if (fromMeta) return fromMeta;

        const fromForm = document.querySelector('.request-actions form input[name="_token"]')?.value || '';
        if (fromForm) return fromForm;

        const cookieMatch = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
        if (cookieMatch && cookieMatch[1]) {
            try {
                return decodeURIComponent(cookieMatch[1]);
            } catch (_) {
                return cookieMatch[1];
            }
        }

        return '';
    }
    const consultationActionBase = @json(url('/instructor/consultations'));
    const statusClassList = ['pending', 'approved', 'in_progress', 'completed', 'incompleted', 'declined'];

    function mapActionToStatus(actionUrl) {
        if (actionUrl.includes('/approve')) return 'approved';
        if (actionUrl.includes('/decline')) return 'declined';
        if (actionUrl.includes('/start')) return 'in_progress';
        if (actionUrl.includes('/end')) return 'completed';
        if (actionUrl.includes('/mark-incomplete')) return 'incompleted';
        return null;
    }

    function renderRequestActions(actionsWrap, consultationId, status, requestRow = null) {
        if (!actionsWrap) return;
        const requestCsrfToken = getRequestCsrfToken();
        const modeValue = String(requestRow?.dataset.mode || '').toLowerCase();
        const isFaceToFace = modeValue.includes('face');

        function renderSummaryActionButton() {
            const summaryValue = String(requestRow?.dataset.summary || '').trim();
            const actionTakenValue = String(requestRow?.dataset.transcript || '').trim();
            const summaryBtnLabel = summaryValue ? 'View / Edit Summary' : 'Add Summary';
            actionsWrap.innerHTML = `
                <button type="button" class="request-btn summary summary-open-btn">${summaryBtnLabel}</button>
            `;
            const summaryBtn = actionsWrap.querySelector('.summary-open-btn');
            if (summaryBtn && requestRow) {
                const requestMetaCols = requestRow.querySelectorAll('.request-meta');
                const dateMeta = requestMetaCols[0]?.querySelectorAll('span') || [];
                const studentName = requestRow.querySelector('.request-user-name')?.textContent?.trim() || 'Student';
                const studentIdRaw = requestRow.querySelector('.request-user-id')?.textContent?.trim() || 'ID: --';
                const studentId = studentIdRaw.replace(/^ID:\s*/i, '').trim() || '--';
                const typeValue = requestMetaCols[1]?.querySelector('.request-type-title')?.textContent?.trim()
                    || requestMetaCols[1]?.querySelector('span')?.textContent?.trim()
                    || '--';
                const modeLabel = requestRow.dataset.modeLabel
                    || requestMetaCols[2]?.querySelector('.request-tag')?.textContent?.trim()
                    || '--';
                const dateValue = dateMeta[0]?.textContent?.trim() || '--';
                const timeValue = dateMeta[1]?.textContent?.trim() || '--';
                summaryBtn.addEventListener('click', () => {
                    openSummaryModal({
                        id: consultationId,
                        student: studentName,
                        studentId,
                        date: dateValue,
                        time: timeValue,
                        type: typeValue,
                        mode: modeLabel,
                        summary: summaryValue,
                        actionTaken: actionTakenValue,
                    });
                });
            }
        }

        if (status === 'pending') {
            actionsWrap.innerHTML = `
                <form method="POST" action="${consultationActionBase}/${consultationId}/approve">
                    <input type="hidden" name="_token" value="${requestCsrfToken}">
                    <button type="submit" class="request-btn approve">Approve</button>
                </form>
                <form method="POST" action="${consultationActionBase}/${consultationId}/decline">
                    <input type="hidden" name="_token" value="${requestCsrfToken}">
                    <button type="submit" class="request-btn decline">Decline</button>
                </form>
            `;
        } else if (status === 'approved') {
            if (isFaceToFace) {
                renderSummaryActionButton();
            } else {
            const callAttempts = Number(requestRow?.dataset.callAttempts || 0);
            const canMarkIncomplete = callAttempts >= 3;
            const nextAttempt = Math.min(callAttempts + 1, 3);
            const buttonLabel = callAttempts > 0 ? 'Call Again' : 'Video Call';
            actionsWrap.innerHTML = `
                ${canMarkIncomplete ? '' : `
                    <form method="POST" action="${consultationActionBase}/${consultationId}/start" class="start-session-form">
                        <input type="hidden" name="_token" value="${requestCsrfToken}">
                        <button type="submit"
                                class="request-btn start start-session-btn"
                                data-consultation-id="${consultationId}">
                            ${buttonLabel} (Attempt ${nextAttempt}/3)
                        </button>
                    </form>
                `}
                <form method="POST"
                      action="${consultationActionBase}/${consultationId}/mark-incomplete"
                      class="mark-incomplete-form"
                      style="${canMarkIncomplete ? '' : 'display:none;'}"
                      data-consultation-id="${consultationId}">
                    <input type="hidden" name="_token" value="${requestCsrfToken}">
                    <button type="submit" class="request-btn decline mark-incomplete-btn">Mark as Incompleted</button>
                </form>
            `;
            }
        } else if (status === 'in_progress') {
            actionsWrap.innerHTML = '<span class="request-tag">Video call in progress</span>';
        } else if (status === 'completed' || status === 'incompleted' || status === 'declined') {
            renderSummaryActionButton();
        } else {
            actionsWrap.innerHTML = '<span class="request-tag">No Action</span>';
        }

        bindRequestActionForms(actionsWrap);
    }

    function updateRequestRowState(requestRow, nextStatus, options = {}) {
        if (!requestRow || !nextStatus) return;
        const preservePlacement = options.preservePlacement === true;

        requestRow.dataset.status = nextStatus;

        const statusChip = requestRow.querySelector('.request-status');
        if (statusChip) {
            statusClassList.forEach((statusClass) => statusChip.classList.remove(statusClass));
            statusChip.classList.add(nextStatus);
            statusChip.textContent = nextStatus.replace('_', ' ').toUpperCase();
        }

        const consultationId = requestRow.dataset.consultationId;
        const actionsWrap = requestRow.querySelector('.request-actions');
        renderRequestActions(actionsWrap, consultationId, nextStatus, requestRow);

        // Keep declined items in the same visible spot right after the action.
        if (!preservePlacement) {
            refreshRequestOrdering(false);
        }
    }

    function bindRequestActionForms(scope = document) {
        const forms = Array.from(scope.querySelectorAll('.request-actions form'));
        forms.forEach((form) => {
            if (form.dataset.ajaxBound === '1') return;
            form.dataset.ajaxBound = '1';
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                const requestRow = form.closest('.request-row');
                const nextStatus = mapActionToStatus(form.action);
                if (!requestRow || !nextStatus) {
                    form.submit();
                    return;
                }

                try {
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.7';
                    }

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getRequestCsrfToken(),
                        },
                        body: (() => {
                            const formData = new FormData(form);
                            if (!formData.get('_token')) {
                                formData.set('_token', getRequestCsrfToken());
                            }
                            return formData;
                        })(),
                    });

                    if (!response.ok) {
                        if (response.status === 419) {
                            window.location.reload();
                            return;
                        }
                        throw new Error(`Request failed: ${response.status}`);
                    }

                    let responseData = null;
                    const responseContentType = response.headers.get('content-type') || '';
                    if (responseContentType.includes('application/json')) {
                        responseData = await response.json();
                    }

                    if (responseData && typeof responseData.call_attempts !== 'undefined') {
                        requestRow.dataset.callAttempts = String(responseData.call_attempts);
                    }

                    updateRequestRowState(requestRow, nextStatus, {
                        preservePlacement: nextStatus === 'declined',
                    });
                    updateIncompleteButtonVisibility();

                    if (nextStatus === 'in_progress') {
                        const modeValue = String(requestRow.dataset.mode || '').toLowerCase();
                        const consultationId = requestRow.dataset.consultationId;
                        if (modeValue.includes('video') && consultationId) {
                            startVideoCall(consultationId, 'instructor', {
                                alreadyAnswered: false,
                            });
                        }
                    }
                } catch (error) {
                    form.submit();
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '';
                    }
                }
            });
        });
    }

    bindRequestActionForms();

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideAvailabilityModal();
            hideHistoryModal();
            closeSummary();
            closeDetails();
        }
    });

    // ===== REQUEST PAGINATION =====
    const requestTable = document.querySelector('.request-table');
    let requestRows = Array.from(document.querySelectorAll('.request-row-wrap'));
    const requestPaginationInfo = document.getElementById('requestPaginationInfo');
    const requestPageNumbers = document.getElementById('requestPageNumbers');
    const prevRequestBtn = document.getElementById('prevRequestBtn');
    const nextRequestBtn = document.getElementById('nextRequestBtn');
    const requestStatusFilterDropdown = document.getElementById('requestStatusFilterDropdown');
    const requestStatusFilterBtn = document.getElementById('requestStatusFilterBtn');
    const requestStatusFilterLabel = document.getElementById('requestStatusFilterLabel');
    const requestStatusFilterMenu = document.getElementById('requestStatusFilterMenu');
    const requestStatusFilterOptions = Array.from(document.querySelectorAll('.request-status-filter-option'));
    const requestSearchInput = document.getElementById('requestSearchInput');

    const requestItemsPerPage = 10;
    let currentRequestPage = 1;
    let selectedRequestStatus = 'all';
    let requestSearchTerm = '';
    let filteredRequestRows = [...requestRows];
    const requestStatusPriority = {
        pending: 0,
        approved: 1,
        in_progress: 2,
        incompleted: 3,
        declined: 4,
        completed: 5,
        cancelled: 6,
    };

    requestRows.forEach((item, index) => {
        if (!item.dataset.initialOrder) {
            item.dataset.initialOrder = String(index);
        }
    });

    function normalizeFilterStatus(statusValue) {
        const status = String(statusValue || '').toLowerCase();
        if (status === 'decline') return 'declined';
        return status;
    }

    function isRequestStatusMatched(rowStatus, filterStatus) {
        if (!filterStatus || filterStatus === 'all') return true;
        return rowStatus === normalizeFilterStatus(filterStatus);
    }

    function isRequestSearchMatched(item, searchTerm) {
        if (!searchTerm) return true;
        const row = item.querySelector('.request-row');
        const searchSource = String(row?.textContent || '').toLowerCase();
        return searchSource.includes(searchTerm);
    }

    function applyRequestFilters() {
        filteredRequestRows = requestRows.filter((item) => {
            const rowStatus = String(item.querySelector('.request-row')?.dataset.status || '').toLowerCase();
            const statusMatched = isRequestStatusMatched(rowStatus, selectedRequestStatus);
            const searchMatched = isRequestSearchMatched(item, requestSearchTerm);
            return statusMatched && searchMatched;
        });
    }

    function openRequestStatusFilter() {
        if (!requestStatusFilterBtn || !requestStatusFilterMenu) return;
        requestStatusFilterMenu.classList.add('open');
        requestStatusFilterMenu.setAttribute('aria-hidden', 'false');
        requestStatusFilterBtn.setAttribute('aria-expanded', 'true');
    }

    function closeRequestStatusFilter() {
        if (!requestStatusFilterBtn || !requestStatusFilterMenu) return;
        requestStatusFilterMenu.classList.remove('open');
        requestStatusFilterMenu.setAttribute('aria-hidden', 'true');
        requestStatusFilterBtn.setAttribute('aria-expanded', 'false');
    }

    function setRequestStatusFilter(status, label) {
        selectedRequestStatus = String(status || 'all').toLowerCase();
        if (requestStatusFilterLabel) {
            requestStatusFilterLabel.textContent = label || 'Choose a status...';
        }
        closeRequestStatusFilter();
        refreshRequestOrdering(true);
    }

    function getRequestTotals() {
        const totalRequestItems = filteredRequestRows.length;
        const totalRequestPages = Math.max(1, Math.ceil(totalRequestItems / requestItemsPerPage));
        return { totalRequestItems, totalRequestPages };
    }

    function getRequestStatusPriority(status) {
        return Object.prototype.hasOwnProperty.call(requestStatusPriority, status)
            ? requestStatusPriority[status]
            : 99;
    }

    function sortRequestRows() {
        if (!requestTable || !requestRows.length) return;

        requestRows.sort((a, b) => {
            const rowA = a.querySelector('.request-row');
            const rowB = b.querySelector('.request-row');
            const statusA = (rowA?.dataset.status || '').toLowerCase();
            const statusB = (rowB?.dataset.status || '').toLowerCase();

            const priorityA = getRequestStatusPriority(statusA);
            const priorityB = getRequestStatusPriority(statusB);
            if (priorityA !== priorityB) {
                return priorityA - priorityB;
            }

            const orderA = Number(a.dataset.initialOrder || 0);
            const orderB = Number(b.dataset.initialOrder || 0);
            return orderA - orderB;
        });

        requestRows.forEach((item) => requestTable.appendChild(item));
    }

    function createRequestPagination() {
        if (!requestPageNumbers) return;
        const { totalRequestItems, totalRequestPages } = getRequestTotals();
        requestPageNumbers.innerHTML = '';

        if (totalRequestItems === 0) {
            if (prevRequestBtn) prevRequestBtn.style.display = 'none';
            if (nextRequestBtn) nextRequestBtn.style.display = 'none';
            return;
        }

        for (let i = 1; i <= totalRequestPages; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pagination-page-btn' + (i === currentRequestPage ? ' active' : '');
            btn.textContent = i;
            btn.addEventListener('click', () => showRequestPage(i));
            requestPageNumbers.appendChild(btn);
        }

        if (prevRequestBtn) prevRequestBtn.style.display = currentRequestPage > 1 ? 'block' : 'none';
        if (nextRequestBtn) nextRequestBtn.style.display = currentRequestPage < totalRequestPages ? 'block' : 'none';
    }

    function showRequestPage(pageNum, options = {}) {
        const { scroll = true } = options;
        const { totalRequestItems, totalRequestPages } = getRequestTotals();

        currentRequestPage = Math.min(Math.max(1, pageNum), totalRequestPages);
        const start = (currentRequestPage - 1) * requestItemsPerPage;
        const end = start + requestItemsPerPage;

        requestRows.forEach((item) => {
            item.style.display = 'none';
        });

        filteredRequestRows.forEach((item, index) => {
            item.style.display = (index >= start && index < end) ? 'block' : 'none';
        });

        if (requestPaginationInfo) {
            const displayStart = totalRequestItems > 0 ? Math.min(start + 1, totalRequestItems) : 0;
            const displayEnd = totalRequestItems > 0 ? Math.min(end, totalRequestItems) : 0;
            requestPaginationInfo.textContent = totalRequestItems > 0
                ? `Showing ${displayStart} to ${displayEnd} of ${totalRequestItems} requests`
                : 'No requests found';
        }

        createRequestPagination();
        if (scroll && requestTable) {
            window.scrollTo({ top: requestTable.offsetTop - 100, behavior: 'smooth' });
        }
    }

    function refreshRequestOrdering(goToFirstPage = false) {
        requestRows = Array.from(document.querySelectorAll('.request-row-wrap'));
        requestRows.forEach((item, index) => {
            if (!item.dataset.initialOrder) {
                item.dataset.initialOrder = String(index);
            }
        });

        sortRequestRows();
        applyRequestFilters();
        if (goToFirstPage) currentRequestPage = 1;
        showRequestPage(currentRequestPage, { scroll: false });
        if (requestPaginationInfo) {
            requestPaginationInfo.style.display = 'block';
        }
    }

    if (requestStatusFilterBtn) {
        requestStatusFilterBtn.addEventListener('click', () => {
            if (!requestStatusFilterMenu) return;
            if (requestStatusFilterMenu.classList.contains('open')) {
                closeRequestStatusFilter();
            } else {
                openRequestStatusFilter();
            }
        });
    }

    if (requestStatusFilterOptions.length) {
        requestStatusFilterOptions.forEach((optionBtn) => {
            optionBtn.addEventListener('click', () => {
                const nextStatus = optionBtn.dataset.status || 'all';
                const nextLabel = optionBtn.dataset.label || 'Choose a status...';
                setRequestStatusFilter(nextStatus, nextLabel);
            });
        });
    }

    if (requestSearchInput) {
        requestSearchInput.addEventListener('input', () => {
            requestSearchTerm = String(requestSearchInput.value || '').trim().toLowerCase();
            refreshRequestOrdering(true);
        });
    }

    document.addEventListener('click', (event) => {
        if (!requestStatusFilterDropdown) return;
        if (requestStatusFilterDropdown.contains(event.target)) return;
        closeRequestStatusFilter();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeRequestStatusFilter();
        }
    });

    if (prevRequestBtn) {
        prevRequestBtn.addEventListener('click', () => {
            if (currentRequestPage > 1) showRequestPage(currentRequestPage - 1);
        });
    }

    if (nextRequestBtn) {
        nextRequestBtn.addEventListener('click', () => {
            const { totalRequestPages } = getRequestTotals();
            if (currentRequestPage < totalRequestPages) showRequestPage(currentRequestPage + 1);
        });
    }

    // Initialize pagination on page load
    refreshRequestOrdering(true);

    const hasAvailabilityError = @json($errors->has('days'));
    if (hasAvailabilityError) {
        showAvailabilityModal();
    }

// Auto-refresh consultation data every 3 seconds
    // Keep status/action rows and counters synced across devices without manual refresh.
    let lastPendingCount = @json($stats['pending'] ?? 0);
    let knownConsultationIds = new Set(
        Array.from(document.querySelectorAll('.request-row'))
            .map((row) => Number(row.dataset.consultationId || 0))
            .filter((id) => id > 0)
    );

    function updateInstructorStatCounters(stats = {}) {
        const counters = document.querySelectorAll('.stat-count[data-stat]');
        counters.forEach((counter) => {
            const key = counter.dataset.stat;
            if (!key) return;
            if (Object.prototype.hasOwnProperty.call(stats, key)) {
                counter.textContent = String(Number(stats[key] || 0));
            }
        });
    }

    function updateInstructorNotificationBadge(unreadCount = 0) {
        const badge = document.getElementById('notificationBadge');
        if (!badge) return;
        const count = Number(unreadCount || 0);
        badge.textContent = String(count);
        badge.style.display = count > 0 ? 'inline-flex' : 'none';
    }

    function escapeInstructorNotificationHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function renderInstructorNotificationList(notifications = []) {
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
            const title = escapeInstructorNotificationHtml(notification?.title || 'Notification');
            const message = escapeInstructorNotificationHtml(notification?.message || '');
            const timeLabel = escapeInstructorNotificationHtml(notification?.created_at_human || 'Just now');
            const unreadClass = notification?.is_read ? '' : ' unread';

            return `
                <li class="notification-item${unreadClass}">
                    <span class="notification-dot"></span>
                    <div>
                        <div style="font-weight:700">${title}</div>
                        <div style="color:var(--muted);margin-top:4px">${message}</div>
                        <div style="color:#9ca3af;font-size:11px;margin-top:6px">${timeLabel}</div>
                    </div>
                </li>
            `;
        }).join('');
    }

    function getInstructorManilaDateIso(dateInput = new Date()) {
        try {
            const dtf = new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Manila',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
            });
            const parts = dtf.formatToParts(dateInput);
            const year = parts.find((p) => p.type === 'year')?.value || '0000';
            const month = parts.find((p) => p.type === 'month')?.value || '00';
            const day = parts.find((p) => p.type === 'day')?.value || '00';
            return `${year}-${month}-${day}`;
        } catch (_) {
            const d = new Date(dateInput);
            const year = String(d.getFullYear());
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    }

    function getInstructorManilaNowParts(dateInput = new Date()) {
        try {
            const dtf = new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Manila',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hourCycle: 'h23',
            });
            const parts = dtf.formatToParts(dateInput);
            const year = parts.find((p) => p.type === 'year')?.value || '0000';
            const month = parts.find((p) => p.type === 'month')?.value || '00';
            const day = parts.find((p) => p.type === 'day')?.value || '00';
            const hour = Number(parts.find((p) => p.type === 'hour')?.value || '0');
            const minute = Number(parts.find((p) => p.type === 'minute')?.value || '0');
            return { iso: `${year}-${month}-${day}`, minutesOfDay: (hour * 60) + minute };
        } catch (_) {
            const d = new Date(dateInput);
            const year = String(d.getFullYear());
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return { iso: `${year}-${month}-${day}`, minutesOfDay: (Number(d.getHours()) * 60) + Number(d.getMinutes()) };
        }
    }

    function isInstructorUpcomingStatus(status) {
        return ['pending', 'approved', 'in_progress'].includes(String(status || '').toLowerCase());
    }

    function getInstructorMinutesFromTimeValue(timeValue) {
        const match = String(timeValue || '').match(/^(\d{1,2}):(\d{2})/);
        if (!match) return null;
        const hour = Number(match[1]);
        const minute = Number(match[2]);
        if (!Number.isFinite(hour) || !Number.isFinite(minute)) return null;
        return (hour * 60) + minute;
    }

    function isInstructorUpcomingByDateTime(consultation, nowParts) {
        const status = String(consultation?.status || '').toLowerCase();
        if (!isInstructorUpcomingStatus(status)) return false;

        const dateValue = String(consultation?.consultation_date || '').trim();
        if (!dateValue) return false;
        if (dateValue > nowParts.iso) return true;
        if (dateValue < nowParts.iso) return false;
        if (status === 'in_progress') return true;

        const startMinutes = getInstructorMinutesFromTimeValue(consultation?.consultation_time);
        if (startMinutes === null) return true;
        return startMinutes >= nowParts.minutesOfDay;
    }

    function renderInstructorUpcomingSchedule(consultations = []) {
        if (!instructorUpcomingContent) return;

        const nowParts = getInstructorManilaNowParts();
        const upcoming = consultations
            .filter((item) => isInstructorUpcomingByDateTime(item, nowParts))
            .sort((a, b) => {
                const left = `${a.consultation_date || ''} ${a.consultation_time || ''}`;
                const right = `${b.consultation_date || ''} ${b.consultation_time || ''}`;
                return left.localeCompare(right);
            })
            .slice(0, 3);

        if (!upcoming.length) {
            instructorUpcomingContent.innerHTML = '<div class="overview-empty">No upcoming consultations scheduled.</div>';
            return;
        }

        const html = upcoming.map((consultation) => {
            const dateValue = String(consultation.consultation_date || '');
            const day = dateValue.length >= 10 ? dateValue.slice(8, 10) : '--';
            let monthLabel = '---';
            try {
                const parsed = new Date(`${dateValue}T00:00:00`);
                monthLabel = parsed.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
            } catch (_) {
                monthLabel = '---';
            }
            const title = consultation.type_label || 'Consultation Session';
            const timeRange = consultation.time_range || '--';

            return `
                <div class="schedule-item">
                    <div class="schedule-date-chip">
                        <span class="schedule-date-day">${day}</span>
                        <span class="schedule-date-month">${monthLabel}</span>
                    </div>
                    <div>
                        <p class="schedule-title">${title}</p>
                        <p class="schedule-time"><i class="fa-solid fa-clock" aria-hidden="true"></i> ${timeRange}</p>
                    </div>
                </div>
            `;
        }).join('');

        instructorUpcomingContent.innerHTML = `<div class="schedule-list">${html}</div>`;
    }

    function syncInstructorRowFromApi(requestRow, consultation) {
        if (!requestRow || !consultation) return;
        const nextStatus = String(consultation.status || '').toLowerCase();
        const nextAttempts = Number(consultation.call_attempts || 0);
        const prevStatus = String(requestRow.dataset.status || '').toLowerCase();
        const prevAttempts = Number(requestRow.dataset.callAttempts || 0);
        const nextNotes = String(consultation.student_notes || '').trim();

        requestRow.dataset.mode = String(consultation.consultation_mode || '').toLowerCase();
        requestRow.dataset.modeLabel = String(consultation.consultation_mode || '');
        requestRow.dataset.callAttempts = String(nextAttempts);
        requestRow.dataset.startedAt = consultation.started_at || '';
        requestRow.dataset.notes = nextNotes;

        const typeMeta = requestRow.querySelector('.request-meta.request-type');
        if (typeMeta) {
            let notePreview = typeMeta.querySelector('.request-note-preview');
            if (nextNotes) {
                if (!notePreview) {
                    notePreview = document.createElement('div');
                    notePreview.className = 'request-note-preview';
                    typeMeta.appendChild(notePreview);
                }
                notePreview.title = nextNotes;
                notePreview.innerHTML = `<span class="request-note-label">Note:</span> ${escapeHistoryHtml(nextNotes)}`;
            } else if (notePreview) {
                notePreview.remove();
            }
        }

        if (prevStatus !== nextStatus) {
            updateRequestRowState(requestRow, nextStatus);
            return;
        }

        if (prevAttempts !== nextAttempts) {
            const actionsWrap = requestRow.querySelector('.request-actions');
            renderRequestActions(actionsWrap, consultation.id, nextStatus, requestRow);
        }
    }

    function showNewRequestToast(studentName) {
        const notificationDiv = document.createElement('div');
        notificationDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 14px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            font-size: 14px;
            z-index: 10000;
            font-weight: 500;
            animation: slideDown 0.3s ease-out;
        `;
        notificationDiv.innerHTML = `New consultation request from <strong>${studentName || 'a student'}</strong>`;
        document.body.appendChild(notificationDiv);

        setTimeout(() => {
            notificationDiv.style.animation = 'slideUp 0.3s ease-out';
            setTimeout(() => notificationDiv.remove(), 300);
        }, 4000);
    }

    function pollConsultationUpdates() {
        fetch('{{ route("api.instructor.consultations-summary") }}', {
            cache: 'no-store',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((response) => response.json())
            .then((data) => {
                const consultations = Array.isArray(data?.consultations) ? data.consultations : [];
                const stats = data?.stats || {};
                updateInstructorStatCounters(stats);
                updateInstructorNotificationBadge(data?.unreadNotifications || 0);
                renderInstructorNotificationList(data?.notifications || []);
                const latestUnreadNotification = data?.latestUnreadNotification || null;
                if (latestUnreadNotification && notifToast && toastTitle && toastBody) {
                    const token = _buildInstructorNotificationToken(latestUnreadNotification);
                    if (!_hasShownInstructorToast(token)) {
                        toastTitle.textContent = latestUnreadNotification.title ?? 'New Notification';
                        toastBody.textContent = latestUnreadNotification.message ?? 'You have a new notification.';
                        notifToast.classList.add('show');
                        _markShownInstructorToast(token);
                        setTimeout(() => notifToast.classList.remove('show'), 6000);
                    }
                }
                renderInstructorUpcomingSchedule(consultations);

                const incomingIds = new Set();
                const newPendingConsultations = [];
                let structuralChanged = false;

                consultations.forEach((consultation) => {
                    const consultationId = Number(consultation.id || 0);
                    if (!consultationId) return;
                    incomingIds.add(consultationId);

                    const requestRow = document.querySelector(`.request-row[data-consultation-id="${consultationId}"]`);
                    if (!requestRow) {
                        if (requestTable) {
                            const rowWrap = createConsultationRow(consultation);
                            requestTable.insertBefore(rowWrap, requestTable.firstChild);
                            structuralChanged = true;
                        }
                    } else {
                        syncInstructorRowFromApi(requestRow, consultation);
                    }

                    if (
                        String(consultation.status || '').toLowerCase() === 'pending' &&
                        !knownConsultationIds.has(consultationId)
                    ) {
                        newPendingConsultations.push(consultation);
                    }
                });

                document.querySelectorAll('.request-row').forEach((row) => {
                    const consultationId = Number(row.dataset.consultationId || 0);
                    if (!consultationId) return;
                    if (!incomingIds.has(consultationId)) {
                        row.closest('.request-row-wrap')?.remove();
                        structuralChanged = true;
                    }
                });

                if (structuralChanged) {
                    refreshRequestOrdering(false);
                }
                updateIncompleteButtonVisibility();

                if (newPendingConsultations.length > 0) {
                    showNewRequestToast(newPendingConsultations[0]?.student_name);
                }

                knownConsultationIds = incomingIds;
                lastPendingCount = Number(stats.pending || 0);
            })
            .catch((error) => {
                console.log('Consultation update check failed (will retry):', error);
            });
    }
    function createConsultationRow(consultation) {
        const wrapper = document.createElement('div');
        wrapper.className = 'request-row-wrap';
        wrapper.dataset.initialOrder = '0'; // New items get priority

        const statusLower = (consultation.status || '').toLowerCase();
        const statusDisplay = statusLower.charAt(0).toUpperCase() + statusLower.slice(1).replace('_', ' ');
        const studentName = String(consultation.student_name || 'Student');
        const studentNotes = String(consultation.student_notes || '').trim();
        const initials = studentName
            .trim()
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map((part) => part.charAt(0).toUpperCase())
            .join('') || 'ST';

        wrapper.innerHTML = `
            <div class="request-row"
                 data-consultation-id="${consultation.id}"
                 data-status="${statusLower}"
                 data-mode="${consultation.consultation_mode.toLowerCase()}"
                 data-mode-label="${consultation.consultation_mode}"
                 data-call-attempts="${Number(consultation.call_attempts || 0)}"
                 data-started-at="${consultation.started_at || ''}"
                 data-summary=""
                 data-notes="${escapeHistoryHtml(studentNotes)}">
                <div class="request-user">
                    <div class="request-avatar">${initials}</div>
                    <div class="request-user-main">
                        <div class="request-user-top">
                            <div class="request-user-name">${studentName}</div>
                        </div>
                        <div class="request-user-id">ID: ${consultation.student_id}</div>
                        <span class="instructor-active-minutes-badge">Active —</span>
                    </div>
                </div>
                <div class="request-meta request-datetime">
                    <span><i class="fa-regular fa-calendar"></i> ${consultation.consultation_date}</span>
                    <span><i class="fa-regular fa-clock"></i> ${consultation.time_range}</span>
                </div>
                <div class="request-meta request-type">
                    <span class="request-type-title">${consultation.type_label}</span>
                    ${studentNotes ? `<div class="request-note-preview" title="${escapeHistoryHtml(studentNotes)}"><span class="request-note-label">Note:</span> ${escapeHistoryHtml(studentNotes)}</div>` : ''}
                </div>
                <div class="request-meta request-mode">
                    <span class="request-tag ${consultation.is_face_to_face ? 'face' : ''}">${consultation.consultation_mode}</span>
                </div>
                <div class="request-status-col">
                    <span class="request-status ${statusLower}">${statusDisplay.toUpperCase()}</span>
                </div>
                <div class="request-updated-col">just now</div>
                <div class="request-actions">
                    <!-- Action buttons will be rendered here -->
                </div>
            </div>
        `;

        // Render actions for this row
        const actionWrap = wrapper.querySelector('.request-actions');
        renderRequestActions(actionWrap, consultation.id, statusLower, wrapper);

        return wrapper;
    }

    // Add CSS animations for notifications
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @keyframes slideUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // Start polling immediately and repeat every 3 seconds
    pollConsultationUpdates();
    setInterval(pollConsultationUpdates, 3000);
</script>
@endsection
