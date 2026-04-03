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
    .notification-wrap {
        position: relative;
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

    .profile .absolute.z-50 {
        margin-top: 10px;
        z-index: 130;
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
        overflow: hidden;
    }

    .request-table-head {
        display: grid;
        width: 100%;
        grid-template-columns: minmax(0, 1.45fr) minmax(0, 1.2fr) minmax(0, 1.7fr) minmax(0, 1fr) minmax(0, 1.25fr);
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
        min-width: 0;
        width: 100%;
        grid-template-columns: minmax(0, 1.45fr) minmax(0, 1.2fr) minmax(0, 1.7fr) minmax(0, 1fr) minmax(0, 1.25fr);
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
        overflow-wrap: anywhere;
    }

    .request-meta.request-type .request-tag {
        margin-top: 4px;
    }

    .request-meta.request-mode {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
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

    .request-updated-inline {
        font-size: 12px;
        color: #64748b;
        font-style: italic;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .request-updated-col {
        font-size: 12px;
        color: #64748b;
        font-style: italic;
        white-space: nowrap;
    }

    .request-status-col,
    .request-updated-col {
        display: none;
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

    .request-action-status {
        width: 100%;
        display: flex;
        justify-content: flex-start;
        margin-top: 4px;
    }

    .request-action-status .request-status {
        gap: 4px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 10px;
        letter-spacing: 0.04em;
        width: auto;
        max-width: 100%;
        line-height: 1;
    }

    .request-actions .request-tag {
        font-size: 12px;
        font-weight: 700;
        padding: 7px 12px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
    }

    @media (max-width: 1280px) {
        .request-table-head,
        .request-row {
            grid-template-columns: minmax(0, 1.35fr) minmax(0, 1.1fr) minmax(0, 1.45fr) minmax(0, 0.95fr) minmax(0, 1.15fr);
        }

        .request-user,
        .request-meta,
        .request-actions {
            padding: 11px 12px;
        }

        .request-table-head > div {
            padding: 11px 12px;
            font-size: 10px;
        }

        .request-user-name,
        .request-meta.request-type .request-type-title {
            font-size: 13px;
        }

        .request-meta.request-datetime,
        .request-updated-inline,
        .request-meta.request-mode .request-tag,
        .request-actions .request-tag,
        .request-status {
            font-size: 11px;
        }

        .request-action-status .request-status {
            font-size: 10px;
            padding: 5px 9px;
        }
    }

    @media (max-width: 1120px) {
        .request-table-head,
        .request-row {
            grid-template-columns: minmax(0, 1.25fr) minmax(0, 1fr) minmax(0, 1.25fr) minmax(0, 0.9fr) minmax(0, 1.05fr);
        }

        .request-user,
        .request-meta,
        .request-actions {
            padding: 10px 11px;
        }

        .request-btn {
            padding: 7px 10px;
            font-size: 11px;
        }

        .request-actions {
            gap: 6px;
        }

        .request-action-status .request-status {
            font-size: 9.5px;
            padding: 4px 8px;
        }
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
            flex: 0 0 auto;
            gap: 6px;
        }
        .request-filter-label {
            margin: 0;
            font-size: 12px;
        }
        .request-search-wrap {
            gap: 6px;
        }
        .request-status-filter-btn,
        .request-search-input {
            min-height: 44px;
            padding: 10px 12px;
            font-size: 13px;
            border-radius: 12px;
        }
        .request-search-input {
            height: 44px;
        }
        .request-status-filter-menu {
            padding: 8px 10px;
            border-radius: 12px;
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

    .schedule-head-main {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .schedule-head-copy {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .schedule-head-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .schedule-head-exit {
        flex: 0 0 auto;
    }

    .schedule-head-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
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
    background:
        radial-gradient(circle at top left, rgba(56, 189, 248, 0.18), transparent 28%),
        radial-gradient(circle at top right, rgba(14, 116, 144, 0.2), transparent 24%),
        rgba(2, 6, 23, 0.74);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 24px;
    backdrop-filter: blur(12px);
}

.call-modal.open { display: flex; }

.call-dialog {
    width: 100%;
    max-width: 1220px;
    background: linear-gradient(180deg, rgba(248, 252, 255, 0.96), rgba(238, 246, 255, 0.98));
    border: 1px solid rgba(125, 211, 252, 0.5);
    border-radius: 30px;
    box-shadow: 0 40px 100px rgba(15, 23, 42, 0.34);
    overflow: hidden;
    animation: popIn 0.5s ease-out;
}

.call-header {
    padding: 18px 24px 12px;
    background: transparent;
    color: #0f172a;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.call-title {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.02em;
}
.call-timer {
    font-size: 13px;
    font-weight: 700;
    background: rgba(14, 165, 233, 0.12);
    color: #0f172a;
    padding: 8px 12px;
    border-radius: 999px;
}

.call-close {
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.08);
    color: #0f172a;
    font-size: 22px;
    cursor: pointer;
}

.call-body { padding: 0 24px 24px; }

.call-videos {
    display: grid;
    gap: 18px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.call-video {
    width: 100%;
    aspect-ratio: 16 / 9;
    background:
        linear-gradient(180deg, rgba(15, 23, 42, 0.22), rgba(15, 23, 42, 0.7)),
        #0f172a;
    border-radius: 22px;
    overflow: hidden;
    position: relative;
    border: 3px solid rgba(56, 189, 248, 0.85);
    box-shadow: 0 18px 36px rgba(15, 23, 42, 0.2);
}

.call-video video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 18px;
}

.call-video::after {
    content: attr(data-participant);
    position: absolute;
    left: 16px;
    bottom: 14px;
    color: #fff;
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.03em;
    text-shadow: 0 4px 14px rgba(15, 23, 42, 0.55);
}

.call-video::before {
    content: "";
    position: absolute;
    inset: auto 12px 12px auto;
    width: 36px;
    height: 36px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.65);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
    z-index: 1;
}

.call-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 22px;
    padding: 14px 18px;
    background: rgba(255, 255, 255, 0.88);
    border: 1px solid rgba(148, 163, 184, 0.22);
    border-radius: 999px;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 0 18px 36px rgba(148, 163, 184, 0.24);
}

.call-btn {
    width: 54px;
    height: 54px;
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
    background: #e2e8f0;
    color: #0f172a;
    box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.18);
}

.call-btn.end {
    background: #ef4444;
    color: #fff;
    width: 68px;
    height: 54px;
    font-size: 13px;
    padding: 0 14px;
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
    .call-videos {
        grid-template-columns: 1fr;
    }

    .call-dialog {
        max-width: 96vw;
        border-radius: 24px;
    }

    .call-video::after {
        font-size: 22px;
    }

    .call-actions {
        width: 100%;
        flex-wrap: wrap;
        border-radius: 22px;
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

#consultationHistoryInline .history-table {
    background: #f3f4f6;
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: none;
}

#consultationHistoryInline.section {
    background: transparent;
    box-shadow: none;
    padding: 0;
    overflow: visible;
}

#consultationHistoryInline .history-row-wrap .history-row {
    border-bottom: 1px solid #dbe1ea;
}

#consultationHistoryInline .history-row-wrap:last-child .history-row {
    border-bottom: none;
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
    .history-student-name {
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
        border: none;
        box-shadow: none;
        background: transparent;
    }

    .history-row {
        gap: 7px;
        padding: 11px 12px;
    }

    .history-row.header {
        letter-spacing: 0.02em;
    }

    .history-action-cell .view-link {
        padding: 6px 9px;
    }
}

.date-time {
    display: grid;
    gap: 4px;
}

.date-time span:last-child {
    color: var(--muted);
    font-size: 12px;
}

.history-student-cell {
    display: grid;
    grid-template-columns: auto 1fr;
    align-items: start;
    gap: 10px;
    min-width: 0;
}

.history-student-cell .request-avatar {
    display: none;
}

.history-student-meta {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.history-student-name {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
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
    align-items: flex-start;
    line-height: 1.45;
    overflow-wrap: anywhere;
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

.details-actions-content .request-btn,
.details-actions-content .view-link,
.details-actions-content form {
    width: 100%;
}

.request-mobile-details {
    display: none;
}

.request-mobile-details-btn {
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

.request-mobile-details-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 18px rgba(47, 78, 178, 0.16);
    border-color: #8fa8ff;
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
    .availability-head {
        flex-direction: column;
        align-items: stretch;
        gap: 14px;
    }

    .schedule-head-main {
        gap: 10px;
    }

    .schedule-head-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .schedule-layout {
        display: block;
        overflow-x: auto;
        padding-bottom: 6px;
        -webkit-overflow-scrolling: touch;
    }

    .schedule-grid {
        grid-template-columns: repeat(6, minmax(92px, 1fr));
        min-width: 646px;
        gap: 12px 14px;
    }

    .schedule-cell {
        min-height: 72px;
        align-items: flex-start;
    }

    .schedule-slot,
    .schedule-empty {
        width: 100%;
        min-width: 0;
    }

    .schedule-slot {
        border-radius: 12px;
        padding: 10px 8px;
    }

    .schedule-empty {
        text-align: center;
        padding-top: 16px;
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
    .availability-head {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        grid-template-areas:
            "main exit"
            "actions actions";
        align-items: start;
        gap: 12px;
    }

    .schedule-head-main {
        grid-area: main;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .schedule-head-copy {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .schedule-head-meta {
        gap: 8px;
    }

    .schedule-head-exit {
        grid-area: exit;
        align-self: start;
        justify-self: end;
    }

    .schedule-head-actions {
        grid-area: actions;
        width: 100%;
        justify-content: flex-start;
    }

    .schedule-meta-inline {
        width: fit-content;
        max-width: 100%;
    }

    .schedule-head-actions .export-btn,
    .schedule-head-actions .section-close,
    .schedule-head-actions .availability-open-btn {
        min-height: 40px;
        padding: 9px 12px;
        font-size: 12px;
    }

    .schedule-grid {
        min-width: 620px;
        gap: 10px 12px;
    }

    .schedule-day {
        font-size: 11px;
        padding-bottom: 8px;
    }

    .schedule-slot {
        font-size: 11px;
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

@media (max-width: 768px) {
    .history-row {
        grid-template-columns: 1fr;
        gap: 10px;
        align-items: flex-start;
    }
.history-row.header {
    display: none;
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
    }

    .history-row.history-row-item > :not(:nth-child(2)):not(:nth-child(4)):not(:nth-child(7)) {
        display: none !important;
    }

    .history-row.history-row-item > div:nth-child(2),
    .history-row.history-row-item > div:nth-child(4),
    .history-row.history-row-item > div:nth-child(7) {
        min-width: 0;
    }

    .history-student-cell {
        gap: 10px;
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
    }

    .history-student-cell .request-avatar {
        display: inline-flex;
        width: 36px;
        height: 36px;
        font-size: 11px;
        flex: 0 0 auto;
    }

    .history-student-meta {
        min-width: 0;
    }

    .history-student-name {
        font-size: 13px;
    }

    .history-student-id {
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
        display: flex;
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
        text-decoration: none;
    }

    .details-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .feedback-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 560px) {
    .details-modal {
        padding: 8px;
        align-items: center;
    }

    .details-dialog {
        max-width: none;
        border-radius: 16px;
        max-height: min(96vh, 760px);
    }

    .details-header {
        padding: 16px 16px 14px;
        align-items: flex-start;
    }

    .details-title {
        font-size: 17px;
    }

    .details-subtitle {
        font-size: 11px;
        line-height: 1.35;
        max-width: 220px;
    }

    .details-close {
        font-size: 24px;
    }

    .details-body {
        padding: 10px 12px 14px;
    }

    .details-grid {
        gap: 8px;
        margin-bottom: 10px;
    }

    .details-card {
        min-height: 0;
        padding: 10px 11px;
        font-size: 12px;
    }

    .details-summary {
        margin-top: 10px;
        padding: 11px 12px;
    }

    .details-summary-title,
    #detailsNotesText,
    #detailsSummaryText,
    #detailsTranscriptText,
    .details-actions-content {
        font-size: 12px;
        line-height: 1.45;
    }

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
        transform: translateX(-100%);
        transition: transform 0.25s ease;
    }

    .instructor-cyber-theme .sidebar.open {
        transform: translateX(0);
    }

    .instructor-cyber-theme .main {
        margin-left: 0;
    }

    .instructor-cyber-theme .content {
        padding: 14px 12px 28px;
    }

    .instructor-cyber-theme .content-header {
        display: grid;
        grid-template-columns: auto 1fr auto;
        grid-template-areas:
            "menu spacer actions"
            "copy copy copy";
        align-items: stretch;
        gap: 12px;
        padding: 16px;
    }

    .instructor-cyber-theme .menu-btn {
        grid-area: menu;
        display: inline-flex;
        align-self: start;
        justify-self: start;
        gap: 8px;
        background: #dbeafe;
        border: 1px solid #bfdbfe;
        color: #1F3A8A;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
    }

    .instructor-cyber-theme .dashboard-header-copy {
        grid-area: copy;
        width: 100%;
    }

    .instructor-cyber-theme .topbar-actions {
        grid-area: actions;
        width: auto;
        justify-content: flex-end;
        justify-self: end;
        align-self: start;
        flex-wrap: nowrap;
        gap: 10px;
        min-width: max-content;
    }

    .instructor-cyber-theme .notification-panel {
        width: min(86vw, 300px);
        right: 0;
        top: 46px;
        border-radius: 14px;
    }

    .instructor-cyber-theme .notification-header {
        padding: 12px 14px;
        font-size: 12px;
    }

    .instructor-cyber-theme .notification-list {
        max-height: 240px;
    }

    .instructor-cyber-theme .notification-item {
        padding: 12px 14px;
        font-size: 12px;
        gap: 10px;
    }

    .instructor-cyber-theme .notification-item > div {
        min-width: 0;
    }

    .instructor-cyber-theme .stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .instructor-cyber-theme .stat-card {
        padding: 14px;
        gap: 12px;
        border-radius: 14px;
    }

    .instructor-cyber-theme .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        font-size: 15px;
    }

    .instructor-cyber-theme .stat-count {
        font-size: 28px;
        line-height: 1;
        margin-bottom: 4px;
    }

    .instructor-cyber-theme .stat-card [style*="font-size: 13px"] {
        font-size: 12px !important;
        margin-top: 2px !important;
    }

    .instructor-cyber-theme .overview-panels,
    .instructor-cyber-theme .feedback-grid {
        grid-template-columns: 1fr;
    }

    .instructor-cyber-theme .details-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .instructor-cyber-theme .details-card {
        min-width: 0;
        min-height: 48px;
        padding: 9px 10px;
        font-size: 12px;
        line-height: 1.35;
        overflow-wrap: anywhere;
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
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
        padding: 14px;
        border: 1px solid #dbe1ea;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    }

    .instructor-cyber-theme .request-row:hover {
        transform: none;
        border-color: transparent;
        background: #ffffff;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    }

    .instructor-cyber-theme .request-row > :not(.request-user):not(.request-mobile-details) {
        display: none !important;
    }

    .instructor-cyber-theme .request-user,
    .instructor-cyber-theme .request-mobile-details {
        display: flex !important;
    }

    .instructor-cyber-theme .request-user,
    .instructor-cyber-theme .request-meta,
    .instructor-cyber-theme .request-status-col,
    .instructor-cyber-theme .request-updated-col,
    .instructor-cyber-theme .request-actions {
        padding: 0;
    }

    .instructor-cyber-theme .request-user {
        display: grid !important;
        grid-template-columns: auto 1fr;
        gap: 10px;
        align-items: start;
    }

    .instructor-cyber-theme .request-user-top {
        align-items: flex-start;
    }

    .instructor-cyber-theme .request-mobile-details {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        align-self: center;
    }

    .instructor-cyber-theme .request-mobile-details-btn {
        padding: 8px 12px;
        font-size: 11px;
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
        width: 100%;
        border-radius: 20px;
    }

    .instructor-cyber-theme .call-btn {
        flex: 0 0 auto;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .instructor-cyber-theme .content {
        padding: 10px 8px 24px;
    }

    .instructor-cyber-theme .content-header {
        padding: 12px 14px;
        border-radius: 12px;
    }

    .instructor-cyber-theme .dashboard-header-title {
        font-size: 20px;
    }

    .instructor-cyber-theme .dashboard-header-subtitle {
        font-size: 11px;
    }

    .instructor-cyber-theme .menu-btn {
        padding: 7px 10px;
        font-size: 12px;
    }

    .instructor-cyber-theme .menu-btn span {
        display: none;
    }

    .instructor-cyber-theme .notification-btn,
    .instructor-cyber-theme .header-profile-trigger {
        width: 40px;
        height: 40px;
    }

    .instructor-cyber-theme .topbar-actions {
        width: auto;
        gap: 8px;
    }

    .instructor-cyber-theme .stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .instructor-cyber-theme .schedule-layout {
        display: block;
        overflow-x: auto;
        padding-bottom: 6px;
        -webkit-overflow-scrolling: touch;
    }

    .instructor-cyber-theme .schedule-grid {
        grid-template-columns: repeat(6, minmax(92px, 1fr));
        min-width: 620px;
        gap: 10px 12px;
    }

    .instructor-cyber-theme .request-actions > *,
    .instructor-cyber-theme .request-actions form,
    .instructor-cyber-theme .call-btn {
        flex-basis: 100%;
    }

    .instructor-cyber-theme .request-actions .start-session-form,
    .instructor-cyber-theme .request-actions .start-session-btn {
        flex: 0 0 auto;
        width: auto;
    }

    .instructor-cyber-theme .summary-body {
        padding: 12px;
    }
</style>
