@extends('layouts.app')

@section('title')

@section('content')
@php
    $consultations = $consultations ?? collect();
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
    $formatManilaTimeLowerSpaced = function (?string $time): string {
        if (! $time) {
            return '--';
        }
        $value = strlen($time) === 5 ? $time . ':00' : $time;
        return strtolower(\Illuminate\Support\Carbon::createFromFormat('H:i:s', $value, 'Asia/Manila')
            ->setTimezone('Asia/Manila')
            ->format('g:i A'));
    };
    $formatManilaRangeSpaced = function (?string $start, ?string $end) use ($formatManilaTimeLowerSpaced): string {
        if (! $start && ! $end) {
            return '--';
        }
        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');
            return $formatManilaTimeLowerSpaced($start) . ' to ' . $formatManilaTimeLowerSpaced($endValue);
        }
        return $formatManilaTimeLowerSpaced($start) . ' to ' . $formatManilaTimeLowerSpaced($end);
    };
    $formatManilaRangeDash = function (?string $start, ?string $end) use ($formatManilaTime): string {
        if (! $start && ! $end) {
            return '--';
        }
        if (! $end && $start) {
            $startValue = strlen($start) === 5 ? $start . ':00' : $start;
            $endValue = \Illuminate\Support\Carbon::createFromFormat('H:i:s', $startValue, 'Asia/Manila')
                ->copy()
                ->addHour()
                ->format('H:i:s');
            return $formatManilaTime($start) . ' - ' . $formatManilaTime($endValue);
        }
        return $formatManilaTime($start) . ' - ' . $formatManilaTime($end);
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
    $isOnlineMode = function (?string $mode): bool {
        $value = strtolower((string) $mode);
        return str_contains($value, 'audio') || str_contains($value, 'video') || str_contains($value, 'call');
    };
    $isJoinWindow = function (?string $date, ?string $time): bool {
        if (! $date || ! $time) {
            return false;
        }
        $normalizedTime = strlen($time) === 5 ? $time . ':00' : $time;
        try {
            $start = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $normalizedTime, 'Asia/Manila');
        } catch (\Exception $e) {
            return false;
        }
        $now = \Illuminate\Support\Carbon::now('Asia/Manila');
        $windowStart = $start->copy()->subMinutes(10);
        $windowEnd = $start->copy()->addMinutes(30);
        return $now->between($windowStart, $windowEnd);
    };



    $unreadCount = $notifications->where('is_read', false)->count();
    $userName = auth()->user()->name ?? 'Student';
    $userEmail = auth()->user()->email ?? 'student@example.com';
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
    $flashSuccess = session('success');
    $onlineInstructorIds = $onlineInstructorIds ?? [];
    $instructorActiveMinutes = $instructorActiveMinutes ?? [];
    $todayManila = \Illuminate\Support\Carbon::now('Asia/Manila')->toDateString();
    $nowManila = \Illuminate\Support\Carbon::now('Asia/Manila');
    
    // Make flash message available globally
    $successModalDisplay = $flashSuccess ? 'flex' : 'none';
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
    $totalConsultationsCount = $consultations->count();
    $completedSessionsCount = $consultations->filter(function ($consultation) {
        return strtolower((string) ($consultation->status ?? '')) === 'completed';
    })->count();
    $pendingRequestsCount = $consultations->filter(function ($consultation) {
        return strtolower((string) ($consultation->status ?? '')) === 'pending';
    })->count();
    $upcomingTodayCount = $consultations->filter(function ($consultation) use ($todayManila, $isUpcomingConsultation) {
        return (string) ($consultation->consultation_date ?? '') === $todayManila
            && $isUpcomingConsultation($consultation);
    })->count();
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* ==== VARIABLES ==== */
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

* { box-sizing: border-box; }
body { margin: 0; font-family: "Inter", "Segoe UI", Tahoma, sans-serif; background: var(--bg); }

/* utility hidden class */
.is-hidden { display: none !important; }

.online-badge {
    font-size: 12px;
    font-weight: 700;
    color: #059669;
    background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
    border: 1px solid #a7f3d0;
    padding: 6px 12px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.instructor-active-minutes-badge {
    font-size: 12px;
    font-weight: 700;
    color: #b45309;
    background: linear-gradient(135deg, #fef3c7 0%, #fef9e7 100%);
    border: 1px solid #fcd34d;
    padding: 6px 12px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* ==== ANIMATIONS ==== */
@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideInTop {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes popIn {
    0% {
        transform: scale(0.95);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes shimmer {
    0% {
        background-position: -1000px 0;
    }
    100% {
        background-position: 1000px 0;
    }
}

/* ==== LAYOUT ==== */
.dashboard { min-height: 100vh; }

/* ==== SIDEBAR ==== */
.sidebar {
    width: 260px;
    background: linear-gradient(180deg, #1F3A8A 0%, #1e40af 100%);
    box-shadow: 2px 0 14px rgba(31, 58, 138, 0.15);
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
    padding: 13px 22px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 14px;
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
    border-radius: 0 12px 12px 0;
    margin: 4px 0;
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
.sidebar-menu-link:nth-child(4) { animation-delay: 0.5s; }

.sidebar-menu-link:hover,
.sidebar-menu-link.active {
    background: rgba(74, 144, 226, 0.25);
    color: #ffffff;
    border-left-color: #4A90E2;
    font-weight: 600;
    padding-left: 26px;
}

.sidebar-logout {
    padding: 18px 22px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.logout-btn {
    width: 100%;
    border: 2px solid #4A90E2;
    background: transparent;
    color: #4A90E2;
    padding: 11px 14px;
    border-radius: 10px;
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

/* ==== MAIN ==== */
.main {
    margin-left: 260px;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

/* ==== TOPBAR ==== */

.notification-btn {
    background: #dbeafe;
    border: none;
    font-size: 13px;
    cursor: pointer;
    position: relative;
    font-weight: 700;
    color: #1F3A8A;
    padding: 8px 12px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.notification-btn:hover {
    background: #4A90E2;
    color: #ffffff;
}

.notification-badge {
    position: absolute;
    top: -6px;
    right: -8px;
    background: #ef4444;
    color: #fff;
    border-radius: 999px;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 800;
}


.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}


.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
}


.topbar {
    background: linear-gradient(180deg, #f0f9ff, #dbeafe);
    padding: 14px 26px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #bfdbfe;
    position: sticky;
    top: 0;
    z-index: 10;
    backdrop-filter: none;
    box-shadow: 0 6px 18px rgba(31, 58, 138, 0.08);
    animation: slideInTop 0.5s ease-out;
    display: none;
}


.topbar-left {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 700;
}

.menu-btn {
    display: none;
    background: #dbeafe;
    border: 1px solid #bfdbfe;
    padding: 8px 12px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #1F3A8A;
    transition: all 0.3s ease;
}

.menu-btn:hover {
    background: #4A90E2;
    color: #ffffff;
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
    padding: 0 5px;
    font-size: 9px;
    font-weight: 800;
    border: 2px solid #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
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


/* ==== CONTENT ==== */
.content {
    padding: 18px 28px 44px;
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

.dashboard-overview {
    display: grid;
    gap: 18px;
    margin-bottom: 20px;
}

.overview-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 18px;
}

.overview-metric-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    border-top: 4px solid #4A90E2;
    padding: 20px;
    box-shadow: 0 12px 28px rgba(17, 24, 39, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    gap: 16px;
    align-items: center;
}

.overview-metric-card.clickable {
    cursor: pointer;
}

.overview-metric-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 44px rgba(31, 58, 138, 0.18);
}

.overview-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.overview-icon.total { color: #2563eb; background: #dbeafe; }
.overview-icon.completed { color: #059669; background: #d1fae5; }
.overview-icon.pending { color: #7c3aed; background: #ede9fe; }
.overview-icon.upcoming { color: #d97706; background: #ffedd5; }

.overview-value {
    font-size: 28px;
    line-height: 1;
    margin: 0 0 4px;
    font-weight: 800;
    color: #0f172a;
}

.overview-label {
    margin: 0;
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
}

.overview-copy {
    display: flex;
    flex-direction: column;
}

.overview-panels {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
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

.btn {
    background: linear-gradient(135deg, #4A90E2, #2563eb);
    color: #ffffff;
    border: none;
    padding: 11px 18px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(74, 144, 226, 0.3);
}

/* ==== STATS ==== */
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
    border-top: 4px solid;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: popIn 0.5s ease-out backwards;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; border-top-color: #1F3A8A; }
.stat-card:nth-child(2) { animation-delay: 0.15s; border-top-color: #4A90E2; }
.stat-card:nth-child(3) { animation-delay: 0.2s; border-top-color: #2563eb; }
.stat-card:nth-child(4) { animation-delay: 0.25s; border-top-color: #1e40af; }

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 44px rgba(31, 58, 138, 0.18);
}

.stat-card.clickable {
    cursor: pointer;
}

.section.is-hidden {
    display: none;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: #dbeafe;
    display: grid;
    place-items: center;
    color: #1F3A8A;
    font-weight: 800;
    font-size: 18px;
}

.stat-count {
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 4px;
}

/* ==== REQUEST CONSULTATION (INLINE) ==== */
.request-card {
    background: var(--surface);
    border-radius: 18px;
    padding: 22px;
    box-shadow: var(--shadow);
    animation: popIn 0.5s ease-out 0.3s backwards;
}

.request-title {
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 6px;
}

.request-subtitle {
    color: var(--muted);
    font-size: 13px;
    margin-bottom: 18px;
}

.hint {
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
}

.request-section {
    margin-bottom: 18px;
}

.request-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 8px;
}

.request-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
}

.request-card-item {
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 10px 12px;
    display: flex;
    gap: 10px;
    align-items: center;
    cursor: pointer;
    background: #fff;
    transition: all 0.3s ease;
}

.request-card-item input {
    display: none;
}

.request-card-item.selected {
    border-color: #4A90E2;
    background: #f0f9ff;
    box-shadow: 0 8px 18px rgba(74, 144, 226, 0.12);
}

.request-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4A90E2, #2563eb);
    color: #fff;
    display: grid;
    place-items: center;
    font-weight: 800;
}

.request-card-text {
    display: grid;
    gap: 2px;
}

.request-card-name {
    font-weight: 700;
    font-size: 13px;
}

.request-card-meta {
    font-size: 12px;
    color: var(--muted);
}

.request-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.request-form-group label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 6px;
}

.request-form-group input,
.request-form-group select,
.request-form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 14px;
    outline: none;
}

.request-form-group input:focus,
.request-form-group select:focus,
.request-form-group textarea:focus {
    border-color: #4A90E2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.15);
}

.preferred-date-input {
    width: 190px !important;
    max-width: 100%;
    display: inline-block;
    border-radius: 15px;
}

.preferred-date-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
}

.preferred-date-wrap .preferred-date-input {
    padding-right: 34px;
}

.preferred-date-trigger {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    border: none;
    background: transparent;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 0;
}

.preferred-date-trigger:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.request-slot-panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 14px;
}

.request-slot-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.request-slot-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
    gap: 10px;
}

.request-slot-btn {
    padding: 9px 10px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: #faf9ff;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.request-slot-btn.active {
    background: linear-gradient(135deg, #1F3A8A, #1e40af);
    color: #fff;
    border-color: transparent;
}

.consultation-item {
    position: relative;
    display: flex;
    flex-direction: column;
}

.history-row-wrap {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
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

.preferred-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
    margin: 8px 0 6px;
}

.preferred-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.preferred-label {
    font-size: 12px;
    font-weight: 800;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.preferred-days {
    display: inline-flex;
    gap: 8px;
    flex-wrap: wrap;
}

.preferred-day-btn {
    border: 1px solid var(--border);
    background: #ffffff;
    color: #374151;
    padding: 6px 12px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    min-width: 46px;
    text-align: center;
    transition: all 0.2s ease;
}

.preferred-day-btn:hover:not(:disabled) {
    background: #e0f2fe;
    border-color: #06b6d4;
}

.preferred-day-btn.active {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
}

.preferred-day-btn:disabled {
    background: #fee2e2;
    color: #991b1b;
    cursor: not-allowed;
    border-color: #fca5a5;
    opacity: 0.6;
}

.preferred-time {
    border: 1px solid var(--border);
    background: #ffffff;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 700;
    color: #374151;
    min-width: 180px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}


.request-mode-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}

.request-mode-card {
    border: 2px solid var(--border);
    border-radius: 14px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    background: #ffffff;
    transition: all 0.3s ease;
    animation: popIn 0.5s ease-out backwards;
}

.request-mode-card input {
    display: none;
}

.request-mode-card .mode-icon {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #dbeafe;
    color: #4A90E2;
    display: grid;
    place-items: center;
    margin: 0 auto 10px;
    font-size: 18px;
}

.request-mode-card .mode-title {
    font-weight: 800;
    font-size: 13px;
    margin-bottom: 4px;
    color: #1F3A8A;
}

.request-mode-card .mode-desc {
    font-size: 12px;
    color: var(--muted);
}

.request-mode-card.selected,
.request-mode-card input:checked + .mode-body {
    border-color: #4A90E2;
    background: #f0f9ff;
    box-shadow: 0 10px 24px rgba(74, 144, 226, 0.2);
}

.request-mode-card.selected .mode-icon {
    background: #bfdbfe;
}

.request-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 18px;
}

.request-actions .btn {
    border-radius: 10px;
}

#request-consultation .request-card {
    padding: 16px;
}

#request-consultation .request-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.85fr) minmax(280px, 0.95fr);
    gap: 18px;
    align-items: start;
}

#request-consultation .request-main-pane {
    display: grid;
    gap: 14px;
}

#request-consultation .request-main-pane .request-section {
    margin-bottom: 0;
    background: #fff;
    border: 1px solid rgba(96, 165, 250, 0.28);
    border-radius: 14px;
    padding: 14px;
    box-shadow: 0 0 0 1px rgba(103, 232, 249, 0.08);
}

#request-consultation .request-label {
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.08em;
    color: #1F3A8A;
}

#request-consultation .request-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
}

#request-consultation .request-card-item {
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-height: 124px;
    justify-content: center;
    border-color: rgba(96, 165, 250, 0.28);
    background: linear-gradient(180deg, rgba(239, 246, 255, 0.7) 0%, #ffffff 100%);
    box-shadow: 0 0 0 1px rgba(103, 232, 249, 0.05);
}

#request-consultation .request-card-text {
    place-items: center;
}

#request-consultation .request-card-meta {
    font-size: 11px;
}

#request-consultation .request-mode-grid {
    margin-top: 12px !important;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

#request-consultation .request-mode-card {
    border: 2px solid rgba(96, 165, 250, 0.28);
    border-radius: 14px;
}

#request-consultation .request-form-group input,
#request-consultation .request-form-group select,
#request-consultation .request-form-group textarea,
#request-consultation .preferred-time,
#request-consultation .preferred-day-btn,
#request-consultation .request-slot-panel,
#request-consultation .request-slot-btn {
    border-color: rgba(96, 165, 250, 0.28);
}

#request-consultation .request-form-group input:focus,
#request-consultation .request-form-group select:focus,
#request-consultation .request-form-group textarea:focus {
    border-color: rgba(103, 232, 249, 0.62);
    box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.14);
}

#request-consultation .request-form-grid {
    grid-template-columns: 1fr 1fr;
}

#request-consultation .request-summary-pane {
    position: sticky;
    top: 16px;
}

#request-consultation .request-summary-card {
    background: #f8fafc;
    border: 1px solid rgba(96, 165, 250, 0.28);
    border-radius: 14px;
    padding: 14px;
    box-shadow: 0 0 0 1px rgba(103, 232, 249, 0.08), 0 12px 30px rgba(31, 58, 138, 0.08);
}

#request-consultation .request-summary-title {
    font-size: 22px;
    font-weight: 900;
    color: #0f172a;
    margin-bottom: 2px;
}

#request-consultation .request-summary-subtitle {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 12px;
}

#request-consultation .request-summary-lines {
    display: grid;
    gap: 8px;
}

#request-consultation .request-summary-lines .meta {
    border: 1px solid rgba(96, 165, 250, 0.28);
    background: #fff;
    border-radius: 10px;
    padding: 9px 10px;
    font-size: 13px;
    color: #334155;
    font-weight: 700;
}

#request-consultation .request-actions-sticky {
    margin-top: 14px;
    flex-direction: column;
}

#request-consultation .request-actions-sticky .btn {
    width: 100%;
    justify-content: center;
}

@media (max-width: 720px) {
    #request-consultation .request-layout {
        grid-template-columns: 1fr;
    }

    #request-consultation .request-grid {
        grid-template-columns: 1fr;
    }

    #request-consultation .request-form-grid {
        grid-template-columns: 1fr;
    }

    #request-consultation .request-summary-pane {
        position: static;
    }

    .request-form-grid {
        grid-template-columns: 1fr;
    }
}



/* ==== SECTIONS ==== */
.section {
    background: var(--surface);
    border-radius: 18px;
    padding: 24px;
    box-shadow: var(--shadow);
    margin-bottom: 28px;
    animation: popIn 0.5s ease-out backwards;
}

.section:nth-of-type(1) { animation-delay: 0.2s; }
.section:nth-of-type(2) { animation-delay: 0.3s; }
.section:nth-of-type(3) { animation-delay: 0.4s; }

.section-title {
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 18px;
}

/* ==== CONSULTATION LIST ==== */
.consultation-list {
    display: grid;
    gap: 18px;
    align-items: stretch;
}

.consultation-card {
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 24px;
    display: grid;
    grid-template-columns: 1.5fr 1.2fr 1.2fr 1.2fr auto;
    gap: 24px;
    align-items: start;
    transition: all 0.3s ease;
    background: #ffffff;
    animation: popIn 0.5s ease-out backwards;
    flex: 1;
    box-shadow: 0 2px 8px rgba(31, 58, 138, 0.08);
    position: relative;
    overflow: hidden;
    border-left: 5px solid transparent;
}

.consultation-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(31, 58, 138, 0.02) 0%, transparent 100%);
    pointer-events: none;
    z-index: 0;
}

.consultation-card > * {
    position: relative;
    z-index: 1;
}

.consultation-card.status-pending {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fff 100%);
}

.consultation-card.status-approved {
    border-left-color: #10b981;
    background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%);
}

.consultation-card.status-completed {
    border-left-color: #6b7280;
    background: linear-gradient(135deg, #f9fafb 0%, #fff 100%);

}

.consultation-card.status-in_progress {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #fff 100%);
}

.consultation-card.status-cancelled {
    border-left-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #fff 100%);
}

.consultation-card.status-incompleted {
    border-left-color: #d97706;
    background: linear-gradient(135deg, #fffbeb 0%, #fff 100%);
}

.consultation-card.status-declined {
    border-left-color: #dc2626;
    background: linear-gradient(135deg, #fef2f2 0%, #fff 100%);
}

.consultation-card:hover {
    border-color: rgba(74, 144, 226, 0.6);
    box-shadow: 0 12px 32px rgba(31, 58, 138, 0.15);
    transform: translateY(-4px);
}

.status {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    margin-top: 2px;
    width: max-content;
    letter-spacing: 0.3px;
}

.status-approved {
    background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.status-completed {
    background: linear-gradient(135deg, #f3f4f6 0%, #f9fafb 100%);
    color: #374151;
    border: 1px solid #e5e7eb;
}

.status-pending {
    background: linear-gradient(135deg, #fef3c7 0%, #fef9e7 100%);
    color: #92400e;
    border: 1px solid #fcd34d;
}

.status-cancelled {
    background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.status-incompleted {
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    color: #92400e;
    border: 1px solid #fcd34d;
}

.status-declined {
    background: linear-gradient(135deg, #ffedd5 0%, #fff7ed 100%);
    color: #7c2d12;
    border: 1px solid #fdba74;
}

.status-in_progress {
    background: linear-gradient(135deg, #bfdbfe 0%, #dbeafe 100%);
    color: #1e40af;
    border: 1px solid #7dd3fc;
}

.meta {
    color: var(--muted);
    font-size: 13px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.action-btn {
    background: linear-gradient(135deg, #1F3A8A, #1e40af);
    color: #fff;
    border: none;
    padding: 11px 18px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(31, 58, 138, 0.25);
    vertical-align: middle;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(31, 58, 138, 0.35);
}

.action-btn.secondary {
    background: #ffffff;
    color: #1F3A8A;
    border: 2px solid #1F3A8A;
    box-shadow: none;
}

.action-btn.secondary:hover {
    background: #f0f4f9;
    box-shadow: 0 4px 12px rgba(31, 58, 138, 0.15);
}

.action-btn.disabled,
.action-btn:disabled {
    background: #e5e7eb;
    color: #6b7280;
    border: none;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}

.awaiting {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 12px;
    color: #6b7280;
}

.spinner {
    width: 14px;
    height: 14px;
    border: 2px solid #d1d5db;
    border-top-color: #1F3A8A;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ==== FEATURES ==== */
.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
}

.feature-card {
    background: linear-gradient(180deg, #ffffff, #f0f9ff);
    border-radius: 18px;
    padding: 20px;
    box-shadow: var(--shadow);
    text-align: left;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: popIn 0.5s ease-out backwards;
}

.feature-card:nth-child(1) { animation-delay: 0.25s; }
.feature-card:nth-child(2) { animation-delay: 0.3s; }
.feature-card:nth-child(3) { animation-delay: 0.35s; }

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 18px 40px rgba(31, 58, 138, 0.18);
}

.feature-card h3 {
    margin: 0 0 6px;
    font-size: 16px;
    font-weight: 800;
    color: #1F3A8A;
}

.feature-card p {
    margin: 0;
    font-size: 13px;
    color: var(--muted);
}

/* ==== NOTIFICATIONS ==== */
.notification-panel {
    position: absolute;
    top: 52px;
    right: 0;
    width: 360px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(31, 58, 138, 0.25);
    border: 1px solid var(--border);
    display: none;
    flex-direction: column;
    max-height: 440px;
    overflow: hidden;
    z-index: 30;
    opacity: 1;
    animation: slideInTop 0.3s ease-out;
}

.notification-panel.active { display: flex; }

.notification-header {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    font-weight: 700;
    color: #1F3A8A;
}

.notification-list {
    list-style: none;
    margin: 0;
    padding: 0;
    overflow-y: auto;
    max-height: 320px;
}

.notification-item {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f1f1;
    font-size: 13px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
    background: #ffffff;
    transition: background-color 0.3s ease;
}

.notification-item.unread { background: #f0f9ff; }

.notification-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #4A90E2;
    margin-top: 6px;
    flex-shrink: 0;
}

.notification-actions {
    margin-left: auto;
    display: flex;
    gap: 8px;
}

.notification-actions button {
    background: none;
    border: none;
    color: var(--muted);
    cursor: pointer;
    font-size: 12px;
}
/* ==== MODAL & OVERLAY ==== */
.overlay {
    position: fixed;
    inset: 0;
    background: transparent;
    display: none;
    z-index: 15;
}

.overlay.active { display: block; }

.modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(17, 24, 39, 0.55);
    z-index: 50;
}

.modal.active { display: flex; }

.modal-card {
    background: var(--surface);
    border-radius: 18px;
    padding: 22px;
    width: min(540px, 92vw);
    box-shadow: 0 24px 48px rgba(31, 58, 138, 0.25);
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 14px;
}

/* ==== HISTORY MODAL ==== */
.history-modal {
    position: fixed;
    inset: 0;
    z-index: 90;
    background: rgba(15, 23, 42, 0.55);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.history-modal.open { display: flex; }

.history-dialog {
    width: 100%;
    max-width: 980px;
    border-radius: 18px;
    background: var(--surface);
    border: 1px solid var(--border);
    box-shadow: 0 32px 80px rgba(31, 58, 138, 0.25);
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

/* Success modal */
.success-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.35);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 60;
}
.success-modal {
    width: min(520px, 92vw);
    background: #fff;
    border-radius: 12px;
    padding: 28px;
    box-shadow: 0 30px 60px rgba(2,6,23,0.2);
    text-align: left;
}
.success-modal h3 { margin: 0 0 6px; color: var(--brand); font-size:20px; }
.success-modal p { color: var(--muted); margin: 0 0 18px; }
.success-modal .done-btn { display:inline-block; background:var(--brand); color:#fff; padding:10px 18px; border-radius:8px; border:none; cursor:pointer; font-weight:700; }

.history-modal-body {
    padding: 24px 26px;
    overflow-y: auto;
    flex: 1 1 auto;
    background: #f3f4f6;
}

#history.section {
    padding: 0;
    overflow: hidden;
    margin-top: 0;
}

#history .history-modal-body {
    padding: 24px 26px;
    overflow: visible;
    background: #f3f4f6;
}

.content.history-only > :not(#history) {
    display: none !important;
}

.history-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 18px;
}

.history-header .history-controls {
    flex: 1;
    display: flex;
    gap: 18px;
    align-items: center;
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
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

.year-nav-btn {
    border: 1px solid var(--border);
    background: #fff;
    color: var(--text);
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    min-width: 36px;
}
.year-nav-btn:hover {
    background: var(--brand);
    color: #fff;
    border-color: var(--brand);
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

/* Availability-like filter styles (semester toggle + academic year) */
.semester-toggle {
    display: inline-flex;
    gap: 8px;
    background: #f4f3ff;
    border: 1px solid #ddd6fe;
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
.availability-filter-group label { font-size: 12px; font-weight: 700; color: #6b7280; }
.availability-filter-group select { border: 1px solid #d1d5db; border-radius: 10px; padding: 8px 10px; font-size: 12px; background: #ffffff; }

.history-table {
    --history-columns: minmax(0, 1.15fr) minmax(0, 1.15fr) minmax(0, 0.95fr) minmax(0, 0.8fr) minmax(0, 0.6fr) minmax(0, 0.9fr) minmax(0, 0.8fr);
    background: #f3f4f6;
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: var(--shadow);
    overflow: hidden;
    overflow-y: hidden;
    display: block;
}

.history-row {
    display: grid;
    grid-template-columns: var(--history-columns);
    gap: 10px;
    align-items: center;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
    font-size: 12px;
    min-width: 0;
}

.history-row > div {
    min-width: 0;
    justify-self: start;
    text-align: left;
}

.history-row.header {
    background: #f8fafc;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.4px;
    color: var(--muted);
}

.history-row:last-child { border-bottom: none; }

#history .history-row-wrap .history-row {
    border-bottom: 1px solid var(--border);
}

#history .history-row-wrap:last-child .history-row {
    border-bottom: none;
}

.history-instructor-cell {
    display: grid;
    grid-template-columns: auto 1fr;
    align-items: start;
    gap: 10px;
    min-width: 0;
}

.history-instructor-name {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    overflow-wrap: anywhere;
}

.history-instructor-meta {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.history-instructor-topline {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    min-width: 0;
}

.history-instructor-cell .cc-avatar {
    display: none;
}

.history-mobile-datetime {
    display: none;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    line-height: 1.35;
}

.history-action-cell {
    display: flex;
    align-items: center;
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
    padding: 5px 8px;
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

.history-action-cell .view-link {
    padding: 7px 10px;
    font-size: 11px;
    white-space: nowrap;
}

@media (max-width: 1280px) {
    .history-table {
        --history-columns: minmax(0, 1.1fr) minmax(0, 1.05fr) minmax(0, 0.9fr) minmax(0, 0.78fr) minmax(0, 0.58fr) minmax(0, 0.82fr) minmax(0, 0.72fr);
    }

    .history-row {
        gap: 8px;
        padding: 12px 14px;
        font-size: 11px;
    }

    .history-row.header {
        font-size: 10px;
    }

    .date-time span:first-child,
    .history-instructor-name {
        font-size: 12px;
    }

    .date-time span:last-child,
    .record-pill,
    .history-action-cell .view-link {
        font-size: 10px;
    }
}

@media (max-width: 1100px) {
    .history-table {
        --history-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 0.85fr) minmax(0, 0.72fr) minmax(0, 0.52fr) minmax(0, 0.75fr) minmax(0, 0.68fr);
    }

    .history-row {
        gap: 7px;
        padding: 11px 12px;
    }

    .record-pill {
        padding: 4px 7px;
        letter-spacing: 0.02em;
        margin-right: 4px;
    }

    .history-action-cell .view-link {
        padding: 6px 9px;
    }
}

.view-link {
    color: var(--brand);
    font-weight: 700;
    text-decoration: none;
    font-size: 13px;
}

.empty-state {
    padding: 30px 18px;
    text-align: center;
    color: var(--muted);
    font-size: 14px;
}

/* ==== DETAILS MODAL ==== */
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
    .call-videos { grid-template-columns: 1fr; }
}

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

.details-actions-content {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.details-actions-content .cc-btn,
.details-actions-content button {
    min-height: 38px;
}

.details-actions-content form {
    margin: 0;
}

.cc-mobile-details {
    display: none;
}

.cc-mobile-details-btn {
    border: 1px solid #c9d7f0;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    color: #284a9d;
    border-radius: 999px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    white-space: nowrap;
}

.cc-mobile-details-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 18px rgba(47, 78, 178, 0.16);
    border-color: #8fa8ff;
}

.cc-mobile-meta {
    display: none;
}

.cc-instructor-label {
    display: none;
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

/* ==== RESPONSIVE ==== */
@media (max-width: 1400px) {
    .consultation-card { grid-template-columns: 1.2fr 1fr 1fr auto; gap: 20px; }
}

@media (max-width: 1200px) {
    .consultation-card { grid-template-columns: 1.2fr 1fr 1.1fr auto; gap: 18px; }
    .overview-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .overview-panel-title { font-size: 24px; }
    .recent-item-title,
    .schedule-title { font-size: 16px; }
}

@media (max-width: 1024px) {
    .consultation-card { grid-template-columns: 1.3fr 1.2fr 1fr auto; gap: 16px; }
}

@media (max-width: 900px) {
    .sidebar { width: 220px; }
    .main { margin-left: 220px; }
    .history-row {
        grid-template-columns: var(--history-columns);
        gap: 12px;
        align-items: center;
    }
    .overview-panels { grid-template-columns: 1fr; }
    .history-row.header { display: grid; }
    .consultation-card { grid-template-columns: 1.5fr 1fr 1fr auto; gap: 14px; padding: 18px; }
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.25s ease;
    }
    .sidebar.open { transform: translateX(0); }
    .main { margin-left: 0; }
    .menu-btn { display: inline-flex; }
    .overview-metrics { grid-template-columns: 1fr; }
    .consultation-card { grid-template-columns: 1fr auto; gap: 16px; padding: 16px; }
    .consultation-card > div:nth-child(2),
    .consultation-card > div:nth-child(3),
    .consultation-card > div:nth-child(4) {
        display: none;
    }
    .content-header {
        display: grid;
        grid-template-columns: auto 1fr auto;
        grid-template-areas:
            "menu spacer actions"
            "copy copy copy";
        align-items: start;
        gap: 12px;
    }
    .menu-btn {
        grid-area: menu;
        display: inline-flex;
        justify-self: start;
    }
    .dashboard-header-copy {
        grid-area: copy;
        width: 100%;
    }
    .topbar-actions {
        grid-area: actions;
        width: auto;
        justify-content: flex-end;
        align-self: start;
        justify-self: end;
        flex-wrap: nowrap;
        min-width: max-content;
    }
}

@media (max-width: 520px) {
    .content {
        padding: 14px 12px 32px;
    }

    .content-header {
        grid-template-areas:
            "menu spacer actions"
            "copy copy copy";
        padding: 12px 14px;
        border-radius: 12px;
    }
    .overview-panel {
        padding: 14px;
    }
    .overview-value {
        font-size: 32px;
    }
    .overview-panel-title {
        font-size: 21px;
    }
    .content { padding: 16px 16px 36px; }
    .dashboard-header-title {
        font-size: 22px;
    }
    .dashboard-header-subtitle {
        font-size: 12px;
    }
    .menu-btn {
        padding: 7px 10px;
        font-size: 12px;
    }
    .menu-btn span {
        display: none;
    }
    .notification-btn,
    .header-profile-trigger,
    .student-cyber-theme .header-profile-trigger {
        width: 40px;
        height: 40px;
    }
    .topbar-actions {
        width: auto;
        justify-content: flex-end;
        gap: 8px;
        min-width: max-content;
    }
    .notification-panel {
        width: min(94vw, 360px);
        right: -12px;
    }
}

/* History header responsive tweaks */
@media (max-width: 720px) {
    .history-header { flex-direction: column; align-items: stretch; gap: 12px; }
    .history-right .export-btn { align-self: flex-end; }
}

.history-controls { display:flex; align-items:center; gap:18px; }
.history-controls .semester-toggle { background:#f0f9ff; border:1px solid #bfdbfe; padding:6px; border-radius:12px; }
.history-controls .semester-btn { border:1px solid transparent; background:transparent; color:#4b5563; padding:8px 12px; border-radius:10px; font-size:12px; font-weight:700; cursor:pointer; transition: all 0.3s ease; }
.history-controls .semester-btn.active { background:#fff; color:var(--brand); border-color:#d1d5db; box-shadow:0 6px 14px rgba(31,58,138,0.1); }
.history-controls .availability-filter-group select { min-width:160px; }
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
    background: #fff;
    color: var(--text);
    font-weight: 600;
}
.history-inline-filter input {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 13px;
    background: #fff;
    color: var(--text);
    font-weight: 600;
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
    grid-template-columns: repeat(5, minmax(120px, 1fr));
    gap: 12px;
    width: 100%;
}

#history .history-search-filter {
    grid-column: span 2;
}

#history .history-search-actions {
    display: flex;
    align-items: stretch;
    gap: 10px;
    width: 100%;
}

#history .history-search-actions input {
    flex: 1 1 auto;
    min-width: 0;
}

#history .history-search-actions .export-btn {
    min-height: 42px;
    white-space: nowrap;
    flex: 0 0 auto;
}

@media (max-width:720px) {
    #history .history-header {
        grid-template-columns: 1fr;
        align-items: stretch;
    }
    #history .history-filter-row-top {
        grid-template-columns: 1fr;
    }
    #history .history-inline-filters { grid-template-columns: 1fr; }
    #history .history-inline-filter { min-width: 100%; }
    #history .history-search-filter {
        grid-column: auto;
    }
    #history .history-search-actions .export-btn { align-self: flex-start; }
}
</style>
<style>
/* Student dashboard cyber theme (reference-matched) */
.student-cyber-theme {
    background:
        radial-gradient(circle at 16% 22%, rgba(0, 186, 255, 0.12), transparent 36%),
        radial-gradient(circle at 86% 8%, rgba(30, 64, 175, 0.16), transparent 34%),
        linear-gradient(180deg, #f3fbff 0%, #eef6ff 100%);
}

.student-cyber-theme .sidebar {
    background:
        linear-gradient(180deg, rgba(6, 19, 64, 0.72) 0%, rgba(9, 35, 104, 0.72) 100%),
        url('{{ asset('sidebar.JPG') }}') center/cover no-repeat;
    border: 1px solid rgba(94, 217, 255, 0.45);
    box-shadow: 0 0 0 1px rgba(103, 232, 249, 0.2), 0 0 24px rgba(8, 145, 178, 0.4);
}

.student-cyber-theme .sidebar::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        radial-gradient(circle at 14% 10%, rgba(0, 247, 255, 0.14), transparent 35%),
        linear-gradient(130deg, transparent 0 35%, rgba(70, 207, 255, 0.09) 35% 36%, transparent 36% 100%);
}

.student-cyber-theme .sidebar-menu-link {
    border: 1px solid rgba(96, 165, 250, 0.28);
    background: rgba(21, 46, 122, 0.7);
    border-radius: 12px;
    margin: 8px 14px;
    color: #e2edff;
    min-height: 46px;
}

.student-cyber-theme .sidebar-menu-link:hover,
.student-cyber-theme .sidebar-menu-link.active {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.65), rgba(20, 184, 166, 0.45));
    border-color: rgba(103, 232, 249, 0.62);
    box-shadow: 0 0 20px rgba(56, 189, 248, 0.3);
}

.student-cyber-theme .sidebar.icon-only .sidebar-menu-link {
    margin: 8px auto;
}

.student-cyber-theme .logout-btn {
    background: rgba(14, 34, 96, 0.9);
    border: 1px solid rgba(125, 211, 252, 0.5);
    color: #dbeafe;
}

.student-cyber-theme .content-header {
    position: relative;
    overflow: visible;
    background: url('{{ asset('head1.JPG') }}') center/cover no-repeat;
    border: 1px solid rgba(59, 130, 246, 0.34);
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.22);
}

.student-cyber-theme .content-header::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(31, 58, 138, 0.34) 0%, rgba(30, 64, 175, 0.3) 100%);
    pointer-events: none;
    z-index: 0;
}

.student-cyber-theme .content-header::after {
    content: none;
}

.student-cyber-theme .dashboard-header-copy,
.student-cyber-theme .topbar-actions {
    position: relative;
    z-index: 2;
}

.student-cyber-theme .dashboard-header-title {
    color: #ffffff;
    text-shadow: 0 2px 10px rgba(15, 23, 42, 0.45);
    letter-spacing: 0;
}

.student-cyber-theme .dashboard-header-subtitle {
    color: #e2e8f0;
}

.student-cyber-theme .notification-btn {
    border-color: rgba(125, 211, 252, 0.7);
    background: rgba(20, 58, 138, 0.45);
    color: #ffffff;
}

.student-cyber-theme .header-profile-trigger {
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

.student-cyber-theme .header-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.student-cyber-theme .overview-metric-card {
    position: relative;
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-top: 4px solid #4A90E2;
    color: #111827;
    box-shadow: 0 12px 28px rgba(17, 24, 39, 0.08);
}

.student-cyber-theme .overview-metric-card::before {
    content: none;
}

.student-cyber-theme .overview-icon {
    border: 1px solid transparent;
}

.student-cyber-theme .overview-icon.total {
    background: #dbeafe !important;
    color: #2563eb !important;
}

.student-cyber-theme .overview-icon.completed {
    background: #d1fae5 !important;
    color: #059669 !important;
}

.student-cyber-theme .overview-icon.pending {
    background: #ede9fe !important;
    color: #7c3aed !important;
}

.student-cyber-theme .overview-icon.upcoming {
    background: #ffedd5 !important;
    color: #d97706 !important;
}

.student-cyber-theme .overview-value,
.student-cyber-theme .overview-label {
    color: #111827;
    position: relative;
    z-index: 1;
}

.student-cyber-theme .overview-label {
    color: #64748b;
}

.student-cyber-theme .overview-panel {
    background: rgba(245, 251, 255, 0.92);
    border: 1px solid rgba(56, 189, 248, 0.35);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.1);
}

.student-cyber-theme .recent-item,
.student-cyber-theme .schedule-item {
    background:
        linear-gradient(145deg, #0f2e7a 0%, #173f94 55%, #0b2662 100%),
        repeating-linear-gradient(165deg, rgba(56, 189, 248, 0.16) 0, rgba(56, 189, 248, 0.16) 1px, transparent 1px, transparent 16px);
    border: 1px solid rgba(56, 189, 248, 0.65);
    box-shadow: 0 0 0 1px rgba(186, 230, 253, 0.16), 0 10px 20px rgba(15, 23, 42, 0.25);
}

.student-cyber-theme .recent-item-title,
.student-cyber-theme .schedule-title {
    color: #f8fdff;
}

.student-cyber-theme .recent-item-meta,
.student-cyber-theme .schedule-time {
    color: #d7f4ff;
}

.student-cyber-theme .schedule-date-chip {
    border: 1px solid rgba(125, 211, 252, 0.6);
    box-shadow: inset 0 0 12px rgba(186, 230, 253, 0.5);
}

@media (max-width: 1024px) {
    .student-cyber-theme .overview-metrics {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>

<style>
@media (max-width: 768px) {
    body,
    .dashboard,
    .main,
    .content {
        overflow-x: hidden;
    }

    .sidebar {
        width: min(84vw, 300px);
    }

    .content {
        padding: 14px 14px 28px;
    }

    .section,
    #my-consultations,
    #history {
        padding: 16px;
        border-radius: 14px;
    }

    .content-header {
        grid-template-columns: auto 1fr auto;
        grid-template-areas:
            "menu spacer actions"
            "copy copy copy";
        align-items: start;
        gap: 12px;
        padding: 16px;
    }

    .menu-btn {
        display: inline-flex;
        align-self: start;
        gap: 8px;
    }

    .topbar-actions {
        width: auto;
        justify-content: flex-end;
        flex-wrap: nowrap;
        gap: 10px;
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

    .notification-item .notification-actions {
        margin-left: auto;
        align-self: flex-start;
    }

    .notification-item .dismiss-btn {
        font-size: 11px;
        white-space: nowrap;
    }

    #request-consultation .request-card,
    #request-consultation .request-main-pane .request-section,
    #request-consultation .request-summary-card {
        padding: 12px;
    }

    #request-consultation .request-card-item {
        min-height: unset;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        text-align: left;
    }

    #request-consultation .request-card-text {
        place-items: start;
    }

    #request-consultation .request-mode-grid,
    #request-consultation .request-form-grid,
    .request-mode-grid,
    .request-form-grid {
        grid-template-columns: 1fr;
    }

    .preferred-row {
        flex-direction: column;
        align-items: stretch;
    }

    .preferred-group,
    .preferred-date-wrap,
    .preferred-date-input,
    .preferred-time {
        width: 100% !important;
        min-width: 0;
    }

    .request-actions,
    #request-consultation .request-actions-sticky {
        flex-direction: column;
    }

    .request-actions .btn,
    #request-consultation .request-actions-sticky .btn {
        width: 100%;
        justify-content: center;
    }

    .myc-table-wrap {
        overflow: visible;
        background: transparent;
        border: none;
        padding: 0;
        box-shadow: none;
    }

    .myc-table-head {
        display: none;
    }

    .consultation-list {
        gap: 12px;
    }

    .consultation-card {
        grid-template-columns: 1fr !important;
        gap: 12px;
        padding: 14px;
    }

    .consultation-card > div:nth-child(2),
    .consultation-card > div:nth-child(3),
    .consultation-card > div:nth-child(4) {
        display: flex !important;
    }

    .cc-col {
        padding: 0;
        border-right: none !important;
        width: 100%;
        align-items: flex-start;
    }

    .cc-col-instructor {
        min-width: 0;
        max-width: none;
    }

    .cc-instructor-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .cc-col-action,
    .cc-col-action form {
        width: 100%;
    }

    .cc-col-action .cc-btn,
    .cc-col-action button {
        width: 100%;
        justify-content: center;
    }

    .history-header,
    #history .history-header {
        flex-direction: column;
        align-items: stretch;
        gap: 14px;
    }

    .history-inline-filters,
    #history .history-inline-filters {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    #history .history-filter-row-top {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: stretch;
    }

    #history .history-filter-row-top .semester-toggle {
        grid-column: 1 / 2;
        justify-self: start;
    }

    #history .history-month-group {
        grid-column: 1 / 2;
    }

    #history .history-year-group {
        grid-column: 2 / 3;
    }

    #history .history-search-filter {
        grid-column: 1 / -1;
    }

    #history .history-month-group,
    #history .history-year-group,
    #history .history-inline-filter {
        min-width: 0;
    }

    #history .history-year-group input,
    #history .history-month-group select,
    #history .history-inline-filter select,
    #history .history-inline-filter input {
        min-width: 0;
    }

    .history-row.header {
        display: none !important;
    }

    .history-row,
    .history-row.history-row-item {
        grid-template-columns: minmax(0, 1fr) auto auto !important;
        gap: 12px;
        padding: 14px;
        border: 1px solid #dfe7f4;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        min-width: 0;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
    }

    .history-row.history-row-item:hover {
        transform: translateY(-4px);
        border-color: rgba(74, 144, 226, 0.6);
        background: #f8fbff;
        box-shadow: 0 12px 32px rgba(31, 58, 138, 0.15);
    }

    .history-row.history-row-item > :not(:nth-child(2)):not(:nth-child(4)):not(:nth-child(7)) {
        display: none !important;
    }

    .history-row.history-row-item > div:nth-child(2),
    .history-row.history-row-item > div:nth-child(4),
    .history-row.history-row-item > div:nth-child(7) {
        min-width: 0;
    }

    .history-instructor-cell {
        gap: 10px;
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: start;
    }

    .history-instructor-meta {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .history-instructor-topline {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        min-width: 0;
    }

    .history-instructor-cell .cc-avatar {
        display: inline-flex;
    }

    .history-instructor-name {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        flex: 1 1 auto;
        min-width: 0;
    }

    .history-mobile-datetime {
        display: none;
    }

    .history-mode-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        align-self: center;
    }

    .history-mode-cell .badge {
        white-space: nowrap;
        font-size: 11px;
        padding: 6px 10px;
    }

    .history-action-cell {
        justify-content: flex-end;
        align-self: center;
    }

    .history-action-cell .view-link {
        border: 1px solid #c9d7f0;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        color: #284a9d;
        padding: 8px 12px;
        font-size: 11px;
        font-weight: 800;
        border-radius: 999px;
        white-space: nowrap;
        box-shadow: none;
    }

    .history-action-cell .view-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(47, 78, 178, 0.16);
        border-color: #8fa8ff;
    }

    #requestInstructorPaginationContainer,
    #consultationPaginationContainer,
    #historyPaginationContainer {
        flex-direction: column;
        align-items: stretch !important;
    }

    #requestInstructorPaginationControls,
    #consultationPaginationControls,
    #historyPaginationControls {
        justify-content: center;
        flex-wrap: wrap;
    }

    .incoming-call-modal {
        width: min(92vw, 340px) !important;
        padding: 22px 18px !important;
    }

    .call-dialog {
        width: min(96vw, 560px);
        margin: 12px;
    }

    .call-body {
        padding: 12px;
    }

    .call-video {
        min-height: 180px;
    }
}

@media (max-width: 480px) {
    .menu-btn {
        width: auto;
        justify-content: center;
        padding: 8px 10px;
    }

    .dashboard-header-title {
        font-size: 20px;
    }

    .dashboard-header-subtitle {
        font-size: 11px;
    }

    .content-header {
        grid-template-columns: auto 1fr auto;
        grid-template-areas:
            "menu spacer actions"
            "copy copy copy";
        padding: 12px;
        gap: 10px;
    }

    .topbar-actions {
        gap: 6px;
    }

    .overview-panel,
    .request-summary-card {
        padding: 12px;
    }

    .overview-value {
        font-size: 28px;
    }

    .request-avatar,
    .cc-avatar {
        width: 40px;
        height: 40px;
    }

    .call-actions {
        grid-template-columns: 1fr;
    }

    .call-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="dashboard student-cyber-theme">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('student.dashboard') }}" class="sidebar-logo">
            <span class="logo-badge">
                <img src="{{ asset('cslogo.jpg') }}" alt="CS Logo" class="logo-img">
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
                <a href="{{ route('student.dashboard') }}" class="sidebar-menu-link" id="dashboardLink"><i class="fa-solid fa-house"></i>Dashboard</a>
            </li>
            <li>
                <a href="#request-consultation" class="sidebar-menu-link" id="requestConsultationLink"><i class="fa-solid fa-clipboard-list"></i>Request Consultation</a>
            </li>
            <li>
                <a href="#my-consultations" class="sidebar-menu-link" id="myConsultationsLink"><i class="fa-solid fa-calendar-check"></i>My Consultations</a>
            </li>
            <li>
                <a href="#history" class="sidebar-menu-link" id="historyLink"><i class="fa-solid fa-clock-rotate-left"></i>History</a>
            </li>
            <!-- Feedback link removed -->
        </ul>

        <div class="sidebar-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main">
        <!-- CONTENT -->
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
                                    <button id="markAllReadBtn" type="submit" style="background:none;border:none;color:var(--brand);font-weight:700;cursor:pointer">Mark all read</button>
                                </form>
                            </div>
                            <ul class="notification-list" id="notificationList">
                                @forelse ($notifications as $notification)
                                    <li class="notification-item {{ $notification->is_read ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                                        <span class="notification-dot"></span>
                                        <div>
                                            <div style="font-weight:700">{{ $notification->title }}</div>
                                            <div style="color:var(--muted);margin-top:4px">{{ $notification->message }}</div>
                                            <div style="color:#9ca3af;font-size:11px;margin-top:6px">{{ $notification->created_at?->diffForHumans() }}</div>
                                        </div>
                                        <div class="notification-actions">
                                            <button type="button" class="dismiss-btn">Dismiss</button>
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
                            <div style="padding:12px 16px;border-top:1px solid var(--border);text-align:center;">
                                <a href="{{ route('notifications.index') }}" style="color:var(--brand);font-weight:700;text-decoration:none;font-size:13px;">View all</a>
                            </div>
                        </div>
                    </div>

                    <div class="profile" style="position: relative;">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="header-profile-trigger" type="button" title="{{ $userName }}" aria-label="Open profile menu">
                                    <span class="header-avatar">{{ $userInitial }}</span>
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

            <section class="dashboard-overview">
                <div class="overview-metrics">
                    <article class="overview-metric-card clickable" id="totalConsultationsCard" title="View consultation history">
                        <span class="overview-icon total"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i></span>
                        <div class="overview-copy">
                            <h3 class="overview-value" id="studentOverviewTotal">{{ $totalConsultationsCount }}</h3>
                            <p class="overview-label">Total Consultations</p>
                        </div>
                    </article>
                    <article class="overview-metric-card">
                        <span class="overview-icon completed"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></span>
                        <div class="overview-copy">
                            <h3 class="overview-value" id="studentOverviewCompleted">{{ $completedSessionsCount }}</h3>
                            <p class="overview-label">Completed Sessions</p>
                        </div>
                    </article>
                    <article class="overview-metric-card">
                        <span class="overview-icon pending"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i></span>
                        <div class="overview-copy">
                            <h3 class="overview-value" id="studentOverviewPending">{{ $pendingRequestsCount }}</h3>
                            <p class="overview-label">Pending Requests</p>
                        </div>
                    </article>
                    <article class="overview-metric-card">
                        <span class="overview-icon upcoming"><i class="fa-solid fa-calendar-day" aria-hidden="true"></i></span>
                        <div class="overview-copy">
                            <h3 class="overview-value" id="studentOverviewUpcomingToday">{{ $upcomingTodayCount }}</h3>
                            <p class="overview-label">Upcoming Today</p>
                        </div>
                    </article>
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
                                            <span><i class="fa-solid fa-user" aria-hidden="true"></i> {{ $consultation->instructor?->name ?? 'Instructor' }}</span>
                                            <span><i class="fa-solid fa-clock" aria-hidden="true"></i> {{ $formatRelativeDay($consultation->consultation_date) }}, {{ $formatManilaRangeSpaced($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </article>

                    <article class="overview-panel" id="studentUpcomingPanel">
                        <div class="overview-panel-header">
                            <h2 class="overview-panel-title">Upcoming Schedule</h2>
                            <button type="button" class="overview-panel-link history-open-btn">View Calendar <span aria-hidden="true">→</span></button>
                        </div>
                        <div id="studentUpcomingContent">
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
            </section>

            <!-- Success modal (shown after successful consultation request) -->
            <div class="success-modal-overlay" id="successModalOverlay" aria-hidden="{{ $flashSuccess ? 'false' : 'true' }}" style="display: {{ $successModalDisplay }};">
                <div class="success-modal" role="dialog" aria-modal="true" aria-labelledby="successModalTitle">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:68px;height:68px;border-radius:50%;background:#eef2ff;display:grid;place-items:center;">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12.5L11.5 15L15 10.5" stroke="#3746d6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div style="flex:1;">
                            <h3 id="successModalTitle">Submission Successful!</h3>
                            <p id="successModalMessage">{{ $flashSuccess ?? 'Your consultation request was submitted successfully.' }}</p>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;margin-top:18px;">
                        <button class="done-btn" id="successModalDone">Done</button>
                    </div>
                </div>
            </div>
            <div class="section is-hidden" id="request-consultation">
                <div class="request-card">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:6px;">
                        <div class="request-title">Request Consultation</div>
                        <button type="button" class="feedback-cancel" id="requestCloseBtn">X</button>
                    </div>
                    <div class="request-subtitle">Fill in the details to schedule a consultation with your instructor.</div>

                    <form method="POST" action="{{ route('student.consultation.store') }}">
                        @csrf
                        <div class="request-layout">
                            <div class="request-main-pane">
                                <div class="request-section">
                                    <span class="request-label">1. Select Instructor</span>
                                    <div class="request-grid" id="requestInstructorGrid">
                                        @forelse ($instructors as $instructor)
                                            <label class="request-card-item">
                                                <input type="radio" name="instructor_id" value="{{ $instructor->id }}" required>
                                                <div class="request-avatar">{{ strtoupper(substr($instructor->name, 0, 1)) }}</div>
                                                <div class="request-card-text">
                                                    <div style="display:flex;align-items:center;gap:8px;">
                                                        <div class="request-card-name">{{ $instructor->name }}</div>
                                                    </div>
                                                    <div class="request-card-meta">{{ $instructor->email }}</div>
                                                </div>
                                            </label>
                                        @empty
                                            <div style="color:var(--muted);font-size:13px;">No instructors found.</div>
                                        @endforelse
                                    </div>
                                    <div id="requestInstructorPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;gap:12px;flex-wrap:wrap;">
                                        <div id="requestInstructorPaginationInfo" style="font-size:12px;color:var(--muted);font-weight:600;"></div>
                                        <div id="requestInstructorPaginationControls" style="display:flex;gap:8px;align-items:center;">
                                            <button id="prevRequestInstructorBtn" class="pagination-nav-btn" style="display:none;">
                                                <span style="font-size:16px;">‹</span>
                                            </button>
                                            <div id="requestInstructorPageNumbers" style="display:flex;gap:4px;"></div>
                                            <button id="nextRequestInstructorBtn" class="pagination-nav-btn" style="display:none;">
                                                <span style="font-size:16px;">›</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="request-section">
                                    <span class="request-label">2. Schedule & Mode</span>
                                    <div class="preferred-row">
                                        <div class="preferred-group">
                                            <div class="preferred-label">Preferred Day</div>
                                            <div class="preferred-days" id="preferredDays">
                                                <button type="button" class="preferred-day-btn" data-day="monday" disabled>Mon</button>
                                                <button type="button" class="preferred-day-btn" data-day="tuesday" disabled>Tue</button>
                                                <button type="button" class="preferred-day-btn" data-day="wednesday" disabled>Wed</button>
                                                <button type="button" class="preferred-day-btn" data-day="thursday" disabled>Thu</button>
                                                <button type="button" class="preferred-day-btn" data-day="friday" disabled>Fri</button>
                                                <button type="button" class="preferred-day-btn" data-day="saturday" disabled>Sat</button>
                                            </div>
                                        </div>
                                        <div class="preferred-group">
                                            <div class="preferred-label">Preferred Time Slot</div>
                                            <div class="preferred-time" id="preferredTimeDisplay">Select a day</div>
                                        </div>
                                        <div class="preferred-group">
                                            <div class="preferred-label">Consultation Date</div>
                                            <div class="preferred-date-wrap">
                                                <input type="date" name="consultation_date" id="requestConsultationDate" class="preferred-date-input" required min="{{ date('Y-m-d') }}" disabled>
                                                <button type="button" class="preferred-date-trigger" id="requestDateTrigger" aria-label="Open calendar" disabled>
                                                    <i class="fa-regular fa-calendar"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hint" id="requestDateHint">Choose an instructor first. Available dates are Monday to Saturday only.</div>
                                    <input type="hidden" name="consultation_time" id="requestConsultationTime">

                                    <div class="request-mode-grid" id="requestModeGrid" style="margin-top:12px;">
                                        <label class="request-mode-card">
                                            <input type="radio" name="consultation_mode" value="Video Call" required>
                                            <div class="mode-body">
                                                <div class="mode-icon"><i class="fa-solid fa-video" aria-hidden="true"></i></div>
                                                <div class="mode-title">Video</div>
                                                <div class="mode-desc">Virtual meeting</div>
                                            </div>
                                        </label>
                                        <label class="request-mode-card">
                                            <input type="radio" name="consultation_mode" value="Face-to-Face" required>
                                            <div class="mode-body">
                                                <div class="mode-icon"><i class="fa-solid fa-user-group" aria-hidden="true"></i></div>
                                                <div class="mode-title">In-Person</div>
                                                <div class="mode-desc">Face-to-face</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="request-section">
                                    <span class="request-label">3. Topic & Details</span>
                                    <div class="request-form-grid">
                                        <div class="request-form-group">
                                            <label>Main Category</label>
                                            <select id="consultationCategory" name="consultation_category" required>
                                                <option value="" disabled selected>Select category</option>
                                                <option value="Curricular Activities">CURRICULAR ACTIVITIES</option>
                                                <option value="Behavior-Related">Behavior-Related</option>
                                                <option value="Co-curricular activities">Co-curricular activities</option>
                                            </select>
                                        </div>

                                        <div class="request-form-group">
                                            <label>Topic</label>
                                            <select id="consultationType" name="consultation_type" required>
                                                <option value="" disabled selected>Select a topic</option>
                                            </select>
                                        </div>

                                        <div class="request-form-group">
                                            <label>Urgency Level</label>
                                            <select id="consultationPriority" name="consultation_priority">
                                                <option value="" selected>Normal</option>
                                                <option value="Urgent">Urgent</option>
                                                <option value="Low">Low</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="request-form-group" style="margin-top:10px;">
                                        <label>Discussion Brief (Optional)</label>
                                        <textarea name="student_notes" rows="4" placeholder="Briefly describe what you'd like to discuss..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <aside class="request-summary-pane">
                                <div class="request-summary-card">
                                    <div class="request-summary-title">Summary</div>
                                    <div class="request-summary-subtitle">Review your request</div>
                                    <div class="request-summary-lines">
                                        <div class="meta" id="reviewLine1">Instructor: —</div>
                                        <div class="meta" id="reviewLine2">Date & Time: —</div>
                                        <div class="meta" id="reviewLine3">Type: —</div>
                                        <div class="meta" id="reviewLine4">Mode: —</div>
                                        <div class="meta" id="reviewLine5">Notes: —</div>
                                    </div>
                                    <div class="request-actions request-actions-sticky">
                                        <button type="button" class="btn secondary" id="requestCancelBtn">Cancel</button>
                                        <button type="submit" class="btn primary">Confirm & Submit</button>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </form>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Populate consultation topics based on selected category
                    const topicsByCategory = {
                        'Curricular Activities': [
                            'Thesis/Project',
                            'Grades',
                            'Requirements not submitted',
                            'Lack of quizzes/assignments',
                            'Other curricular concern'
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

                    const categoryEl = document.getElementById('consultationCategory');
                    const typeEl = document.getElementById('consultationType');
                    const priorityEl = document.getElementById('consultationPriority');
                    const reviewLine3 = document.getElementById('reviewLine3');

                    function populateTopics(category) {
                        typeEl.innerHTML = '<option value="" disabled selected>Select a topic</option>';
                        if (!category || !topicsByCategory[category]) return;
                        topicsByCategory[category].forEach(function (t) {
                            const opt = document.createElement('option');
                            opt.value = t;
                            opt.textContent = t;
                            typeEl.appendChild(opt);
                        });
                    }

                    function updateReviewLine3() {
                        const category = categoryEl?.value || '';
                        const topic = typeEl?.value || '';
                        const priority = priorityEl?.value || '';
                        let display = '';
                        if (category) display += category;
                        if (topic) display += (display ? ' - ' : '') + topic;
                        if (priority) display += ' (' + priority + ')';
                        if (reviewLine3) reviewLine3.textContent = `Type: ${display || '—'}`;
                    }

                    if (categoryEl && typeEl) {
                        categoryEl.addEventListener('change', function (e) {
                            populateTopics(e.target.value);
                            updateReviewLine3();
                        });
                        // initialize if preselected
                        if (categoryEl.value) populateTopics(categoryEl.value);
                    }

                    if (typeEl) {
                        typeEl.addEventListener('change', function () {
                            updateReviewLine3();
                        });
                    }

                    if (priorityEl) {
                        priorityEl.addEventListener('change', function () {
                            updateReviewLine3();
                        });
                    }
                    const overlay = document.getElementById('successModalOverlay');
                    const doneBtn = document.getElementById('successModalDone');
                    const flashMsg = {!! json_encode($flashSuccess) !!};
                    
                    // Show success modal if there's a flash message
                    if (flashMsg && overlay) {
                        const msgEl = document.getElementById('successModalMessage');
                        if (msgEl) msgEl.textContent = String(flashMsg);
                        overlay.style.display = 'flex';
                        overlay.setAttribute('aria-hidden', 'false');
                    }

                    if (doneBtn && overlay) {
                        doneBtn.addEventListener('click', function () {
                            overlay.style.display = 'none';
                            overlay.setAttribute('aria-hidden', 'true');
                        });
                    }
                });
            </script>
          <style>
/* ===== Consultation Card Styles ===== */
.consultation-item {
    margin-bottom: 5px;
}

.consultation-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: box-shadow 0.2s;
    gap: 24px;
}

.consultation-card:hover {
    box-shadow: 0 4px 16px rgba(60,80,140,0.11);
}

/* Status left border accent */
.consultation-card.status-pending   { border-left: 4px solid #f5a623; }
.consultation-card.status-approved  { border-left: 4px solid #4a90e2; }
.consultation-card.status-in_progress { border-left: 4px solid #7ed321; }
.consultation-card.status-completed { border-left: 4px solid #9b9b9b; }
.consultation-card.status-cancelled { border-left: 4px solid #d0021b; }
.consultation-card.status-incompleted { border-left: 4px solid #d97706; }

/* ---- Card Section Columns ---- */
.cc-col {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    padding: 0 20px;
}

.cc-col:not(:last-child) {
    border-right: 1.5px solid #ececf3;
}

.cc-col-instructor {
    min-width: 190px;
    max-width: 220px;
    padding-left: 0;
}

.cc-col-type   { min-width: 170px; max-width: 220px; }
.cc-col-mode   { min-width: 120px; max-width: 160px; }
.cc-col-status { min-width: 100px; max-width: 130px; }
.cc-col-action {
    min-width: 140px;
    max-width: 180px;
    padding-right: 0;
    align-items: flex-start;
}

/* ---- Instructor block ---- */
.cc-instructor-name {
    font-weight: 800;
    font-size: 15px;
    color: #1a1a2e;
    line-height: 1.2;
}

.cc-instructor-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.online-badge {
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

.instructor-active-minutes-badge {
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
}

.cc-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
    margin-top: 3px;
}

.cc-meta span {
    font-size: 12.5px;
    color: #555;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ---- Section label ---- */
.cc-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #a0a4b8;
    margin-bottom: 2px;
}

.cc-value {
    font-weight: 700;
    font-size: 14px;
    color: #1a1a2e;
    line-height: 1.3;
}

/* ---- Mode pill ---- */
.cc-mode-pill {
    display: inline-block;
    font-weight: 700;
    font-size: 13px;
    color: #3a4a7a;
    background: #fff;
    border: 1.5px solid #c5cde8;
    border-radius: 8px;
    padding: 5px 14px;
    width: fit-content;
}

/* ---- Status badge ---- */
.cc-status-badge {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: 0.08em;
    border-radius: 8px;
    padding: 5px 13px;
    text-transform: uppercase;
    border: 2px solid transparent;
}

.cc-status-badge.status-pending {
    color: #c07000;
    background: #fffbef;
    border-color: #f5a623;
}

.cc-status-badge.status-approved {
    color: #1a60bb;
    background: #eef4ff;
    border-color: #4a90e2;
}

.cc-status-badge.status-in_progress {
    color: #2d7a00;
    background: #f0fff0;
    border-color: #7ed321;
}

.cc-status-badge.status-completed {
    color: #555;
    background: #f5f5f5;
    border-color: #bbb;
}

.cc-status-badge.status-incompleted {
    color: #92400e;
    background: #fffbeb;
    border-color: #f59e0b;
}

.cc-status-badge.status-cancelled {
    color: #b00020;
    background: #fff0f0;
    border-color: #d0021b;
}

/* ---- Action area ---- */
.cc-awaiting {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 12.5px;
    font-weight: 700;
    color: #6b7280;
    margin-bottom: 6px;
}

/* Spinner */
.cc-spinner {
    width: 15px;
    height: 15px;
    border: 2.5px solid #d1d5db;
    border-top-color: #4a90e2;
    border-radius: 50%;
    animation: cc-spin 0.8s linear infinite;
    display: inline-block;
    flex-shrink: 0;
}

@keyframes cc-spin {
    to { transform: rotate(360deg); }
}

/* Buttons */
.cc-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-weight: 700;
    font-size: 12.5px;
    border-radius: 9px;
    padding: 7px 16px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.18s;
    text-decoration: none;
    white-space: nowrap;
}

.cc-btn-cancel {
    color: #1a1a2e;
    background: #fff;
    border-color: #1a1a2e;
}

.cc-btn-cancel:hover {
    background: #1a1a2e;
    color: #fff;
}

.cc-btn-join {
    color: #fff;
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    border-color: #4a90e2;
    box-shadow: 0 2px 8px rgba(74,144,226,0.25);
    font-size: 13px;
    padding: 8px 18px;
}

.cc-btn-join:hover {
    background: linear-gradient(135deg, #357abd 0%, #2860a0 100%);
    box-shadow: 0 4px 14px rgba(74,144,226,0.35);
}

.cc-btn-view {
    color: #4a4a6a;
    background: #f4f5fa;
    border-color: #d0d4e8;
}

.cc-btn-view:hover {
    background: #e8eaf8;
    border-color: #4a90e2;
    color: #4a90e2;
}

.cc-completed-check {
    font-size: 12px;
    font-weight: 600;
    color: #888;
    margin-bottom: 6px;
}

.myc-filter-row {
    margin: 0 0 16px;
    display: flex;
    gap: 14px;
    align-items: flex-end;
    flex-wrap: wrap;
    max-width: 100%;
}

#my-consultations .myc-top-panel {
    margin-bottom: 18px;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

#my-consultations {
    background: #ffffff;
    border-radius: 16px;
    padding: 22px;
    box-shadow: var(--shadow);
    margin-bottom: 24px;
}

#my-consultations .history-modal-header {
    padding: 0 0 14px;
    margin-bottom: 14px;
    border-bottom: 0;
    background: transparent;
    align-items: center;
}

#my-consultations .history-modal-title {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: 0;
}

#my-consultations .history-close {
    width: auto;
    min-width: 58px;
    height: 34px;
    padding: 0 14px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    background: #ffffff;
}

#my-consultations .myc-filter-row {
    margin-bottom: 0;
}

.myc-filter-group {
    display: grid;
    gap: 8px;
    min-width: 260px;
    flex: 1 1 300px;
    max-width: 360px;
}

.myc-filter-label {
    font-size: 14px;
    font-weight: 700;
    color: var(--muted);
}

.myc-status-filter {
    position: relative;
    width: 100%;
}

.myc-status-filter-btn {
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

.myc-status-filter-caret {
    color: #111827;
    font-size: 13px;
    line-height: 1;
}

.myc-status-filter-menu {
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

.myc-status-filter-menu.open {
    display: grid;
    gap: 10px;
}

.myc-status-filter-option {
    border: none;
    background: transparent;
    text-align: left;
    padding: 0;
    cursor: pointer;
}

.myc-status-pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.myc-status-pill.all { background: #eef2ff; color: #4338ca; }
.myc-status-pill.pending { background: #fef3c7; color: #92400e; }
.myc-status-pill.approved { background: #d1fae5; color: #166534; }
.myc-status-pill.in_progress { background: #ede9fe; color: #5b21b6; }
.myc-status-pill.completed { background: #cfeef6; color: #155e75; }
.myc-status-pill.incompleted { background: #fef3c7; color: #92400e; }
.myc-status-pill.decline { background: #fee2e2; color: #991b1b; }

.myc-search-wrap {
    display: grid;
    gap: 8px;
}

.myc-search-input {
    width: 100%;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    background: #fff;
    color: #111827;
    font-size: 14px;
    font-weight: 600;
    padding: 12px 14px;
}

.myc-search-input:focus {
    outline: none;
    border-color: #5b6bff;
    box-shadow: 0 0 0 3px rgba(91, 107, 255, 0.15);
}

/* ===== Professional Table Layout (My Consultations) ===== */
.myc-table-wrap {
    border: 1px solid #dbe1ea;
    border-radius: 14px;
    background: #ffffff;
    overflow: hidden;
}

.myc-table-head {
    display: grid;
    width: 100%;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.12fr) minmax(0, 1.6fr) minmax(0, 1.05fr) minmax(0, 1fr);
    gap: 0;
    align-items: center;
    background: #eef2f7;
    border-bottom: 1px solid #dbe1ea;
}

.myc-table-head > div {
    padding: 12px 14px;
    font-size: 11px;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: #425066;
    font-weight: 800;
}

.consultation-list {
    display: block;
}

.consultation-item {
    margin: 0;
}

.consultation-card {
    display: grid;
    width: 100%;
    min-width: 0;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.12fr) minmax(0, 1.6fr) minmax(0, 1.05fr) minmax(0, 1fr);
    align-items: center;
    gap: 0;
    padding: 0;
    border: 0;
    border-bottom: 1px solid #edf1f6;
    border-radius: 0;
    background: #fff;
    box-shadow: none;
}

.consultation-card::before {
    display: none;
}

.consultation-card:hover {
    background: #ffffff;
    box-shadow: none;
    border-color: var(--border);
    transform: none;
}

.consultation-card.status-pending,
.consultation-card.status-approved,
.consultation-card.status-in_progress,
.consultation-card.status-completed,
.consultation-card.status-cancelled,
.consultation-card.status-incompleted,
.consultation-card.status-declined {
    border-left: 0;
}

.cc-col {
    border-right: 0 !important;
    padding: 12px 14px;
    min-width: 0;
    overflow-wrap: anywhere;
}

.cc-col-instructor {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
}

.cc-avatar {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    background: linear-gradient(135deg, #7489ff 0%, #5b6bff 100%);
    color: #fff;
    font-size: 13px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cc-instructor-name {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    overflow-wrap: anywhere;
}

.cc-date {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
}

.cc-time {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
}

.cc-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    overflow-wrap: anywhere;
}

.cc-mode-pill {
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid #bfd3f5;
    background: #eaf1ff;
    color: #214a93;
}

.cc-status-badge {
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    padding: 6px 12px;
}

.cc-updated {
    font-size: 12px;
    color: #64748b;
    font-style: italic;
    white-space: normal;
}

#my-consultations .cc-col-mode {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
}

.cc-col-action {
    align-items: flex-start;
}

#my-consultations .cc-col-status {
    display: none;
}

#my-consultations .cc-col-updated {
    display: none;
}

.cc-btn {
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 12px;
}

@media (max-width: 1240px) {
    #my-consultations .myc-table-wrap {
        border: none;
        background: transparent;
        box-shadow: none;
        overflow: visible;
    }

    #my-consultations .myc-table-head {
        display: none;
    }

    #my-consultations .consultation-list {
        display: grid;
        gap: 12px;
    }

    #my-consultations .consultation-card {
        display: grid;
        width: 100%;
        min-width: 0;
        grid-template-columns: minmax(0, 1fr) minmax(132px, 156px);
        grid-template-areas:
            "instructor action"
            "date type"
            "mode mode";
        gap: 10px 14px;
        padding: 14px 16px;
        border: 1px solid #dfe7f4;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        background: #ffffff;
        align-items: start;
    }

    #my-consultations .consultation-card:hover {
        background: #ffffff;
        border-color: #dfe7f4;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        transform: none;
    }

    #my-consultations .cc-col {
        padding: 0;
        border-right: none !important;
        min-width: 0;
    }

    #my-consultations .cc-col::before {
        display: block;
        margin-bottom: 3px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    #my-consultations .cc-col-instructor {
        grid-area: instructor;
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: start;
        gap: 10px;
    }

    #my-consultations .cc-col-instructor::before,
    #my-consultations .cc-col-action::before {
        display: none;
        content: none;
    }

    #my-consultations .cc-col-date {
        grid-area: date;
    }

    #my-consultations .cc-col-date::before {
        content: "Date & Time";
    }

    #my-consultations .cc-col-type {
        grid-area: type;
    }

    #my-consultations .cc-col-type::before {
        content: "Session Type";
    }

    #my-consultations .cc-col-mode {
        grid-area: mode;
    }

    #my-consultations .cc-col-mode::before {
        content: "Mode";
    }

    #my-consultations .cc-col-action {
        grid-area: action;
        width: 100%;
        justify-self: end;
        align-self: start;
        display: grid;
        gap: 8px;
        justify-items: stretch;
    }

    #my-consultations .cc-col-action form,
    #my-consultations .cc-col-action .cc-btn,
    #my-consultations .cc-col-action button {
        width: 100%;
        margin: 0;
    }

    #my-consultations .cc-awaiting,
    #my-consultations .cc-completed-check {
        white-space: normal;
        justify-content: center;
        text-align: center;
    }

    #my-consultations .cc-instructor-meta {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    #my-consultations .cc-instructor-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
        min-width: 0;
    }

    #my-consultations .cc-mobile-details {
        display: none !important;
    }

    #my-consultations .cc-instructor-name,
    #my-consultations .cc-date,
    #my-consultations .cc-value {
        font-size: 13px;
        line-height: 1.35;
    }

    #my-consultations .cc-mode-pill,
    #my-consultations .cc-status-badge {
        font-size: 11px;
        padding: 6px 10px;
    }

    #my-consultations .cc-updated {
        font-size: 11px;
        line-height: 1.4;
    }

    #my-consultations .cc-btn {
        min-height: 36px;
        padding: 8px 10px;
        font-size: 11px;
    }
}

@media (max-width: 768px) {
    #my-consultations {
        padding: 14px;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    #my-consultations .myc-top-panel {
        margin-bottom: 14px;
        padding: 0;
    }

    #my-consultations .history-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e8edf5;
    }

    #my-consultations .history-modal-title {
        font-size: 16px;
    }

    #my-consultations .history-close {
        min-width: 54px;
        height: 32px;
        padding: 0 12px;
        border-radius: 10px;
        font-size: 12px;
    }

    .myc-filter-row {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }

    .myc-filter-group {
        min-width: 0;
        max-width: none;
        width: 100%;
        flex: 0 0 auto;
        gap: 6px;
    }

    .myc-filter-label {
        font-size: 12px;
        font-weight: 700;
        margin: 0;
    }

    .myc-search-wrap {
        gap: 6px;
    }

    .myc-status-filter-btn,
    .myc-search-input {
        min-height: 44px;
        padding: 10px 12px;
        font-size: 13px;
        border-radius: 12px;
    }

    .myc-search-input {
        height: 44px;
    }

    .myc-status-filter-menu {
        padding: 8px 10px;
        border-radius: 12px;
    }

    .myc-table-wrap {
        border: none;
        background: transparent;
        box-shadow: none;
        overflow: visible;
    }

    .myc-table-head {
        display: none;
    }

    .consultation-list {
        gap: 12px;
    }

    .consultation-card {
        min-width: 0;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto !important;
        gap: 12px;
        padding: 14px;
        border: 1px solid #dfe7f4;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        background: #ffffff;
        align-items: center;
    }

    #my-consultations .consultation-card > :not(.cc-col-instructor):not(.cc-mobile-details) {
        display: none !important;
    }

    #my-consultations .cc-col-instructor,
    #my-consultations .cc-mobile-details {
        display: flex !important;
    }

    #my-consultations .cc-col {
        width: 100%;
        padding: 0;
        border-right: none !important;
        align-items: flex-start;
        gap: 4px;
    }

    #my-consultations .cc-col::before {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    #my-consultations .cc-col-instructor::before {
        display: none;
        content: none;
    }

    #my-consultations .cc-col-instructor {
        min-width: 0;
        max-width: none;
        display: grid !important;
        grid-template-columns: auto 1fr;
        align-items: start;
        gap: 10px;
    }

    #my-consultations .cc-instructor-meta {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    #my-consultations .cc-instructor-label { display: none; }

    #my-consultations .cc-instructor-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
        min-width: 0;
    }

    #my-consultations .cc-mobile-meta {
        display: none !important;
    }

    #my-consultations .cc-mobile-details {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        align-self: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    #my-consultations .cc-mobile-details-btn {
        padding: 8px 12px;
        font-size: 11px;
    }

    .cc-instructor-name,
    .cc-date,
    .cc-value {
        font-size: 13px;
    }

    .cc-mode-pill,
    .cc-status-badge {
        font-size: 11px;
    }

    .cc-updated {
        white-space: normal;
        font-size: 11px;
    }

    }
}
</style>

<div id="my-consultations" class="is-hidden">
<div class="myc-top-panel">
<div class="history-modal-header">
    <div class="history-title-wrap">
        <h2 class="history-modal-title">My Consultations</h2>
    </div>
    <button type="button" class="history-close" id="exitMyConsultationsBtn" aria-label="Close my consultations">Exit</button>
</div>

<div class="myc-filter-row">
    <div class="myc-filter-group">
        <label class="myc-filter-label" for="myConsultationStatusFilterBtn">Select Status:</label>
        <div class="myc-status-filter" id="myConsultationStatusFilterDropdown">
            <button type="button" id="myConsultationStatusFilterBtn" class="myc-status-filter-btn" aria-expanded="false" aria-controls="myConsultationStatusFilterMenu">
                <span id="myConsultationStatusFilterLabel">Choose a status...</span>
                <span class="myc-status-filter-caret">&#9650;</span>
            </button>
            <div id="myConsultationStatusFilterMenu" class="myc-status-filter-menu" aria-hidden="true">
                <button type="button" class="myc-status-filter-option" data-status="all" data-label="All">
                    <span class="myc-status-pill all">All</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="pending" data-label="Pending">
                    <span class="myc-status-pill pending">Pending</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="approved" data-label="Approved">
                    <span class="myc-status-pill approved">Approved</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="in_progress" data-label="In Progress">
                    <span class="myc-status-pill in_progress">In Progress</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="completed" data-label="Completed">
                    <span class="myc-status-pill completed">Completed</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="incompleted" data-label="Incomplete">
                    <span class="myc-status-pill incompleted">Incomplete</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="decline" data-label="Decline">
                    <span class="myc-status-pill decline">Decline</span>
                </button>
            </div>
        </div>
    </div>
    <div class="myc-filter-group myc-search-wrap">
        <label class="myc-filter-label" for="myConsultationSearch">Search:</label>
        <input
            type="search"
            id="myConsultationSearch"
            class="myc-search-input"
            placeholder="Search instructor, date, type, mode, status..."
            autocomplete="off"
        >
    </div>
</div>
</div>

<div class="myc-table-wrap">
<div class="myc-table-head" role="row">
    <div>Instructor</div>
    <div>Date &amp; Time</div>
    <div>Session Type</div>
    <div>Mode</div>
    <div>Action</div>
</div>

<div class="consultation-list" id="consultationList">
    @foreach ($consultations as $consultation)
        @php
            $instructorOnline = in_array($consultation->instructor?->id ?? 0, (array) $onlineInstructorIds);
            $instructorId = $consultation->instructor?->id;
            $lastActiveMinutes = $instructorId && isset($instructorActiveMinutes[$instructorId])
                ? $instructorActiveMinutes[$instructorId]['last_active_minutes']
                : null;
            $statusSlug = strtolower($consultation->status);
            $statusLabel = ucwords(str_replace('_', ' ', $statusSlug));
            $instructorName = $consultation->instructor?->name ?? 'Instructor';
            $initialsParts = array_values(array_filter(explode(' ', trim((string) $instructorName))));
            $initials = strtoupper(substr($initialsParts[0] ?? 'I', 0, 1) . substr($initialsParts[1] ?? '', 0, 1));
            $updatedLabel = $consultation->updated_at?->diffForHumans() ?? '--';
            $durationLabel = $consultation->duration_minutes !== null ? $consultation->duration_minutes . ' min' : '—';
        @endphp

        <div class="consultation-item" data-consultation-index="{{ $loop->index }}" data-status="{{ $statusSlug }}">
            <div class="consultation-card status-{{ $statusSlug }}" data-consultation-id="{{ $consultation->id }}">

                {{-- -- INSTRUCTOR -- --}}
                <div class="cc-col cc-col-instructor">
                    <div class="cc-avatar" aria-hidden="true">{{ $initials ?: 'I' }}</div>
                    <div class="cc-instructor-meta">
                        <span class="cc-instructor-label">Instructor</span>
                        <div class="cc-instructor-row">
                            <span class="cc-instructor-name">
                                {{ $instructorName }}
                            </span>
                            @if ($instructorOnline)
                                <span class="online-badge" aria-hidden="true">● Online</span>
                            @elseif ($lastActiveMinutes !== null)
                                <span class="instructor-active-minutes-badge">
                                    ⏱ {{ $lastActiveMinutes }}{{ $lastActiveMinutes === 1 ? ' min' : ' mins' }} ago
                                </span>
                            @endif
                        </div>
                        <div class="cc-mobile-meta">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i>
                            <span>{{ $updatedLabel }}</span>
                        </div>
                    </div>
                </div>

                <div class="cc-mobile-details">
                    <button type="button"
                            class="cc-mobile-details-btn"
                            data-id="{{ $consultation->id }}"
                            data-show-status-updated="true"
                            data-instructor="{{ $instructorName }}"
                            data-type="{{ $consultation->type_label }}"
                            data-mode="{{ $consultation->consultation_mode }}"
                            data-date="{{ $consultation->consultation_date }}"
                            data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                            data-duration="{{ $durationLabel }}"
                            data-status="{{ $statusLabel }}"
                            data-updated="{{ $updatedLabel }}"
                            data-summary="{{ e($consultation->summary_text) }}"
                            data-transcript="{{ e($consultation->transcript_text) }}"
                            data-action-source="consultationAction{{ $consultation->id }}">
                        View Details
                    </button>
                </div>

                <div class="cc-col cc-col-date">
                    <div class="cc-date">{{ $consultation->consultation_date }}</div>
                    <div class="cc-time">{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}</div>
                </div>

                {{-- -- TYPE -- --}}
                <div class="cc-col cc-col-type">
                    <div class="cc-value">{{ $consultation->type_label }}</div>
                </div>

                <div class="cc-col cc-col-mode">
                    <div class="cc-mode-pill">{{ $consultation->consultation_mode }}</div>
                    <div class="cc-updated">{{ $updatedLabel }}</div>
                </div>

                {{-- -- STATUS -- --}}
                <div class="cc-col cc-col-status">
                    <span class="cc-status-badge status-{{ $statusSlug }}">
                        {{ strtoupper($consultation->status) }}
                    </span>
                </div>

                {{-- -- ACTION -- --}}
                <div class="cc-col cc-col-action" id="consultationAction{{ $consultation->id }}">
                    @if ($consultation->status === 'pending')
                        <div class="cc-awaiting">
                            <span class="cc-spinner" aria-hidden="true"></span>
                            <span>Awaiting</span>
                        </div>
                        <form method="POST"
                              action="{{ route('student.consultation.cancel', $consultation) }}"
                              class="student-cancel-form"
                              data-consultation-id="{{ $consultation->id }}"
                              style="margin:0">
                            @csrf
                            <button type="submit"
                                    class="cc-btn cc-btn-cancel">
                                Cancel
                            </button>
                        </form>

                    @elseif ($consultation->status === 'approved')
                        <div class="cc-awaiting">
                            <span class="cc-spinner" aria-hidden="true"></span>
                            <span>Starting soon</span>
                        </div>

                    @elseif ($consultation->status === 'in_progress')
                        <button class="cc-btn cc-btn-join join-call-btn"
                                data-consultation-id="{{ $consultation->id }}"
                                data-mode="{{ strtolower((string) $consultation->consultation_mode) }}">
                            🎯 Join Now
                        </button>

                    @elseif ($consultation->status === 'completed')
                        <div class="cc-completed-check">✓ Completed</div>
                        <button type="button"
                                class="cc-btn cc-btn-feedback feedback-open-btn"
                                data-id="{{ $consultation->id }}"
                                data-instructor="{{ $consultation->instructor?->name ?? 'Instructor' }}"
                                data-type="{{ $consultation->type_label }}"
                                data-mode="{{ $consultation->consultation_mode }}"
                                data-date="{{ $consultation->consultation_date }}"
                                data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                data-duration="{{ $durationLabel }}"
                                data-summary="{{ e($consultation->summary_text) }}"
                                data-transcript="{{ e($consultation->transcript_text) }}">
                            💬 Feedback
                        </button>

                    @elseif ($consultation->status === 'incompleted')
                        <span style="font-size:12px;font-weight:700;color:#92400e;">
                            Incomplete
                        </span>

                    @else
                        <span style="font-size:12px;font-weight:600;color:#888;">
                            {{ ucfirst($consultation->status) }}
                        </span>
                    @endif
                </div>

            </div>
        </div>
    @endforeach
</div>
</div>

<!-- Pagination Controls -->
                <div id="consultationPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;">
                    <div id="consultationPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to {{ min(10, $consultations->count()) }} of {{ $consultations->count() }} consultations
                    </div>
                    <div id="consultationPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevConsultationBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="consultationPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextConsultationBtn" class="pagination-nav-btn" style="display:none;">
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
    </div>
</div>
</div>

<div id="history" class="section is-hidden" aria-hidden="true">
        <div class="history-modal-header">
            <div class="history-title-wrap">
                <h2 class="history-modal-title">Consultation History</h2>
                <p class="history-modal-subtitle">Manage and track all completed consultations</p>
            </div>
            <button type="button" class="history-close" id="closeHistoryModal" aria-label="Close history">&times;</button>
        </div>
        <div class="history-modal-body">
            @php
                $completedConsultations = $consultations->where('status', 'completed');
                $historyTypes = $completedConsultations
                    ->pluck('type_label')
                    ->filter()
                    ->unique()
                    ->values();
                $historyModes = $completedConsultations
                    ->pluck('consultation_mode')
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
                            <button type="button" id="semAll" class="semester-btn" data-sem="all">All</button>
                            <button type="button" id="sem1" class="semester-btn" data-sem="1">1st Sem</button>
                            <button type="button" id="sem2" class="semester-btn" data-sem="2">2nd Sem</button>
                        </div>
                        <div class="history-month-group" id="monthPickerContainer" style="display:none;">
                            <label for="historyMonthSelect">Month</label>
                            <select id="historyMonthSelect">
                                <option value="">All months</option>
                            </select>
                        </div>
                        <div class="history-year-group">
                            <label for="historyYearInput">Academic Year</label>
                            <input type="text" id="historyYearInput" placeholder="e.g., 2026-2027">
                        </div>
                    </div>
                    <div class="history-inline-filters">
                        <div class="availability-filter-group history-inline-filter">
                            <label for="historyCategoryFilter">Category</label>
                            <select id="historyCategoryFilter">
                                <option value="">All Categories</option>
                                <option value="Curricular Activities">CURRICULAR ACTIVITIES</option>
                                <option value="Behavior-Related">Behavior-Related</option>
                                <option value="Co-curricular activities">Co-curricular activities</option>
                            </select>
                        </div>
                        <div class="availability-filter-group history-inline-filter">
                            <label for="historyTopicFilter">Topic</label>
                            <select id="historyTopicFilter">
                                <option value="">All Topics</option>
                            </select>
                        </div>
                        <div class="availability-filter-group history-inline-filter">
                            <label for="historyModeFilter">Mode</label>
                            <select id="historyModeFilter">
                                <option value="">All Modes</option>
                                <option value="Video Call">Video Call</option>
                                <option value="Face-to-Face">Face-to-Face</option>
                            </select>
                        </div>
                        <div class="availability-filter-group history-inline-filter history-search-filter">
                            <label for="historySearch">Search</label>
                            <div class="history-search-actions">
                                <input type="search" id="historySearch" placeholder="Search history...">
                                <button class="export-btn" type="button" id="historyExport">Export History</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="filters" aria-hidden="true" style="display:none;">
                <div class="filters-grid">
                    <!-- moved semester and academic year into header for compact layout -->
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

                @forelse ($consultations->where('status', 'completed') as $consultation)
                    @php
                        $modeValue = strtolower((string) $consultation->consultation_mode);
                        $isFaceToFace = str_contains($modeValue, 'face');
                        $duration = $consultation->duration_minutes ?? null;
                        $instructorName = $consultation->instructor?->name ?? 'Instructor';
                        $initialsParts = array_values(array_filter(explode(' ', trim((string) $instructorName))));
                        $initials = strtoupper(substr($initialsParts[0] ?? 'I', 0, 1) . substr($initialsParts[1] ?? '', 0, 1));
                        $dateObj = \Illuminate\Support\Carbon::parse($consultation->consultation_date);
                        $month = (int) $dateObj->format('n');
                        $year = (int) $dateObj->format('Y');
                        $academicYear = $month >= 8 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
                        $semester = $month >= 8 || $month <= 5 ? ($month >= 8 ? 'first' : 'second') : '';
                    @endphp
                    <div class="history-row-wrap">
                        <div class="history-row history-row-item"
                             data-category="{{ strtolower((string) $consultation->consultation_category ?? '') }}"
                             data-topic="{{ strtolower((string) $consultation->consultation_type ?? '') }}"
                             data-date="{{ $consultation->consultation_date }}"
                             data-month="{{ $dateObj->format('F') }}"
                             data-year="{{ $year }}"
                             data-academic-year="{{ $academicYear }}"
                             data-semester="{{ $semester }}"
                             data-type="{{ strtolower((string) $consultation->type_label) }}"
                             data-mode="{{ strtolower((string) $consultation->consultation_mode) }}"
                             data-instructor="{{ strtolower((string) ($consultation->instructor?->name ?? '')) }}"
                             data-time="{{ strtolower((string) substr($consultation->consultation_time, 0, 5)) }}"
                             data-searchable="{{ strtolower($consultation->type_label . ' ' . ($consultation->instructor?->name ?? '') . ' ' . $consultation->consultation_mode . ' ' . $dateObj->format('F') . ' ' . $year) }}"
                        >
                        <div class="date-time">
                            <span>{{ $consultation->consultation_date }}</span>
                            <span>{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                        </div>
                        <div class="history-instructor-cell">
                            <div class="cc-avatar" aria-hidden="true">{{ $initials ?: 'I' }}</div>
                            <div class="history-instructor-meta">
                                <div class="history-instructor-topline">
                                    <div class="history-instructor-name">{{ $instructorName }}</div>
                                    <div class="history-mobile-datetime">
                                        <span>{{ $consultation->consultation_date }}</span>
                                        <span>{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>{{ $consultation->type_label }}</div>
                        <div class="history-mode-cell">
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
                        <div class="history-action-cell">
                            <a href="#"
                               class="view-link details-open-btn"
                               data-show-status-updated="false"
                               data-type="{{ $consultation->type_label }}"
                               data-mode="{{ $consultation->consultation_mode }}"
                               data-id="{{ $consultation->id }}"
                               data-date="{{ $consultation->consultation_date }}"
                               data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                               data-instructor="{{ $instructorName }}"
                               data-duration="{{ $consultation->duration_minutes !== null ? $consultation->duration_minutes . ' min' : '—' }}"
                               data-summary="{{ e($consultation->summary_text) }}"
                               data-transcript="{{ e($consultation->transcript_text) }}"
                            >View Details</a>
                        </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No consultation history found.</div>
                @endforelse
                <div class="empty-state" id="historyEmptyState" style="display:none;">No matching results.</div>
            </div>

            <!-- History Pagination Controls -->
            <div id="historyPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;">
                <div id="historyPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                    Showing 1 to {{ min(10, $consultations->where('status', 'completed')->count()) }} of {{ $consultations->where('status', 'completed')->count() }} consultations
                </div>
                <div id="historyPaginationControls" style="display:flex;gap:8px;align-items:center;">
                    <button id="prevHistoryBtn" class="pagination-nav-btn" style="display:none;">
                        <span style="font-size:16px;">‹</span>
                    </button>
                    <div id="historyPageNumbers" style="display:flex;gap:4px;">
                        <!-- Page numbers will be generated by JavaScript -->
                    </div>
                    <button id="nextHistoryBtn" class="pagination-nav-btn" style="display:none;">
                        <span style="font-size:16px;">›</span>
                    </button>
                </div>
            </div>
        </div>
</div>

<!-- Decline Confirmation Modal -->
<div id="declineConfirmModal" style="display:none;position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);z-index:1300;background:#fff;border-radius:12px;padding:28px;box-shadow:0 20px 50px rgba(0,0,0,0.3);width:340px;max-width:90%;text-align:center;">
    <div style="font-weight:700;font-size:16px;color:#111827;margin-bottom:12px;">Decline Call?</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:24px;">Are you sure you want to decline this incoming call?</div>
    <div style="display:flex;gap:12px;justify-content:center;">
        <button id="declineConfirmNo" type="button" style="background:#e5e7eb;color:#111827;border:none;border-radius:8px;padding:10px 20px;font-weight:600;cursor:pointer;">No</button>
        <button id="declineConfirmYes" type="button" style="background:#ef4444;color:#fff;border:none;border-radius:8px;padding:10px 20px;font-weight:600;cursor:pointer;">Yes, Decline</button>
    </div>
</div>

<!-- Decline Confirmation Overlay -->
<div id="declineConfirmOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1299;"></div>

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

<!-- Incoming call modal -->
<div class="incoming-call-modal" id="incomingCallModal" aria-hidden="true" style="display:none;position:fixed;left:50%;top:12%;transform:translateX(-50%);z-index:1200;background:#fff;border-radius:12px;padding:26px 28px;box-shadow:0 10px 30px rgba(2,6,23,0.5);width:320px;max-width:90%;text-align:center;">
    <div style="display:flex;flex-direction:column;align-items:center;gap:12px;position:relative;">
        <button id="closeIncomingBtn" type="button" title="Close" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:20px;cursor:pointer;color:#9ca3af;">✕</button>
        <div id="incomingAvatar" style="width:84px;height:84px;border-radius:50%;background:linear-gradient(180deg,#7c5cff,#5aa6ff);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:28px;box-shadow:0 10px 30px rgba(90,106,255,0.18);">SM</div>
        <div id="incomingInstructorName" style="font-weight:800;font-size:18px;color:#111827;">Instructor Name</div>
        <div id="incomingCallBadge" style="font-size:13px;color:#6b7280;background:#eef2ff;padding:6px 10px;border-radius:999px;display:inline-block;">Incoming Video Call</div>
        <div id="incomingButtonsContainer" style="display:flex;gap:18px;margin-top:12px;">
            <button id="declineIncomingBtn" type="button" style="background:#ef4444;color:#fff;border:none;border-radius:999px;width:64px;height:64px;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 6px 18px rgba(239,68,68,0.2);">✕</button>
            <button id="acceptIncomingBtn" type="button" style="background:#10b981;color:#fff;border:none;border-radius:999px;width:64px;height:64px;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 6px 18px rgba(16,185,129,0.2);">✓</button>
        </div>
    </div>
</div>

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
            <button type="button" class="details-close" id="closeDetailsModal">x</button>
        </div>
        <div class="details-body">
            <div class="details-grid">
                <div class="details-card" id="detailsDate">Date & Time: —</div>
                <div class="details-card" id="detailsInstructor">Instructor: —</div>
                <div class="details-card" id="detailsMode">Mode: —</div>
                <div class="details-card" id="detailsType">Type: —</div>
                <div class="details-card" id="detailsDuration">Duration: —</div>
                <div class="details-card" id="detailsStatus">Status: —</div>
                <div class="details-card" id="detailsUpdated">Updated: —</div>
            </div>
            <div class="details-summary" id="detailsActionsWrap" style="display:none;">
                <div class="details-summary-title">Available Actions</div>
                <div class="details-actions-content" id="detailsActionsContent"></div>
            </div>
            <div class="details-summary" id="detailsSummaryWrap">
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

<div class="overlay" id="overlay"></div>

<div class="modal" id="feedbackModal">
    <div class="modal-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <strong>Submit Feedback</strong>
            <button id="closeFeedbackBtn" style="background:none;border:none;cursor:pointer;font-size:18px">✕</button>
        </div>
        <form id="feedbackForm" method="POST" action="{{ route('student.dashboard.submit') }}">
            @csrf
            <input type="hidden" name="consultation_id" id="feedbackConsultationId" value="">
            <div style="display:block;margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px">Instructor</label>
                <div id="feedbackInstructorName" style="width:100%;padding:11px;border:1px solid var(--border);border-radius:10px;background:#f9fafb;color:#6b7280;font-weight:600;">—</div>
            </div>
            <div style="display:block;margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px">Type of Consultation</label>
                <div id="feedbackConsultationType" style="width:100%;padding:11px;border:1px solid var(--border);border-radius:10px;background:#f9fafb;color:#6b7280;font-weight:600;">—</div>
            </div>
            <label style="display:block;margin-bottom:12px">
                Rating (1-5)
                <input type="number" name="rating" min="1" max="5" value="5" style="width:100%;padding:11px;border:1px solid var(--border);border-radius:10px;margin-top:6px">
            </label>
            <label style="display:block;margin-bottom:12px">
                Comments
                <textarea name="comments" rows="4" style="width:100%;padding:11px;border:1px solid var(--border);border-radius:10px;margin-top:6px"></textarea>
            </label>
            <div class="modal-actions">
                <button type="button" class="action-btn secondary" id="cancelFeedbackBtn">Cancel</button>
                <button type="submit" class="action-btn">Submit</button>
            </div>
        </form>
    </div>
</div>

<div class="toast" id="notifToast">
    <button class="toast-close" id="closeToast">x</button>
    <div class="toast-title" id="toastTitle">New Notification</div>
    <div class="toast-body" id="toastBody">You have a new notification.</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const sidebar = document.getElementById('sidebar');
const menuBtn = document.getElementById('menuBtn');
const overlay = document.getElementById('overlay');
const notificationBtn = document.getElementById('notificationBtn');
const notificationPanel = document.getElementById('notificationPanel');
const markAllReadForm = document.getElementById('markAllReadForm');
const markAllReadBtn = document.getElementById('markAllReadBtn');
const notificationList = document.getElementById('notificationList');
const notificationBadge = document.getElementById('notificationBadge');
const feedbackModal = document.getElementById('feedbackModal');
const openFeedbackBtn = document.getElementById('openFeedbackBtn');
const closeFeedbackBtn = document.getElementById('closeFeedbackBtn');
const cancelFeedbackBtn = document.getElementById('cancelFeedbackBtn');
const totalConsultationsCard = document.getElementById('totalConsultationsCard');
const studentUpcomingContent = document.getElementById('studentUpcomingContent');
const studentOverviewTotal = document.getElementById('studentOverviewTotal');
const studentOverviewCompleted = document.getElementById('studentOverviewCompleted');
const studentOverviewPending = document.getElementById('studentOverviewPending');
const studentOverviewUpcomingToday = document.getElementById('studentOverviewUpcomingToday');
const notifToast = document.getElementById('notifToast');
const toastTitle = document.getElementById('toastTitle');
const toastBody = document.getElementById('toastBody');
const closeToast = document.getElementById('closeToast');
const historySection = document.getElementById('history');
const contentContainer = document.querySelector('.main .content');
const overviewSection = document.querySelector('.dashboard-overview');
const dashboardLink = document.getElementById('dashboardLink');
const myConsultationsSection = document.getElementById('my-consultations');
const myConsultationsLink = document.getElementById('myConsultationsLink');
const overviewViewAllBtn = document.getElementById('overviewViewAllBtn');
const historyLink = document.getElementById('historyLink');
const requestSection = document.getElementById('request-consultation');
const requestConsultationLink = document.getElementById('requestConsultationLink');
const requestCancelBtn = document.getElementById('requestCancelBtn');
const requestCloseBtn = document.getElementById('requestCloseBtn');
const historyOpenBtns = document.querySelectorAll('.history-open-btn');
const closeHistoryModal = document.getElementById('closeHistoryModal');
const contentHeaderSection = document.querySelector('.content-header');
const historyDateRange = document.getElementById('historyDateRange');
const historyType = document.getElementById('historyType');
const historyMode = document.getElementById('historyMode');
const historyYearInput = document.getElementById('historyYearInput');
const historySemButtons = Array.from(document.querySelectorAll('.semester-btn'));
const historyExport = document.getElementById('historyExport');
const monthPickerContainer = document.getElementById('monthPickerContainer');
const historyAcademicYears = @json($historyAcademicYears ?? []);

// Generate years 2026-2027, 2028-2029, ..., 9090-9091 (every other year)
function generateYearRange(start, end, step = 1) {
    const years = [];
    for (let y = start; y <= end; y += step) {
        years.push(`${y}-${y + 1}`);
    }
    return years;
}

// Merge generated years with DB-sourced years, remove duplicates, and sort
const generatedYears = generateYearRange(2026, 9090, 2);
const allYears = [...new Set([...historyAcademicYears, ...generatedYears])];
allYears.sort((a, b) => {
    const aStart = parseInt(a.split('-')[0]);
    const bStart = parseInt(b.split('-')[0]);
    return aStart - bStart; // ascending order (oldest first)
});

let currentHistoryYearIndex = -1; // -1 means 'All Years' (not selected)
let historyYearFilterEnabled = false; // only apply year filter after user explicitly picks a year
const monthSelect = document.getElementById('historyMonthSelect');
const historyRows = Array.from(document.querySelectorAll('.history-row-item'));

// Month mapping for semesters
const semesterMonths = {
    '1': [
        { name: 'August', num: 8 },
        { name: 'September', num: 9 },
        { name: 'October', num: 10 },
        { name: 'November', num: 11 },
        { name: 'December', num: 12 }
    ],
    '2': [
        { name: 'January', num: 1 },
        { name: 'February', num: 2 },
        { name: 'March', num: 3 },
        { name: 'April', num: 4 },
        { name: 'May', num: 5 }
    ]
};
const allHistoryMonths = [
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
    { name: 'December', num: 12 }
];

let selectedMonth = null;
const detailsModal = document.getElementById('detailsModal');
const detailsOpenBtns = document.querySelectorAll('.details-open-btn');
const closeDetailsModal = document.getElementById('closeDetailsModal');
const detailsSubtitle = document.getElementById('detailsSubtitle');
const detailsDate = document.getElementById('detailsDate');
const detailsDuration = document.getElementById('detailsDuration');
const detailsInstructor = document.getElementById('detailsInstructor');
const detailsMode = document.getElementById('detailsMode');
const detailsType = document.getElementById('detailsType');
const detailsStatus = document.getElementById('detailsStatus');
const detailsUpdated = document.getElementById('detailsUpdated');
const detailsActionsWrap = document.getElementById('detailsActionsWrap');
const detailsActionsContent = document.getElementById('detailsActionsContent');
const detailsSummaryWrap = document.getElementById('detailsSummaryWrap');
const detailsSummaryText = document.getElementById('detailsSummaryText');
const detailsTranscriptWrap = document.getElementById('detailsTranscriptWrap');
const detailsTranscriptText = document.getElementById('detailsTranscriptText');
const callModal = document.getElementById('callModal');
const callTimer = document.getElementById('callTimer');
const callStatusLabel = document.getElementById('callStatusLabel');
const localVideo = document.getElementById('localVideo');
const remoteVideo = document.getElementById('remoteVideo');
const toggleCameraBtn = document.getElementById('toggleCameraBtn');
const toggleMicBtn = document.getElementById('toggleMicBtn');
const endCallBtn = document.getElementById('endCallBtn');
const joinCallButtons = document.querySelectorAll('.join-call-btn');
// Incoming call elements & polling
const incomingCallModal = document.getElementById('incomingCallModal');
const incomingAvatar = document.getElementById('incomingAvatar');
const incomingInstructorNameEl = document.getElementById('incomingInstructorName');
const incomingCallBadge = document.getElementById('incomingCallBadge');
const acceptIncomingBtn = document.getElementById('acceptIncomingBtn');
const declineIncomingBtn = document.getElementById('declineIncomingBtn');
const closeIncomingBtn = document.getElementById('closeIncomingBtn');
const incomingButtonsContainer = document.getElementById('incomingButtonsContainer');
let lastConsultationId = null;

// Load shown consultations from localStorage to persist across page reloads
function getShownConsultations() {
    const stored = localStorage.getItem('shownIncomingConsultations');
    return stored ? JSON.parse(stored) : [];
}

function markConsultationAsShown(consultationId) {
    const shown = getShownConsultations();
    if (!shown.includes(consultationId)) {
        shown.push(consultationId);
        localStorage.setItem('shownIncomingConsultations', JSON.stringify(shown));
    }
}

function isConsultationShown(consultationId) {
    return getShownConsultations().includes(consultationId);
}

function clearShownConsultations() {
    localStorage.removeItem('shownIncomingConsultations');
}

function openFeedbackModal(data) {
    if (!feedbackModal || !overlay) return;

    if (detailsModal && detailsModal.classList.contains('open')) {
        closeDetails();
    }

    document.getElementById('feedbackConsultationId').value = data.id || '';
    document.getElementById('feedbackInstructorName').textContent = data.instructor || 'Instructor';
    document.getElementById('feedbackConsultationType').textContent = data.type || '—';

    feedbackModal.classList.add('active');
    overlay.classList.add('active');
}

function bindFeedbackButtons(root = document) {
    root.querySelectorAll('.feedback-open-btn').forEach((btn) => {
        if (btn.__feedbackBound) return;
        btn.__feedbackBound = true;

        btn.addEventListener('click', (event) => {
            event.preventDefault();
            openFeedbackModal({
                id: btn.dataset.id,
                instructor: btn.dataset.instructor,
                type: btn.dataset.type,
            });
        });
    });
}

function bindJoinCallButtons(root = document) {
    root.querySelectorAll('.join-call-btn').forEach((btn) => {
        if (btn.__joinBound) return;
        btn.__joinBound = true;

        btn.addEventListener('click', () => {
            const mode = (btn.dataset.mode || '').toLowerCase();
            if (!mode.includes('video')) return;
            startVideoCall(btn.dataset.consultationId);
        });
    });
}

function bindCancelButtons(root = document) {
    root.querySelectorAll('.student-cancel-form').forEach((form) => {
        if (form.__cancelBound) return;
        form.__cancelBound = true;

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const consultationId = form.dataset.consultationId || '';
            const consultationItem = form.closest('.consultation-item');
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Cancelling...';
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                    },
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data?.message || 'Unable to cancel consultation.');
                }

                if (consultationItem && data?.consultation) {
                    updateConsultationItemStatus(consultationItem, data.consultation);
                }

                if (consultationId) {
                    studentConsultationStateMap.set(Number(consultationId), 'cancelled');
                }

                if (toastTitle && toastBody && notifToast) {
                    toastTitle.textContent = 'Consultation Cancelled';
                    toastBody.textContent = data?.message || 'Consultation request cancelled.';
                    notifToast.classList.add('show');
                    setTimeout(() => notifToast.classList.remove('show'), 4000);
                }

                if (typeof pollStudentNotifications === 'function') {
                    pollStudentNotifications();
                }
            } catch (error) {
                if (toastTitle && toastBody && notifToast) {
                    toastTitle.textContent = 'Cancellation Failed';
                    toastBody.textContent = error?.message || 'Unable to cancel consultation.';
                    notifToast.classList.add('show');
                    setTimeout(() => notifToast.classList.remove('show'), 5000);
                }

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Cancel';
                }
            }
        });
    });
}

function setDetailsText(wrap, textNode, value, fallbackText) {
    if (!wrap || !textNode) return;

    const cleanedValue = String(value || '').trim();
    wrap.style.display = 'block';
    textNode.textContent = cleanedValue || fallbackText;
}

function setDetailsCardState(card, label, value) {
    if (!card) return;

    const cleanedValue = String(value || '').trim();
    if (!cleanedValue) {
        card.style.display = 'none';
        return;
    }

    card.style.display = 'flex';
    card.textContent = `${label}: ${cleanedValue}`;
}

function hideDetailsCard(card) {
    if (!card) return;
    card.style.display = 'none';
}

function setDetailsActions(actionHtml) {
    if (!detailsActionsWrap || !detailsActionsContent) return;

    const cleanedHtml = String(actionHtml || '').trim();
    if (!cleanedHtml) {
        detailsActionsWrap.style.display = 'none';
        detailsActionsContent.innerHTML = '';
        return;
    }

    detailsActionsWrap.style.display = 'block';
    detailsActionsContent.innerHTML = cleanedHtml;
    bindFeedbackButtons(detailsActionsContent);
    bindJoinCallButtons(detailsActionsContent);
    bindCancelButtons(detailsActionsContent);
}

function bindDetailsButtons(root = document) {
    root.querySelectorAll('.details-open-btn, .cc-mobile-details-btn').forEach((btn) => {
        if (btn.__detailsBound) return;
        btn.__detailsBound = true;

        btn.addEventListener('click', (event) => {
            event.preventDefault();

            let actionHtml = '';
            const actionSourceId = btn.dataset.actionSource || '';
            if (actionSourceId) {
                const actionSource = document.getElementById(actionSourceId);
                actionHtml = actionSource ? actionSource.innerHTML : '';
            }

            openDetailsModal({
                type: btn.dataset.type || '—',
                mode: btn.dataset.mode || '—',
                date: btn.dataset.date || '—',
                time: btn.dataset.time || '—',
                instructor: btn.dataset.instructor || 'Instructor',
                duration: btn.dataset.duration || '—',
                status: btn.dataset.status || '—',
                updated: btn.dataset.updated || '—',
                showStatusUpdated: btn.dataset.showStatusUpdated === 'true',
                summary: btn.dataset.summary || '',
                transcript: btn.dataset.transcript || '',
                actionHtml,
            });

            if (btn.dataset.id) {
                refreshDetailsData(btn.dataset.id);
            }
        });
    });
}

async function checkIncoming() {
    if (!incomingCallModal) return;
    try {
        const res = await fetch('{{ url('/student/incoming-session') }}');
        if (!res.ok) return;
        const data = await res.json();
        const c = data?.consultation ?? null;

        // No active in_progress session
        if (!c) {
            // Reset seen-call flags so the same consultation ID can trigger
            // incoming modal again on the instructor's next call attempt.
            clearShownConsultations();

            // only update if it's a DIFFERENT consultation (session ended)
            if (lastConsultationId !== null) {
                // session ended, keep modal visible but hide action buttons
                if (incomingCallBadge) {
                    incomingCallBadge.textContent = 'Session Ended';
                    incomingCallBadge.style.background = '#fee2e2';
                    incomingCallBadge.style.color = '#991b1b';
                }
                if (incomingButtonsContainer) {
                    incomingButtonsContainer.style.display = 'none';
                }
                lastConsultationId = null;
            }
            return;
        }

        // Only show for video modes
        if (!String(c.mode || '').toLowerCase().includes('video')) return;

        // ONLY show if this is a NEW consultation (never shown before)
        if (isConsultationShown(c.id)) {
            // already shown before, keep it visible but don't poll again
            lastConsultationId = c.id;
            return;
        }

        // First time seeing this consultation - show the modal
        incomingInstructorNameEl.textContent = c.instructor_name || 'Instructor';
        incomingAvatar.textContent = c.instructor_initials || 'IN';
        if (incomingCallBadge) {
            incomingCallBadge.textContent = 'Incoming Video Call';
            incomingCallBadge.style.background = '#eef2ff';
            incomingCallBadge.style.color = '#6b7280';
        }
        if (incomingButtonsContainer) {
            incomingButtonsContainer.style.display = 'flex';
        }
        markConsultationAsShown(c.id);
        lastConsultationId = c.id;
        showIncomingModal();

        acceptIncomingBtn.onclick = () => {
            // Hide modal and start call
            hideIncomingModal();
            try { startVideoCall(c.id); } catch (e) { /* ignore */ }
        };

        declineIncomingBtn.onclick = () => {
            // Show confirmation modal
            showDeclineConfirmation();
        };

    } catch (e) {
        // ignore
    }
}

function showIncomingModal() {
    if (!incomingCallModal) return;
    incomingCallModal.style.display = 'block';
    incomingCallModal.setAttribute('aria-hidden', 'false');
}

function hideIncomingModal() {
    if (!incomingCallModal) return;
    incomingCallModal.style.display = 'none';
    incomingCallModal.setAttribute('aria-hidden', 'true');
}

// Close button handler
if (closeIncomingBtn) {
    closeIncomingBtn.addEventListener('click', () => {
        hideIncomingModal();
    });
}

// Keep session alive by making periodic requests
async function keepSessionAlive() {
    try {
        // Simple GET request to a lightweight endpoint to keep session active
        await fetch('{{ url('/student/incoming-session') }}');
    } catch (e) {
        // ignore errors
    }
}

// Keep session alive every 4 minutes
setInterval(keepSessionAlive, 4 * 60 * 1000);

setInterval(checkIncoming, 3000);
checkIncoming();
const latestNotification = @json($notifications->firstWhere('is_read', false));
const unreadCount = @json($unreadCount);
const flashSuccess = @json($flashSuccess);
const studentToastUserId = @json(auth()->id());

if (menuBtn && sidebar) {
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

function showStudentSection(section, options = {}) {
    const shouldScroll = options.scroll !== false;
    const hideHeader = section === 'request' || section === 'my';

    if (contentHeaderSection) {
        contentHeaderSection.style.display = hideHeader ? 'none' : '';
    }

    if (overviewSection) {
        overviewSection.classList.toggle('is-hidden', section !== 'dashboard');
    }
    if (requestSection) {
        requestSection.classList.toggle('is-hidden', section !== 'request');
    }
    if (myConsultationsSection) {
        myConsultationsSection.classList.toggle('is-hidden', section !== 'my');
    }
    if (historySection) {
        const isHistory = section === 'history';
        historySection.classList.toggle('is-hidden', !isHistory);
        historySection.setAttribute('aria-hidden', isHistory ? 'false' : 'true');
    }

    const isIconOnlySection = false;
    setHistorySidebarIconOnly(isIconOnlySection);
    setHistoryOnlyMode(section === 'history');

    // Ensure dashboard layout always returns to clean default state.
    if (section === 'dashboard') {
        if (sidebar) sidebar.classList.remove('icon-only');
        if (contentContainer) contentContainer.classList.remove('history-only');
    }

    let target = overviewSection;
    if (section === 'request') target = requestSection;
    if (section === 'my') target = myConsultationsSection;
    if (section === 'history') target = historySection;
    if (section === 'dashboard') target = contentHeaderSection || overviewSection;
    if (shouldScroll && target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
}

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
            updateStudentNotificationBadge(data?.unreadNotifications || 0);
            renderStudentNotificationList(data?.notifications || []);
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

if (notificationList) {
    notificationList.addEventListener('click', (event) => {
        const target = event.target;
        if (target && target.classList.contains('dismiss-btn')) {
            const item = target.closest('.notification-item');
            if (item) item.remove();
        }
    });
}

if (openFeedbackBtn) {
    openFeedbackBtn.addEventListener('click', () => {
        feedbackModal.classList.add('active');
        overlay.classList.add('active');
    });
}

if (closeFeedbackBtn) {
    closeFeedbackBtn.addEventListener('click', () => {
        feedbackModal.classList.remove('active');
        overlay.classList.remove('active');
    });
}

if (cancelFeedbackBtn) {
    cancelFeedbackBtn.addEventListener('click', () => {
        feedbackModal.classList.remove('active');
        overlay.classList.remove('active');
    });
}

if (totalConsultationsCard) {
    totalConsultationsCard.addEventListener('click', () => {
        if (typeof showHistoryModal === 'function') {
            showHistoryModal();
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            return;
        }
        if (historySection) {
            historySection.classList.toggle('is-hidden');
            if (!historySection.classList.contains('is-hidden')) {
                historySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
}


if (requestConsultationLink && requestSection) {
    requestConsultationLink.addEventListener('click', (event) => {
        event.preventDefault();
        showStudentSection('request');
    });
}

if (historyLink) {
    historyLink.addEventListener('click', (event) => {
        event.preventDefault();
        showHistoryModal();
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    });
}

if (myConsultationsLink && myConsultationsSection) {
    myConsultationsLink.addEventListener('click', (event) => {
        event.preventDefault();
        showStudentSection('my');
    });
}

if (overviewViewAllBtn && myConsultationsSection) {
    overviewViewAllBtn.addEventListener('click', () => {
        showStudentSection('my');
    });
}

// Handle exit button for My Consultations
const exitMyConsultationsBtn = document.getElementById('exitMyConsultationsBtn');
if (exitMyConsultationsBtn) {
    exitMyConsultationsBtn.addEventListener('click', () => {
        showStudentSection('dashboard');
    });
}

if (requestCancelBtn && requestSection) {
    requestCancelBtn.addEventListener('click', () => {
        showStudentSection('dashboard');
    });
}

if (requestCloseBtn && requestSection) {
    requestCloseBtn.addEventListener('click', () => {
        showStudentSection('dashboard');
    });
}

if (dashboardLink) {
    dashboardLink.addEventListener('click', (event) => {
        event.preventDefault();
        showStudentSection('dashboard');
    });
}

function showHistoryModal() {
    showStudentSection('history');
    if (historyYearInput) {
        historyYearInput.disabled = false;
        historyYearInput.readOnly = false;
    }
}

function hideHistoryModal() {
    showStudentSection('dashboard');
}

if (historyOpenBtns.length) {
    historyOpenBtns.forEach((btn) => {
        btn.addEventListener('click', showHistoryModal);
    });
}

if (historyYearInput) {
    historyYearInput.disabled = false;
    historyYearInput.readOnly = false;
    historyYearInput.addEventListener('keydown', (event) => {
        event.stopPropagation();
    });
}

if (closeHistoryModal) {
    closeHistoryModal.addEventListener('click', hideHistoryModal);
}

const initialStudentHash = window.location.hash;

if (initialStudentHash === '#history') {
    showStudentSection('history', { scroll: false });
} else if (initialStudentHash === '#my-consultations') {
    try {
        window.history.replaceState(null, '', `${window.location.pathname}${window.location.search}`);
    } catch (_) {
        window.location.hash = '';
    }
    showStudentSection('my', { scroll: false });
} else if (initialStudentHash === '#request-consultation') {
    try {
        window.history.replaceState(null, '', `${window.location.pathname}${window.location.search}`);
    } catch (_) {
        window.location.hash = '';
    }
    showStudentSection('dashboard', { scroll: false });
} else {
    showStudentSection('dashboard', { scroll: false });
}




if (historyExport) {
    historyExport.addEventListener('click', () => {
        const visibleRows = historyRows.filter((row) => row.style.display !== 'none');
        const exportRows = visibleRows.length ? visibleRows : historyRows;
        const rowsHtml = exportRows.map((row) => {
            const cells = Array.from(row.children).map((cell) => cell.textContent.replace(/\s+/g, ' ').trim());
            const dateTime = cells[0] || '';
            const instructor = cells[1] || '';
            const type = cells[2] || '';
            const mode = cells[3] || '';
            const duration = cells[4] || '';
            const records = cells[5] || '';
            return `
                <tr>
                    <td>${dateTime}</td>
                    <td>${instructor}</td>
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
                            <th>Instructor</th>
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

function openDetailsModal(data) {
    if (!detailsModal) return;
    const typeText = data.type || '--';
    const modeText = data.mode || '--';
    const dateText = data.date || '--';
    const timeText = data.time || '--';
    const durationText = data.duration || '--';
    const instructorText = data.instructor || '--';

    if (detailsSubtitle) detailsSubtitle.textContent = `${typeText} - ${modeText} Session`;
    if (detailsDate) detailsDate.textContent = `Date & Time: ${dateText} at ${timeText}`;
    if (detailsDuration) detailsDuration.textContent = `Duration: ${durationText}`;
    if (detailsInstructor) detailsInstructor.textContent = `Instructor: ${instructorText}`;
    if (detailsMode) detailsMode.textContent = `Mode: ${modeText}`;
    if (detailsType) detailsType.textContent = `Type: ${typeText}`;
    if (data.showStatusUpdated) {
        setDetailsCardState(detailsStatus, 'Status', data.status || '');
        setDetailsCardState(detailsUpdated, 'Updated', data.updated || '');
    } else {
        hideDetailsCard(detailsStatus);
        hideDetailsCard(detailsUpdated);
    }

    setDetailsActions(data.actionHtml || '');
    setDetailsText(detailsSummaryWrap, detailsSummaryText, data.summary || '', 'Summary not yet available.');
    setDetailsText(detailsTranscriptWrap, detailsTranscriptText, data.transcript || '', 'Action taken not yet available.');

    detailsModal.classList.add('open');
    detailsModal.setAttribute('aria-hidden', 'false');
}

async function refreshDetailsData(consultationId) {
    if (!consultationId) return;
    try {
        const response = await fetch(`{{ url('/consultations') }}/${consultationId}/details`);
        if (!response.ok) return;
        const data = await response.json();
        if (detailsSubtitle) detailsSubtitle.textContent = `${data.type || '--'} - ${data.mode || '--'} Session`;
        if (detailsDate) detailsDate.textContent = `Date & Time: ${data.date || '--'} at ${data.time || '--'}`;
        if (detailsDuration) detailsDuration.textContent = `Duration: ${data.duration || '--'}`;
        if (detailsInstructor) detailsInstructor.textContent = `Instructor: ${data.instructor || '--'}`;
        if (detailsMode) detailsMode.textContent = `Mode: ${data.mode || '--'}`;
        if (detailsType) detailsType.textContent = `Type: ${data.type || '--'}`;
        setDetailsText(detailsSummaryWrap, detailsSummaryText, data.summary || '', 'Summary not yet available.');
        setDetailsText(detailsTranscriptWrap, detailsTranscriptText, data.transcript || '', 'Action taken not yet available.');
    } catch (_) {
        // ignore
    }
}

function closeDetails() {
    if (!detailsModal) return;
    detailsModal.classList.remove('open');
    detailsModal.setAttribute('aria-hidden', 'true');
}

bindDetailsButtons();
bindFeedbackButtons();
bindCancelButtons();

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
let currentDeviceSessionId = null;
let callAnswered = false;
let isEndingCall = false;
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
    if (callTimerInterval) {
        clearInterval(callTimerInterval);
        callTimerInterval = null;
    }
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
    if (callTimer) callTimer.textContent = '00:00';
    callAnswered = false;
    setCallStatusLabel('Video Session');
    closeCallModalUI();
}

function stopCall() {
    // Show confirmation dialog
    showEndCallConfirmation();
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

async function markConsultationAnswered(consultationId) {
    if (!consultationId) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    await fetch(`{{ url('/consultations') }}/${consultationId}/answer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        },
        body: JSON.stringify({}),
    });
}

async function finalizeCall(consultationId) {
    if (!consultationId) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    await fetch(`{{ url('/consultations') }}/${consultationId}/end-call`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        },
        body: JSON.stringify({}),
    });
}

async function declineIncomingCall(consultationId) {
    if (!consultationId) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        await fetch("{{ url('/webrtc/signal') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({
                consultation_id: consultationId,
                type: 'disconnect',
                payload: { reason: 'declined' },
                device_session_id: null,
            }),
        });
    } catch (_) {
        // ignore
    }

    await fetch(`{{ url('/consultations') }}/${consultationId}/decline-call`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        },
        body: JSON.stringify({}),
    });
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
        const reason = String(payload?.reason || '');
        const message = reason === 'no_answer'
            ? 'Instructor ended this call attempt.'
            : 'Call ended by the other participant.';
        actuallyStopCall();
        const toastMsg = document.createElement('div');
        toastMsg.style.cssText = 'position:fixed;top:16px;right:16px;background:#fff3cd;border:1px solid #ffc107;color:#856404;padding:12px 16px;border-radius:8px;z-index:9999;font-weight:600;';
        toastMsg.textContent = message;
        document.body.appendChild(toastMsg);
        setTimeout(() => toastMsg.remove(), 5000);
        if (reason === 'call_ended' || reason === 'no_answer' || reason === 'declined') {
            setTimeout(() => {
                try { pollStudentConsultationUpdates(); } catch (_) { /* ignore */ }
                try { checkIncoming(); } catch (_) { /* ignore */ }
            }, 150);
        }
        return;
    }

    if (!peerConnection) {
        createPeerConnection();
    }

    if (type === 'offer') {
        await markConsultationAnswered(currentConsultationId);
        await peerConnection.setRemoteDescription(new RTCSessionDescription(payload));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        await sendSignal('answer', answer);
        callAnswered = true;
        setCallStatusLabel('Video Session');
        if (!callStartAt) {
            startCallTimer();
        }
    }

    if (type === 'answer') {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(payload));
        callAnswered = true;
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

async function startVideoCall(consultationId) {
    if (!consultationId) return;
    if (currentConsultationId && currentConsultationId !== consultationId) {
        actuallyStopCall();
    }
    currentConsultationId = consultationId;
    currentDeviceSessionId = DEVICE_SESSION_ID;
    callAnswered = false;
    setCallStatusLabel('Connecting...');
    openCallModal();

    if (!window.isSecureContext && location.hostname !== 'localhost') {
        actuallyStopCall();
        alert('Camera/Mic requires HTTPS on other devices. Open this site via https:// to join video calls.');
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

    pollTimer = setInterval(pollSignals, 2000);
}

// --- History filtering (search, semester, academic year) ---
function getSemesterFromDate(dateStr) {
    try {
        const d = new Date(dateStr);
        const m = d.getMonth() + 1; // 1-12
        if (m >= 8 && m <= 12) return '1'; // Aug-Dec -> 1st sem
        if (m >= 1 && m <= 5) return '2'; // Jan-May -> 2nd sem
        return 'other';
    } catch (_) {
        return 'other';
    }
}

function getAcademicYearFromDate(dateStr) {
    try {
        const d = new Date(dateStr);
        const m = d.getMonth() + 1;
        const y = d.getFullYear();
        if (m >= 8) return `${y}-${y+1}`;
        if (m <= 5) return `${y-1}-${y}`;
        return '';
    } catch (_) {
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

function renderMonthSelector(semester) {
    if (!monthSelect) return;
    monthSelect.innerHTML = '<option value="">All months</option>';
    selectedMonth = null;

    const months = (!semester || semester === 'all')
        ? allHistoryMonths
        : (semesterMonths[semester] || []);
    months.forEach((month) => {
        const opt = document.createElement('option');
        opt.value = month.num;
        opt.textContent = month.name;
        monthSelect.appendChild(opt);
    });

    monthSelect.value = '';
    monthSelect.onchange = () => {
        selectedMonth = monthSelect.value ? parseInt(monthSelect.value) : null;
        applyHistoryFilters();
    };

    monthPickerContainer.style.display = 'block';
    applyHistoryFilters();
}

function applyHistoryFilters() {
    filterHistoryRows();
}

// search removed: no input listener needed

// Academic Year Text Input - Handle year selection
if (historyYearInput) {
    historyYearInput.addEventListener('input', () => {
        const inputValue = historyYearInput.value.trim();

        if (!inputValue) {
            // Empty input = All Years
            currentHistoryYearIndex = -1;
            historyYearFilterEnabled = false;
        } else {
            // Try to find matching year in allYears array
            const foundIndex = allYears.findIndex(year => year.includes(inputValue));
            if (foundIndex !== -1) {
                currentHistoryYearIndex = foundIndex;
                historyYearFilterEnabled = true;
            } else {
                // No match found, show all years
                currentHistoryYearIndex = -1;
                historyYearFilterEnabled = false;
            }
        }
        applyHistoryFilters();
    });
}

historySemButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
        historySemButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderMonthSelector(btn.dataset.sem);
    });
});

// default select 'All' semester
const semAllBtn = document.getElementById('semAll');
if (semAllBtn) semAllBtn.classList.add('active');

// Confirmation modal handlers
const declineConfirmModal = document.getElementById('declineConfirmModal');
const declineConfirmOverlay = document.getElementById('declineConfirmOverlay');
const endCallConfirmModal = document.getElementById('endCallConfirmModal');
const endCallConfirmOverlay = document.getElementById('endCallConfirmOverlay');
const declineConfirmYes = document.getElementById('declineConfirmYes');
const declineConfirmNo = document.getElementById('declineConfirmNo');
const endCallConfirmYes = document.getElementById('endCallConfirmYes');
const endCallConfirmNo = document.getElementById('endCallConfirmNo');

function showDeclineConfirmation() {
    if (declineConfirmModal && declineConfirmOverlay) {
        declineConfirmModal.style.display = 'block';
        declineConfirmOverlay.style.display = 'block';
    }
}

function hideDeclineConfirmation() {
    if (declineConfirmModal && declineConfirmOverlay) {
        declineConfirmModal.style.display = 'none';
        declineConfirmOverlay.style.display = 'none';
    }
}

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

if (declineConfirmYes) {
    declineConfirmYes.addEventListener('click', async () => {
        hideDeclineConfirmation();
        const consultationId = Number(lastConsultationId || 0);
        try {
            if (consultationId > 0) {
                await declineIncomingCall(consultationId);
            }
        } catch (_) {
            // ignore
        }
        hideIncomingModal();
        clearShownConsultations();
        lastConsultationId = null;
    });
}

if (declineConfirmNo) {
    declineConfirmNo.addEventListener('click', () => {
        hideDeclineConfirmation();
    });
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
            try {
                await sendSignal('disconnect', { reason: 'call_ended' });
            } catch (_) {
                // ignore
            }
            if (callAnswered) {
                await finalizeCall(consultationId);
            }
        } catch (_) {
            // ignore
        } finally {
            isEndingCall = false;
            actuallyStopCall();
            try { pollStudentConsultationUpdates(); } catch (_) { /* ignore */ }
            try { checkIncoming(); } catch (_) { /* ignore */ }
        }
    });
}

if (endCallConfirmNo) {
    endCallConfirmNo.addEventListener('click', () => {
        hideEndCallConfirmation();
    });
}

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
if (callModal) {
    callModal.addEventListener('click', (event) => {
        if (event.target === callModal) {
            stopCall();
        }
    });
}

bindJoinCallButtons();

// Request consultation (inline)
const requestInstructorCards = document.querySelectorAll('#requestInstructorGrid .request-card-item');
const requestInstructorPaginationInfo = document.getElementById('requestInstructorPaginationInfo');
const requestInstructorPageNumbers = document.getElementById('requestInstructorPageNumbers');
const prevRequestInstructorBtn = document.getElementById('prevRequestInstructorBtn');
const nextRequestInstructorBtn = document.getElementById('nextRequestInstructorBtn');
const requestDateHint = document.getElementById('requestDateHint');
const requestConsultationDate = document.getElementById('requestConsultationDate');
const requestDateTrigger = document.getElementById('requestDateTrigger');
const requestConsultationTime = document.getElementById('requestConsultationTime');
const requestAvailabilities = @json($availabilities ?? collect());
const requestBookedSlots = @json($bookedSlots ?? collect());
let requestSelectedInstructorId = null;
let preferredAutoStart = null;
const requestInstructorItemsPerPage = 3;
let currentRequestInstructorPage = 1;
const preferredDayButtons = document.querySelectorAll('.preferred-day-btn');
const preferredTimeDisplay = document.getElementById('preferredTimeDisplay');

let requestDatePicker = null;

function buildDateDisableFn(daysSet) {
    return (date) => {
        if (!daysSet || !daysSet.size) return true;
        const dayName = date.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
        return !daysSet.has(dayName);
    };
}

if (typeof flatpickr !== 'undefined' && requestConsultationDate) {
    requestDatePicker = flatpickr(requestConsultationDate, {
        dateFormat: 'Y-m-d',
        minDate: 'today',
        disable: [() => true],
        onChange: () => {
            requestConsultationDate.dispatchEvent(new Event('change'));
        },
    });
}
const requestModeCards = document.querySelectorAll('#requestModeGrid .request-mode-card');
const reviewLine1 = document.getElementById('reviewLine1');
const reviewLine2 = document.getElementById('reviewLine2');
const reviewLine3 = document.getElementById('reviewLine3');
const reviewLine4 = document.getElementById('reviewLine4');
const reviewLine5 = document.getElementById('reviewLine5');

function getRequestInstructorTotals() {
    const totalItems = requestInstructorCards.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / requestInstructorItemsPerPage));
    return { totalItems, totalPages };
}

function createRequestInstructorPagination() {
    if (!requestInstructorPageNumbers) return;
    const { totalItems, totalPages } = getRequestInstructorTotals();
    requestInstructorPageNumbers.innerHTML = '';

    if (totalItems <= requestInstructorItemsPerPage) {
        if (prevRequestInstructorBtn) prevRequestInstructorBtn.style.display = 'none';
        if (nextRequestInstructorBtn) nextRequestInstructorBtn.style.display = 'none';
    } else {
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pagination-page-btn' + (i === currentRequestInstructorPage ? ' active' : '');
            btn.textContent = i;
            btn.addEventListener('click', () => showRequestInstructorPage(i));
            requestInstructorPageNumbers.appendChild(btn);
        }

        if (prevRequestInstructorBtn) prevRequestInstructorBtn.style.display = currentRequestInstructorPage > 1 ? 'block' : 'none';
        if (nextRequestInstructorBtn) nextRequestInstructorBtn.style.display = currentRequestInstructorPage < totalPages ? 'block' : 'none';
    }

    if (requestInstructorPaginationInfo) {
        const start = totalItems > 0 ? ((currentRequestInstructorPage - 1) * requestInstructorItemsPerPage) + 1 : 0;
        const end = totalItems > 0 ? Math.min(currentRequestInstructorPage * requestInstructorItemsPerPage, totalItems) : 0;
        requestInstructorPaginationInfo.textContent = totalItems > 0
            ? `Showing ${start} to ${end} of ${totalItems} instructors`
            : 'No instructors found';
    }
}

function showRequestInstructorPage(pageNum) {
    const { totalItems, totalPages } = getRequestInstructorTotals();
    if (!totalItems) return;
    currentRequestInstructorPage = Math.min(Math.max(1, pageNum), totalPages);

    const start = (currentRequestInstructorPage - 1) * requestInstructorItemsPerPage;
    const end = start + requestInstructorItemsPerPage;

    requestInstructorCards.forEach((card, index) => {
        card.style.display = (index >= start && index < end) ? 'flex' : 'none';
    });

    createRequestInstructorPagination();
}

if (requestInstructorCards.length) {
    requestInstructorCards.forEach(card => {
        const input = card.querySelector('input');
        card.addEventListener('click', () => {
            requestInstructorCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            input.checked = true;
            requestSelectedInstructorId = input.value;
            const name = card.querySelector('.request-card-name')?.textContent || '—';
            if (reviewLine1) reviewLine1.textContent = `Instructor: ${name}`;

            if (requestConsultationDate) {
                requestConsultationDate.disabled = false;
                requestConsultationDate.value = '';
            }
            if (requestDateTrigger) {
                requestDateTrigger.disabled = false;
            }

            if (requestConsultationTime) {
                requestConsultationTime.value = '';
            }

            updatePreferredDays(requestSelectedInstructorId);
            updateDatePickerForInstructor(requestSelectedInstructorId);
            renderRequestSlotPlaceholder();
        });
    });

    showRequestInstructorPage(1);
}

if (prevRequestInstructorBtn) {
    prevRequestInstructorBtn.addEventListener('click', () => {
        if (currentRequestInstructorPage > 1) {
            showRequestInstructorPage(currentRequestInstructorPage - 1);
        }
    });
}

if (nextRequestInstructorBtn) {
    nextRequestInstructorBtn.addEventListener('click', () => {
        const { totalPages } = getRequestInstructorTotals();
        if (currentRequestInstructorPage < totalPages) {
            showRequestInstructorPage(currentRequestInstructorPage + 1);
        }
    });
}

if (requestDateTrigger && requestConsultationDate) {
    requestDateTrigger.addEventListener('click', () => {
        if (requestDateTrigger.disabled || requestConsultationDate.disabled) return;
        if (requestDatePicker && typeof requestDatePicker.open === 'function') {
            requestDatePicker.open();
            return;
        }
        if (typeof requestConsultationDate.showPicker === 'function') {
            requestConsultationDate.showPicker();
            return;
        }
        requestConsultationDate.focus();
    });
}

if (!requestInstructorCards.length && requestInstructorPaginationInfo) {
    requestInstructorPaginationInfo.textContent = 'No instructors found';
}

if (requestConsultationDate) {
    requestConsultationDate.addEventListener('change', () => {
        if (requestConsultationTime) requestConsultationTime.value = '';

        if (!requestSelectedInstructorId) {
            requestConsultationDate.value = '';
            renderRequestSlotPlaceholder();
            return;
        }

        const selectedDate = new Date(`${requestConsultationDate.value}T00:00:00`);
        if (Number.isNaN(selectedDate.getTime())) {
            renderRequestSlotPlaceholder();
            return;
        }

        if (selectedDate.getDay() === 0) {
            requestConsultationDate.value = '';
            requestConsultationDate.setCustomValidity('Sunday is not available. Please choose Monday to Saturday.');
            requestConsultationDate.reportValidity();
            requestConsultationDate.setCustomValidity('');
            renderRequestSlotPlaceholder();
            return;
        }

        const dayName = selectedDate.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
        const selectedDateKey = requestConsultationDate.value;
        const availableDays = getAvailableDays(requestSelectedInstructorId);

        if (!availableDays.has(dayName)) {
            requestConsultationDate.value = '';
            requestConsultationDate.setCustomValidity('Selected date is not available for this instructor.');
            requestConsultationDate.reportValidity();
            requestConsultationDate.setCustomValidity('');
            renderRequestSlotPlaceholder();
            return;
        }

        const occupiedTimes = (requestBookedSlots[requestSelectedInstructorId] && requestBookedSlots[requestSelectedInstructorId][selectedDateKey])
            ? requestBookedSlots[requestSelectedInstructorId][selectedDateKey]
            : [];

        const slots = (requestAvailabilities[requestSelectedInstructorId] || []).filter(slot =>
            (slot.available_day || '').toLowerCase() === dayName
        ).filter(slot => !occupiedTimes.includes(requestNormalizeTime(slot.start_time)));

        preferredDayButtons.forEach((btn) => {
            btn.classList.toggle('active', btn.dataset.day === dayName);
        });

        renderRequestSlots(slots, selectedDateKey);
    });
}

function renderRequestSlots(slots, selectedDateKey = '') {
    if (!slots.length) {
        if (requestConsultationTime) requestConsultationTime.value = '';
        if (reviewLine2) {
            const dateLabel = selectedDateKey || requestConsultationDate?.value || '--';
            reviewLine2.textContent = `Date & Time: ${dateLabel} --`;
        }
        return;
    }

    const slot = slots[0];
    if (requestConsultationTime) requestConsultationTime.value = requestNormalizeTime(slot.start_time);
    if (reviewLine2) {
        const endValue = slot.end_time || '';
        const timeLabel = endValue
            ? `${requestFormatTime12(slot.start_time)} to ${requestFormatTime12(endValue)}`
            : requestFormatTime12(slot.start_time);
        const dateLabel = selectedDateKey || requestConsultationDate?.value || '--';
        reviewLine2.textContent = `Date & Time: ${dateLabel} ${timeLabel}`;
    }
}

function renderRequestSlotPlaceholder() {
    if (requestConsultationTime) requestConsultationTime.value = '';
}

function requestNormalizeTime(time) {
    if (!time) return '';
    return String(time).slice(0, 5);
}

function requestFormatTime12(time) {
    const cleanTime = requestNormalizeTime(time);
    const parts = cleanTime.split(':');
    if (parts.length !== 2) return cleanTime;
    let hour = Number(parts[0]);
    const minute = parts[1];
    const suffix = hour >= 12 ? 'PM' : 'AM';
    hour = hour % 12;
    if (hour === 0) hour = 12;
    return `${hour}:${minute} ${suffix}`;
}

function requestFormatTime(time) {
    const cleanTime = requestNormalizeTime(time);
    const parts = cleanTime.split(':');
    if (parts.length !== 2) return cleanTime;
    const hour = Number(parts[0]);
    const minute = parts[1];
    return `${String(hour).padStart(2, '0')}:${minute}`;
}

function formatLocalDate(dateObj) {
    const year = dateObj.getFullYear();
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const day = String(dateObj.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function getNextDateForDay(targetDay) {
    const dayMap = {
        sunday: 0,
        monday: 1,
        tuesday: 2,
        wednesday: 3,
        thursday: 4,
        friday: 5,
        saturday: 6,
    };
    const targetIndex = dayMap[targetDay];
    if (typeof targetIndex !== 'number') return '';
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    let diff = (targetIndex - today.getDay() + 7) % 7;
    if (diff === 0) diff = 7;
    const next = new Date(today);
    next.setDate(today.getDate() + diff);
    return formatLocalDate(next);
}

function getPreferredSlotForDay(instructorId, dayName) {
    const slots = (requestAvailabilities[instructorId] || []).filter(slot =>
        (slot.available_day || '').toLowerCase() === dayName
    );
    if (!slots.length) return null;
    return slots[0];
}

function getAvailableDays(instructorId) {
    return new Set(
        (requestAvailabilities[instructorId] || [])
            .map(slot => String(slot.available_day || '').toLowerCase())
            .filter(Boolean)
    );
}

function formatAvailableDays(daysSet) {
    const order = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    const labels = {
        monday: 'Mon',
        tuesday: 'Tue',
        wednesday: 'Wed',
        thursday: 'Thu',
        friday: 'Fri',
        saturday: 'Sat',
    };
    return order.filter(day => daysSet.has(day)).map(day => labels[day]).join(', ');
}

function updateDatePickerForInstructor(instructorId) {
    if (!requestDatePicker) return;
    const daysSet = getAvailableDays(instructorId);
    requestDatePicker.set('disable', [buildDateDisableFn(daysSet)]);
    requestDatePicker.clear();
}

function updatePreferredDays(instructorId) {
    if (!preferredDayButtons.length) return;
    const daysAvailable = getAvailableDays(instructorId);
    preferredDayButtons.forEach(btn => {
        const dayName = btn.dataset.day;
        const hasSlots = daysAvailable.has(dayName);
        const hasBookableSlots = hasAvailableSlotsForDay(instructorId, dayName);

        btn.disabled = !hasSlots || !hasBookableSlots;
        btn.classList.remove('active');

        // Add tooltip
        if (btn.disabled && !hasSlots) {
            btn.title = 'No available slots this day';
        } else if (btn.disabled && !hasBookableSlots) {
            btn.title = 'Choose Another date';
        } else {
            btn.title = 'Click to select';
        }
    });
    if (preferredTimeDisplay) preferredTimeDisplay.textContent = 'Select a day';

    if (requestConsultationDate) {
        requestConsultationDate.disabled = daysAvailable.size === 0;
    }
    if (requestDateTrigger) {
        requestDateTrigger.disabled = daysAvailable.size === 0;
    }

    if (requestDateHint) {
        const label = formatAvailableDays(daysAvailable);
        requestDateHint.textContent = label
            ? `Choose a date that matches available days: ${label}.`
            : 'No available days for this instructor.';
    }

}

function hasAvailableSlotsForDay(instructorId, dayName) {
    // Get all available slots for this day from instructor
    const slotsByDay = (requestAvailabilities[instructorId] || []).filter(slot =>
        (slot.available_day || '').toLowerCase() === dayName
    );

    if (!slotsByDay.length) return false;

    // Get all booked consultations for this instructor
    const allBookedDates = new Set();
    for (const [dateKey, times] of Object.entries(requestBookedSlots[instructorId] || {})) {
        if (times && times.length > 0) {
            allBookedDates.add(dateKey);
        }
    }

    // For each day-of-week, check if there are ANY bookings
    // If yes, disable the entire day
    for (const bookedDate of allBookedDates) {
        try {
            const bookedDateObj = new Date(`${bookedDate}T00:00:00`);
            const bookedDayName = bookedDateObj.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();

            if (bookedDayName === dayName) {
                // If ANY consultation is booked on this day of week -> disable it
                return false;
            }
        } catch (e) {
            // ignore invalid dates
        }
    }

    return true;
}

preferredDayButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        if (!requestSelectedInstructorId) return;
        preferredDayButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const dayName = btn.dataset.day;
        const slot = getPreferredSlotForDay(requestSelectedInstructorId, dayName);
        if (preferredTimeDisplay) {
            if (slot) {
                const endValue = slot.end_time || '';
                const text = endValue
                    ? `${requestFormatTime12(slot.start_time)} - ${requestFormatTime12(endValue)}`
                    : requestFormatTime12(slot.start_time);
                preferredTimeDisplay.textContent = text;
            } else {
                preferredTimeDisplay.textContent = 'No slots';
            }
        }

        const nextDate = getNextDateForDay(dayName);
        if (requestConsultationDate && nextDate) {
            requestConsultationDate.value = nextDate;
            preferredAutoStart = slot ? requestNormalizeTime(slot.start_time) : null;
            requestConsultationDate.dispatchEvent(new Event('change'));
        }
    });
});

if (requestModeCards.length) {
    requestModeCards.forEach(card => {
        const input = card.querySelector('input');
        card.addEventListener('click', () => {
            requestModeCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            input.checked = true;
            const title = card.querySelector('.mode-title')?.textContent || '—';
            if (reviewLine4) reviewLine4.textContent = `Mode: ${title}`;
        });
    });
}

                    const notesField = document.querySelector('textarea[name="student_notes"]');
                    if (notesField) {
                        notesField.addEventListener('input', () => {
                            const value = notesField.value.trim() || '—';
                            if (reviewLine5) reviewLine5.textContent = `Notes: ${value}`;
                        });
                    }

                    // Handle form submission to validate all required fields
                    const requestForm = document.querySelector('form[action="{{ route("student.consultation.store") }}"]');
                    if (requestForm) {
                        let requestFormSubmitting = false;
                        const submitBtn = requestForm.querySelector('button[type="submit"]');

                        requestForm.addEventListener('submit', function (e) {
                            if (requestFormSubmitting) {
                                e.preventDefault();
                                return;
                            }

                            if (!requestForm.reportValidity()) {
                                return;
                            }

                            const instructorId = document.querySelector('input[name="instructor_id"]:checked')?.value;
                            const consultationDateInput = document.getElementById('requestConsultationDate');
                            const consultationTimeInput = document.getElementById('requestConsultationTime');
                            const consultationDate = consultationDateInput?.value || '';
                            let consultationTime = consultationTimeInput?.value || '';

                            // Ensure hidden time field is filled before submit.
                            if (!consultationTime && instructorId && consultationDate) {
                                const selectedDate = new Date(`${consultationDate}T00:00:00`);
                                if (!Number.isNaN(selectedDate.getTime())) {
                                    const dayName = selectedDate.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
                                    const occupiedTimes = (requestBookedSlots[instructorId] && requestBookedSlots[instructorId][consultationDate])
                                        ? requestBookedSlots[instructorId][consultationDate]
                                        : [];
                                    const slots = (requestAvailabilities[instructorId] || []).filter(slot =>
                                        (slot.available_day || '').toLowerCase() === dayName
                                    ).filter(slot => !occupiedTimes.includes(requestNormalizeTime(slot.start_time)));

                                    if (slots.length) {
                                        consultationTime = requestNormalizeTime(slots[0].start_time);
                                        if (consultationTimeInput) {
                                            consultationTimeInput.value = consultationTime;
                                        }
                                    }
                                }
                            }

                            if (!consultationTime) {
                                e.preventDefault();
                                alert('Please select a time slot.');
                                return;
                            }

                            requestFormSubmitting = true;
                            if (submitBtn) {
                                submitBtn.disabled = true;
                            }
                        });

                        window.addEventListener('pageshow', function () {
                            requestFormSubmitting = false;
                            if (submitBtn) {
                                submitBtn.disabled = false;
                            }
                        });
                    }

function _buildStudentNotificationToken(notification) {
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

function _hasShownStudentToast(token) {
    if (!token) return false;
    try {
        const key = `student_last_toast_notification_${studentToastUserId || 'guest'}`;
        return localStorage.getItem(key) === token;
    } catch (_) {
        return false;
    }
}

function _markShownStudentToast(token) {
    if (!token) return;
    try {
        const key = `student_last_toast_notification_${studentToastUserId || 'guest'}`;
        localStorage.setItem(key, token);
    } catch (_) {
        // ignore storage errors
    }
}

                    // Don't show flashSuccess in toast - it will be shown in the success modal
if (unreadCount > 0 && latestNotification && notifToast) {
    const notificationToken = _buildStudentNotificationToken(latestNotification);
    if (!_hasShownStudentToast(notificationToken)) {
        toastTitle.textContent = latestNotification.title ?? 'New Notification';
        toastBody.textContent = latestNotification.message ?? 'You have a new notification.';
        notifToast.classList.add('show');
        _markShownStudentToast(notificationToken);
        setTimeout(() => notifToast.classList.remove('show'), 6000);
    }
}

if (closeToast) {
    closeToast.addEventListener('click', () => {
        notifToast.classList.remove('show');
    });
}

// ===== CONSULTATION PAGINATION =====
const consultationList = document.getElementById('consultationList');
let consultationItems = Array.from(document.querySelectorAll('.consultation-item'));
const consultationPaginationInfo = document.getElementById('consultationPaginationInfo');
const consultationPageNumbers = document.getElementById('consultationPageNumbers');
const prevConsultationBtn = document.getElementById('prevConsultationBtn');
const nextConsultationBtn = document.getElementById('nextConsultationBtn');
const myConsultationStatusFilterDropdown = document.getElementById('myConsultationStatusFilterDropdown');
const myConsultationStatusFilterBtn = document.getElementById('myConsultationStatusFilterBtn');
const myConsultationStatusFilterLabel = document.getElementById('myConsultationStatusFilterLabel');
const myConsultationStatusFilterMenu = document.getElementById('myConsultationStatusFilterMenu');
const myConsultationStatusFilterOptions = Array.from(document.querySelectorAll('.myc-status-filter-option'));
const myConsultationSearchInput = document.getElementById('myConsultationSearch');

const itemsPerPage = 10;
let currentConsultationPage = 1;
let selectedConsultationStatus = 'all';
let consultationSearchTerm = '';
let filteredConsultationItems = [...consultationItems];

function normalizeConsultationStatus(statusValue) {
    const status = String(statusValue || '').toLowerCase();
    if (status === 'decline') return 'declined';
    return status;
}

function isConsultationStatusMatched(itemStatus, filterStatus) {
    if (!filterStatus || filterStatus === 'all') return true;
    return itemStatus === normalizeConsultationStatus(filterStatus);
}

function isConsultationSearchMatched(item, searchTerm) {
    if (!searchTerm) return true;
    const searchSource = String(item.textContent || '').toLowerCase();
    return searchSource.includes(searchTerm);
}

function applyConsultationFilters() {
    filteredConsultationItems = consultationItems.filter((item) => {
        const itemStatus = String(item.dataset.status || '').toLowerCase();
        const statusMatched = isConsultationStatusMatched(itemStatus, selectedConsultationStatus);
        const searchMatched = isConsultationSearchMatched(item, consultationSearchTerm);
        return statusMatched && searchMatched;
    });
}

function getConsultationTotals() {
    const totalItems = filteredConsultationItems.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / itemsPerPage));
    return { totalItems, totalPages };
}

function openMyConsultationStatusFilter() {
    if (!myConsultationStatusFilterBtn || !myConsultationStatusFilterMenu) return;
    myConsultationStatusFilterMenu.classList.add('open');
    myConsultationStatusFilterMenu.setAttribute('aria-hidden', 'false');
    myConsultationStatusFilterBtn.setAttribute('aria-expanded', 'true');
}

function closeMyConsultationStatusFilter() {
    if (!myConsultationStatusFilterBtn || !myConsultationStatusFilterMenu) return;
    myConsultationStatusFilterMenu.classList.remove('open');
    myConsultationStatusFilterMenu.setAttribute('aria-hidden', 'true');
    myConsultationStatusFilterBtn.setAttribute('aria-expanded', 'false');
}

function setMyConsultationStatusFilter(status, label) {
    selectedConsultationStatus = String(status || 'all').toLowerCase();
    if (myConsultationStatusFilterLabel) {
        myConsultationStatusFilterLabel.textContent = label || 'Choose a status...';
    }
    closeMyConsultationStatusFilter();
    refreshConsultationList(true);
}

function createConsultationPagination() {
    if (!consultationPageNumbers) return;
    const { totalItems, totalPages } = getConsultationTotals();
    consultationPageNumbers.innerHTML = '';

    if (totalItems === 0) {
        if (prevConsultationBtn) prevConsultationBtn.style.display = 'none';
        if (nextConsultationBtn) nextConsultationBtn.style.display = 'none';
        return;
    }

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'pagination-page-btn' + (i === currentConsultationPage ? ' active' : '');
        btn.textContent = i;
        btn.addEventListener('click', () => showConsultationPage(i));
        consultationPageNumbers.appendChild(btn);
    }

    if (prevConsultationBtn) prevConsultationBtn.style.display = currentConsultationPage > 1 ? 'block' : 'none';
    if (nextConsultationBtn) nextConsultationBtn.style.display = currentConsultationPage < totalPages ? 'block' : 'none';
}

function showConsultationPage(pageNum, options = {}) {
    const { scroll = true } = options;
    const { totalItems, totalPages } = getConsultationTotals();

    currentConsultationPage = Math.min(Math.max(1, pageNum), totalPages);
    const start = (currentConsultationPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    consultationItems.forEach((item) => {
        item.style.display = 'none';
    });

    filteredConsultationItems.forEach((item, index) => {
        item.style.display = (index >= start && index < end) ? 'block' : 'none';
    });

    if (consultationPaginationInfo) {
        const displayStart = totalItems > 0 ? Math.min(start + 1, totalItems) : 0;
        const displayEnd = totalItems > 0 ? Math.min(end, totalItems) : 0;
        consultationPaginationInfo.textContent = totalItems > 0
            ? `Showing ${displayStart} to ${displayEnd} of ${totalItems} consultations`
            : 'No consultations found';
    }

    createConsultationPagination();
    if (scroll && consultationList) {
        window.scrollTo({ top: consultationList.offsetTop - 100, behavior: 'smooth' });
    }
}

function refreshConsultationList(goToFirstPage = false) {
    consultationItems = Array.from(document.querySelectorAll('.consultation-item'));
    applyConsultationFilters();
    if (goToFirstPage) currentConsultationPage = 1;
    showConsultationPage(currentConsultationPage, { scroll: false });
    if (consultationPaginationInfo) {
        consultationPaginationInfo.style.display = 'block';
    }
}

if (myConsultationStatusFilterBtn) {
    myConsultationStatusFilterBtn.addEventListener('click', () => {
        if (!myConsultationStatusFilterMenu) return;
        if (myConsultationStatusFilterMenu.classList.contains('open')) {
            closeMyConsultationStatusFilter();
        } else {
            openMyConsultationStatusFilter();
        }
    });
}

if (myConsultationStatusFilterOptions.length) {
    myConsultationStatusFilterOptions.forEach((optionBtn) => {
        optionBtn.addEventListener('click', () => {
            const nextStatus = optionBtn.dataset.status || 'all';
            const nextLabel = optionBtn.dataset.label || 'Choose a status...';
            setMyConsultationStatusFilter(nextStatus, nextLabel);
        });
    });
}

if (myConsultationSearchInput) {
    myConsultationSearchInput.addEventListener('input', () => {
        consultationSearchTerm = String(myConsultationSearchInput.value || '').trim().toLowerCase();
        refreshConsultationList(true);
    });
}

document.addEventListener('click', (event) => {
    if (!myConsultationStatusFilterDropdown) return;
    if (myConsultationStatusFilterDropdown.contains(event.target)) return;
    closeMyConsultationStatusFilter();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeMyConsultationStatusFilter();
    }
});

refreshConsultationList(true);

if (initialStudentHash === '#my-consultations') {
    setMyConsultationStatusFilter('all', 'All');
}

if (prevConsultationBtn) {
    prevConsultationBtn.addEventListener('click', () => {
        if (currentConsultationPage > 1) showConsultationPage(currentConsultationPage - 1);
    });
}

if (nextConsultationBtn) {
    nextConsultationBtn.addEventListener('click', () => {
        const { totalPages } = getConsultationTotals();
        if (currentConsultationPage < totalPages) showConsultationPage(currentConsultationPage + 1);
    });
}

// ===== STUDENT CONSULTATION STATUS POLLING =====
let lastConsultationStates = {};
let studentInitialPoll = true;

function initializeConsultationStates() {
    console.log('Initializing consultation states');
    document.querySelectorAll('.consultation-card').forEach(card => {
        const consultationId = card.dataset.consultationId;
        const item = card.closest('.consultation-item');
        const status = item?.dataset.status || 'unknown';
        if (consultationId) {
            lastConsultationStates[consultationId] = status;
            console.log(`  id=${consultationId} status=${status}`);
        }
    });
}

// ===== Persistent notification guard (localStorage) =====
function _getStudentShownNotifications() {
    try {
        return JSON.parse(localStorage.getItem('student_shown_notifications') || '{}');
    } catch (e) {
        return {};
    }
}

function _setStudentShownNotification(consultationId, status) {
    try {
        const map = _getStudentShownNotifications();
        map[consultationId] = status;
        localStorage.setItem('student_shown_notifications', JSON.stringify(map));
    } catch (e) {
        // ignore
    }
}

function _hasShownStudentNotification(consultationId, status) {
    const map = _getStudentShownNotifications();
    return map[consultationId] === status;
}

function getManilaDateParts(dateInput = new Date()) {
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
        return { year, month, day, hour, minute, minutesOfDay: (hour * 60) + minute, iso: `${year}-${month}-${day}` };
    } catch (_) {
        const d = new Date(dateInput);
        const year = String(d.getFullYear());
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const hour = Number(d.getHours());
        const minute = Number(d.getMinutes());
        return { year, month, day, hour, minute, minutesOfDay: (hour * 60) + minute, iso: `${year}-${month}-${day}` };
    }
}

function isStudentUpcomingStatus(status) {
    return ['pending', 'approved', 'in_progress'].includes(String(status || '').toLowerCase());
}

function getMinutesFromTimeValue(timeValue) {
    const match = String(timeValue || '').match(/^(\d{1,2}):(\d{2})/);
    if (!match) return null;
    const hour = Number(match[1]);
    const minute = Number(match[2]);
    if (!Number.isFinite(hour) || !Number.isFinite(minute)) return null;
    return (hour * 60) + minute;
}

function formatConsultationStatusLabel(statusValue) {
    const normalized = String(statusValue || '').trim().replace(/_/g, ' ');
    if (!normalized) return '--';

    return normalized.replace(/\b\w/g, (char) => char.toUpperCase());
}

function isStudentUpcomingByDateTime(consultation, nowParts) {
    const status = String(consultation?.status || '').toLowerCase();
    if (!isStudentUpcomingStatus(status)) return false;

    const dateValue = String(consultation?.consultation_date || '').trim();
    if (!dateValue) return false;
    if (dateValue > nowParts.iso) return true;
    if (dateValue < nowParts.iso) return false;
    if (status === 'in_progress') return true;

    const startMinutes = getMinutesFromTimeValue(consultation?.consultation_time);
    if (startMinutes === null) return true;
    return startMinutes >= nowParts.minutesOfDay;
}

function renderStudentUpcomingSchedule(consultations = []) {
    if (!studentUpcomingContent) return;

    const nowParts = getManilaDateParts();
    const upcoming = consultations
        .filter((item) => isStudentUpcomingByDateTime(item, nowParts))
        .sort((a, b) => {
            const left = `${a.consultation_date || ''} ${a.consultation_time || ''}`;
            const right = `${b.consultation_date || ''} ${b.consultation_time || ''}`;
            return left.localeCompare(right);
        })
        .slice(0, 3);

    if (!upcoming.length) {
        studentUpcomingContent.innerHTML = '<div class="overview-empty">No upcoming consultations scheduled.</div>';
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

    studentUpcomingContent.innerHTML = `<div class="schedule-list">${html}</div>`;
}

function updateStudentOverviewMetrics(consultations = []) {
    const total = consultations.length;
    const completed = consultations.filter((item) => String(item.status || '').toLowerCase() === 'completed').length;
    const pending = consultations.filter((item) => String(item.status || '').toLowerCase() === 'pending').length;
    const nowParts = getManilaDateParts();
    const todayIso = nowParts.iso;
    const upcomingToday = consultations.filter((item) => {
        return String(item.consultation_date || '').trim() === todayIso
            && isStudentUpcomingByDateTime(item, nowParts);
    }).length;

    if (studentOverviewTotal) studentOverviewTotal.textContent = String(total);
    if (studentOverviewCompleted) studentOverviewCompleted.textContent = String(completed);
    if (studentOverviewPending) studentOverviewPending.textContent = String(pending);
    if (studentOverviewUpcomingToday) studentOverviewUpcomingToday.textContent = String(upcomingToday);
}

function pollStudentConsultationUpdates() {
    console.log('Polling student consultations...');
    // Seed existing states once so polling won't raise false-positive notifications.
    if (studentInitialPoll) {
        try {
            initializeConsultationStates();
        } catch (e) {
            console.log('Failed to initialize consultation states on first poll', e);
        }
    }

    fetch('{{ route("api.student.consultations-summary") }}')
        .then(response => response.json())
        .then(data => {
            if (!data.consultations || !Array.isArray(data.consultations)) {
                console.log('Unexpected polling response:', data);
                return;
            }
            renderStudentUpcomingSchedule(data.consultations);
            updateStudentOverviewMetrics(data.consultations);
            // debug output
            console.log('Received', data.consultations.length, 'consultations from API');

            data.consultations.forEach(consultation => {
                const consultationCard = document.querySelector(`.consultation-card[data-consultation-id="${consultation.id}"]`);
                if (!consultationCard) return;

                const consultationItem = consultationCard.closest('.consultation-item');
                if (!consultationItem) return;

                const currentDomStatus = String(consultationItem.dataset.status || '').toLowerCase();
                const newStatus = String(consultation.status || '').toLowerCase();

                // During the first poll after page load we only seed the state to avoid
                // showing notifications for statuses that already existed prior to the reload.
                if (studentInitialPoll) {
                    lastConsultationStates[consultation.id] = newStatus;
                    return;
                }

                if (currentDomStatus !== newStatus) {
                    console.log(`Detected status change in DOM for item ${consultation.id}: ${currentDomStatus} -> ${newStatus}`);
                    updateConsultationItemStatus(consultationItem, consultation);
                    // update stored state as well for future reference
                    lastConsultationStates[consultation.id] = newStatus;

                    // Re-run filter to keep UI in sync
                    if (typeof refreshConsultationList === 'function') {
                        refreshConsultationList(false);
                    }

                    showStatusChangeNotification(consultation.id, consultation.instructor_name, newStatus);
                }
            });
            // mark initial poll as completed so subsequent polls show notifications
            if (studentInitialPoll) studentInitialPoll = false;
        })
        .catch(error => {
            console.log('Consultation status check failed (will retry):', error);
        });
}

function updateConsultationItemStatus(consultationItem, consultation) {
    const statusLower = consultation.status.toLowerCase();

    // Update the data-status attribute
    consultationItem.dataset.status = statusLower;

    // Update status badge
    const statusBadge = consultationItem.querySelector('.cc-status-badge');
    if (statusBadge) {
        // Remove all status classes
        statusBadge.className = 'cc-status-badge status-' + statusLower;
        statusBadge.textContent = consultation.status.toUpperCase();
    }

    // Update the consultation card's status class
    const consultationCard = consultationItem.querySelector('.consultation-card');
    if (consultationCard) {
        consultationCard.className = 'consultation-card status-' + statusLower;
    }

    const mobileDetailsBtn = consultationItem.querySelector('.cc-mobile-details-btn');
    if (mobileDetailsBtn) {
        mobileDetailsBtn.dataset.status = formatConsultationStatusLabel(consultation.status);
        if (consultation.instructor_name) mobileDetailsBtn.dataset.instructor = consultation.instructor_name;
        if (consultation.type_label) mobileDetailsBtn.dataset.type = consultation.type_label;
        if (consultation.consultation_mode) mobileDetailsBtn.dataset.mode = consultation.consultation_mode;
        if (consultation.consultation_date) mobileDetailsBtn.dataset.date = consultation.consultation_date;
        if (consultation.time_range) mobileDetailsBtn.dataset.time = consultation.time_range;
        if (typeof consultation.duration_minutes !== 'undefined') {
            mobileDetailsBtn.dataset.duration = consultation.duration_minutes !== null
                ? `${consultation.duration_minutes} min`
                : '—';
        }
        if (typeof consultation.summary_text !== 'undefined') {
            mobileDetailsBtn.dataset.summary = consultation.summary_text || '';
        }
        if (typeof consultation.transcript_text !== 'undefined') {
            mobileDetailsBtn.dataset.transcript = consultation.transcript_text || '';
        }

        const updatedLabel = consultation.updated_at_human || consultation.updated_label || consultation.updated_at || '';
        if (updatedLabel) {
            mobileDetailsBtn.dataset.updated = updatedLabel;
            const mobileMetaText = consultationItem.querySelector('.cc-mobile-meta span');
            if (mobileMetaText) {
                mobileMetaText.textContent = updatedLabel;
            }
            const updatedText = consultationItem.querySelector('.cc-col-mode .cc-updated');
            if (updatedText) {
                updatedText.textContent = updatedLabel;
            }
        }
    }

    // Update action buttons based on new status
    const actionCol = consultationItem.querySelector('.cc-col-action');
    if (actionCol) {
        updateConsultationActions(actionCol, consultation);
    }
}

function updateConsultationActions(actionCol, consultation) {
    const statusLower = consultation.status.toLowerCase();

    // Create new action HTML based on status
    let actionHtml = '';

    if (statusLower === 'pending') {
        actionHtml = `
            <div class="cc-awaiting">
                <span class="cc-spinner" aria-hidden="true"></span>
                <span>Awaiting</span>
            </div>
            <form method="POST"
                  action="/student/consultations/${consultation.id}/cancel"
                  class="student-cancel-form"
                  data-consultation-id="${consultation.id}"
                  style="margin:0">
                @csrf
                <button type="submit"
                        class="cc-btn cc-btn-cancel">
                    Cancel
                </button>
            </form>
        `;
    } else if (statusLower === 'approved') {
        actionHtml = `
            <div class="cc-awaiting">
                <span class="cc-spinner" aria-hidden="true"></span>
                <span>Starting soon</span>
            </div>
        `;
    } else if (statusLower === 'in_progress') {
        actionHtml = `
            <button class="cc-btn cc-btn-join join-call-btn"
                    data-consultation-id="${consultation.id}"
                    data-mode="${consultation.consultation_mode.toLowerCase()}">
                🎯 Join Now
            </button>
        `;
        } else if (statusLower === 'completed') {
        const durationLabel = consultation.duration_minutes !== null && typeof consultation.duration_minutes !== 'undefined'
            ? `${consultation.duration_minutes} min`
            : '—';
        actionHtml = `
            <div class="cc-completed-check">✓ Completed</div>
            <button type="button"
                    class="cc-btn cc-btn-feedback feedback-open-btn"
                    data-id="${consultation.id}"
                    data-instructor="${consultation.instructor_name}"
                    data-type="${consultation.type_label}"
                    data-mode="${consultation.consultation_mode}"
                    data-date="${consultation.consultation_date}"
                    data-time="${consultation.time_range || ''}"
                    data-duration="${durationLabel}"
                    data-summary="${consultation.summary_text || ''}"
                    data-transcript="${consultation.transcript_text || ''}">
                💬 Feedback
            </button>
        `;
    } else if (statusLower === 'incompleted') {
        actionHtml = `
            <span style="font-size:12px;font-weight:700;color:#92400e;">
                Incomplete
            </span>
        `;
    } else if (statusLower === 'declined') {
        actionHtml = `
            <span style="font-size:12px;font-weight:600;color:#b91c1c;">
                Declined
            </span>
        `;
    } else if (statusLower === 'cancelled') {
        actionHtml = `
            <span style="font-size:12px;font-weight:600;color:#b91c1c;">
                Cancelled
            </span>
        `;
    } else {
        actionHtml = `
            <span style="font-size:12px;font-weight:600;color:#888;">
                ${consultation.status}
            </span>
        `;
    }

    actionCol.innerHTML = actionHtml;

    // Re-bind event listeners for new buttons
    rebindConsultationActionListeners(actionCol);
}

function rebindConsultationActionListeners(actionCol) {
    bindFeedbackButtons(actionCol);
    bindJoinCallButtons(actionCol);
    bindCancelButtons(actionCol);
    bindDetailsButtons(actionCol);
}

function showStatusChangeNotification(consultationId, instructorName, newStatus) {
    const statusMessages = {
        'approved': 'Your consultation request was approved.',
        'declined': 'Your consultation request was declined.',
        'in_progress': 'Your consultation is starting now.',
        'completed': 'Your consultation is complete.',
        'incompleted': 'Consultation marked as incomplete.'
    };
    // Only show once per consultation+status
    try {
        if (_hasShownStudentNotification(String(consultationId), String(newStatus))) {
            return;
        }
    } catch (e) {
        // ignore and proceed to show
    }

    const message = statusMessages[newStatus] || `Status changed to ${newStatus}`;
    const bgColor = newStatus === 'approved' ? '#10b981' : newStatus === 'declined' ? '#ef4444' : newStatus === 'incompleted' ? '#f59e0b' : '#3b82f6';

    const notificationDiv = document.createElement('div');
    notificationDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 14px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        font-size: 14px;
        z-index: 10000;
        font-weight: 600;
        animation: slideDown 0.3s ease-out;
    `;
    notificationDiv.innerHTML = message;
    document.body.appendChild(notificationDiv);

    // mark as shown so refresh won't show the same notification again
    try { _setStudentShownNotification(String(consultationId), String(newStatus)); } catch (e) {}

    setTimeout(() => {
        notificationDiv.style.animation = 'slideUp 0.3s ease-out';
        setTimeout(() => notificationDiv.remove(), 300);
    }, 4000);
}

// Initialize consultation states on page load
initializeConsultationStates();

// Start polling every 3 seconds
pollStudentConsultationUpdates();
setInterval(pollStudentConsultationUpdates, 3000);

// ===== HISTORY SEARCH & FILTERING =====
const historyCategoryFilter = document.getElementById('historyCategoryFilter');
const historyTopicFilter = document.getElementById('historyTopicFilter');
const historyModeFilter = document.getElementById('historyModeFilter');
const historySearch = document.getElementById('historySearch');
const historyEmptyState = document.getElementById('historyEmptyState');

const historyTable = document.querySelector('.history-table');
const historyRowWraps = Array.from(document.querySelectorAll('.history-row-wrap'));
const historyPaginationInfo = document.getElementById('historyPaginationInfo');
const historyPageNumbers = document.getElementById('historyPageNumbers');
const prevHistoryBtn = document.getElementById('prevHistoryBtn');
const nextHistoryBtn = document.getElementById('nextHistoryBtn');

const historyItemsPerPage = 10;
let currentHistoryPage = 1;
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

function normalizeFilterValue(value) {
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

function getRowSemesterCode(row) {
    const sem = normalizeFilterValue(row.dataset.semester);
    if (sem === 'first') return '1';
    if (sem === 'second') return '2';
    return getSemesterFromDate(row.dataset.date || '');
}

function getRowAcademicYear(row) {
    return normalizeFilterValue(row.dataset.academicYear || getAcademicYearFromDate(row.dataset.date || ''));
}

function getRowMonthNumber(row) {
    const monthName = normalizeFilterValue(row.dataset.month);
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
    const normalizedCategory = normalizeFilterValue(category);
    normalizedCategoryKeys.push(normalizedCategory);
    topics.forEach((topic) => {
        const normalizedTopic = normalizeFilterValue(topic);
        normalizedCategoryByTopic.set(normalizedTopic, normalizedCategory);
        normalizedTopicKeys.push(normalizedTopic);
    });
});
normalizedTopicKeys.sort((a, b) => b.length - a.length);

function deriveHistoryCategoryAndTopic(row) {
    let rowCategory = normalizeFilterValue(row.dataset.category);
    let rowTopic = normalizeFilterValue(row.dataset.topic);
    const rowType = normalizeFilterValue(row.dataset.type);

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

function populateHistoryTopicFilter() {
    if (!historyTopicFilter) return;
    const selectedCategoryRaw = historyCategoryFilter?.value || '';
    const previousValue = normalizeFilterValue(historyTopicFilter.value);

    const topicSource = selectedCategoryRaw
        ? (consultationTopicsByCategory[selectedCategoryRaw] || [])
        : Object.values(consultationTopicsByCategory).flat();

    const uniqueSortedTopics = Array.from(new Set(topicSource))
        .sort((a, b) => a.localeCompare(b));

    historyTopicFilter.innerHTML = '<option value="">All Topics</option>';

    uniqueSortedTopics.forEach((topic) => {
        const opt = document.createElement('option');
        opt.value = topic;
        opt.textContent = topic;
        historyTopicFilter.appendChild(opt);
    });

    const previousRaw = topicSource.find((topic) => normalizeFilterValue(topic) === previousValue);
    if (previousValue && previousRaw) {
        historyTopicFilter.value = previousRaw;
    } else {
        historyTopicFilter.value = '';
    }
}

function filterHistoryRows() {
    const selectedSemBtn = historySemButtons.find((btn) => btn.classList.contains('active'));
    const selectedSem = selectedSemBtn ? normalizeFilterValue(selectedSemBtn.dataset.sem) : 'all';
    const selectedYear = normalizeFilterValue(historyYearInput?.value);
    const selectedCategory = normalizeFilterValue(historyCategoryFilter?.value);
    const selectedTopic = normalizeFilterValue(historyTopicFilter?.value);
    const selectedMode = normalizeFilterValue(historyModeFilter?.value);
    const selectedSearch = normalizeFilterValue(historySearch?.value);

    historyRowWraps.forEach((wrap) => {
        const row = wrap.querySelector('.history-row-item');
        if (!row) {
            wrap.dataset.match = '0';
            return;
        }

        const rowSem = getRowSemesterCode(row);
        const rowMonth = getRowMonthNumber(row);
        const rowAcademicYear = getRowAcademicYear(row);
        const { rowCategory, rowTopic } = deriveHistoryCategoryAndTopic(row);
        const rowMode = normalizeFilterValue(row.dataset.mode);
        const rowSearchable = normalizeFilterValue(row.dataset.searchable || row.textContent || '');
        const compactSelectedYear = selectedYear.replace(/\s+/g, '');
        const compactRowYear = rowAcademicYear.replace(/\s+/g, '');

        let matches = true;

        if (selectedSem !== 'all') {
            matches = matches && rowSem === selectedSem;
        }

        if (matches && selectedMonth) {
            matches = rowMonth === Number(selectedMonth);
        }

        if (matches && selectedYear) {
            matches = rowAcademicYear === selectedYear
                || rowAcademicYear.includes(selectedYear)
                || compactRowYear.includes(compactSelectedYear);
        }

        if (matches && selectedCategory) {
            matches = rowCategory === selectedCategory;
        }

        if (matches && selectedTopic) {
            const rowType = normalizeFilterValue(row.dataset.type || '');
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

    currentHistoryPage = 1;
    renderHistoryPage();
}

function renderHistoryPage(page = currentHistoryPage) {
    const matchedWraps = historyRowWraps.filter((wrap) => wrap.dataset.match === '1');
    const totalMatched = matchedWraps.length;
    const totalPages = Math.max(1, Math.ceil(totalMatched / historyItemsPerPage));
    currentHistoryPage = Math.min(Math.max(1, page), totalPages);

    historyRowWraps.forEach((wrap) => {
        wrap.style.display = 'none';
    });

    if (totalMatched > 0) {
        const start = (currentHistoryPage - 1) * historyItemsPerPage;
        const end = start + historyItemsPerPage;
        matchedWraps.forEach((wrap, index) => {
            if (index >= start && index < end) {
                wrap.style.display = 'flex';
            }
        });

        const displayStart = start + 1;
        const displayEnd = Math.min(end, totalMatched);
        if (historyPaginationInfo) {
            historyPaginationInfo.textContent = `Showing ${displayStart} to ${displayEnd} of ${totalMatched} consultations`;
            historyPaginationInfo.style.display = 'block';
        }
        if (historyEmptyState) historyEmptyState.style.display = 'none';
    } else {
        if (historyPaginationInfo) {
            historyPaginationInfo.textContent = 'No consultations found';
            historyPaginationInfo.style.display = 'block';
        }
        if (historyEmptyState) historyEmptyState.style.display = 'block';
    }

    if (historyPageNumbers) {
        historyPageNumbers.innerHTML = '';
        if (totalMatched > 0) {
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'pagination-page-btn' + (i === currentHistoryPage ? ' active' : '');
                btn.textContent = i;
                btn.addEventListener('click', () => renderHistoryPage(i));
                historyPageNumbers.appendChild(btn);
            }
        }
    }

    if (prevHistoryBtn) prevHistoryBtn.style.display = totalMatched > 0 && currentHistoryPage > 1 ? 'block' : 'none';
    if (nextHistoryBtn) nextHistoryBtn.style.display = totalMatched > 0 && currentHistoryPage < totalPages ? 'block' : 'none';
}

if (historyCategoryFilter) {
    historyCategoryFilter.addEventListener('change', () => {
        populateHistoryTopicFilter();
        filterHistoryRows();
    });
}

if (historyTopicFilter) {
    historyTopicFilter.addEventListener('change', filterHistoryRows);
}

if (historyModeFilter) {
    historyModeFilter.addEventListener('change', filterHistoryRows);
}

if (historySearch) {
    historySearch.addEventListener('input', filterHistoryRows);
}

if (prevHistoryBtn) {
    prevHistoryBtn.addEventListener('click', () => {
        if (currentHistoryPage > 1) {
            renderHistoryPage(currentHistoryPage - 1);
        }
    });
}

if (nextHistoryBtn) {
    nextHistoryBtn.addEventListener('click', () => {
        const matchedCount = historyRowWraps.filter((wrap) => wrap.dataset.match === '1').length;
        const totalPages = Math.max(1, Math.ceil(matchedCount / historyItemsPerPage));
        if (currentHistoryPage < totalPages) {
            renderHistoryPage(currentHistoryPage + 1);
        }
    });
}

populateHistoryTopicFilter();
renderMonthSelector('all');
filterHistoryRows();

// ===== STUDENT NOTIFICATION POLLING =====
const studentConsultationStateMap = new Map();

function updateStudentNotificationBadge(unreadCount) {
    const badge = document.getElementById('notificationBadge');
    const count = Number(unreadCount || 0);
    if (badge) {
        badge.textContent = String(count);
        badge.style.display = count > 0 ? 'inline-flex' : 'none';
    }
}

function escapeStudentNotificationHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function renderStudentNotificationList(notifications = []) {
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
        const id = Number(notification?.id || 0);
        const title = escapeStudentNotificationHtml(notification?.title || 'Notification');
        const message = escapeStudentNotificationHtml(notification?.message || '');
        const timeLabel = escapeStudentNotificationHtml(notification?.created_at_human || 'Just now');
        const unreadClass = notification?.is_read ? '' : ' unread';

        return `
            <li class="notification-item${unreadClass}" data-id="${id}">
                <span class="notification-dot"></span>
                <div>
                    <div style="font-weight:700">${title}</div>
                    <div style="color:var(--muted);margin-top:4px">${message}</div>
                    <div style="color:#9ca3af;font-size:11px;margin-top:6px">${timeLabel}</div>
                </div>
                <div class="notification-actions">
                    <button type="button" class="dismiss-btn">Dismiss</button>
                </div>
            </li>
        `;
    }).join('');
}

function showStudentStatusChangeNotification(consultationData) {
    if (!consultationData || !notifToast) return;
    
    let message = '';
    const status = (consultationData.status || '').toLowerCase();
    
    if (status === 'approved') {
        message = `Your consultation with ${consultationData.instructor_name} has been <strong>approved</strong>! ✓`;
    } else if (status === 'declined') {
        message = `Your consultation request has been <strong>declined</strong>. 📋`;
    } else if (status === 'in_progress') {
        message = `Your consultation is now <strong>in progress</strong>! 🎥`;
    } else if (status === 'completed') {
        message = `Your consultation has been <strong>completed</strong>! ✓`;
    } else if (status === 'incompleted') {
        message = `Your consultation is marked as <strong>incomplete</strong>. ⚠`;
    }
    
    if (message && toastTitle && toastBody) {
        toastTitle.textContent = 'Consultation Status Update';
        toastBody.innerHTML = message;
        notifToast.classList.add('show');
        setTimeout(() => notifToast.classList.remove('show'), 6000);
    }
}

function updateStudentConsultationRow(consultationId, newStatus) {
    const consultationCard = document.querySelector(`.consultation-card[data-consultation-id="${consultationId}"]`);
    if (consultationCard) {
        consultationCard.dataset.status = newStatus.toLowerCase();
        consultationCard.className = `consultation-card status-${newStatus.toLowerCase()}`;
        const statusBadge = consultationCard.querySelector('.cc-status-badge');
        if (statusBadge) {
            statusBadge.className = `cc-status-badge status-${newStatus.toLowerCase()}`;
            statusBadge.textContent = formatConsultationStatusLabel(newStatus);
        }

        const mobileDetailsBtn = consultationCard.querySelector('.cc-mobile-details-btn');
        if (mobileDetailsBtn) {
            mobileDetailsBtn.dataset.status = formatConsultationStatusLabel(newStatus);
        }
    }
}

function pollStudentNotifications() {
    fetch('{{ route("api.student.consultations-summary") }}', {
        cache: 'no-store',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
    })
        .then((response) => response.json())
        .then((data) => {
            updateStudentNotificationBadge(data?.unreadNotifications || 0);
            renderStudentNotificationList(data?.notifications || []);
            const latestUnreadNotification = data?.latestUnreadNotification || null;
            if (latestUnreadNotification && notifToast && toastTitle && toastBody) {
                const token = _buildStudentNotificationToken(latestUnreadNotification);
                if (!_hasShownStudentToast(token)) {
                    toastTitle.textContent = latestUnreadNotification.title ?? 'New Notification';
                    toastBody.textContent = latestUnreadNotification.message ?? 'You have a new notification.';
                    notifToast.classList.add('show');
                    _markShownStudentToast(token);
                    setTimeout(() => notifToast.classList.remove('show'), 6000);
                }
            }
            
            // Check for consultation status changes
            if (Array.isArray(data?.consultations)) {
                data.consultations.forEach((consultation) => {
                    const consultationId = consultation.id;
                    const currentStatus = studentConsultationStateMap.get(consultationId);
                    const newStatus = consultation.status;
                    
                    if (currentStatus && currentStatus !== newStatus) {
                        // Status changed - show notification
                        showStudentStatusChangeNotification(consultation);
                        updateStudentConsultationRow(consultationId, newStatus);
                    }
                    
                    // Update state map
                    studentConsultationStateMap.set(consultationId, newStatus);
                });
            }
        })
        .catch((error) => {
            console.log('Student notification check failed (will retry):', error);
        });
}

// Start polling for notifications every 3 seconds
pollStudentNotifications();
setInterval(pollStudentNotifications, 3000);
</script>
@endsection
