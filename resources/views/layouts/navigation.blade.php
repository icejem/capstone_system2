@php
    $authUser = Auth::user();
    $extractFirstName = function (?string $name, string $fallback): string {
        $trimmedName = trim((string) $name);
        if ($trimmedName === '') {
            return $fallback;
        }

        $parts = preg_split('/\s+/', $trimmedName, -1, PREG_SPLIT_NO_EMPTY);
        return $parts[0] ?? $fallback;
    };
    $isStudentDashboard = request()->routeIs('student.dashboard');
    $isInstructorDashboard = request()->routeIs('instructor.dashboard');
    $isAdminDashboard = request()->routeIs('admin.dashboard');
    $usesDashboardLogoutModal = $isStudentDashboard || $isInstructorDashboard || $isAdminDashboard;
    $logoutTheme = match (true) {
        $isInstructorDashboard => [
            'accent' => '#4A90E2',
            'accentStrong' => '#1F3A8A',
            'surface' => '#0b1733',
            'surfaceSoft' => '#111f46',
            'border' => 'rgba(148, 163, 184, 0.22)',
            'text' => '#f8fafc',
            'muted' => '#b6c2de',
        ],
        $isAdminDashboard => [
            'accent' => '#60a5fa',
            'accentStrong' => '#1d4ed8',
            'surface' => '#081428',
            'surfaceSoft' => '#10203f',
            'border' => 'rgba(148, 163, 184, 0.22)',
            'text' => '#f8fafc',
            'muted' => '#c4d0ea',
        ],
        default => [
            'accent' => '#4A90E2',
            'accentStrong' => '#1F3A8A',
            'surface' => '#081631',
            'surfaceSoft' => '#10244a',
            'border' => 'rgba(148, 163, 184, 0.22)',
            'text' => '#f8fafc',
            'muted' => '#bfd0ef',
        ],
    };
    $showSidebarToggle = $isStudentDashboard;
    $studentNotifications = collect($notifications ?? []);
    $studentUnreadCount = $studentNotifications->where('is_read', false)->count();
    $studentName = $authUser?->name ?? 'Student';
    $studentFirstName = $extractFirstName($authUser?->name, 'Student');
    $studentEmail = $authUser?->email ?? 'student@example.com';
    $studentInitial = 'U';
    if ($authUser?->name) {
        $firstChar = function_exists('mb_substr') ? mb_substr(trim($authUser->name), 0, 1) : substr(trim($authUser->name), 0, 1);
        $studentInitial = function_exists('mb_strtoupper') ? mb_strtoupper($firstChar) : strtoupper($firstChar);
    }
    $instructorNotifications = collect($notifications ?? []);
    $instructorUnreadCount = $instructorNotifications->where('is_read', false)->count();
    $instructorName = $authUser?->name ?? 'Instructor';
    $instructorFirstName = $extractFirstName($authUser?->name, 'Instructor');
    $instructorEmail = $authUser?->email ?? 'instructor@example.com';
    $instructorInitial = 'U';
    if ($authUser?->name) {
        $firstChar = function_exists('mb_substr') ? mb_substr(trim($authUser->name), 0, 1) : substr(trim($authUser->name), 0, 1);
        $instructorInitial = function_exists('mb_strtoupper') ? mb_strtoupper($firstChar) : strtoupper($firstChar);
    }
    $adminNotifications = collect($notifications ?? [])
        ->map(function ($notification) {
            if (is_array($notification)) {
                $notification['read'] = (bool) ($notification['read'] ?? ($notification['is_read'] ?? false));
                return $notification;
            }

            if (is_object($notification)) {
                $notification->read = (bool) ($notification->read ?? ($notification->is_read ?? false));
            }

            return $notification;
        });
    $adminUnreadCount = $adminNotifications->filter(function ($notification) {
        if (is_array($notification)) {
            return !($notification['read'] ?? false);
        }

        return !($notification->read ?? false);
    })->count();
    $adminName = $authUser?->name ?? 'Admin';
    $adminFirstName = $extractFirstName($authUser?->name, 'Admin');
    $adminEmail = $authUser?->email ?? 'admin@example.com';
    $adminInitial = 'U';
    if ($authUser?->name) {
        $firstChar = function_exists('mb_substr') ? mb_substr(trim($authUser->name), 0, 1) : substr(trim($authUser->name), 0, 1);
        $adminInitial = function_exists('mb_strtoupper') ? mb_strtoupper($firstChar) : strtoupper($firstChar);
    }
@endphp

@if ($usesDashboardLogoutModal)
    <style>
        .dashboard-logout-modal[hidden] {
            display: none !important;
        }

        .dashboard-logout-modal {
            position: fixed;
            inset: 0;
            z-index: 1200;
        }

        .dashboard-logout-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2, 6, 23, 0.64);
            backdrop-filter: blur(8px);
        }

        .dashboard-logout-modal__dialog {
            position: relative;
            width: min(100% - 32px, 420px);
            margin: min(12vh, 96px) auto 0;
            padding: 22px 22px 18px;
            border-radius: 22px;
            border: 1px solid {{ $logoutTheme['border'] }};
            background:
                linear-gradient(160deg, {{ $logoutTheme['surfaceSoft'] }} 0%, {{ $logoutTheme['surface'] }} 100%);
            box-shadow: 0 28px 80px rgba(2, 6, 23, 0.42);
            color: {{ $logoutTheme['text'] }};
        }

        .dashboard-logout-modal__close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: {{ $logoutTheme['muted'] }};
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .dashboard-logout-modal__close:hover {
            background: rgba(148, 163, 184, 0.12);
            color: {{ $logoutTheme['text'] }};
        }

        .dashboard-logout-modal__title {
            margin: 0 36px 10px 0;
            font-size: 28px;
            line-height: 1.1;
            font-weight: 800;
            color: {{ $logoutTheme['text'] }};
        }

        .dashboard-logout-modal__text {
            margin: 0;
            font-size: 15px;
            line-height: 1.6;
            color: {{ $logoutTheme['muted'] }};
        }

        .dashboard-logout-modal__actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 28px;
        }

        .dashboard-logout-modal__btn {
            min-width: 102px;
            height: 42px;
            padding: 0 18px;
            border-radius: 12px;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.18s ease, background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
        }

        .dashboard-logout-modal__btn:hover {
            transform: translateY(-1px);
        }

        .dashboard-logout-modal__btn--cancel {
            background: transparent;
            border-color: rgba(148, 163, 184, 0.2);
            color: {{ $logoutTheme['muted'] }};
        }

        .dashboard-logout-modal__btn--cancel:hover {
            background: rgba(148, 163, 184, 0.08);
            color: {{ $logoutTheme['text'] }};
        }

        .dashboard-logout-modal__btn--confirm {
            background: linear-gradient(135deg, {{ $logoutTheme['accent'] }} 0%, {{ $logoutTheme['accentStrong'] }} 100%);
            color: #ffffff;
            box-shadow: 0 10px 24px rgba(31, 58, 138, 0.28);
        }

        .dashboard-logout-modal__btn--confirm:hover {
            filter: brightness(1.05);
        }

        body.logout-modal-open {
            overflow: hidden;
        }

        @media (max-width: 640px) {
            .dashboard-logout-modal__dialog {
                width: min(100% - 20px, 420px);
                margin-top: 84px;
                padding: 20px 18px 16px;
                border-radius: 18px;
            }

            .dashboard-logout-modal__title {
                font-size: 24px;
            }

            .dashboard-logout-modal__actions {
                gap: 10px;
            }

            .dashboard-logout-modal__btn {
                min-width: 96px;
                height: 40px;
                padding: 0 16px;
            }
        }
    </style>
@endif

@if ($isStudentDashboard)
    <nav class="student-shell-nav" aria-label="Student dashboard navigation">
        <header class="student-shell-header">
            <div class="student-shell-header-inner">
                <div class="student-shell-header-start">
                    <button
                        id="menuBtn"
                        type="button"
                        class="menu-btn student-shell-menu-btn"
                        aria-label="Open sidebar menu"
                    >
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    </button>

                    <a href="{{ route('student.dashboard') }}" class="student-shell-brand">
                        <span class="logo-badge">
                            <img src="{{ asset('cslogo.jpg') }}" alt="CS Logo" class="logo-img">
                        </span>
                        <span class="student-shell-brand-copy">
                            <span class="student-shell-brand-title">Computer Studies</span>
                            <span class="student-shell-brand-subtitle">Consultation Platform</span>
                        </span>
                        <span class="student-shell-brand-divider" aria-hidden="true"></span>
                        <span class="logo-badge secondary-logo">
                            <img src="{{ asset('philcstlogo.png') }}" alt="PhilCST Logo" class="logo-img">
                        </span>
                    </a>
                </div>

                <div class="student-shell-header-main">
                    <div class="student-shell-header-copy">
                        <h1 class="student-shell-header-title">
                            Welcome back, <span class="student-shell-header-name"><span class="header-name-full">{{ $studentName }}</span><span class="header-name-short">{{ $studentFirstName }}</span></span>
                            <span class="student-shell-header-wave" aria-hidden="true">&#128075;</span>
                        </h1>
                        <p class="student-shell-header-subtitle">
                            Here's what's happening with your consultations today
                            <span class="student-shell-header-date">&mdash; {{ now()->format('F j, Y') }}</span>
                        </p>
                    </div>

                    <span class="student-shell-header-bits" aria-hidden="true">
                        10110101 01101001 10100110
                        01101011 10110010 01010101
                    </span>
                </div>

                <div class="topbar-actions student-shell-header-actions">
                    <div class="notification-wrap">
                        <button class="notification-btn" id="notificationBtn" type="button" aria-label="Open notifications">
                            <i class="fa-solid fa-bell" aria-hidden="true"></i>
                            <span class="notification-badge" id="notificationBadge" @if ($studentUnreadCount <= 0) style="display:none" @endif>{{ $studentUnreadCount }}</span>
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
                                @forelse ($studentNotifications as $notification)
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
                        <x-dropdown align="right" width="w-72" contentClasses="profile-menu-panel">
                            <x-slot name="trigger">
                                <button class="header-profile-trigger" type="button" title="{{ $studentName }}" aria-label="Open profile menu">
                                    <span class="header-avatar">{{ $studentInitial }}</span>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="profile-menu-header">
                                    <div class="profile-menu-avatar">{{ $studentInitial }}</div>
                                    <div class="profile-menu-copy">
                                        <div class="profile-menu-name">{{ $studentName }}</div>
                                        <div class="profile-menu-email">{{ $studentEmail }}</div>
                                    </div>
                                </div>

                                <div class="profile-menu-divider"></div>

                                <a href="{{ route('profile.edit') }}" class="profile-menu-item">
                                    <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
                                    <span>Account</span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="js-dashboard-logout-form">
                                    @csrf
                                    <button type="submit" class="profile-menu-item profile-menu-item-signout js-dashboard-logout-trigger">
                                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                                        <span>Sign out</span>
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
        </header>

        <aside class="sidebar" id="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-section">Main Menu</li>
                <li>
                    <a href="{{ route('student.dashboard') }}" class="sidebar-menu-link" id="dashboardLink">
                        <i class="fa-solid fa-house" aria-hidden="true"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="#request-consultation" class="sidebar-menu-link" id="requestConsultationLink">
                        <i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>
                        Request Consultation
                    </a>
                </li>
                <li>
                    <a href="#my-consultations" class="sidebar-menu-link" id="myConsultationsLink">
                        <i class="fa-solid fa-calendar-check" aria-hidden="true"></i>
                        My Consultations
                    </a>
                </li>
                <li class="sidebar-menu-section sidebar-menu-section-spaced">Records</li>
                <li>
                    <a href="#history" class="sidebar-menu-link" id="historyLink">
                        <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
                        History
                    </a>
                </li>
            </ul>

            <div class="sidebar-logout">
                <form method="POST" action="{{ route('logout') }}" class="js-dashboard-logout-form">
                    @csrf
                    <button class="logout-btn js-dashboard-logout-trigger" type="submit">Logout</button>
                </form>
            </div>
        </aside>
    </nav>
@elseif ($isInstructorDashboard)
    <nav class="instructor-shell-nav" aria-label="Instructor dashboard navigation">
        <header class="instructor-shell-header">
            <div class="instructor-shell-header-inner">
                <div class="instructor-shell-header-start">
                    <button
                        id="menuBtn"
                        type="button"
                        class="menu-btn instructor-shell-menu-btn"
                        aria-label="Open sidebar menu"
                    >
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    </button>

                    <a href="{{ route('instructor.dashboard') }}" class="instructor-shell-brand">
                        <span class="logo-badge">
                            <img src="{{ asset('cslogo1.jpeg.png') }}" alt="CS Logo" class="logo-img">
                        </span>
                        <span class="instructor-shell-brand-copy">
                            <span class="instructor-shell-brand-title">Computer Studies</span>
                            <span class="instructor-shell-brand-subtitle">Consultation Platform</span>
                        </span>
                        <span class="instructor-shell-brand-divider" aria-hidden="true"></span>
                        <span class="logo-badge secondary-logo">
                            <img src="{{ asset('philcstlogo.png') }}" alt="PhilCST Logo" class="logo-img">
                        </span>
                    </a>
                </div>

                <div class="instructor-shell-header-main">
                    <div class="instructor-shell-header-copy">
                        <h1 class="instructor-shell-header-title">
                            Welcome back, <span class="instructor-shell-header-name"><span class="header-name-full">{{ $instructorName }}</span><span class="header-name-short">{{ $instructorFirstName }}</span></span>
                            <span class="instructor-shell-header-wave" aria-hidden="true">&#128075;</span>
                        </h1>
                        <p class="instructor-shell-header-subtitle">
                            Here's what's happening with your consultations today
                            <span class="instructor-shell-header-date">&mdash; {{ now()->format('F j, Y') }}</span>
                        </p>
                    </div>

                    <span class="instructor-shell-header-bits" aria-hidden="true">
                        10110101 01101001 10100110
                        01101011 10110010 01010101
                    </span>
                </div>

                <div class="topbar-actions instructor-shell-header-actions">
                    <div class="notification-wrap">
                        <button class="notification-btn" id="notificationBtn" type="button" aria-label="Open notifications">
                            <i class="fa-solid fa-bell" aria-hidden="true"></i>
                            <span class="notification-badge" id="notificationBadge" @if ($instructorUnreadCount <= 0) style="display:none" @endif>{{ $instructorUnreadCount }}</span>
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
                                @forelse ($instructorNotifications as $notification)
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
                        <x-dropdown align="right" width="w-72" contentClasses="profile-menu-panel">
                            <x-slot name="trigger">
                                <button class="header-profile-trigger" type="button" title="{{ $instructorName }}" aria-label="Open profile menu">
                                    <span class="header-avatar">{{ $instructorInitial }}</span>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="profile-menu-header">
                                    <div class="profile-menu-avatar">{{ $instructorInitial }}</div>
                                    <div class="profile-menu-copy">
                                        <div class="profile-menu-name">{{ $instructorName }}</div>
                                        <div class="profile-menu-email">{{ $instructorEmail }}</div>
                                    </div>
                                </div>

                                <div class="profile-menu-divider"></div>

                                <a href="{{ route('profile.edit') }}" class="profile-menu-item">
                                    <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
                                    <span>Account</span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="js-dashboard-logout-form">
                                    @csrf
                                    <button type="submit" class="profile-menu-item profile-menu-item-signout js-dashboard-logout-trigger">
                                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                                        <span>Sign out</span>
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
        </header>

        <aside class="sidebar" id="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-section">Main Menu</li>
                <li>
                    <a href="#dashboard" class="sidebar-menu-link active" id="dashboardLink">
                        <i class="fa-solid fa-house" aria-hidden="true"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="#requests" class="sidebar-menu-link" id="requestsLink">
                        <i class="fa-solid fa-inbox" aria-hidden="true"></i>
                        Consultations
                    </a>
                </li>
                <li>
                    <a href="#schedule" class="sidebar-menu-link" id="scheduleLink">
                        <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                        Schedule
                    </a>
                </li>
                <li class="sidebar-menu-section sidebar-menu-section-spaced">Records</li>
                <li>
                    <a href="#history" class="sidebar-menu-link" id="historyLink">
                        <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
                        History
                    </a>
                </li>
                <li>
                    <a href="#feedback" class="sidebar-menu-link" id="feedbackLink">
                        <i class="fa-solid fa-comments" aria-hidden="true"></i>
                        Feedback
                    </a>
                </li>
            </ul>

            <div class="sidebar-logout">
                <form method="POST" action="{{ route('logout') }}" class="js-dashboard-logout-form">
                    @csrf
                    <button class="logout-btn js-dashboard-logout-trigger" type="submit">Logout</button>
                </form>
            </div>
        </aside>
    </nav>
@elseif ($isAdminDashboard)
    <nav class="admin-shell-nav" aria-label="Admin dashboard navigation">
        <header class="admin-shell-header">
            <div class="admin-shell-header-inner">
                <div class="admin-shell-header-start">
                    <button
                        id="menuBtn"
                        type="button"
                        class="menu-btn admin-shell-menu-btn"
                        aria-label="Open sidebar menu"
                    >
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    </button>

                    <a href="{{ route('admin.dashboard') }}" class="admin-shell-brand">
                        <span class="logo-badge">
                            <img src="{{ asset('cslogo1.jpeg.png') }}" alt="CS Logo" class="logo-img">
                        </span>
                        <span class="admin-shell-brand-copy">
                            <span class="admin-shell-brand-title">Computer Studies</span>
                            <span class="admin-shell-brand-subtitle">Consultation Platform</span>
                        </span>
                        <span class="admin-shell-brand-divider" aria-hidden="true"></span>
                        <span class="logo-badge secondary-logo">
                            <img src="{{ asset('philcstlogo.png') }}" alt="PhilCST Logo" class="logo-img">
                        </span>
                    </a>
                </div>

                <div class="admin-shell-header-main">
                    <div class="admin-shell-header-copy">
                        <h1 class="admin-shell-header-title">
                            Welcome back, <span class="admin-shell-header-name"><span class="header-name-full">{{ $adminName }}</span><span class="header-name-short">{{ $adminFirstName }}</span></span>
                            <span class="admin-shell-header-wave" aria-hidden="true">&#128075;</span>
                        </h1>
                        <p class="admin-shell-header-subtitle">
                            Here's what's happening with your consultations today
                            <span class="admin-shell-header-date">&mdash; {{ now()->format('F j, Y') }}</span>
                        </p>
                    </div>

                    <span class="admin-shell-header-bits" aria-hidden="true">
                        10110101 01101001 10100110
                        01101011 10110010 01010101
                    </span>
                </div>

                <div class="topbar-actions admin-shell-header-actions">
                    <div class="notification-wrap">
                        <button class="notification-btn" id="notificationBtn" type="button" aria-label="Open notifications">
                            <i class="fa-solid fa-bell" aria-hidden="true"></i>
                            <span class="notification-badge" id="notificationBadge" @if ($adminUnreadCount <= 0) style="display:none" @endif>{{ $adminUnreadCount }}</span>
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
                                @forelse ($adminNotifications as $notification)
                                    @php
                                        $isRead = is_array($notification) ? ($notification['read'] ?? false) : ($notification->read ?? false);
                                        $title = is_array($notification) ? ($notification['title'] ?? 'Notification') : ($notification->title ?? 'Notification');
                                        $message = is_array($notification) ? ($notification['message'] ?? '') : ($notification->message ?? '');
                                        $timeLabel = is_array($notification)
                                            ? ($notification['timestamp'] ?? 'Just now')
                                            : ($notification->created_at?->diffForHumans() ?? 'Just now');
                                    @endphp
                                    <li class="notification-item {{ $isRead ? '' : 'unread' }}">
                                        <span class="notification-dot"></span>
                                        <div>
                                            <div style="font-weight:700">{{ $title }}</div>
                                            <div style="color:var(--muted);margin-top:4px">{{ $message }}</div>
                                            <div style="color:#9ca3af;font-size:11px;margin-top:6px">{{ $timeLabel }}</div>
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
                        <x-dropdown align="right" width="w-72" contentClasses="profile-menu-panel">
                            <x-slot name="trigger">
                                <button class="header-profile-trigger" type="button" title="{{ $adminName }}" aria-label="Open profile menu">
                                    <span class="header-avatar">{{ $adminInitial }}</span>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="profile-menu-header">
                                    <div class="profile-menu-avatar">{{ $adminInitial }}</div>
                                    <div class="profile-menu-copy">
                                        <div class="profile-menu-name">{{ $adminName }}</div>
                                        <div class="profile-menu-email">{{ $adminEmail }}</div>
                                    </div>
                                </div>

                                <div class="profile-menu-divider"></div>

                                <a href="{{ route('profile.edit') }}" class="profile-menu-item">
                                    <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
                                    <span>Account</span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="js-dashboard-logout-form">
                                    @csrf
                                    <button type="submit" class="profile-menu-item profile-menu-item-signout js-dashboard-logout-trigger">
                                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                                        <span>Sign out</span>
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
        </header>

        <aside class="sidebar" id="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-menu-section">Main Menu</li>
                <li><a href="#overview" class="sidebar-menu-link active" id="overviewLink"><i class="fa-solid fa-house" aria-hidden="true"></i>Dashboard</a></li>
                <li><a href="#students" class="sidebar-menu-link" id="studentsLink"><i class="fa-solid fa-user-graduate" aria-hidden="true"></i>Students</a></li>
                <li><a href="#instructors" class="sidebar-menu-link" id="instructorsLink"><i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i>Instructors</a></li>
                <li class="sidebar-menu-section sidebar-menu-section-spaced">Records</li>
                <li><a href="#consultations" class="sidebar-menu-link" id="consultationsLink"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>Consultations</a></li>
                <li><a href="#statistics" class="sidebar-menu-link" id="statisticsLink"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i>Statistics</a></li>
                <li><a href="#system-logs" class="sidebar-menu-link" id="systemLogsLink"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i>System Logs</a></li>
            </ul>

            <div class="sidebar-logout">
                <form method="POST" action="{{ route('logout') }}" class="js-dashboard-logout-form">
                    @csrf
                    <button class="logout-btn js-dashboard-logout-trigger" type="submit">Logout</button>
                </form>
            </div>
        </aside>
    </nav>
@else
    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="shrink-0 flex items-center gap-3">
                        @if ($showSidebarToggle)
                            <button
                                id="menuBtn"
                                type="button"
                                class="inline-flex items-center justify-center rounded-md border border-gray-200 p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 md:hidden"
                                aria-label="Open sidebar menu"
                            >
                                <i class="fa-solid fa-bars" aria-hidden="true"></i>
                            </button>
                        @endif

                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold tracking-wide text-gray-800">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('student.dashboard') || request()->routeIs('instructor.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                <div>{{ $authUser?->name }}</div>

                                <div class="ms-1">
                                    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

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

                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="space-y-1 pb-3 pt-2">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('student.dashboard') || request()->routeIs('instructor.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            </div>

            <div class="border-t border-gray-200 pb-1 pt-4">
                <div class="px-4">
                    <div class="text-base font-medium text-gray-800">{{ $authUser?->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ $authUser?->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </nav>
@endif

@if ($usesDashboardLogoutModal)
    <div id="dashboardLogoutModal" class="dashboard-logout-modal" hidden>
        <div class="dashboard-logout-modal__backdrop" data-logout-dismiss></div>
        <div class="dashboard-logout-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="dashboardLogoutTitle" aria-describedby="dashboardLogoutText">
            <button type="button" class="dashboard-logout-modal__close" aria-label="Close logout dialog" data-logout-dismiss>&times;</button>
            <h2 id="dashboardLogoutTitle" class="dashboard-logout-modal__title">Log out?</h2>
            <p id="dashboardLogoutText" class="dashboard-logout-modal__text">You'll need to sign in again to keep using your dashboard.</p>
            <div class="dashboard-logout-modal__actions">
                <button type="button" class="dashboard-logout-modal__btn dashboard-logout-modal__btn--cancel" data-logout-dismiss>Cancel</button>
                <button type="button" class="dashboard-logout-modal__btn dashboard-logout-modal__btn--confirm" id="dashboardLogoutConfirmBtn">Log out</button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('dashboardLogoutModal');
            const confirmBtn = document.getElementById('dashboardLogoutConfirmBtn');

            if (!modal || !confirmBtn) {
                return;
            }

            const forms = document.querySelectorAll('.js-dashboard-logout-form');
            const triggers = document.querySelectorAll('.js-dashboard-logout-trigger');
            const dismissers = modal.querySelectorAll('[data-logout-dismiss]');
            let pendingForm = null;
            let lastTrigger = null;
            let allowLogoutSubmit = false;

            const openModal = (form, trigger) => {
                pendingForm = form;
                lastTrigger = trigger;
                modal.hidden = false;
                document.body.classList.add('logout-modal-open');
                confirmBtn.focus();
            };

            const closeModal = () => {
                modal.hidden = true;
                document.body.classList.remove('logout-modal-open');

                if (lastTrigger && typeof lastTrigger.focus === 'function') {
                    lastTrigger.focus();
                }

                pendingForm = null;
                lastTrigger = null;
            };

            triggers.forEach((trigger) => {
                trigger.addEventListener('click', (event) => {
                    const form = trigger.closest('form');

                    if (!form) {
                        return;
                    }

                    event.preventDefault();
                    openModal(form, trigger);
                });
            });

            forms.forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (allowLogoutSubmit) {
                        allowLogoutSubmit = false;
                        return;
                    }

                    event.preventDefault();
                    openModal(form, form.querySelector('.js-dashboard-logout-trigger'));
                });
            });

            dismissers.forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            confirmBtn.addEventListener('click', () => {
                if (!pendingForm) {
                    closeModal();
                    return;
                }

                document.body.classList.remove('logout-modal-open');
                allowLogoutSubmit = true;
                pendingForm.submit();
            });

            document.addEventListener('keydown', (event) => {
                if (modal.hidden) {
                    return;
                }

                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeModal();
                }
            });
        })();
    </script>
@endif
