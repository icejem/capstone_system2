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
        transition: background-color 0.18s ease, color 0.18s ease;
        cursor: pointer;
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

    .profile-menu-item-signout {
        color: #111827;
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

    .dashboard-header-name {
        color: #60a5fa;
    }

    .dashboard-header-wave {
        font-size: 0.8em;
        margin-left: 4px;
    }

    .dashboard-header-date {
        color: #8fb7ff;
        font-weight: 700;
    }

    .dashboard-header-bits {
        margin-left: auto;
        font-size: 10px;
        line-height: 1.35;
        letter-spacing: 0.18em;
        color: rgba(147, 197, 253, 0.28);
        text-align: right;
        white-space: pre-line;
    }

    .header-account-shortcut {
        width: 36px;
        height: 36px;
        border: 1px solid rgba(255, 255, 255, 0.45);
        background: rgba(255, 255, 255, 0.18);
        color: #ffffff;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .header-account-shortcut:hover {
        background: rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }

    .header-profile-trigger {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 999px;
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
        font-size: 14px;
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
        border: 1px solid rgba(31, 58, 138, 0.16);
        border-radius: 22px;
        box-shadow: 0 12px 28px rgba(17, 24, 39, 0.07);
        padding: 18px 18px 17px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: popIn 0.5s ease-out backwards;
        display: grid;
        grid-template-columns: 50px minmax(0, 1fr);
        gap: 16px;
        align-items: center;
        min-height: 104px;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.15s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.25s; }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 34px rgba(31, 58, 138, 0.12);
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

    .stat-copy {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1px;
        min-width: 0;
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
        font-size: 27px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 7px;
        letter-spacing: -0.03em;
    }

    .stat-label {
        font-size: 14px;
        color: #7181ad;
        font-weight: 500;
    }

    .stat-meta {
        margin-top: 3px;
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        line-height: 1.2;
    }

    .stat-meta-positive {
        color: #10b981;
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
    .status-cancelled { background: #ffe4e6; color: #be123c; }
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
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .consultations-filter-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
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

    .consultation-filter-group-request .students-filter {
        padding: 10px 12px;
        border: 1px solid rgba(96, 165, 250, 0.28);
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(241, 245, 249, 0.95));
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
    }

    .consultation-filter-group-request .students-filter:focus {
        border-color: rgba(103, 232, 249, 0.62);
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.14);
        outline: none;
    }

    .consultation-filter-group-request label {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0;
        text-transform: none;
        color: #1e293b;
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

    .stats-metric-card.consultations { background: linear-gradient(135deg, #1e3a8a, #2563eb); }
    .stats-metric-card.types { background: linear-gradient(135deg, #1d4ed8, #38bdf8); }
    .stats-metric-card.period { background: linear-gradient(135deg, #0f172a, #1e40af); }

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
        overflow-x: hidden;
    }

    .admin-consultation-head {
        display: grid;
        grid-template-columns: 1.05fr 1.05fr 1fr 1.2fr 0.8fr 0.8fr 0.72fr;
        align-items: center;
        background: #eef2f7;
        border-bottom: 1px solid #dbe1ea;
    }

    .admin-consultation-head > div {
        padding: 10px 12px;
        font-size: 10px;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #425066;
        font-weight: 800;
    }

    .admin-consultation-table {
        display: block;
        min-width: 0;
    }

    .admin-consultation-row {
        display: grid;
        grid-template-columns: 1.05fr 1.05fr 1fr 1.2fr 0.8fr 0.8fr 0.72fr;
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
        padding: 10px 12px;
        min-width: 0;
    }

    .admin-consultation-party {
        display: grid;
        gap: 4px;
    }

    .admin-consultation-primary {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
        word-break: break-word;
    }

    .admin-consultation-secondary {
        font-size: 11px;
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
        radial-gradient(circle at 18% 14%, rgba(15, 209, 255, 0.18), transparent 34%),
        radial-gradient(circle at 82% 86%, rgba(42, 127, 255, 0.16), transparent 38%),
        linear-gradient(160deg, #07122b 0%, #0b1e40 100%);
    border: 1px solid rgba(94, 217, 255, 0.22);
    box-shadow: 0 0 22px rgba(8, 145, 178, 0.22);
}

.admin-cyber-theme .sidebar::before {
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

.admin-cyber-theme .sidebar-menu-link {
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

.admin-cyber-theme .sidebar-menu-link:hover {
    background: rgba(29, 58, 140, 0.42);
    border-color: rgba(96, 165, 250, 0.28);
    color: #f3f8ff;
    box-shadow: none;
    padding-left: 16px;
}

.admin-cyber-theme .sidebar-menu-link.active {
    background: linear-gradient(135deg, rgba(34, 63, 149, 0.88), rgba(31, 54, 128, 0.9));
    border-color: rgba(96, 165, 250, 0.72);
    color: #ffffff;
    box-shadow: inset 0 0 0 1px rgba(191, 219, 254, 0.1), 0 10px 24px rgba(16, 63, 145, 0.28);
    padding-left: 16px;
}

.admin-cyber-theme .sidebar.icon-only .sidebar-menu-link {
    margin: 8px auto;
}

.admin-cyber-theme .sidebar-menu-link i {
    width: 18px;
    font-size: 15px;
    color: rgba(191, 219, 254, 0.82);
}

.admin-cyber-theme .sidebar-menu-link:hover i,
.admin-cyber-theme .sidebar-menu-link.active i {
    color: #dff4ff;
}

.admin-cyber-theme .sidebar-menu-section {
    padding: 0 18px;
    margin: 2px 0 10px;
    color: rgba(160, 184, 226, 0.62);
}

.admin-cyber-theme .sidebar-menu-section-spaced {
    margin-top: 26px;
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
    background:
        radial-gradient(circle at 14% 10%, rgba(0, 247, 255, 0.12), transparent 35%),
        linear-gradient(rgba(128, 200, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(128, 200, 255, 0.05) 1px, transparent 1px),
        linear-gradient(135deg, #10224d 0%, #173574 48%, #1e3a8a 100%);
    background-size: auto, 38px 38px, 38px 38px, cover;
    border: 1px solid rgba(94, 217, 255, 0.22);
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.22);
}

.admin-cyber-theme .content-header::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 12% 0%, rgba(96, 165, 250, 0.12), transparent 28%),
        linear-gradient(180deg, rgba(31, 58, 138, 0.2) 0%, rgba(30, 64, 175, 0.16) 100%);
    pointer-events: none;
    z-index: 0;
}

.admin-cyber-theme .content-header::after {
    content: none;
}

    .admin-cyber-theme .dashboard-header-copy,
    .admin-cyber-theme .dashboard-header-bits,
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
    text-shadow: 0 2px 10px rgba(15, 23, 42, 0.45);
    letter-spacing: 0;
}

.admin-cyber-theme .dashboard-header-subtitle {
    color: #e2e8f0;
}

.admin-cyber-theme .dashboard-header-date {
    color: #8fb7ff;
}

.admin-cyber-theme .dashboard-header-bits {
    color: rgba(147, 197, 253, 0.3);
}

.admin-cyber-theme .notification-btn {
    border-color: rgba(125, 211, 252, 0.7);
    background: rgba(20, 58, 138, 0.24);
    color: #ffffff;
}

.admin-cyber-theme .header-account-shortcut {
    border-color: rgba(125, 211, 252, 0.36);
    background: rgba(20, 58, 138, 0.22);
}

.admin-cyber-theme .header-profile-trigger {
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

.admin-cyber-theme .header-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.admin-cyber-theme .stat-card {
    position: relative;
    overflow: hidden;
    background:
        linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 251, 255, 0.98) 100%);
    border: 1px solid rgba(31, 58, 138, 0.38);
    color: #111827;
    box-shadow: 0 0 0 1px rgba(29, 78, 216, 0.08), 0 12px 26px rgba(15, 23, 42, 0.08);
}

.admin-cyber-theme .stat-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 0% 0%, rgba(59, 130, 246, 0.06), transparent 34%),
        linear-gradient(180deg, rgba(255, 255, 255, 0.24), transparent 55%);
    pointer-events: none;
}

.admin-cyber-theme .stat-icon {
    background: #dbeafe !important;
    color: #1d4ed8 !important;
    border: 1px solid #bfdbfe;
}

.admin-cyber-theme .stat-value,
.admin-cyber-theme .stat-label,
.admin-cyber-theme .stat-meta {
    color: #111827;
    position: relative;
    z-index: 1;
}

.admin-cyber-theme .stat-label {
    color: #7483ad;
}

.admin-cyber-theme .stat-meta {
    color: #9aa8c1;
}

.admin-cyber-theme .stat-meta-positive {
    color: #10b981;
}

.admin-cyber-theme .stat-chip {
    background: #eef2ff !important;
    color: #3730a3 !important;
    border-color: #c7d2fe !important;
}

.admin-cyber-theme .stat-card-students {
    box-shadow: 0 12px 26px rgba(7, 89, 133, 0.08);
}

.admin-cyber-theme .stat-card-instructors {
    box-shadow: 0 12px 26px rgba(4, 120, 87, 0.08);
}

.admin-cyber-theme .stat-card-consultations {
    box-shadow: 0 12px 26px rgba(194, 65, 12, 0.08);
}

.admin-cyber-theme .stat-card-completed {
    box-shadow: 0 12px 26px rgba(91, 33, 182, 0.08);
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

    @media (max-width: 900px) {
        .dashboard-header-bits {
            display: none;
        }

        .stat-card {
            grid-template-columns: 48px minmax(0, 1fr);
            min-height: 96px;
            padding: 16px;
            gap: 14px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            font-size: 17px;
        }
    }

    @media (max-width: 520px) {
        .stat-card {
            grid-template-columns: 44px minmax(0, 1fr);
            min-height: 90px;
            padding: 14px 13px;
            border-radius: 18px;
            gap: 12px;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            font-size: 16px;
        }

        .stat-value {
            font-size: 22px;
        }

        .stat-label {
            font-size: 13px;
        }

        .stat-meta {
            font-size: 11px;
        }
    }

    /* Match student header layout and spacing */
    .admin-cyber-theme .content-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 70px;
        margin-bottom: 20px;
        padding: 10px 16px 10px 18px;
        border-radius: 0;
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

    .admin-cyber-theme .content-header::before {
        background:
            radial-gradient(circle at 14% 10%, rgba(0, 247, 255, 0.1), transparent 35%),
            linear-gradient(rgba(128, 200, 255, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(128, 200, 255, 0.05) 1px, transparent 1px);
        background-size: auto, 38px 38px, 38px 38px;
        animation: headerGridDrift 18s linear infinite;
        opacity: 0.58;
    }

    .admin-cyber-theme .topbar-actions {
        gap: 8px;
        position: relative;
        z-index: 220;
    }

    .admin-cyber-theme .notification-wrap {
        position: relative;
        z-index: 230;
    }

    .admin-cyber-theme .notification-btn,
    .admin-cyber-theme .header-account-shortcut,
    .admin-cyber-theme .header-profile-trigger {
        width: 36px;
        height: 36px;
    }

    .admin-cyber-theme .notification-btn {
        border-radius: 12px;
    }

    .admin-cyber-theme .notification-btn i,
    .admin-cyber-theme .header-account-shortcut i {
        font-size: 13px;
    }

    .admin-cyber-theme .dashboard-header-copy {
        min-width: 0;
    }

    .admin-cyber-theme .dashboard-header-title {
        font-size: clamp(18px, 1.6vw, 29px);
        line-height: 1.08;
    }

    .admin-cyber-theme .dashboard-header-name {
        color: #63a6ff;
    }

    .admin-cyber-theme .dashboard-header-wave {
        display: inline-block;
        margin-left: 4px;
        font-size: 0.9em;
    }

    .admin-cyber-theme .dashboard-header-subtitle {
        margin: 4px 0 0;
        font-size: 12px;
        color: rgba(226, 232, 240, 0.9);
    }

    .admin-cyber-theme .dashboard-header-date {
        color: rgba(191, 219, 254, 0.72);
        font-weight: 600;
    }

    .admin-cyber-theme .dashboard-header-bits {
        white-space: pre-line;
        font-size: 9px;
        line-height: 1.35;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-align: right;
        pointer-events: none;
        margin-left: auto;
    }

    .admin-cyber-theme .header-account-shortcut {
        border-radius: 999px;
        color: rgba(239, 246, 255, 0.94);
        transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
    }

    .admin-cyber-theme .header-account-shortcut:hover {
        transform: translateY(-1px);
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(191, 219, 254, 0.34);
        color: rgba(239, 246, 255, 0.94);
    }

    .admin-cyber-theme .header-profile-trigger {
        border: 1px solid rgba(255, 255, 255, 0.16);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .admin-cyber-theme .header-profile-trigger:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 26px rgba(37, 99, 235, 0.34);
    }

    .admin-cyber-theme .header-avatar {
        font-size: 13px;
        font-weight: 800;
        line-height: 1;
    }

    .admin-cyber-theme .profile .absolute.z-50 {
        margin-top: 10px;
        z-index: 320;
    }

    @media (max-width: 768px) {
        .admin-cyber-theme .content {
            padding: 74px 14px 28px;
        }

        .admin-cyber-theme .content-header {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: start;
            gap: 12px;
            padding: 9px 14px 9px 16px;
            min-height: 64px;
        }

        .admin-cyber-theme .content-header .menu-btn {
            display: inline-flex;
            align-self: start;
        }

        .admin-cyber-theme .dashboard-header-bits {
            display: none;
        }

        .admin-cyber-theme .content-header .topbar-actions {
            width: auto;
            justify-content: flex-end;
            align-self: start;
            flex-wrap: nowrap;
            min-width: max-content;
        }
    }

    @media (max-width: 520px) {
        .admin-cyber-theme .content {
            padding: 86px 12px 32px;
        }

        .admin-cyber-theme .content-header {
            padding: 8px 12px 8px 14px;
            grid-template-columns: auto 1fr auto;
            gap: 10px;
        }
    }

    @media (max-width: 480px) {
        .admin-cyber-theme .content-header .menu-btn {
            width: auto;
            justify-content: center;
            padding: 7px 9px;
        }

        .admin-cyber-theme .dashboard-header-title {
            font-size: 14px;
            line-height: 1.12;
            max-width: 100%;
        }

        .admin-cyber-theme .dashboard-header-wave,
        .admin-cyber-theme .dashboard-header-date,
        .admin-cyber-theme .dashboard-header-bits,
        .admin-cyber-theme .dashboard-header-subtitle {
            display: none;
        }

        .admin-cyber-theme .content-header {
            padding: 7px 10px 7px 12px;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 8px;
            min-height: 56px;
            align-items: start;
        }

        .admin-cyber-theme .content-header .topbar-actions {
            align-self: start;
            justify-content: flex-end;
            gap: 6px;
        }
    }
</style>
