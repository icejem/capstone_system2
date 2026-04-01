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
                    <h1 class="dashboard-header-title">Welcome back, {{ $userName }}!</h1>
                    <p class="dashboard-header-subtitle">Here's what's happening with consultations today</p>
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
