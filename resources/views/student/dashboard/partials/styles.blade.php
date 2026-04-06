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

@keyframes headerGridDrift {
    0% {
        background-position: 0 0, 0 0, 0 0;
    }
    100% {
        background-position: 0 0, 38px 38px, 38px 38px;
    }
}

@keyframes headerGlowSweep {
    0% {
        transform: translateX(-20%) skewX(-18deg);
        opacity: 0;
    }
    20% {
        opacity: 0.22;
    }
    100% {
        transform: translateX(165%) skewX(-18deg);
        opacity: 0;
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

.sidebar.icon-only .sidebar-menu-section {
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

.sidebar-menu-section {
    padding: 0 20px;
    margin: 6px 0 10px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.42);
}

.sidebar-menu-section-spaced {
    margin-top: 22px;
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
    gap: 8px;
    position: relative;
    z-index: 220;
}

.notification-wrap {
    position: relative;
    z-index: 230;
}

.notification-btn {
    width: 36px;
    height: 36px;
    border: 1px solid rgba(255, 255, 255, 0.45);
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff;
    font-size: 14px;
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

.notification-btn i,
.header-account-shortcut i {
    font-size: 13px;
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
    font-size: clamp(18px, 1.6vw, 29px);
    line-height: 1.08;
    font-weight: 800;
    color: #ffffff;
    text-shadow: 0 2px 10px rgba(15, 23, 42, 0.45);
}

.dashboard-header-name {
    color: #63a6ff;
}

.dashboard-header-wave {
    display: inline-block;
    margin-left: 4px;
    font-size: 0.9em;
}

.dashboard-header-subtitle {
    margin: 4px 0 0;
    font-size: 12px;
    color: #e2e8f0;
    font-weight: 600;
}

.dashboard-header-date {
    color: rgba(191, 219, 254, 0.72);
}

.dashboard-header-bits {
    white-space: pre-line;
    font-size: 9px;
    line-height: 1.35;
    font-weight: 700;
    letter-spacing: 0.18em;
    color: rgba(147, 197, 253, 0.26);
    text-align: right;
    pointer-events: none;
}

.header-profile-trigger {
    width: 36px;
    height: 36px;
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 999px;
    background: linear-gradient(135deg, #4f5bff 0%, #8b5cf6 100%);
    color: #ffffff;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.28);
}

.header-profile-trigger:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 26px rgba(37, 99, 235, 0.34);
}

.header-avatar {
    font-size: 13px;
    font-weight: 800;
    line-height: 1;
}

.header-account-shortcut {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    border: 1px solid rgba(191, 219, 254, 0.18);
    background: rgba(255, 255, 255, 0.08);
    color: rgba(239, 246, 255, 0.94);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
}

.header-account-shortcut:hover {
    transform: translateY(-1px);
    background: rgba(255, 255, 255, 0.14);
    border-color: rgba(191, 219, 254, 0.34);
}

.profile .absolute.z-50 {
    margin-top: 10px;
    z-index: 320;
}

.profile .rounded-md.ring-1 {
    background: #ffffff;
    border: 1px solid rgba(148, 163, 184, 0.28);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 18px 36px rgba(15, 23, 42, 0.18);
}

.profile .profile-menu-panel {
    background: #ffffff;
    padding: 0;
}

.profile-menu-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px 12px;
}

.profile-menu-avatar {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    border: 1px solid #e9d5ff;
    background: #faf5ff;
    color: #9333ea;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    flex-shrink: 0;
}

.profile-menu-copy {
    min-width: 0;
}

.profile-menu-name {
    font-size: 14px;
    font-weight: 800;
    color: #1f2937;
    line-height: 1.2;
}

.profile-menu-email {
    margin-top: 2px;
    font-size: 12px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.profile-menu-divider {
    height: 1px;
    background: #e5e7eb;
}

.profile-menu-item {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: 0;
    background: transparent;
    color: #1f2937;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.18s ease, color 0.18s ease;
}

.profile-menu-item:hover {
    background: #f8fafc;
    color: #0f172a;
}

.profile-menu-item i {
    width: 18px;
    text-align: center;
    color: #6b7280;
    font-size: 15px;
    flex-shrink: 0;
}


/* ==== CONTENT ==== */
.content {
    padding: 84px 28px 44px;
}

.content.header-hidden {
    padding-top: 18px;
}

.content-header {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto auto;
    align-items: center;
    gap: 16px;
    margin: 0;
    position: fixed;
    top: 0;
    left: 259px;
    right: 0;
    overflow: visible;
    z-index: 50;
    border-radius: 0;
    padding: 10px 28px 10px 32px;
    min-height: 68px;
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.14);
}

.sidebar.icon-only ~ .main .content-header {
    left: 85px;
}



.content-header::before {
    content: "";
    position: absolute;
    inset: 0;
    background: var(--surface);
    pointer-events: none;
}

.content-header::after {
    content: "";
    position: absolute;
    top: 0;
    bottom: 0;
    left: -24%;
    width: 28%;
    background: linear-gradient(90deg, transparent 0%, rgba(125, 211, 252, 0.16) 50%, transparent 100%);
    pointer-events: none;
    animation: headerGlowSweep 13s linear infinite;
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
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
}

.overview-metric-card {
    background: #ffffff;
    border: 1px solid rgba(226, 232, 240, 0.92);
    border-radius: 22px;
    padding: 18px 18px 17px;
    box-shadow: 0 12px 28px rgba(17, 24, 39, 0.07);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: grid;
    grid-template-columns: 60px minmax(0, 1fr);
    gap: 16px;
    align-items: center;
    min-height: 104px;
}

.overview-metric-card.clickable {
    cursor: pointer;
}

.overview-metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 34px rgba(31, 58, 138, 0.12);
}

.overview-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.overview-icon.total { color: #2563eb; background: #dbeafe; }
.overview-icon.completed { color: #059669; background: #d1fae5; }
.overview-icon.pending { color: #7c3aed; background: #ede9fe; }
.overview-icon.upcoming { color: #d97706; background: #ffedd5; }

.overview-value {
    font-size: 27px;
    line-height: 1;
    margin: 0 0 7px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.03em;
}

.overview-label {
    margin: 0;
    font-size: 14px;
    color: #7181ad;
    font-weight: 500;
}

.overview-copy {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 1px;
    min-width: 0;
}

.overview-meta {
    margin: 3px 0 0;
    font-size: 12px;
    font-weight: 700;
    color: #94a3b8;
    line-height: 1.2;
}

.overview-meta-positive {
    color: #10b981;
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
    background: transparent;
    border: 1px solid rgba(96, 165, 250, 0.28);
    border-radius: 14px;
    padding: 14px;
    box-shadow: none;
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
    transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease, background 0.22s ease;
}

#request-consultation .request-card-item:hover {
    transform: translateY(-2px);
    border-color: rgba(59, 130, 246, 0.46);
    box-shadow: 0 12px 24px rgba(37, 99, 235, 0.12);
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
    background: transparent;
    border: 1px solid rgba(96, 165, 250, 0.28);
    border-radius: 14px;
    padding: 14px;
    box-shadow: none;
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

#request-consultation .request-card-item.selected {
    border-color: rgba(37, 99, 235, 0.9);
    background: linear-gradient(180deg, rgba(219, 234, 254, 0.98) 0%, rgba(239, 246, 255, 1) 100%);
    box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.34), 0 16px 34px rgba(37, 99, 235, 0.22);
    transform: translateY(-2px);
}

#request-consultation .request-card-item.selected .request-avatar {
    box-shadow: 0 0 0 6px rgba(96, 165, 250, 0.16);
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
    z-index: 300;
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
    background:
        radial-gradient(circle at top left, rgba(96, 165, 250, 0.18), transparent 28%),
        radial-gradient(circle at top right, rgba(99, 102, 241, 0.2), transparent 24%),
        rgba(2, 6, 23, 0.82);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 24px;
    backdrop-filter: blur(12px);
}

.call-modal.open { display: flex; }

.call-dialog {
    width: min(100%, 720px);
    max-width: 720px;
    max-height: min(92vh, 760px);
    background: linear-gradient(180deg, #16255c 0%, #0c1738 100%);
    border: 1px solid rgba(90, 130, 255, 0.35);
    border-radius: 28px;
    box-shadow: 0 40px 100px rgba(2, 6, 23, 0.46);
    overflow: hidden;
    animation: popIn 0.5s ease-out;
    display: flex;
    flex-direction: column;
}

.call-header {
    padding: 16px 18px 12px;
    background: rgba(9, 18, 46, 0.52);
    border-bottom: 1px solid rgba(130, 160, 255, 0.16);
    color: #eef5ff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.call-title-wrap {
    display: grid;
    gap: 4px;
}

.call-title {
    font-size: 17px;
    font-weight: 800;
    letter-spacing: -0.02em;
}

.call-hint {
    font-size: 11px;
    font-weight: 600;
    color: rgba(214, 228, 255, 0.72);
}

.call-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.call-live-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    border-radius: 999px;
    background: rgba(239, 68, 68, 0.18);
    border: 1px solid rgba(248, 113, 113, 0.45);
    color: #ffd6d6;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.06em;
}

.call-live-pill::before {
    content: "";
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: #f87171;
    box-shadow: 0 0 0 4px rgba(248, 113, 113, 0.16);
}

.call-timer {
    font-size: 12px;
    font-weight: 800;
    background: rgba(255, 255, 255, 0.08);
    color: #eef5ff;
    padding: 8px 14px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}

.call-close {
    border: 1px solid rgba(255, 255, 255, 0.12);
    width: 38px;
    height: 38px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.06);
    color: #eef5ff;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
}

.call-close:hover {
    background: rgba(255, 255, 255, 0.12);
}

.call-body {
    position: relative;
    padding: 14px;
    padding-bottom: 86px;
    overflow: hidden;
}

.call-stage {
    position: relative;
    min-height: clamp(280px, 58vh, 420px);
}

.call-video {
    width: 100%;
    background:
        radial-gradient(circle at center, rgba(111, 135, 255, 0.2), transparent 45%),
        linear-gradient(180deg, #2b3c8a 0%, #1e2b68 100%);
    border-radius: 22px;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(106, 134, 255, 0.32);
    box-shadow: 0 22px 40px rgba(2, 6, 23, 0.32);
}

.call-video-remote {
    aspect-ratio: auto;
    height: clamp(280px, 58vh, 420px);
    min-height: 280px;
}

.call-video-local {
    position: absolute;
    left: calc(100% - 158px);
    top: calc(100% - 132px);
    right: auto;
    bottom: auto;
    width: min(20vw, 132px);
    min-width: 104px;
    aspect-ratio: 1 / 1;
    z-index: 3;
    background: linear-gradient(180deg, #1a9260 0%, #116f4b 100%);
    border-color: rgba(110, 231, 183, 0.35);
    cursor: grab;
    touch-action: none;
    user-select: none;
}

.call-video-local.is-dragging {
    cursor: grabbing;
}

.call-media-surface {
    position: absolute;
    inset: 0;
    overflow: hidden;
}

.call-media-surface > div,
.call-media-surface video,
.call-media-surface canvas {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
}

.call-video video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: inherit;
}

.call-video-placeholder {
    position: absolute;
    inset: 0;
    z-index: 1;
    display: grid;
    place-items: center;
    align-content: center;
    gap: 12px;
    padding: 20px;
    text-align: center;
    background: linear-gradient(180deg, rgba(10, 17, 40, 0.12), rgba(8, 18, 43, 0.18));
    transition: opacity 0.22s ease;
}

.call-video.has-video .call-video-placeholder {
    opacity: 0;
    pointer-events: none;
}

.call-avatar {
    width: 74px;
    height: 74px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #6d8dff 0%, #7c4dff 100%);
    color: #ffffff;
    font-size: 38px;
    font-weight: 900;
    box-shadow: 0 18px 36px rgba(16, 24, 62, 0.28);
}

.call-video-local .call-avatar {
    width: 48px;
    height: 48px;
    font-size: 22px;
    background: rgba(16, 185, 129, 0.32);
    box-shadow: none;
}

.call-video-status {
    max-width: 28ch;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.45;
    color: rgba(226, 239, 255, 0.84);
}

.call-video[data-state="audio-only"] .call-avatar,
.call-video[data-state="camera-off"] .call-avatar {
    background: linear-gradient(135deg, #334155 0%, #0f172a 100%);
}

.call-video::after {
    display: none;
}

.call-video::before {
    content: "";
    position: absolute;
    top: 14px;
    right: 14px;
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: #f59e0b;
    box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.16);
    z-index: 3;
}

.call-video.has-video::before,
.call-video[data-state="audio-only"]::before {
    background: #34d399;
    box-shadow: 0 0 0 5px rgba(52, 211, 153, 0.14);
}

.call-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    position: absolute;
    left: 50%;
    bottom: 10px;
    transform: translateX(-50%);
    margin: 0;
    padding: 12px 14px;
    background: rgba(7, 17, 40, 0.94);
    border: 1px solid rgba(83, 116, 255, 0.22);
    border-radius: 18px;
    width: max-content;
    box-shadow: 0 24px 38px rgba(2, 6, 23, 0.3);
    z-index: 4;
}

.call-btn {
    width: 46px;
    height: 46px;
    border-radius: 999px;
    border: none;
    padding: 0;
    font-size: 0;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    background: rgba(255, 255, 255, 0.1);
    color: #eef5ff;
    box-shadow: inset 0 0 0 1px rgba(147, 197, 253, 0.12);
    transition: transform 0.18s ease, background 0.18s ease;
}

.call-btn:hover {
    transform: translateY(-1px);
    background: rgba(255, 255, 255, 0.16);
}

.call-btn.is-off {
    background: rgba(37, 99, 235, 0.24);
    color: #bfdbfe;
}

.call-btn.end {
    background: #ff4d5e;
    color: #fff;
    width: 46px;
    height: 46px;
}

.call-btn-icon {
    width: 18px;
    height: 18px;
    display: inline-flex;
}

.call-btn-text {
    display: none;
}

@media (max-width: 860px) {
    .call-dialog {
        width: min(96vw, 640px);
        max-width: 96vw;
        border-radius: 24px;
    }

    .call-header {
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .call-header-actions {
        width: 100%;
        justify-content: space-between;
    }

    .call-stage {
        min-height: clamp(250px, 50vh, 340px);
    }

    .call-video-remote {
        height: clamp(250px, 50vh, 340px);
        min-height: 250px;
    }

    .call-video-local {
        width: 110px;
        min-width: 110px;
    }

    .call-actions {
        width: auto;
        justify-content: center;
    }
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
    .content-header {
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        left: 260px;
    }
    .dashboard-header-bits {
        display: none;
    }
}

@media (max-width: 900px) {
    .sidebar { width: 220px; }
    .main { margin-left: 220px; }
    .content-header {
        left: 219px;
        padding: 9px 20px 9px 24px;
    }
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
        z-index: 260;
    }
    .sidebar.open { transform: translateX(0); }
    .sidebar.open ~ .main .content-header {
        z-index: 30;
    }
    .main { margin-left: 0; }
    .menu-btn { display: inline-flex; }
    .content {
        padding: 74px 14px 28px;
    }
    .overview-metrics { grid-template-columns: 1fr; }
    .overview-metric-card {
        grid-template-columns: 54px minmax(0, 1fr);
        min-height: 96px;
        padding: 16px;
        gap: 14px;
    }
    .overview-icon {
        width: 52px;
        height: 52px;
        font-size: 18px;
    }
    .consultation-card { grid-template-columns: 1fr auto; gap: 16px; padding: 16px; }
    .consultation-card > div:nth-child(2),
    .consultation-card > div:nth-child(3),
    .consultation-card > div:nth-child(4) {
        display: none;
    }
    .content-header {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: start;
        gap: 12px;
        top: 0;
        left: 0;
        right: 0;
        padding: 9px 14px 9px 16px;
        min-height: 64px;
    }
    .content-header .menu-btn {
        display: inline-flex;
        align-self: start;
    }
    .dashboard-header-bits {
        display: none;
    }
    .content-header .topbar-actions {
        width: auto;
        justify-content: flex-end;
        align-self: start;
        flex-wrap: nowrap;
        min-width: max-content;
    }
}

@media (max-width: 520px) {
    .content {
        padding: 86px 12px 32px;
    }

    .content.header-hidden {
        padding-top: 10px;
    }

    .content-header {
        left: 0;
        right: 0;
        padding: 8px 12px 8px 14px;
        border-radius: 0;
        grid-template-columns: auto 1fr auto;
        gap: 10px;
    }
    .overview-panel {
        padding: 14px;
    }
    .overview-metric-card {
        grid-template-columns: 50px minmax(0, 1fr);
        min-height: 92px;
        padding: 15px 14px;
        border-radius: 18px;
        gap: 12px;
    }
    .overview-icon {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        font-size: 17px;
    }
    .overview-value {
        font-size: 24px;
    }
    .overview-label { font-size: 13px; }
    .overview-meta { font-size: 11px; }
    .overview-panel-title {
        font-size: 21px;
    }
    .content { padding: 86px 16px 36px; }
    .content.header-hidden { padding-top: 10px; }
    .dashboard-header-title {
        font-size: 22px;
    }
    .dashboard-header-subtitle {
        font-size: 12px;
    }
    .content-header .menu-btn {
        padding: 7px 10px;
        font-size: 12px;
    }
    .content-header .menu-btn span {
        display: none;
    }
    .notification-btn,
    .header-account-shortcut,
    .header-profile-trigger,
    .student-cyber-theme .header-profile-trigger {
        width: 34px;
        height: 34px;
    }
    .topbar-actions {
        width: auto;
        justify-content: flex-end;
        gap: 8px;
        min-width: max-content;
    }
    .dashboard-header-date {
        display: none;
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
        radial-gradient(circle at 18% 14%, rgba(15, 209, 255, 0.18), transparent 34%),
        radial-gradient(circle at 82% 86%, rgba(42, 127, 255, 0.16), transparent 38%),
        linear-gradient(160deg, #07122b 0%, #0b1e40 100%);
    border-top: 1px solid rgba(94, 217, 255, 0.22);
    border-right: 0;
    border-bottom: 1px solid rgba(94, 217, 255, 0.22);
    border-left: 1px solid rgba(94, 217, 255, 0.22);
    box-shadow: 0 0 22px rgba(8, 145, 178, 0.22);
}

.student-cyber-theme .sidebar::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        radial-gradient(circle at 14% 10%, rgba(0, 247, 255, 0.1), transparent 35%),
        linear-gradient(rgba(128, 200, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(128, 200, 255, 0.05) 1px, transparent 1px);
    background-size: auto, 38px 38px, 38px 38px;
    opacity: 0.55;
}

.student-cyber-theme .sidebar::after {
    content: "";
    position: absolute;
    top: 0;
    right: -2px;
    width: 6px;
    height: 100%;
    pointer-events: none;
    background: linear-gradient(90deg, #0b1734 0%, rgba(11, 23, 52, 0) 100%);
}

.student-cyber-theme .sidebar-menu-link {
    border: 1px solid transparent;
    background: transparent;
    border-radius: 16px;
    margin: 4px 8px;
    padding: 13px 16px;
    color: rgba(226, 237, 255, 0.78);
    min-height: 46px;
    font-size: 15px;
    font-weight: 700;
    box-shadow: none;
}

.student-cyber-theme .sidebar-menu-link:hover {
    background: rgba(29, 58, 140, 0.42);
    border-color: rgba(96, 165, 250, 0.28);
    color: #f3f8ff;
    box-shadow: none;
    padding-left: 16px;
}

.student-cyber-theme .sidebar-menu-link.active {
    background: linear-gradient(135deg, rgba(34, 63, 149, 0.88), rgba(31, 54, 128, 0.9));
    border-color: rgba(96, 165, 250, 0.72);
    color: #ffffff;
    box-shadow: inset 0 0 0 1px rgba(191, 219, 254, 0.1), 0 10px 24px rgba(16, 63, 145, 0.28);
    padding-left: 16px;
}

.student-cyber-theme .sidebar.icon-only .sidebar-menu-link {
    margin: 8px auto;
}

.student-cyber-theme .sidebar-menu-link i {
    width: 18px;
    font-size: 15px;
    color: rgba(191, 219, 254, 0.82);
}

.student-cyber-theme .sidebar-menu-link:hover i,
.student-cyber-theme .sidebar-menu-link.active i {
    color: #dff4ff;
}

.student-cyber-theme .sidebar-menu-section {
    padding: 0 18px;
    margin: 2px 0 10px;
    color: rgba(160, 184, 226, 0.62);
}

.student-cyber-theme .sidebar-menu-section-spaced {
    margin-top: 26px;
}

.student-cyber-theme .logout-btn {
    background: rgba(14, 34, 96, 0.9);
    border: 1px solid rgba(125, 211, 252, 0.5);
    color: #dbeafe;
}

.student-cyber-theme .content-header {
    position: fixed;
    top: 0;
    left: 258px;
    right: 0;
    overflow: visible;
    background:
        linear-gradient(90deg, #07122b 0%, #081631 14%, rgba(8, 22, 49, 0.96) 22%, rgba(11, 30, 64, 0.98) 100%),
        radial-gradient(circle at 18% 14%, rgba(15, 209, 255, 0.18), transparent 34%),
        radial-gradient(circle at 82% 86%, rgba(42, 127, 255, 0.16), transparent 38%),
        linear-gradient(160deg, #07122b 0%, #0b1e40 100%);
    border-top: 0;
    border-right: 1px solid rgba(96, 165, 250, 0.2);
    border-bottom: 1px solid rgba(96, 165, 250, 0.2);
    border-left: 0;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.18);
}

.student-cyber-theme .sidebar.icon-only ~ .main .content-header {
    left: 84px;
}

.student-cyber-theme .content-header::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 14% 10%, rgba(0, 247, 255, 0.1), transparent 35%),
        linear-gradient(rgba(128, 200, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(128, 200, 255, 0.05) 1px, transparent 1px);
    background-size: auto, 38px 38px, 38px 38px;
    animation: headerGridDrift 18s linear infinite;
    opacity: 0.58;
    pointer-events: none;
    z-index: 0;
}

.student-cyber-theme .content-header::after {
    z-index: 0;
}

.student-cyber-theme .dashboard-header-copy,
.student-cyber-theme .notification-wrap,
.student-cyber-theme .profile,
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
    color: rgba(226, 232, 240, 0.9);
}

.student-cyber-theme .dashboard-header-bits {
    color: rgba(147, 197, 253, 0.3);
}

.student-cyber-theme .notification-btn {
    border-color: rgba(125, 211, 252, 0.7);
    background: rgba(20, 58, 138, 0.24);
    color: #ffffff;
}

.student-cyber-theme .header-account-shortcut {
    border-color: rgba(125, 211, 252, 0.36);
    background: rgba(20, 58, 138, 0.22);
}

.student-cyber-theme .header-profile-trigger {
    width: 36px;
    height: 36px;
    padding: 0;
    border-radius: 50%;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 999px;
    background: linear-gradient(135deg, #1d4ed8, #4f46e5);
    color: #fff;
    box-shadow: 0 0 0 2px rgba(125, 211, 252, 0.28), 0 0 18px rgba(59, 130, 246, 0.32);
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
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 251, 255, 0.98) 100%);
    border: 1px solid rgba(226, 232, 240, 0.95);
    color: #111827;
    box-shadow: 0 12px 26px rgba(15, 23, 42, 0.08);
}

.student-cyber-theme .overview-metric-total {
    box-shadow: 0 12px 26px rgba(37, 99, 235, 0.09);
}

.student-cyber-theme .overview-metric-completed {
    box-shadow: 0 12px 26px rgba(5, 150, 105, 0.08);
}

.student-cyber-theme .overview-metric-pending {
    box-shadow: 0 12px 26px rgba(124, 58, 237, 0.08);
}

.student-cyber-theme .overview-metric-upcoming {
    box-shadow: 0 12px 26px rgba(217, 119, 6, 0.08);
}

.student-cyber-theme .overview-metric-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 0% 0%, rgba(59, 130, 246, 0.06), transparent 34%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.24), transparent 55%);
    pointer-events: none;
}

.student-cyber-theme .overview-icon {
    border: 1px solid transparent;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
}

.student-cyber-theme .overview-icon.total {
    background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%) !important;
    color: #2563eb !important;
}

.student-cyber-theme .overview-icon.completed {
    background: linear-gradient(180deg, #ecfdf5 0%, #d1fae5 100%) !important;
    color: #059669 !important;
}

.student-cyber-theme .overview-icon.pending {
    background: linear-gradient(180deg, #f5f3ff 0%, #ede9fe 100%) !important;
    color: #7c3aed !important;
}

.student-cyber-theme .overview-icon.upcoming {
    background: linear-gradient(180deg, #fff7ed 0%, #ffedd5 100%) !important;
    color: #d97706 !important;
}

.student-cyber-theme .overview-value,
.student-cyber-theme .overview-label {
    color: #111827;
    position: relative;
    z-index: 1;
}

.student-cyber-theme .overview-label {
    color: #7483ad;
}

.student-cyber-theme .overview-meta {
    position: relative;
    z-index: 1;
    color: #9aa8c1;
}

.student-cyber-theme .overview-meta-positive {
    color: #10b981;
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
        width: min(78vw, 300px);
    }

    .content {
        padding: 74px 14px 28px;
    }

    .content.header-hidden {
        padding-top: 12px;
    }

    .section,
    #my-consultations,
    #history {
        padding: 16px;
        border-radius: 14px;
    }

    .content-header {
        padding: 9px 14px 9px 16px;
    }

    .student-cyber-theme .content-header {
        left: 0;
        right: 0;
    }

    .student-cyber-theme .sidebar.open ~ .main .content-header {
        left: 0;
        right: 0;
    }

    .content-header .topbar-actions {
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
    .student-cyber-theme .content-header {
        left: 0;
        right: 0;
    }

    .content-header .menu-btn {
        width: auto;
        justify-content: center;
        padding: 7px 9px;
    }

    .dashboard-header-title {
        font-size: 14px;
        line-height: 1.12;
        max-width: 100%;
    }

    .dashboard-header-wave,
    .dashboard-header-date,
    .dashboard-header-bits {
        display: none;
    }

    .dashboard-header-subtitle {
        display: none;
    }

    .content-header {
        left: 0;
        right: 0;
        padding: 7px 10px 7px 12px;
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 8px;
        min-height: 56px;
        align-items: start;
    }

    .content-header .topbar-actions {
        grid-column: auto;
        align-self: start;
        justify-content: flex-end;
        gap: 6px;
    }

    .overview-panel,
    .request-summary-card {
        padding: 12px;
    }

    .overview-metric-card {
        min-height: 88px;
        padding: 14px 13px;
    }

    .overview-value {
        font-size: 22px;
    }

    .request-avatar,
    .cc-avatar {
        width: 40px;
        height: 40px;
    }

    .call-actions {
        width: calc(100% - 20px);
    }

    .call-btn {
        width: 48px;
        justify-content: center;
    }
}
</style>
