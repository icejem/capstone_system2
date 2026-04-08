<script src="https://download.agora.io/sdk/release/AgoraRTC_N.js"></script>
<script>
    const AGORA_APP_ID = @json(config('services.agora.app_id'));
    const AGORA_TOKEN_ENDPOINT = @json(route('consultations.agora-token', ['consultation' => '__CONSULTATION__']));
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
    const scheduleExport = document.getElementById('scheduleExport');
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
    const detailsStatus = document.getElementById('detailsStatus');
    const detailsUpdated = document.getElementById('detailsUpdated');
    const detailsNotesWrap = document.getElementById('detailsNotesWrap');
    const detailsNotesText = document.getElementById('detailsNotesText');
    const detailsActionsWrap = document.getElementById('detailsActionsWrap');
    const detailsActionsContent = document.getElementById('detailsActionsContent');
    const detailsSummaryWrap = document.getElementById('detailsSummaryWrap');
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
        setHistorySidebarIconOnly(false);
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
            let actionHtml = '';
            const actionSourceId = btn.dataset.actionSource || '';
            if (actionSourceId) {
                const actionSource = document.getElementById(actionSourceId);
                actionHtml = actionSource ? actionSource.innerHTML : '';
            }
            const fallbackData = {
                source: btn.dataset.source || 'history',
                showRequestMeta: btn.dataset.showRequestMeta === 'true',
                actionHtml,
                actionSourceId,
                requestRowId: actionSourceId.replace('requestAction', ''),
                date: btn.dataset.date || '--',
                time: btn.dataset.time || '--',
                student: btn.dataset.student || 'Student',
                studentId: btn.dataset.studentId || '--',
                mode: btn.dataset.mode || '--',
                type: btn.dataset.type || '--',
                duration: btn.dataset.duration || '--',
                status: btn.dataset.status || '',
                updated: btn.dataset.updated || '',
                notes: btn.dataset.notes || '',
                summary: btn.dataset.summary || '',
                transcript: btn.dataset.transcript || '',
            };

            if (fallbackData.source === 'request' || fallbackData.showRequestMeta) {
                const requestRow = btn.closest('.request-row')
                    || document.querySelector(`.request-row[data-consultation-id="${btn.dataset.id || fallbackData.requestRowId}"]`);
                openDetailsModal(buildRequestDetailsDataFromRow(requestRow, fallbackData));
                return;
            }

            openDetailsModal(fallbackData);
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

    const allInstructorHistoryMonths = [...semesterMonths['1'], ...semesterMonths['2']];

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

        const months = (!semester || semester === 'all')
            ? allInstructorHistoryMonths
            : (semesterMonths[semester] || []);
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

        applyHistoryFilters();
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

    if (scheduleExport) {
        scheduleExport.addEventListener('click', () => {
            const scheduleGrid = scheduleSection?.querySelector('.schedule-grid');
            if (!scheduleGrid) return;

            const metaValues = Array.from(scheduleSection.querySelectorAll('.schedule-meta-inline-value'))
                .map((node) => node.textContent.replace(/\s+/g, ' ').trim());
            const semester = metaValues[0] || '--';
            const academicYear = metaValues[1] || '--';
            const dayLabels = Array.from(scheduleGrid.querySelectorAll('.schedule-day'))
                .map((node) => node.textContent.replace(/\s+/g, ' ').trim());
            const slotCells = Array.from(scheduleGrid.querySelectorAll('.schedule-cell'));

            const rowsHtml = slotCells.map((cell, index) => {
                const day = dayLabels[index] || `Day ${index + 1}`;
                const slotText = cell.textContent.replace(/\s+/g, ' ').trim() || '--';

                return `
                    <tr>
                        <td>${escapeHistoryHtml(day)}</td>
                        <td>${escapeHistoryHtml(slotText)}</td>
                    </tr>`;
            }).join('');

            const exportHtml = `
                <html>
                <head>
                    <title>Weekly Schedule</title>
                    <style>
                        body { font-family: "Segoe UI", Arial, sans-serif; margin: 24px; color: #111827; }
                        h1 { font-size: 20px; margin: 0 0 8px; }
                        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 14px; }
                        th, td { border: 1px solid #e5e7eb; padding: 9px 10px; text-align: left; vertical-align: top; }
                        th { background: #f3f4f6; font-weight: 700; }
                        .meta { color: #6b7280; font-size: 12px; margin-bottom: 4px; }
                    </style>
                </head>
                <body>
                    <h1>Weekly Schedule</h1>
                    <div class="meta">Semester: ${escapeHistoryHtml(semester)}</div>
                    <div class="meta">Academic Year: ${escapeHistoryHtml(academicYear)}</div>
                    <div class="meta">Exported on ${escapeHistoryHtml(new Date().toLocaleString())}</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Available Time</th>
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
    let agoraClient = null;
    let localAudioTrack = null;
    let localVideoTrack = null;
    let joinedAgoraChannel = '';
    let pollTimer = null;
    let lastSignalId = 0;
    let callTimerInterval = null;
    let callStartAt = null;
    let transcriptActive = false;
    let transcriptText = '';
    let speechRecognizer = null;
    let callAnswered = false;
    let remoteMediaConnected = false;
    let mediaSyncInterval = null;
    let outgoingCountdownSeconds = 0;
    let outgoingCountdownInterval = null;
    let isEndingCall = false;
    let activeCallRole = 'instructor';
    let localVideoEnabled = true;
    let localAudioEnabled = true;

    function buildAgoraChannelName(consultationId) {
        return `consultation-${consultationId}`;
    }

    function buildAgoraTokenUrl(consultationId) {
        return AGORA_TOKEN_ENDPOINT.replace('__CONSULTATION__', String(consultationId));
    }

    function isLocalTestingHost() {
        const host = String(location.hostname || '').toLowerCase();
        return host === 'localhost' || host === '127.0.0.1' || host === '::1' || host.endsWith('.localhost');
    }

    function clearAgoraContainer(container) {
        if (container) container.innerHTML = '';
    }

    function playRemoteVideoTrack(track) {
        if (!remoteVideo || !track) return;

        const nextTrackId = String(track.getTrackId?.() || '');
        const currentTrackId = String(remoteVideo.dataset.trackId || '');
        const hasRenderedVideo = Boolean(remoteVideo.querySelector('video'));
        if (!hasRenderedVideo || (nextTrackId && currentTrackId && nextTrackId !== currentTrackId)) {
            clearAgoraContainer(remoteVideo);
        }

        track.play(remoteVideo);
        if (nextTrackId) {
            remoteVideo.dataset.trackId = nextTrackId;
        }
    }

    function getAgoraCallErrorMessage(error, stage = 'media') {
        const rawMessage = String(error?.message || error?.reason || error?.code || '').trim();
        const message = rawMessage.toLowerCase();
        const name = String(error?.name || '').toLowerCase();

        if (stage === 'join') {
            if (message.includes('invalid app id') || message.includes('invalid vendor key')) {
                return 'Agora configuration error: invalid AGORA_APP_ID.';
            }

            if (message.includes('dynamic key') || message.includes('token')) {
                return 'Agora token/certificate error. Check your Agora project security settings.';
            }

            return rawMessage
                ? `Unable to join the video channel: ${rawMessage}`
                : 'Unable to join the video channel. Check Agora settings and try again.';
        }

        if (name.includes('notallowed') || message.includes('permission denied') || message.includes('permission dismissed')) {
            return 'Allow camera and microphone access in your browser, then try again.';
        }

        if (name.includes('notfound') || message.includes('requested device not found')) {
            return 'No camera or microphone was found on this device.';
        }

        if (name.includes('notreadable') || message.includes('could not start video source') || message.includes('device is in use')) {
            return 'Camera or microphone is busy in another app or browser tab. If you are testing both accounts on one PC, use two devices or free the camera first.';
        }

        if (name.includes('overconstrained')) {
            return 'Your camera does not support the requested video settings.';
        }

        if (message.includes('microphone is required for this video call')) {
            return 'Microphone is required for this video call. Check browser permissions and make sure no other app is using it.';
        }

        return rawMessage
            ? `Unable to start camera/microphone: ${rawMessage}`
            : 'Camera/Mic access is required for video call.';
    }

    function requireMicrophoneTrack(failures = []) {
        if (localAudioTrack) return;

        const microphoneFailure = failures.find((failure) => failure?.kind === 'microphone')?.error;
        throw microphoneFailure ?? new Error('Microphone is required for this video call.');
    }

    async function createLocalAgoraTracks() {
        const tracks = [];
        const failures = [];

        try {
            localAudioTrack = await AgoraRTC.createMicrophoneAudioTrack();
            tracks.push(localAudioTrack);
            localAudioEnabled = true;
        } catch (error) {
            localAudioTrack = null;
            localAudioEnabled = false;
            failures.push({ kind: 'microphone', error });
            console.warn('Agora microphone track failed:', error);
        }

        try {
            localVideoTrack = await AgoraRTC.createCameraVideoTrack({ encoderConfig: '720p_1' });
            tracks.push(localVideoTrack);
            localVideoEnabled = true;
        } catch (error) {
            localVideoTrack = null;
            localVideoEnabled = false;
            failures.push({ kind: 'camera', error });
            console.warn('Agora camera track failed:', error);
        }

        if (!tracks.length) {
            throw failures[0]?.error ?? new Error('No local media tracks could be created.');
        }

        return { tracks, failures };
    }

    async function fetchAgoraJoinCredentials(consultationId) {
        const response = await fetch(buildAgoraTokenUrl(consultationId), {
            headers: {
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            let message = 'Unable to fetch Agora token.';
            try {
                const data = await response.json();
                if (data?.message) {
                    message = data.message;
                }
            } catch (_) {
                // ignore
            }
            throw new Error(message);
        }

        return response.json();
    }

    function markInstructorCallConnected() {
        remoteMediaConnected = true;
        if (mediaSyncInterval) {
            clearInterval(mediaSyncInterval);
            mediaSyncInterval = null;
        }
        clearOutgoingCountdown();
        setCallStatusLabel('Video Session');
    }

    function ensureAgoraClient() {
        if (agoraClient) return agoraClient;
        if (!window.AgoraRTC) {
            throw new Error('Agora Web SDK failed to load.');
        }

        agoraClient = AgoraRTC.createClient({
            mode: 'rtc',
            codec: 'vp8',
        });

        agoraClient.on('user-joined', (user) => {
            setTimeout(() => {
                void syncRemoteUserMedia(user);
            }, 350);
        });

        agoraClient.on('user-published', async (user, mediaType) => {
            try {
                await subscribeToRemoteMedia(user, mediaType);
            } catch (error) {
                console.error('Agora subscribe failed:', error);
            }
        });

        agoraClient.on('user-unpublished', (user, mediaType) => {
            if (mediaType === 'video') {
                remoteMediaConnected = false;
                clearAgoraContainer(remoteVideo);
                delete remoteVideo.dataset.trackId;
            }
        });

        agoraClient.on('user-left', () => {
            remoteMediaConnected = false;
            clearAgoraContainer(remoteVideo);
            delete remoteVideo.dataset.trackId;
            if (currentConsultationId) {
                setCallStatusLabel('Waiting for student...');
            }
        });

        return agoraClient;
    }

    async function subscribeToRemoteMedia(user, mediaType) {
        if (!agoraClient || !user || !mediaType) return;

        await agoraClient.subscribe(user, mediaType);

        if (mediaType === 'video' && user.videoTrack) {
            playRemoteVideoTrack(user.videoTrack);
            markInstructorCallConnected();
        }

        if (mediaType === 'audio' && user.audioTrack) {
            user.audioTrack.setVolume?.(100);
            user.audioTrack.play();
            if (!user.hasVideo && !user.videoTrack) {
                markInstructorCallConnected();
            }
        }
    }

    async function syncRemoteUserMedia(user) {
        if (!user) return;

        try {
            await subscribeToRemoteMedia(user, 'video');
        } catch (error) {
            if (user.hasVideo || user.videoTrack) {
                console.warn('Agora remote video sync failed:', error);
            }
        }

        try {
            await subscribeToRemoteMedia(user, 'audio');
        } catch (error) {
            if (user.hasAudio || user.audioTrack) {
                console.warn('Agora remote audio sync failed:', error);
            }
        }
    }

    async function syncPublishedRemoteUsers() {
        if (!agoraClient?.remoteUsers?.length) return;

        for (const user of agoraClient.remoteUsers) {
            await syncRemoteUserMedia(user);
        }
    }

    function beginRemoteMediaSync() {
        if (mediaSyncInterval) {
            clearInterval(mediaSyncInterval);
            mediaSyncInterval = null;
        }

        let attempts = 0;
        mediaSyncInterval = setInterval(() => {
            attempts += 1;
            if (!currentConsultationId || remoteMediaConnected || attempts >= 12) {
                clearInterval(mediaSyncInterval);
                mediaSyncInterval = null;
                return;
            }

            void syncPublishedRemoteUsers();
        }, 800);
    }

    async function cleanupAgoraCall() {
        if (localAudioTrack) {
            localAudioTrack.stop();
            localAudioTrack.close();
            localAudioTrack = null;
        }

        if (localVideoTrack) {
            localVideoTrack.stop();
            localVideoTrack.close();
            localVideoTrack = null;
        }

        localVideoEnabled = true;
        localAudioEnabled = true;
        clearAgoraContainer(localVideo);
        clearAgoraContainer(remoteVideo);
        delete remoteVideo.dataset.trackId;

        if (agoraClient && joinedAgoraChannel) {
            try {
                await agoraClient.leave();
            } catch (_) {
                // ignore
            }
        }

        joinedAgoraChannel = '';
    }

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
        if (mediaSyncInterval) {
            clearInterval(mediaSyncInterval);
            mediaSyncInterval = null;
        }
        stopTranscript();
        saveTranscript();
        if (callTimerInterval) {
            clearInterval(callTimerInterval);
            callTimerInterval = null;
        }
        clearOutgoingCountdown();
        void cleanupAgoraCall();
        currentConsultationId = null;
        lastSignalId = 0;
        callStartAt = null;
        transcriptText = '';
        callAnswered = false;
        remoteMediaConnected = false;
        activeCallRole = 'instructor';
        if (callTimer) callTimer.textContent = 'LIVE';
        if (toggleCameraBtn) toggleCameraBtn.querySelector('.call-btn-text').textContent = 'Camera On';
        if (toggleMicBtn) toggleMicBtn.querySelector('.call-btn-text').textContent = 'Mic On';
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
        speechRecognizer.start();
    }

    async function stopTranscript() {
        if (!transcriptActive) return;
        transcriptActive = false;
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
        const parsedStartAt = Number(callStartAt);
        callStartAt = Number.isFinite(parsedStartAt) && parsedStartAt > 0
            ? parsedStartAt
            : Date.now();
        if (callTimer) callTimer.textContent = 'LIVE';
        if (callTimerInterval) clearInterval(callTimerInterval);
        renderCallTimer();
        callTimerInterval = null;
    }

    function renderCallTimer() {
        if (!callTimer) return;
        callTimer.textContent = 'LIVE';
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
        if (callAnswered) {
            clearOutgoingCountdown();
            setCallStatusLabel('Video Session');
            if (!callStartAt) {
                startCallTimer();
            }
            return;
        }

        clearOutgoingCountdown();
        outgoingCountdownSeconds = seconds;
        setCallStatusLabel('Calling Student...');
        if (callTimer) callTimer.textContent = 'LIVE';
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
            if (callTimer) callTimer.textContent = 'LIVE';
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

    async function handleSignal(type, payload) {
        if (type === 'answered') {
            callAnswered = true;
            const consultationId = Number(currentConsultationId || 0);
            if (consultationId > 0) {
                syncRequestRowStatus(consultationId, 'in_progress');
            }
            const sharedStartedAt = Date.parse(String(payload?.started_at || ''));
            callStartAt = Number.isFinite(sharedStartedAt) && sharedStartedAt > 0
                ? sharedStartedAt
                : Date.now();
            setCallStatusLabel('Connecting...');
            startCallTimer();
            void syncPublishedRemoteUsers();
            beginRemoteMediaSync();
            setTimeout(() => {
                void syncPublishedRemoteUsers();
            }, 150);
            setTimeout(() => {
                void syncPublishedRemoteUsers();
            }, 500);
            setTimeout(() => {
                void syncPublishedRemoteUsers();
            }, 1000);
            return;
        }

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
            if (reason === 'call_ended') {
                setTimeout(() => {
                    try { pollConsultationUpdates(); } catch (_) { /* ignore */ }
                }, 150);
            }
            return;
        }
    }

    async function startVideoCall(consultationId, role, options = {}) {
        if (!consultationId) return;
        if (currentConsultationId && currentConsultationId !== consultationId) {
            actuallyStopCall();
        }

        if (!AGORA_APP_ID) {
            alert('Set AGORA_APP_ID in your .env file before testing Agora calls.');
            return;
        }

        currentConsultationId = consultationId;
        activeCallRole = role || 'instructor';
        callAnswered = false;
        callStartAt = null;
        remoteMediaConnected = false;
        openCallModal();
        setCallStatusLabel('Joining channel...');

        if (!window.isSecureContext && !isLocalTestingHost()) {
            actuallyStopCall();
            alert('For quick testing, open the app on localhost or 127.0.0.1 on this same PC. For other devices, HTTPS is required for camera/mic.');
            return;
        }

        const client = ensureAgoraClient();
        joinedAgoraChannel = buildAgoraChannelName(consultationId);

        try {
            const credentials = await fetchAgoraJoinCredentials(consultationId);
            await client.join(
                credentials.app_id || AGORA_APP_ID,
                credentials.channel || joinedAgoraChannel,
                credentials.token || null,
                credentials.uid || null
            );
            await syncPublishedRemoteUsers();
            setTimeout(() => { void syncPublishedRemoteUsers(); }, 500);
        } catch (error) {
            console.error('Agora join failed:', error);
            actuallyStopCall();
            alert(getAgoraCallErrorMessage(error, 'join'));
            return;
        }

        try {
            const { tracks, failures } = await createLocalAgoraTracks();
            requireMicrophoneTrack(failures);

            clearAgoraContainer(localVideo);
            if (localVideoTrack) {
                await localVideoTrack.setEnabled(true);
                localVideoTrack.play(localVideo);
            }

            if (localAudioTrack) {
                try {
                    await localAudioTrack.setEnabled(true);
                    await localAudioTrack.setMuted?.(false);
                    localAudioTrack.setVolume?.(100);
                } catch (_) {
                    // ignore
                }
            }

            await client.publish(tracks);
            await syncPublishedRemoteUsers();
            setTimeout(() => { void syncPublishedRemoteUsers(); }, 500);

            if (failures.length > 0) {
                if (localAudioTrack && !localVideoTrack) {
                    setCallStatusLabel('Waiting for student (microphone only)...');
                    alert('Camera is unavailable, so the call joined with microphone only.');
                } else if (!localAudioTrack && localVideoTrack) {
                    setCallStatusLabel('Waiting for student (camera only)...');
                    alert('Microphone is unavailable, so the call joined with camera only.');
                }
            }
        } catch (error) {
            console.error('Agora local media failed:', error);
            actuallyStopCall();
            alert(getAgoraCallErrorMessage(error, 'media'));
            return;
        }

        if (role === 'instructor') {
            if (callAnswered) {
                markInstructorCallConnected();
            } else if (options.alreadyAnswered) {
                setCallStatusLabel('Reconnecting...');
            } else {
                clearOutgoingCountdown();
                if (callTimer) callTimer.textContent = 'LIVE';
                setCallStatusLabel('Waiting for student...');
            }
        } else {
            setCallStatusLabel('Video Session');
            startCallTimer();
        }

        pollTimer = setInterval(pollSignals, 1000);
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
                    const finalizeResponse = await finalizeCall(consultationId);
                    syncRequestRowStatus(consultationId, 'completed');
                    if (finalizeResponse?.consultation) {
                        const requestRow = document.querySelector(`.request-row[data-consultation-id="${consultationId}"]`);
                        if (requestRow) {
                            const summaryBtn = requestRow.querySelector('.summary-open-btn');
                            if (summaryBtn) {
                                const data = {
                                    id: summaryBtn.dataset.id,
                                    date: summaryBtn.dataset.date,
                                    time: summaryBtn.dataset.time,
                                    student: summaryBtn.dataset.student,
                                    studentId: summaryBtn.dataset.studentId,
                                    type: summaryBtn.dataset.type,
                                    mode: summaryBtn.dataset.mode,
                                    duration: finalizeResponse.consultation.duration_minutes ? finalizeResponse.consultation.duration_minutes + ' min' : '--',
                                    summary: summaryBtn.dataset.summary || '',
                                    transcript: summaryBtn.dataset.transcript || '',
                                };
                                upsertInstructorHistoryRow(data);
                            }
                        }
                    }
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
        toggleCameraBtn.addEventListener('click', async () => {
            if (!localVideoTrack) return;
            localVideoEnabled = !localVideoEnabled;
            await localVideoTrack.setEnabled(localVideoEnabled);
            toggleCameraBtn.querySelector('.call-btn-text').textContent = localVideoEnabled ? 'Camera On' : 'Camera Off';
        });
    }
    if (toggleMicBtn) {
        toggleMicBtn.addEventListener('click', async () => {
            if (!localAudioTrack) return;
            localAudioEnabled = !localAudioEnabled;
            await localAudioTrack.setEnabled(localAudioEnabled);
            try {
                await localAudioTrack.setMuted?.(!localAudioEnabled);
            } catch (_) {
                // ignore
            }
            toggleMicBtn.querySelector('.call-btn-text').textContent = localAudioEnabled ? 'Mic On' : 'Mic Off';
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
            setHistorySidebarIconOnly(false);
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
            setHistorySidebarIconOnly(false);
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
        if (detailsModal && detailsModal.classList.contains('open')) {
            closeDetails();
        }
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

    function setInstructorDetailsCard(card, label, value) {
        if (!card) return;
        const cleanedValue = String(value || '').trim();
        if (!cleanedValue) {
            card.style.display = 'none';
            return;
        }
        card.style.display = 'flex';
        card.textContent = `${label}: ${cleanedValue}`;
    }

    function setInstructorDetailsSection(wrap, textNode, value, fallbackText, options = {}) {
        if (!wrap || !textNode) return;
        const cleanedValue = String(value || '').trim();
        const shouldHideWhenEmpty = options.hideWhenEmpty === true;

        if (!cleanedValue && shouldHideWhenEmpty) {
            wrap.style.display = 'none';
            textNode.textContent = fallbackText;
            return;
        }

        wrap.style.display = 'block';
        textNode.textContent = cleanedValue || fallbackText;
    }

    function buildRequestSummaryModalData(requestRow, consultationId) {
        if (!requestRow) return null;
        const requestMetaCols = requestRow.querySelectorAll('.request-meta');
        const dateMeta = requestMetaCols[0]?.querySelectorAll('span') || [];
        const studentName = requestRow.querySelector('.request-user-name')?.textContent?.trim() || 'Student';
        const studentId = String(requestRow.dataset.studentId || '').trim()
            || requestRow.querySelector('.request-user-id')?.textContent?.replace(/^ID:\s*/i, '').trim()
            || '--';
        const typeValue = requestMetaCols[1]?.querySelector('.request-type-title')?.textContent?.trim()
            || requestMetaCols[1]?.querySelector('span')?.textContent?.trim()
            || '--';
        const modeLabel = requestRow.dataset.modeLabel
            || requestMetaCols[2]?.querySelector('.request-tag')?.textContent?.trim()
            || '--';
        const dateValue = dateMeta[0]?.textContent?.trim() || '--';
        const timeValue = dateMeta[1]?.textContent?.trim() || '--';

        return {
            id: consultationId,
            student: studentName,
            studentId,
            date: dateValue,
            time: timeValue,
            type: typeValue,
            mode: modeLabel,
            summary: requestRow.dataset.summary || '',
            actionTaken: requestRow.dataset.transcript || '',
        };
    }

    function buildRequestDetailsDataFromRow(requestRow, fallbackData = {}) {
        if (!requestRow) return fallbackData;

        const requestMetaCols = requestRow.querySelectorAll('.request-meta');
        const dateMeta = requestMetaCols[0]?.querySelectorAll('span') || [];
        const studentName = requestRow.querySelector('.request-user-name')?.textContent?.trim() || fallbackData.student || 'Student';
        const studentId = String(requestRow.dataset.studentId || '').trim()
            || requestRow.querySelector('.request-user-id')?.textContent?.replace(/^ID:\s*/i, '').trim()
            || fallbackData.studentId
            || '--';
        const typeValue = requestMetaCols[1]?.querySelector('.request-type-title')?.textContent?.trim()
            || requestMetaCols[1]?.querySelector('span')?.textContent?.trim()
            || fallbackData.type
            || '--';
        const modeLabel = requestRow.dataset.modeLabel
            || requestMetaCols[2]?.querySelector('.request-tag')?.textContent?.trim()
            || fallbackData.mode
            || '--';
        const dateValue = dateMeta[0]?.textContent?.trim() || fallbackData.date || '--';
        const timeValue = dateMeta[1]?.textContent?.trim() || fallbackData.time || '--';
        const statusValue = requestRow.querySelector('.request-actions .request-status')?.textContent?.trim() || fallbackData.status || '';
        const updatedValue = requestRow.querySelector('.request-updated-inline')?.textContent?.trim() || requestRow.dataset.updated || fallbackData.updated || '';
        const actionSourceId = fallbackData.actionSourceId || '';
        let actionHtml = fallbackData.actionHtml || '';
        if (actionSourceId) {
            const actionSource = document.getElementById(actionSourceId);
            actionHtml = actionSource ? actionSource.innerHTML : actionHtml;
        }

        return {
            source: 'request',
            showRequestMeta: true,
            requestRowId: requestRow.dataset.consultationId || fallbackData.requestRowId || '',
            actionHtml,
            date: dateValue,
            time: timeValue,
            student: studentName,
            studentId,
            mode: modeLabel,
            type: typeValue,
            duration: fallbackData.duration || '--',
            status: statusValue,
            updated: updatedValue,
            notes: requestRow.dataset.notes || fallbackData.notes || '',
            summary: requestRow.dataset.summary || fallbackData.summary || '',
            transcript: requestRow.dataset.transcript || fallbackData.transcript || '',
        };
    }

    function bindDetailsActionButtons(container, requestRowId) {
        if (!container) return;

        bindRequestActionForms(container);

        container.querySelectorAll('.summary-open-btn').forEach((btn) => {
            if (btn.__summaryBound) return;
            btn.__summaryBound = true;
            btn.addEventListener('click', () => {
                const requestRow = requestRowId
                    ? document.querySelector(`.request-row[data-consultation-id="${requestRowId}"]`)
                    : null;
                const data = buildRequestSummaryModalData(requestRow, requestRowId);
                if (!data) return;
                openSummaryModal(data);
            });
        });
    }

    function setDetailsActions(actionHtml, requestRowId) {
        if (!detailsActionsWrap || !detailsActionsContent) return;

        const cleanedHtml = String(actionHtml || '').trim();
        if (!cleanedHtml) {
            detailsActionsWrap.style.display = 'none';
            detailsActionsContent.innerHTML = '';
            detailsActionsContent.removeAttribute('data-request-row-id');
            return;
        }

        detailsActionsWrap.style.display = 'block';
        detailsActionsContent.innerHTML = cleanedHtml;
        if (requestRowId) {
            detailsActionsContent.dataset.requestRowId = String(requestRowId);
        } else {
            detailsActionsContent.removeAttribute('data-request-row-id');
        }
        bindDetailsActionButtons(detailsActionsContent, requestRowId);
    }

    function bindSummaryButtons(root = document) {
        root.querySelectorAll('.summary-open-btn').forEach((btn) => {
            if (btn.__summaryBound) return;
            btn.__summaryBound = true;
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

    bindSummaryButtons();

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
        const isRequestSource = data.source === 'request' || data.showRequestMeta === true;

        if (detailsSubtitle) {
            detailsSubtitle.textContent = isRequestSource
                ? `${data.type} - ${data.mode} Request`
                : `${data.type} - ${data.mode} Session`;
        }
        if (detailsDate) detailsDate.textContent = `Date & Time: ${data.date} at ${data.time}`;
        if (detailsStudent) detailsStudent.textContent = `Student: ${data.student}`;
        if (detailsStudentId) detailsStudentId.textContent = `Student ID: ${data.studentId || '--'}`;
        if (detailsMode) detailsMode.textContent = `Mode: ${data.mode}`;
        if (detailsType) detailsType.textContent = `Type: ${data.type}`;
        if (detailsDuration) detailsDuration.textContent = `Duration: ${data.duration || '--'}`;
        setInstructorDetailsCard(detailsStatus, 'Status', isRequestSource ? (data.status || '') : '');
        setInstructorDetailsCard(detailsUpdated, 'Updated', isRequestSource ? (data.updated || '') : '');
        setInstructorDetailsSection(detailsNotesWrap, detailsNotesText, data.notes || '', 'No notes provided.', {
            hideWhenEmpty: !isRequestSource,
        });
        setDetailsActions(isRequestSource ? (data.actionHtml || '') : '', isRequestSource ? (data.requestRowId || '') : '');
        setInstructorDetailsSection(
            detailsSummaryWrap,
            detailsSummaryText,
            data.summary || '',
            'Summary not yet available.',
            { hideWhenEmpty: isRequestSource }
        );
        setInstructorDetailsSection(
            detailsTranscriptWrap,
            detailsTranscriptText,
            data.transcript || '',
            'Action taken not yet available.',
            { hideWhenEmpty: isRequestSource }
        );

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
        const statusLabel = String(status || '').replace('_', ' ').toUpperCase();
        const statusFooter = `
            <div class="request-action-status">
                <span class="request-status ${status}">${statusLabel}</span>
            </div>
        `;

        function renderSummaryActionButton() {
            const summaryValue = String(requestRow?.dataset.summary || '').trim();
            const summaryBtnLabel = summaryValue ? 'View / Edit Summary' : 'Add Summary';
            const summaryData = buildRequestSummaryModalData(requestRow, consultationId) || {};
            actionsWrap.innerHTML = `
                <button type="button"
                        class="request-btn summary summary-open-btn"
                        data-id="${escapeHistoryHtml(summaryData.id || consultationId || '')}"
                        data-student="${escapeHistoryHtml(summaryData.student || 'Student')}"
                        data-student-id="${escapeHistoryHtml(summaryData.studentId || '--')}"
                        data-date="${escapeHistoryHtml(summaryData.date || '--')}"
                        data-time="${escapeHistoryHtml(summaryData.time || '--')}"
                        data-type="${escapeHistoryHtml(summaryData.type || '--')}"
                        data-mode="${escapeHistoryHtml(summaryData.mode || '--')}"
                        data-duration="${escapeHistoryHtml(summaryData.duration || '--')}"
                        data-summary="${escapeHistoryHtml(summaryData.summary || '')}"
                        data-transcript="${escapeHistoryHtml(summaryData.actionTaken || '')}">
                    ${summaryBtnLabel}
                </button>
                ${statusFooter}
            `;
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
                ${statusFooter}
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
                ${statusFooter}
            `;
            }
        } else if (status === 'in_progress') {
            actionsWrap.innerHTML = `<span class="request-tag">Video call in progress</span>${statusFooter}`;
        } else if (status === 'completed' || status === 'incompleted' || status === 'declined') {
            renderSummaryActionButton();
        } else {
            actionsWrap.innerHTML = `<span class="request-tag">No Action</span>${statusFooter}`;
        }

        bindRequestActionForms(actionsWrap);
        bindSummaryButtons(actionsWrap);
    }

    function updateRequestRowState(requestRow, nextStatus, options = {}) {
        if (!requestRow || !nextStatus) return;
        const preservePlacement = options.preservePlacement === true;
        const updatedLabel = String(options.updatedLabel || 'just now');

        requestRow.dataset.status = nextStatus;
        requestRow.dataset.updated = updatedLabel;

        const updatedInline = requestRow.querySelector('.request-updated-inline');
        if (updatedInline) {
            updatedInline.textContent = updatedLabel;
        }

        const consultationId = requestRow.dataset.consultationId;
        const actionsWrap = requestRow.querySelector('.request-actions');
        renderRequestActions(actionsWrap, consultationId, nextStatus, requestRow);

        const statusChip = requestRow.querySelector('.request-actions .request-status');
        if (statusChip) {
            statusClassList.forEach((statusClass) => statusChip.classList.remove(statusClass));
            statusChip.classList.add(nextStatus);
            statusChip.textContent = nextStatus.replace('_', ' ').toUpperCase();
        }

        if (
            detailsModal?.classList.contains('open')
            && detailsActionsContent?.dataset.requestRowId
            && consultationId
            && detailsActionsContent.dataset.requestRowId === consultationId
        ) {
            openDetailsModal(buildRequestDetailsDataFromRow(requestRow, {
                source: 'request',
                showRequestMeta: true,
                actionSourceId: `requestAction${consultationId}`,
                requestRowId: consultationId,
            }));
        }

        // Keep declined items in the same visible spot right after the action.
        if (!preservePlacement) {
            refreshRequestOrdering(false);
        }
    }

    function bindRequestActionForms(scope = document) {
        const forms = [];
        if (scope instanceof Element) {
            if (scope.matches('form')) {
                forms.push(scope);
            }
            if (scope.matches('.request-actions, .details-actions-content')) {
                forms.push(...Array.from(scope.querySelectorAll('form')));
            } else {
                forms.push(...Array.from(scope.querySelectorAll('.request-actions form, .details-actions-content form')));
            }
        } else {
            forms.push(...Array.from(scope.querySelectorAll('.request-actions form, .details-actions-content form')));
        }
        forms.forEach((form) => {
            if (form.__ajaxBound) return;
            form.__ajaxBound = true;
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                const requestRow = form.closest('.request-row')
                    || (() => {
                        const requestRowId = form.closest('[data-request-row-id]')?.dataset.requestRowId || '';
                        return requestRowId
                            ? document.querySelector(`.request-row[data-consultation-id="${requestRowId}"]`)
                            : null;
                    })();
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
                 data-student-id="${escapeHistoryHtml(consultation.student_id || '--')}"
                 data-status="${statusLower}"
                 data-mode="${consultation.consultation_mode.toLowerCase()}"
                 data-mode-label="${consultation.consultation_mode}"
                 data-call-attempts="${Number(consultation.call_attempts || 0)}"
                 data-started-at="${consultation.started_at || ''}"
                 data-updated="just now"
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
                <div class="request-mobile-details">
                    <button type="button"
                            class="request-mobile-details-btn details-open-btn"
                            data-source="request"
                            data-show-request-meta="true"
                            data-action-source="requestAction${consultation.id}"
                            data-id="${escapeHistoryHtml(consultation.id || '')}"
                            data-student="${escapeHistoryHtml(studentName)}"
                            data-student-id="${escapeHistoryHtml(consultation.student_id || '--')}"
                            data-type="${escapeHistoryHtml(consultation.type_label || '--')}"
                            data-mode="${escapeHistoryHtml(consultation.consultation_mode || '--')}"
                            data-date="${escapeHistoryHtml(consultation.consultation_date || '--')}"
                            data-time="${escapeHistoryHtml(consultation.time_range || '--')}"
                            data-duration="--"
                            data-status="${escapeHistoryHtml(statusDisplay.toUpperCase())}"
                            data-updated="just now"
                            data-notes="${escapeHistoryHtml(studentNotes)}"
                            data-summary=""
                            data-transcript="">
                        View Details
                    </button>
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
                    <div class="request-updated-inline">just now</div>
                </div>
                <div class="request-actions" id="requestAction${consultation.id}">
                    <!-- Action buttons will be rendered here -->
                </div>
            </div>
        `;

        // Render actions for this row
        const actionWrap = wrapper.querySelector('.request-actions');
        renderRequestActions(actionWrap, consultation.id, statusLower, wrapper);
        const detailsBtn = wrapper.querySelector('.details-open-btn');
        bindDetailsOpenButton(detailsBtn);

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
