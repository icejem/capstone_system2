<script>
    const sidebar = document.getElementById('sidebar');
    const adminDashboardRoot = document.querySelector('.dashboard.admin-cyber-theme');
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
    const systemLogsSection = document.getElementById('systemLogsSection');
    const dashboardContentHeader = document.getElementById('dashboardContentHeader');
    const adminContentContainer = document.querySelector('.main .content');
    const overviewLink = document.getElementById('overviewLink');
    const studentsLink = document.getElementById('studentsLink');
    const instructorsLink = document.getElementById('instructorsLink');
    const consultationsLink = document.getElementById('consultationsLink');
    const statisticsLink = document.getElementById('statisticsLink');
    const systemLogsLink = document.getElementById('systemLogsLink');
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
    const systemLogSearch = document.getElementById('systemLogSearch');
    const systemLogRoleFilter = document.getElementById('systemLogRoleFilter');
    const systemLogStatusFilter = document.getElementById('systemLogStatusFilter');
    const systemLogDateFrom = document.getElementById('systemLogDateFrom');
    const systemLogDateTo = document.getElementById('systemLogDateTo');
    const systemLogRows = Array.from(document.querySelectorAll('#systemLogTableBody .system-log-row'));
    const systemLogEmptyState = document.getElementById('systemLogEmptyState');
    const systemLogPaginationInfo = document.getElementById('systemLogPaginationInfo');
    const systemLogPageNumbers = document.getElementById('systemLogPageNumbers');
    const prevSystemLogBtn = document.getElementById('prevSystemLogBtn');
    const nextSystemLogBtn = document.getElementById('nextSystemLogBtn');
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
    const consultationCategoryFilter = document.getElementById('consultationCategoryFilter');
    const consultationTypeFilter = document.getElementById('consultationTypeFilter');
    const consultationStatusFilter = document.getElementById('consultationStatusFilter');
    const consultationYearInput = document.getElementById('consultationYearInput');
    const consultationExportBtn = document.getElementById('consultationExportBtn');
    const consultationSemButtons = Array.from(document.querySelectorAll('#consultationsSection .consultation-semester-btn[data-sem]'));
    const consultationMonthPickerContainer = document.getElementById('consultationMonthPickerContainer');
    const consultationMonthSelect = document.getElementById('consultationMonthSelect');
    const consultationTableBody = document.getElementById('consultationTableBody');
    const consultationDetailsModal = document.getElementById('consultationDetailsModal');
    let consultationViewButtons = Array.from(document.querySelectorAll('.consultation-view-btn'));
    let activeConsultationDetailsId = '';
    const closeConsultationDetailsModal = document.getElementById('closeConsultationDetailsModal');
    const detailsSubtitle = document.getElementById('detailsSubtitle');
    const detailsExportBtn = document.getElementById('detailsExportBtn');
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
    const statusConfirmModal = document.getElementById('statusConfirmModal');
    const statusConfirmTitle = document.getElementById('statusConfirmTitle');
    const statusConfirmMessage = document.getElementById('statusConfirmMessage');
    const statusConfirmUser = document.getElementById('statusConfirmUser');
    const closeStatusConfirmModal = document.getElementById('closeStatusConfirmModal');
    const cancelStatusConfirm = document.getElementById('cancelStatusConfirm');
    const confirmStatusChange = document.getElementById('confirmStatusChange');
    const openAddInstructor = document.getElementById('openAddInstructor');
    const addInstructorModal = document.getElementById('addInstructorModal');
    const closeAddInstructor = document.getElementById('closeAddInstructor');
    const cancelAddInstructor = document.getElementById('cancelAddInstructor');
    const statsSource = @json($statisticsRows ?? []);
    const latestNotification = @json($notifications->firstWhere('read', false));
    const unreadCount = @json($unreadCount);
    const adminToastUserId = @json(auth()->id());
    let activeManageRow = null;
    let activeManageUserId = '';
    let activeManageButton = null;
    let pendingStatusChange = null;
    let filteredSystemLogRows = [];
    let currentSystemLogPage = 1;
    const systemLogRowsPerPage = 10;
    let studentRowsAll = [];
    let instructorRowsAll = [];
    const adminUserStatusEndpointTemplate = @json(url('/admin/users/__USER__/status'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const adminAccessDeniedRedirectUrl = @json(route('login'));
    let adminAccessRedirectPending = false;

    async function handleAdminAccessDenied(response) {
        if (response.status !== 423) {
            return response;
        }

        const data = await response.json().catch(() => ({}));
        if (!adminAccessRedirectPending) {
            adminAccessRedirectPending = true;
            window.location.href = data?.redirect || adminAccessDeniedRedirectUrl;
        }

        throw new Error(data?.message || 'Access denied.');
    }

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
                adminDashboardRoot?.classList.remove('admin-sidebar-collapsed');
                syncSidebarBackdropState();
                return;
            }
            sidebar.classList.toggle('open');
            if (sidebar.classList.contains('open')) {
                adminDashboardRoot?.classList.remove('admin-sidebar-collapsed');
            }
            syncSidebarBackdropState();
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', () => {
            if (!sidebar) return;
            sidebar.classList.remove('open');
            adminDashboardRoot?.classList.remove('admin-sidebar-collapsed');
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
        const category = escapeAdminNotificationHtml(row.category || '');
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
            <div class="admin-consultation-row"
                data-status="${escapeAdminNotificationHtml(status)}"
                data-date="${date}"
                data-category="${escapeAdminNotificationHtml(String(row.category || ''))}"
                data-type="${escapeAdminNotificationHtml(String(row.type || ''))}"
                data-mode="${escapeAdminNotificationHtml(String(row.mode || ''))}"
                data-search-all="${searchAll}">
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
                       data-consultation-id="${escapeAdminNotificationHtml(String(row?.consultation_id ?? row?.id ?? ''))}"
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
                    consultationId: btn.dataset.consultationId || '',
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
            updateConsultationFilterOptions();
            showConsultationPage(1, { scroll: false });
            bindConsultationViewButtons();
            return;
        }

        consultationTableBody.innerHTML = consultations.map((row) => buildAdminConsultationRow(row)).join('');
        consultationRowsAll = Array.from(consultationTableBody.querySelectorAll('.admin-consultation-row[data-status]'));
        updateConsultationFilterOptions();
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
            consultationId: matched.id || '',
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

    function buildAdminOnlineStatusHtml(row = {}) {
        if (row?.is_online) {
            return '<span class="online-badge">Online</span>';
        }

        if (row?.last_active_minutes !== null && row?.last_active_minutes !== undefined && row?.last_active_minutes !== '') {
            const minutes = Number(row.last_active_minutes || 0);
            const label = minutes === 1 ? 'min' : 'mins';
            return `<span class="user-active-minutes-badge">Active ${escapeAdminNotificationHtml(minutes)} ${label} ago</span>`;
        }

        return '<span style="color:var(--muted);font-size:11px;font-weight:700;">Offline</span>';
    }

    function buildAdminStudentTableRow(row = {}) {
        const status = String(row?.status || 'inactive').toLowerCase();
        const name = escapeAdminNotificationHtml(row?.name || 'Student');
        const email = escapeAdminNotificationHtml(row?.email || '');
        const studentId = escapeAdminNotificationHtml(row?.student_id || '--');
        const joined = escapeAdminNotificationHtml(row?.joined || '--');
        const consultations = escapeAdminNotificationHtml(row?.consultations || 0);
        const search = escapeAdminNotificationHtml(`${row?.name || ''} ${row?.email || ''} ${row?.student_id || ''}`.toLowerCase());

        return `
            <tr data-status="${escapeAdminNotificationHtml(status)}" data-search="${search}">
                <td>
                    <div class="student-cell">
                        <div class="student-avatar">${name.charAt(0).toUpperCase() || 'S'}</div>
                        <div>
                            <div class="student-name">${name}</div>
                            <div class="student-email">${email}</div>
                        </div>
                    </div>
                </td>
                <td class="student-id-cell">${studentId}</td>
                <td>${joined}</td>
                <td style="font-weight:700">${consultations}</td>
                <td><span class="status-tag status-${escapeAdminNotificationHtml(status)}">${escapeAdminNotificationHtml(status)}</span></td>
                <td>${buildAdminOnlineStatusHtml(row)}</td>
                <td class="student-action-cell">
                    <a href="#"
                       class="manage-link manage-user-btn student-view-details-link"
                       data-user-id="${escapeAdminNotificationHtml(row?.id || '')}"
                       data-role="Student"
                       data-name="${name}"
                       data-email="${email}"
                       data-meta="Student ID: ${studentId}"
                       data-joined="${joined}"
                       data-consultations="${consultations}"
                       data-status="${escapeAdminNotificationHtml(status)}"
                    ><span class="manage-label-desktop">Manage</span><span class="manage-label-mobile">View</span></a>
                </td>
            </tr>
        `;
    }

    function buildAdminInstructorTableRow(row = {}) {
        const status = String(row?.status || 'inactive').toLowerCase();
        const name = escapeAdminNotificationHtml(row?.name || 'Instructor');
        const email = escapeAdminNotificationHtml(row?.email || '');
        const joined = escapeAdminNotificationHtml(row?.joined || '--');
        const consultations = escapeAdminNotificationHtml(row?.consultations || 0);
        const search = escapeAdminNotificationHtml(`${row?.name || ''} ${row?.email || ''}`.toLowerCase());

        return `
            <tr data-status="${escapeAdminNotificationHtml(status)}" data-search="${search}">
                <td>
                    <div class="student-cell">
                        <div class="student-avatar">${name.charAt(0).toUpperCase() || 'I'}</div>
                        <div>
                            <div class="student-name">${name}</div>
                            <div class="student-email">${email}</div>
                        </div>
                    </div>
                </td>
                <td>${joined}</td>
                <td style="font-weight:700">${consultations}</td>
                <td><span class="status-tag status-${escapeAdminNotificationHtml(status)}">${escapeAdminNotificationHtml(status)}</span></td>
                <td>${buildAdminOnlineStatusHtml(row)}</td>
                <td class="student-action-cell">
                    <a href="#"
                       class="manage-link manage-user-btn student-view-details-link"
                       data-user-id="${escapeAdminNotificationHtml(row?.id || '')}"
                       data-role="Instructor"
                       data-name="${name}"
                       data-email="${email}"
                       data-meta="Instructor Account"
                       data-joined="${joined}"
                       data-consultations="${consultations}"
                       data-status="${escapeAdminNotificationHtml(status)}"
                    ><span class="manage-label-desktop">Manage</span><span class="manage-label-mobile">View</span></a>
                </td>
            </tr>
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
                await handleAdminAccessDenied(response);

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
            await handleAdminAccessDenied(response);

            if (!response.ok) {
                throw new Error(`Admin notification poll failed (${response.status})`);
            }

            const data = await response.json();
            updateAdminNotificationBadge(data?.unreadNotifications || 0);
            renderAdminNotificationList(data?.notifications || []);
            updateAdminOverviewStats(data?.stats || {});
            refreshAdminUserTables(data?.studentRows || [], data?.instructorRows || []);
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
        adminDashboardRoot?.classList.add('admin-sidebar-collapsed');
        syncSidebarBackdropState();
    }

    function setSidebarIconOnly(enabled) {
        if (!sidebar) return;
        const shouldEnable = Boolean(enabled) && window.innerWidth > 900;
        sidebar.classList.toggle('icon-only', shouldEnable);
        adminDashboardRoot?.classList.toggle('admin-sidebar-icon-only', shouldEnable);
        if (shouldEnable) {
            sidebar.classList.remove('collapsed');
            sidebar.classList.remove('open');
            adminDashboardRoot?.classList.remove('admin-sidebar-collapsed');
            syncSidebarBackdropState();
            return;
        }

        if (window.innerWidth > 900) {
            sidebar.classList.remove('collapsed');
            sidebar.classList.remove('open');
            adminDashboardRoot?.classList.remove('admin-sidebar-collapsed');
        }

        syncSidebarBackdropState();
    }

    window.addEventListener('resize', syncSidebarBackdropState);
    syncSidebarBackdropState();

    function scrollToOverviewTarget(targetId) {
        const target = document.getElementById(targetId);
        if (!target) return;
        const rootStyles = getComputedStyle(document.documentElement);
        const headerHeight = parseInt(rootStyles.getPropertyValue('--admin-shell-header-height'), 10) || 0;
        const targetTop = target.getBoundingClientRect().top + window.scrollY - headerHeight - 16;
        window.scrollTo({
            top: Math.max(targetTop, 0),
            behavior: 'smooth',
        });
    }

    function bindManageUserButtons(scope = document) {
        scope.querySelectorAll('.manage-user-btn').forEach((btn) => {
            if (btn.dataset.manageBound === '1') return;
            btn.dataset.manageBound = '1';
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                const row = btn.closest('tr');
                openManageModal({
                    userId: btn.dataset.userId || '',
                    role: btn.dataset.role || '--',
                    name: btn.dataset.name || '--',
                    email: btn.dataset.email || '--',
                    meta: btn.dataset.meta || '--',
                    joined: btn.dataset.joined || '--',
                    consultations: btn.dataset.consultations || '0',
                    status: btn.dataset.status || 'inactive',
                }, row);
            });
        });
    }

    function getFilteredSystemLogRows() {
        const searchTerm = String(systemLogSearch?.value || '').trim().toLowerCase();
        const roleValue = String(systemLogRoleFilter?.value || '').toLowerCase();
        const statusValue = String(systemLogStatusFilter?.value || '').toLowerCase();
        const dateFrom = String(systemLogDateFrom?.value || '');
        const dateTo = String(systemLogDateTo?.value || '');

        return systemLogRows.filter((row) => {
            const matchesSearch = !searchTerm || String(row.dataset.search || '').includes(searchTerm);
            const matchesRole = !roleValue || String(row.dataset.role || '') === roleValue;
            const rowStatus = String(row.dataset.status || '');
            const matchesStatus = !statusValue
                || rowStatus === statusValue
                || (statusValue === 'recent' && row.dataset.recent === '1');
            const loginDate = String(row.dataset.loginDate || '');
            const matchesFrom = !dateFrom || (loginDate && loginDate >= dateFrom);
            const matchesTo = !dateTo || (loginDate && loginDate <= dateTo);

            return matchesSearch && matchesRole && matchesStatus && matchesFrom && matchesTo;
        });
    }

    function renderSystemLogPagination() {
        filteredSystemLogRows = getFilteredSystemLogRows();
        const totalRows = filteredSystemLogRows.length;
        const totalPages = Math.max(1, Math.ceil(totalRows / systemLogRowsPerPage));
        currentSystemLogPage = Math.min(Math.max(1, currentSystemLogPage), totalPages);
        const startIndex = (currentSystemLogPage - 1) * systemLogRowsPerPage;
        const endIndex = startIndex + systemLogRowsPerPage;

        systemLogRows.forEach((row) => {
            row.style.display = 'none';
        });
        filteredSystemLogRows.slice(startIndex, endIndex).forEach((row) => {
            row.style.display = '';
        });

        if (systemLogEmptyState) {
            systemLogEmptyState.style.display = totalRows === 0 ? 'block' : 'none';
        }

        if (systemLogPaginationInfo) {
            if (totalRows === 0) {
                systemLogPaginationInfo.textContent = 'Showing 0 activity logs';
            } else {
                systemLogPaginationInfo.textContent = `Showing ${startIndex + 1} to ${Math.min(endIndex, totalRows)} of ${totalRows} activity logs`;
            }
        }

        if (prevSystemLogBtn) {
            prevSystemLogBtn.style.display = totalPages > 1 ? 'inline-flex' : 'none';
            prevSystemLogBtn.disabled = currentSystemLogPage <= 1;
        }
        if (nextSystemLogBtn) {
            nextSystemLogBtn.style.display = totalPages > 1 ? 'inline-flex' : 'none';
            nextSystemLogBtn.disabled = currentSystemLogPage >= totalPages;
        }

        if (systemLogPageNumbers) {
            systemLogPageNumbers.innerHTML = '';
            if (totalPages > 1) {
                for (let page = 1; page <= totalPages; page += 1) {
                    const pageBtn = document.createElement('button');
                    pageBtn.type = 'button';
                    pageBtn.className = `pagination-page-btn${page === currentSystemLogPage ? ' active' : ''}`;
                    pageBtn.textContent = String(page);
                    pageBtn.addEventListener('click', () => {
                        currentSystemLogPage = page;
                        renderSystemLogPagination();
                    });
                    systemLogPageNumbers.appendChild(pageBtn);
                }
            }
        }
    }

    function bindSystemLogFilters() {
        [systemLogSearch, systemLogRoleFilter, systemLogStatusFilter, systemLogDateFrom, systemLogDateTo].forEach((input) => {
            if (!input) return;
            input.addEventListener('input', () => {
                currentSystemLogPage = 1;
                renderSystemLogPagination();
            });
            input.addEventListener('change', () => {
                currentSystemLogPage = 1;
                renderSystemLogPagination();
            });
        });

        if (prevSystemLogBtn) {
            prevSystemLogBtn.addEventListener('click', () => {
                currentSystemLogPage = Math.max(1, currentSystemLogPage - 1);
                renderSystemLogPagination();
            });
        }

        if (nextSystemLogBtn) {
            nextSystemLogBtn.addEventListener('click', () => {
                currentSystemLogPage += 1;
                renderSystemLogPagination();
            });
        }

        renderSystemLogPagination();
    }

    function refreshAdminUserTables(studentRows = [], instructorRows = []) {
        if (studentTableBody) {
            studentTableBody.innerHTML = Array.isArray(studentRows) && studentRows.length
                ? studentRows.map((row) => buildAdminStudentTableRow(row)).join('')
                : '<tr><td colspan="7" style="color:var(--muted);text-align:center;">No student accounts found.</td></tr>';
        }

        if (instructorTableBody) {
            instructorTableBody.innerHTML = Array.isArray(instructorRows) && instructorRows.length
                ? instructorRows.map((row) => buildAdminInstructorTableRow(row)).join('')
                : '<tr><td colspan="6" style="color:var(--muted);text-align:center;">No instructor accounts found.</td></tr>';
        }

        studentRowsAll = Array.from(document.querySelectorAll('#studentTableBody tr[data-status]'));
        instructorRowsAll = Array.from(document.querySelectorAll('#instructorTableBody tr[data-status]'));
        bindManageUserButtons(studentTableBody || document);
        bindManageUserButtons(instructorTableBody || document);
        filterStudentsTable();
        filterInstructorsTable();
    }

    function showOverview() {
        setSidebarIconOnly(false);
        if (overviewSection) overviewSection.classList.remove('statistics-only');
        if (dashboardContentHeader) dashboardContentHeader.classList.remove('is-hidden');
        if (adminContentContainer) adminContentContainer.classList.remove('header-hidden');
        if (overviewSection) overviewSection.classList.remove('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (systemLogsSection) systemLogsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.add('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('overviewLink');
        scrollToOverviewTarget('overviewSection');
    }

    function showStudents() {
        setSidebarIconOnly(false);
        if (dashboardContentHeader) dashboardContentHeader.classList.add('is-hidden');
        if (adminContentContainer) adminContentContainer.classList.add('header-hidden');
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.remove('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (systemLogsSection) systemLogsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.add('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('studentsLink');
        scrollToOverviewTarget('studentsSection');
    }

    function showInstructors() {
        setSidebarIconOnly(false);
        if (dashboardContentHeader) dashboardContentHeader.classList.add('is-hidden');
        if (adminContentContainer) adminContentContainer.classList.add('header-hidden');
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.remove('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (systemLogsSection) systemLogsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.add('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('instructorsLink');
        scrollToOverviewTarget('instructorsSection');
    }

    function showConsultations() {
        setSidebarIconOnly(false);
        if (dashboardContentHeader) dashboardContentHeader.classList.add('is-hidden');
        if (adminContentContainer) adminContentContainer.classList.add('header-hidden');
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.remove('is-hidden');
        if (systemLogsSection) systemLogsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.add('active');
        setActiveSidebar('consultationsLink');
        scrollToOverviewTarget('consultationsSection');
    }

    function showStatistics() {
        setSidebarIconOnly(false);
        if (dashboardContentHeader) dashboardContentHeader.classList.add('is-hidden');
        if (adminContentContainer) adminContentContainer.classList.add('header-hidden');
        if (overviewSection) {
            overviewSection.classList.remove('is-hidden');
            overviewSection.classList.add('statistics-only');
        }
        if (statsWorkspace) statsWorkspace.classList.remove('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (systemLogsSection) systemLogsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('statisticsLink');
        scrollToOverviewTarget('statistics');
    }

    function showSystemLogs() {
        setSidebarIconOnly(false);
        if (dashboardContentHeader) dashboardContentHeader.classList.add('is-hidden');
        if (adminContentContainer) adminContentContainer.classList.add('header-hidden');
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (systemLogsSection) systemLogsSection.classList.remove('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('systemLogsLink');
        scrollToOverviewTarget('systemLogsSection');
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

    if (systemLogsLink) {
        systemLogsLink.addEventListener('click', (event) => {
            event.preventDefault();
            showSystemLogs();
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
                if (btn.dataset.closeSection === 'statistics' && statsWorkspace) {
                    statsWorkspace.classList.add('is-hidden');
                }
                showOverview();
                window.scrollTo({ top: 0, behavior: 'smooth' });
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

    function populateConsultationSelect(select, values = [], placeholder = 'All') {
        if (!select) return;

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

    function getAllConsultationTopicOptions(extraTypes = []) {
        const predefinedTopics = Object.values(consultationTopicsByCategory).flat();
        return Array.from(new Set([
            ...predefinedTopics,
            ...extraTypes.filter(Boolean),
        ])).sort((a, b) => a.localeCompare(b));
    }

    function updateConsultationFilterOptions() {
        const rows = Array.from(document.querySelectorAll('#consultationTableBody .admin-consultation-row[data-status]'));
        const rowCategories = Array.from(new Set(
            rows.map((row) => String(row.dataset.category || '').trim()).filter(Boolean)
        )).sort((a, b) => a.localeCompare(b));
        const rowTypes = Array.from(new Set(
            rows.map((row) => String(row.dataset.type || '').trim()).filter(Boolean)
        )).sort((a, b) => a.localeCompare(b));
        const selectedCategory = String(consultationCategoryFilter?.value || '').trim();
        const currentTypeValue = String(consultationTypeFilter?.value || '').trim();
        const predefinedCategories = Object.keys(consultationTopicsByCategory);
        const categories = Array.from(new Set([
            ...predefinedCategories,
            ...rowCategories,
        ]));
        let typeOptions = [];

        if (selectedCategory && consultationTopicsByCategory[selectedCategory]) {
            typeOptions = Array.from(new Set([
                ...consultationTopicsByCategory[selectedCategory],
                ...rowTypes.filter((type) => {
                    const matchingRows = rows.filter((row) => String(row.dataset.category || '').trim() === selectedCategory);
                    return matchingRows.some((row) => String(row.dataset.type || '').trim() === type);
                }),
            ])).sort((a, b) => a.localeCompare(b));
        } else {
            typeOptions = getAllConsultationTopicOptions(rowTypes);
        }

        populateConsultationSelect(consultationCategoryFilter, categories, 'All Categories');
        
        // Preserve type value if it's still valid in the new options
        const typeValueToPreserve = typeOptions.includes(currentTypeValue) ? currentTypeValue : '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'All Types';
        consultationTypeFilter.innerHTML = '';
        consultationTypeFilter.appendChild(defaultOption);
        
        typeOptions.forEach((value) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            consultationTypeFilter.appendChild(option);
        });
        
        consultationTypeFilter.value = typeValueToPreserve;
    }

    function getCurrentFilteredConsultationRows() {
        return getFilteredConsultationRows();
    }

    function exportConsultationsCsv() {
        const rows = getCurrentFilteredConsultationRows().map((row) => ([
            row.querySelector('.admin-consultation-primary')?.textContent?.trim() || '',
            row.querySelectorAll('.admin-consultation-primary')?.[1]?.textContent?.trim() || '',
            row.dataset.date || '',
            row.querySelector('.admin-consultation-time')?.textContent?.trim() || '',
            row.dataset.category || '',
            row.dataset.type || '',
            row.dataset.mode || '',
            row.dataset.status || '',
        ]));

        const header = ['Student', 'Instructor', 'Date', 'Time', 'Category', 'Type', 'Mode', 'Status'];
        const csvContent = [header, ...rows]
            .map((line) => line.map((item) => escapeCsvCell(item)).join(','))
            .join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'all-consultations.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function getFilteredConsultationRows() {
        if (!consultationTableBody) return [];

        const searchValue = normalizeSearchText(consultationSearch?.value || '');
        const selectedCategory = normalizeSearchText(consultationCategoryFilter?.value || '');
        const selectedType = normalizeSearchText(consultationTypeFilter?.value || '');
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
            const rowCategory = normalizeSearchText(row.dataset.category || '');
            const rowType = normalizeSearchText(row.dataset.type || '');
            const rowDateStr = row.dataset.date || '';
            const rowYear = normalizeSearchText(getAcademicYearFromDate(rowDateStr));
            const rowSemester = getSemesterFromDate(rowDateStr);
            const rowMonth = getMonthFromDate(rowDateStr);

            const matchSearch = !searchValue || rowSearch.includes(searchValue);
            const matchCategory = !selectedCategory || (rowCategory && rowCategory === selectedCategory);
            const matchType = !selectedType || (rowType && rowType === selectedType);
            const matchStatus = !selectedStatus || rowStatus === selectedStatus;
            const matchYear = !yearValue || (rowYear && rowYear.includes(yearValue));
            const matchSemester = selectedSemester === 'all' || rowSemester === selectedSemester;
            const matchMonth = !selectedConsultationMonth
                || rowMonth === Number(selectedConsultationMonth);

            return matchSearch && matchCategory && matchType && matchStatus && matchYear && matchSemester && matchMonth;
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

    if (consultationCategoryFilter) {
        consultationCategoryFilter.addEventListener('change', () => {
            updateConsultationFilterOptions();
            filterConsultationsTable();
        });
    }

    if (consultationTypeFilter) {
        consultationTypeFilter.addEventListener('change', filterConsultationsTable);
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

    if (consultationExportBtn) {
        consultationExportBtn.addEventListener('click', exportConsultationsCsv);
    }

    // ===== STUDENT ACCOUNTS PAGINATION =====
    const studentTableElm = document.querySelector('#studentsSection .students-table');
    studentRowsAll = Array.from(document.querySelectorAll('#studentTableBody tr[data-status]'));
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
    instructorRowsAll = Array.from(document.querySelectorAll('#instructorTableBody tr[data-status]'));
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
    updateConsultationFilterOptions();
    renderConsultationMonthSelector('all');

    function openConsultationDetails(data) {
        if (!consultationDetailsModal) return;
        activeConsultationDetailsId = String(data.consultationId || data.id || '');

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
        if (detailsExportBtn) {
            detailsExportBtn.href = activeConsultationDetailsId
                ? `{{ url('/consultations') }}/${activeConsultationDetailsId}/export-pdf`
                : '#';
            detailsExportBtn.style.pointerEvents = activeConsultationDetailsId ? 'auto' : 'none';
            detailsExportBtn.style.opacity = activeConsultationDetailsId ? '1' : '0.5';
        }
        consultationDetailsModal.classList.add('open');
        consultationDetailsModal.setAttribute('aria-hidden', 'false');
    }

    function closeConsultationDetails() {
        if (!consultationDetailsModal) return;
        activeConsultationDetailsId = '';
        if (detailsExportBtn) {
            detailsExportBtn.href = '#';
            detailsExportBtn.style.pointerEvents = 'none';
            detailsExportBtn.style.opacity = '0.5';
        }
        consultationDetailsModal.classList.remove('open');
        consultationDetailsModal.setAttribute('aria-hidden', 'true');
    }

    if (consultationViewButtons.length) {
        consultationViewButtons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                openConsultationDetails({
                    id: btn.dataset.id || '--',
                    consultationId: btn.dataset.consultationId || '',
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
        activeManageUserId = String(data.userId || '');
        activeManageButton = row?.querySelector('.manage-user-btn') || null;
        if (manageAvatar) manageAvatar.textContent = (data.name || 'U').charAt(0).toUpperCase();
        if (manageName) manageName.textContent = data.name || '--';
        if (manageEmail) manageEmail.textContent = data.email || '--';
        if (manageMeta) manageMeta.textContent = data.meta || '--';
        if (manageRole) manageRole.textContent = data.role || '--';
        if (manageJoined) manageJoined.textContent = data.joined || '--';
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
        activeManageUserId = '';
        activeManageButton = null;
    }

    function getStatusActionLabel(status) {
        const normalized = String(status || '').toLowerCase();
        if (normalized === 'active') return 'activate';
        if (normalized === 'suspended') return 'suspend';
        return 'deactivate';
    }

    function getStatusDisplayLabel(status) {
        const normalized = String(status || '').toLowerCase();
        if (normalized === 'active') return 'Active';
        if (normalized === 'suspended') return 'Suspended';
        return 'Inactive';
    }

    function openStatusConfirmModal(nextStatus, triggerButton) {
        if (!statusConfirmModal || !activeManageUserId) return;

        const actionLabel = getStatusActionLabel(nextStatus);
        const displayLabel = getStatusDisplayLabel(nextStatus);
        const userName = manageName?.textContent?.trim() || 'this user';

        pendingStatusChange = {
            nextStatus,
            triggerButton,
        };

        if (statusConfirmTitle) {
            statusConfirmTitle.textContent = `${displayLabel} account?`;
        }
        if (statusConfirmMessage) {
            statusConfirmMessage.textContent = `Are you sure you want to ${actionLabel} this account? This will update the user's login access immediately.`;
        }
        if (statusConfirmUser) {
            statusConfirmUser.textContent = `User: ${userName}`;
        }
        if (confirmStatusChange) {
            confirmStatusChange.textContent = displayLabel;
            confirmStatusChange.classList.toggle('danger', nextStatus === 'inactive' || nextStatus === 'suspended');
        }

        statusConfirmModal.classList.add('open');
        statusConfirmModal.setAttribute('aria-hidden', 'false');
    }

    function closeStatusConfirmDialog() {
        if (!statusConfirmModal) return;
        statusConfirmModal.classList.remove('open');
        statusConfirmModal.setAttribute('aria-hidden', 'true');
        pendingStatusChange = null;
    }

    async function updateManagedUserStatus(nextStatus) {
        if (!activeManageUserId) return;
        const endpoint = adminUserStatusEndpointTemplate.replace('__USER__', encodeURIComponent(activeManageUserId));

        manageStatusButtons.forEach((item) => {
            item.disabled = true;
        });
        if (confirmStatusChange) {
            confirmStatusChange.disabled = true;
        }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    account_status: nextStatus,
                }),
            });
            await handleAdminAccessDenied(response);

            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(data?.message || 'Unable to update account status.');
            }

            applyStatusPill(manageCurrentStatus, nextStatus);

            if (activeManageRow) {
                activeManageRow.dataset.status = nextStatus;
                const rowPill = activeManageRow.querySelector('.status-tag');
                applyStatusPill(rowPill, nextStatus);
            }

            if (activeManageButton) {
                activeManageButton.dataset.status = nextStatus;
            }

            if (activeManageRow && studentTableBody?.contains(activeManageRow)) {
                filterStudentsTable();
            }

            if (activeManageRow && instructorTableBody?.contains(activeManageRow)) {
                filterInstructorsTable();
            }

            closeStatusConfirmDialog();

            if (adminNotifToast && adminNotifToastTitle && adminNotifToastBody) {
                adminNotifToastTitle.textContent = 'Account Updated';
                adminNotifToastBody.textContent = data?.message || 'Account status updated successfully.';
                adminNotifToast.classList.add('show');
                window.setTimeout(() => {
                    adminNotifToast.classList.remove('show');
                }, 3000);
            }
        } catch (error) {
            if (adminNotifToast && adminNotifToastTitle && adminNotifToastBody) {
                adminNotifToastTitle.textContent = 'Access Update Failed';
                adminNotifToastBody.textContent = error?.message || 'Unable to update account status.';
                adminNotifToast.classList.add('show');
                window.setTimeout(() => {
                    adminNotifToast.classList.remove('show');
                }, 4000);
            }
        } finally {
            manageStatusButtons.forEach((item) => {
                item.disabled = false;
            });
            if (confirmStatusChange) {
                confirmStatusChange.disabled = false;
            }
        }
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

    bindManageUserButtons();
    bindSystemLogFilters();

    if (manageStatusButtons.length) {
        manageStatusButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                if (!activeManageUserId) return;
                const nextStatus = btn.dataset.statusValue || 'inactive';
                openStatusConfirmModal(nextStatus, btn);
            });
        });
    }

    if (confirmStatusChange) {
        confirmStatusChange.addEventListener('click', () => {
            if (!pendingStatusChange?.nextStatus) return;
            updateManagedUserStatus(pendingStatusChange.nextStatus);
        });
    }

    [closeStatusConfirmModal, cancelStatusConfirm].forEach((btn) => {
        if (!btn) return;
        btn.addEventListener('click', closeStatusConfirmDialog);
    });

    if (statusConfirmModal) {
        statusConfirmModal.addEventListener('click', (event) => {
            if (event.target === statusConfirmModal) {
                closeStatusConfirmDialog();
            }
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
