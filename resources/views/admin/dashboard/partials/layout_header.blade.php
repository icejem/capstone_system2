<div class="dashboard admin-cyber-theme">
    <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <div class="main">
        <div class="content">
            <div class="content-header" id="dashboardContentHeader">
                <div class="dashboard-header-copy">
                    <h1 class="dashboard-header-title">
                        Welcome back, <span class="dashboard-header-name">{{ $userName }}</span>
                        <span class="dashboard-header-wave" aria-hidden="true">&#128075;</span>
                    </h1>
                    <p class="dashboard-header-subtitle">
                        Here's what's happening with consultations today
                        <span class="dashboard-header-date">&mdash; {{ now()->format('F j, Y') }}</span>
                    </p>
                </div>

                <span class="dashboard-header-bits" aria-hidden="true">
                    10110101 01101001 10100110
                    01101011 10110010 01010101
                </span>
            </div>
