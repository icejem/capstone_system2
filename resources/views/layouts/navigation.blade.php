@php
    $authUser = Auth::user();
    $isStudentDashboard = request()->routeIs('student.dashboard');
    $showSidebarToggle = $isStudentDashboard;
    $studentNotifications = collect($notifications ?? []);
    $studentUnreadCount = $studentNotifications->where('is_read', false)->count();
    $studentName = $authUser?->name ?? 'Student';
    $studentEmail = $authUser?->email ?? 'student@example.com';
    $studentInitial = 'U';
    if ($authUser?->name) {
        $firstChar = function_exists('mb_substr') ? mb_substr(trim($authUser->name), 0, 1) : substr(trim($authUser->name), 0, 1);
        $studentInitial = function_exists('mb_strtoupper') ? mb_strtoupper($firstChar) : strtoupper($firstChar);
    }
@endphp

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

                    <a href="{{ route('profile.edit') }}" class="header-account-shortcut" aria-label="Open account">
                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                    </a>

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
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="profile-menu-item profile-menu-item-signout">
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
                <a href="{{ route('profile.edit') }}" class="sidebar-menu-link sidebar-footer-link">
                    <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
                    Account
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">Logout</button>
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
