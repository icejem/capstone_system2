<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://download.agora.io/sdk/release/AgoraRTC_N.js"></script>
<script>
const AGORA_APP_ID = @json(config('services.agora.app_id'));
const AGORA_TOKEN_ENDPOINT = @json(route('consultations.agora-token', ['consultation' => '__CONSULTATION__']));
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
const requestFormElement = document.querySelector('form[action="{{ route("student.consultation.store") }}"]');
const historyOpenBtns = document.querySelectorAll('.history-open-btn');
const closeHistoryModal = document.getElementById('closeHistoryModal');
const contentHeaderSection = document.querySelector('.content-header');
const historyDateRange = document.getElementById('historyDateRange');
const historyType = document.getElementById('historyType');
const historyMode = document.getElementById('historyMode');
const historyYearInput = document.getElementById('historyYearInput');
const historySemButtons = Array.from(document.querySelectorAll('.semester-btn'));
const historyExport = document.getElementById('historyExport');
const historyResetFilters = document.getElementById('historyResetFilters');
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
const detailsExportBtn = document.getElementById('detailsExportBtn');
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
const callConnectionHint = document.getElementById('callConnectionHint');
const callNetworkPill = document.getElementById('callNetworkPill');
const localVideo = document.getElementById('localVideo');
const remoteVideo = document.getElementById('remoteVideo');
const localPreviewWindow = callModal?.querySelector('[data-draggable-local]') || localVideo;
const callStage = callModal?.querySelector('.call-stage');
const toggleCameraBtn = document.getElementById('toggleCameraBtn');
const toggleMicBtn = document.getElementById('toggleMicBtn');
const switchCameraBtn = document.getElementById('switchCameraBtn');
const shareScreenBtn = document.getElementById('shareScreenBtn');
const enableAudioBtn = document.getElementById('enableAudioBtn');
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
let suppressIncomingEndedMessageUntil = 0;
const STUDENT_RECENTLY_ENDED_CALL_KEY = 'student_recently_ended_call';

function markStudentCallRecentlyEnded(consultationId) {
    const normalizedId = Number(consultationId || 0);
    if (!normalizedId) return;
    try {
        localStorage.setItem(STUDENT_RECENTLY_ENDED_CALL_KEY, JSON.stringify({
            consultationId: normalizedId,
            at: Date.now(),
        }));
    } catch (_) {
        // ignore storage errors
    }
}

function wasStudentCallRecentlyEnded(consultationId, windowMs = 30000) {
    const normalizedId = Number(consultationId || 0);
    if (!normalizedId) return false;
    try {
        const raw = localStorage.getItem(STUDENT_RECENTLY_ENDED_CALL_KEY);
        if (!raw) return false;
        const parsed = JSON.parse(raw);
        return Number(parsed?.consultationId || 0) === normalizedId
            && (Date.now() - Number(parsed?.at || 0)) < windowMs;
    } catch (_) {
        return false;
    }
}

function suppressIncomingEndedMessage(durationMs = 4000) {
    suppressIncomingEndedMessageUntil = Date.now() + durationMs;
}

function shouldSuppressIncomingEndedMessage() {
    return Date.now() < Number(suppressIncomingEndedMessageUntil || 0);
}

function buildIncomingAttemptKey(consultation) {
    const consultationId = Number(consultation?.id || 0);
    const callAttempts = Math.max(1, Number(consultation?.call_attempts || 0));
    return consultationId > 0 ? `${consultationId}:${callAttempts}` : '';
}

// Load shown call attempts from localStorage to persist across page reloads
function getShownIncomingAttempts() {
    const stored = localStorage.getItem('shownIncomingConsultations');
    return stored ? JSON.parse(stored) : [];
}

function markIncomingAttemptAsShown(consultation) {
    const attemptKey = buildIncomingAttemptKey(consultation);
    if (!attemptKey) return;

    const shown = getShownIncomingAttempts();
    if (!shown.includes(attemptKey)) {
        shown.push(attemptKey);
        localStorage.setItem('shownIncomingConsultations', JSON.stringify(shown));
    }
}

function isIncomingAttemptShown(consultation) {
    const attemptKey = buildIncomingAttemptKey(consultation);
    if (!attemptKey) return false;
    return getShownIncomingAttempts().includes(attemptKey);
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
    detailsActionsContent.querySelectorAll('.cc-summary-details-btn').forEach((btn) => btn.remove());
    if (!detailsActionsContent.textContent.trim() && !detailsActionsContent.children.length) {
        detailsActionsWrap.style.display = 'none';
        detailsActionsContent.innerHTML = '';
        return;
    }
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
            detailsModal?.setAttribute('data-consultation-id', btn.dataset.id || '');

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

            if (lastConsultationId !== null) {
                const previousIncomingConsultationId = Number(lastConsultationId || 0);
                const activeConsultationId = Number(currentConsultationId || 0);
                hideIncomingModal();
                hideDeclineConfirmation();
                if (incomingButtonsContainer) {
                    incomingButtonsContainer.style.display = 'flex';
                }
                if (
                    previousIncomingConsultationId > 0
                    && previousIncomingConsultationId !== activeConsultationId
                    && !shouldSuppressIncomingEndedMessage()
                ) {
                    showStudentCallOutcomeToast('Call ended by Instructor', 'warning');
                }
                lastConsultationId = null;
            }
            return;
        }

        // Only show for video modes
        if (!String(c.mode || '').toLowerCase().includes('video')) return;

        // ONLY show if this is a NEW consultation (never shown before)
        if (isIncomingAttemptShown(c)) {
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
        markIncomingAttemptAsShown(c);
        lastConsultationId = c.id;
        showIncomingModal();

        acceptIncomingBtn.onclick = () => {
            suppressIncomingEndedMessage();
            // Hide modal and start call
            hideIncomingModal();
            try {
                startVideoCall(c.id, {
                    initialSignalId: Number(c.latest_signal_id || 0),
                });
            } catch (e) { /* ignore */ }
        };

        declineIncomingBtn.onclick = () => {
            suppressIncomingEndedMessage();
            // Show confirmation modal
            showDeclineConfirmation();
        };

    } catch (e) {
        // ignore
    }
}

function showIncomingModal() {
    if (!incomingCallModal) return;
    incomingCallModal.style.display = 'flex';
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
    const hideHeader = false;

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
        resetStudentRequestForm();
        showStudentSection('dashboard');
    });
}

if (requestCloseBtn && requestSection) {
    requestCloseBtn.addEventListener('click', () => {
        resetStudentRequestForm();
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
        const visibleRows = historyRowWraps
            .filter((wrap) => wrap.style.display !== 'none')
            .map((wrap) => wrap.querySelector('.history-row-item'))
            .filter(Boolean);
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
    if (detailsExportBtn) {
        const consultationId = String(data.id || detailsModal?.dataset.consultationId || '');
        detailsExportBtn.href = consultationId
            ? `{{ url('/consultations') }}/${consultationId}/export-pdf`
            : '#';
        detailsExportBtn.style.pointerEvents = consultationId ? 'auto' : 'none';
        detailsExportBtn.style.opacity = consultationId ? '1' : '0.5';
    }

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
    detailsModal.removeAttribute('data-consultation-id');
    if (detailsExportBtn) {
        detailsExportBtn.href = '#';
        detailsExportBtn.style.pointerEvents = 'none';
        detailsExportBtn.style.opacity = '0.5';
    }
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
const DEFAULT_CALL_HINT = 'Prepare your camera and microphone.';
const STANDARD_VIDEO_ENCODER_CONFIG = {
    width: 640,
    height: 360,
    frameRate: 15,
    bitrateMin: 320,
    bitrateMax: 900,
};
const LOW_BANDWIDTH_VIDEO_ENCODER_CONFIG = {
    width: 480,
    height: 270,
    frameRate: 12,
    bitrateMin: 180,
    bitrateMax: 520,
};

let currentConsultationId = null;
let agoraClient = null;
let localAudioTrack = null;
let localVideoTrack = null;
let joinedAgoraChannel = '';
let pollTimer = null;
let lastSignalId = 0;
let callTimerInterval = null;
let callStartAt = null;
let callAnswered = false;
let remoteMediaConnected = false;
let mediaSyncInterval = null;
let isEndingCall = false;
let localVideoEnabled = true;
let localAudioEnabled = true;
let remoteAudioNeedsInteraction = false;
let remotePlaybackEnabled = true;
let currentVideoProfile = 'standard';
let studentCallEndToastSuppressUntil = 0;
let studentCompletionToastSuppressUntil = 0;
let lastStudentCallOutcomeToast = { message: '', shownAt: 0 };
let screenVideoTrack = null;
let isScreenSharing = false;
let currentCameraDeviceId = '';
let isResumedFromRefresh = false;  // Track if we're resuming from a page refresh

// Restore consultation ID from session storage if exists (for page refresh)
try {
    const storedConsultationId = sessionStorage.getItem('student_active_consultation_id');
    if (storedConsultationId) {
        const parsed = Number(storedConsultationId);
        if (parsed > 0) {
            currentConsultationId = parsed;
            isResumedFromRefresh = true;
        }
    }
} catch (e) {
    // ignore storage errors
}

function buildAgoraChannelName(consultationId) {
    return `consultation-${consultationId}`;
}

function buildAgoraTokenUrl(consultationId) {
    return AGORA_TOKEN_ENDPOINT.replace('__CONSULTATION__', String(consultationId));
}

function setCallConnectionHint(text = DEFAULT_CALL_HINT) {
    if (!callConnectionHint) return;
    callConnectionHint.textContent = text || DEFAULT_CALL_HINT;
    if (callNetworkPill) {
        const hint = String(text || DEFAULT_CALL_HINT).toLowerCase();
        if (hint.includes('weak network')) {
            callNetworkPill.textContent = 'Low Bandwidth';
        } else if (hint.includes('fluctuating')) {
            callNetworkPill.textContent = 'Stabilizing';
        } else {
            callNetworkPill.textContent = 'Adaptive';
        }
    }
}

function updateCallToggleButton(button, enabled) {
    if (!button) return;

    button.classList.toggle('is-off', !enabled);

    const icon = button.querySelector('.call-btn-icon i');
    if (icon) {
        if (button === toggleCameraBtn) {
            icon.className = `fa-solid ${enabled ? 'fa-video' : 'fa-video-slash'}`;
        } else if (button === toggleMicBtn) {
            icon.className = `fa-solid ${enabled ? 'fa-microphone' : 'fa-microphone-slash'}`;
        }
    }

    const stateText = button.querySelector('.call-btn-text');
    if (stateText) {
        stateText.textContent = enabled ? 'On' : 'Off';
    }
}

function updateShareScreenButton(active) {
    if (!shareScreenBtn) return;

    shareScreenBtn.classList.toggle('is-active', Boolean(active));
    const stateText = shareScreenBtn.querySelector('.call-btn-text');
    if (stateText) {
        stateText.textContent = active ? 'Sharing' : 'Share';
    }
}

function getCallPanelIndicator(panel, kind) {
    return panel?.querySelector(`[data-call-${kind}-indicator]`) || null;
}

function getCallPanelStatus(panel) {
    return panel?.querySelector('[data-call-video-status]') || null;
}

function refreshCallStagePresentation() {
    if (!callStage) return;
    const hasScreenShare = localVideo?.dataset.screenSharing === '1' || remoteVideo?.dataset.screenSharing === '1';
    callStage.classList.toggle('is-screen-focus', hasScreenShare);
}

function setCallPanelState(panel, next = {}) {
    if (!panel) return;

    if ('screenSharing' in next) panel.dataset.screenSharing = next.screenSharing ? '1' : '0';
    if ('micOn' in next) panel.dataset.micOn = next.micOn ? '1' : '0';
    if ('cameraOn' in next) panel.dataset.cameraOn = next.cameraOn ? '1' : '0';
    if ('hasVideo' in next) panel.dataset.hasVideo = next.hasVideo ? '1' : '0';

    const screenSharing = panel.dataset.screenSharing === '1';
    const micOn = panel.dataset.micOn !== '0';
    const cameraOn = panel.dataset.cameraOn !== '0';
    const hasVideo = panel.dataset.hasVideo === '1';

    let state = 'waiting';
    if (screenSharing) {
        state = 'screen-share';
    } else if (!cameraOn && micOn) {
        state = 'audio-only';
    } else if (!cameraOn && !micOn) {
        state = 'camera-off';
    } else if (hasVideo) {
        state = 'live';
    }

    panel.dataset.state = state;
    panel.classList.toggle('has-video', hasVideo);

    const audioIndicator = getCallPanelIndicator(panel, 'audio');
    if (audioIndicator) {
        audioIndicator.textContent = micOn ? 'Mic On' : 'Mic Off';
    }

    const videoIndicator = getCallPanelIndicator(panel, 'video');
    if (videoIndicator) {
        videoIndicator.textContent = cameraOn ? 'Camera On' : 'Camera Off';
    }

    const screenIndicator = getCallPanelIndicator(panel, 'screen');
    if (screenIndicator) {
        screenIndicator.hidden = !screenSharing;
    }

    if (Object.prototype.hasOwnProperty.call(next, 'statusText')) {
        const statusNode = getCallPanelStatus(panel);
        if (statusNode && typeof next.statusText === 'string') {
            statusNode.textContent = next.statusText;
        }
    }

    refreshCallStagePresentation();
}

function isLikelyScreenTrack(track) {
    const rawLabel = String(
        track?.getTrackLabel?.()
        || track?.getMediaStreamTrack?.()?.label
        || ''
    ).toLowerCase();

    return /screen|window|tab|display|monitor/.test(rawLabel);
}

function resetLocalPreviewPosition() {
    if (!localPreviewWindow) return;
    localPreviewWindow.style.left = '';
    localPreviewWindow.style.top = '';
    localPreviewWindow.style.right = '';
    localPreviewWindow.style.bottom = '';
}

function usesFloatingLocalPreview() {
    return window.innerWidth <= 860;
}

function clampLocalPreviewPosition(x, y) {
    if (!callStage || !localPreviewWindow || !usesFloatingLocalPreview()) {
        return { x, y };
    }

    const stageRect = callStage.getBoundingClientRect();
    const previewRect = localPreviewWindow.getBoundingClientRect();
    const inset = window.innerWidth <= 860 ? 10 : 14;
    const maxX = Math.max(inset, stageRect.width - previewRect.width - inset);
    const maxY = Math.max(inset, stageRect.height - previewRect.height - inset);

    return {
        x: Math.min(Math.max(inset, x), maxX),
        y: Math.min(Math.max(inset, y), maxY),
    };
}

function applyLocalPreviewPosition(x, y) {
    if (!localPreviewWindow) return;
    if (!usesFloatingLocalPreview()) {
        resetLocalPreviewPosition();
        return;
    }

    const next = clampLocalPreviewPosition(x, y);
    localPreviewWindow.style.left = `${next.x}px`;
    localPreviewWindow.style.top = `${next.y}px`;
    localPreviewWindow.style.right = 'auto';
    localPreviewWindow.style.bottom = 'auto';
}

function keepLocalPreviewInBounds() {
    if (!usesFloatingLocalPreview()) {
        resetLocalPreviewPosition();
        return;
    }

    if (!localPreviewWindow?.style.left || !localPreviewWindow?.style.top) return;

    const currentX = parseFloat(localPreviewWindow.style.left) || 0;
    const currentY = parseFloat(localPreviewWindow.style.top) || 0;
    applyLocalPreviewPosition(currentX, currentY);
}

function initDraggableLocalPreview() {
    if (!localPreviewWindow || !callStage || localPreviewWindow.dataset.dragBound === '1') return;

    localPreviewWindow.dataset.dragBound = '1';
    let activePointerId = null;
    let pointerOffsetX = 0;
    let pointerOffsetY = 0;

    const stopDragging = () => {
        activePointerId = null;
        localPreviewWindow.classList.remove('is-dragging');
    };

    localPreviewWindow.addEventListener('pointerdown', (event) => {
        if (!usesFloatingLocalPreview()) return;
        if (event.button !== undefined && event.button !== 0) return;

        const stageRect = callStage.getBoundingClientRect();
        const previewRect = localPreviewWindow.getBoundingClientRect();
        applyLocalPreviewPosition(previewRect.left - stageRect.left, previewRect.top - stageRect.top);

        activePointerId = event.pointerId;
        pointerOffsetX = event.clientX - previewRect.left;
        pointerOffsetY = event.clientY - previewRect.top;
        localPreviewWindow.classList.add('is-dragging');
        localPreviewWindow.setPointerCapture?.(activePointerId);
        event.preventDefault();
    });

    localPreviewWindow.addEventListener('pointermove', (event) => {
        if (!usesFloatingLocalPreview()) return;
        if (activePointerId !== event.pointerId || !callStage) return;

        const stageRect = callStage.getBoundingClientRect();
        applyLocalPreviewPosition(
            event.clientX - stageRect.left - pointerOffsetX,
            event.clientY - stageRect.top - pointerOffsetY
        );
    });

    localPreviewWindow.addEventListener('pointerup', (event) => {
        if (activePointerId !== event.pointerId) return;
        localPreviewWindow.releasePointerCapture?.(activePointerId);
        stopDragging();
    });

    localPreviewWindow.addEventListener('pointercancel', stopDragging);
    localPreviewWindow.addEventListener('lostpointercapture', stopDragging);
    window.addEventListener('resize', keepLocalPreviewInBounds);
}

function normalizeAgoraNetworkQuality(stats) {
    if (!stats || typeof stats !== 'object') {
        return { uplink: 0, downlink: 0 };
    }

    return {
        uplink: Number(stats.uplinkNetworkQuality ?? stats.uplink ?? 0),
        downlink: Number(stats.downlinkNetworkQuality ?? stats.downlink ?? 0),
    };
}

function describeAgoraNetworkQuality(level) {
    if (level >= 4) {
        return 'Weak network detected. Low-latency video mode is active.';
    }

    if (level === 3) {
        return 'Connection is fluctuating. Adaptive video is smoothing the call.';
    }

    return 'Connected with adaptive video quality.';
}

async function syncRemoteVideoStreamProfile() {
    if (!agoraClient?.remoteUsers?.length) return;

    for (const user of agoraClient.remoteUsers) {
        if (user?.uid == null || (!user.hasVideo && !user.videoTrack)) continue;

        try {
            await agoraClient.setRemoteVideoStreamType?.(user.uid, currentVideoProfile === 'low' ? 1 : 0);
        } catch (_) {
            // ignore
        }
    }
}

async function applyAdaptiveVideoProfile(profile = 'standard') {
    currentVideoProfile = profile === 'low' ? 'low' : 'standard';
    const encoderConfig = currentVideoProfile === 'low'
        ? LOW_BANDWIDTH_VIDEO_ENCODER_CONFIG
        : STANDARD_VIDEO_ENCODER_CONFIG;

    if (localVideoTrack) {
        try {
            await localVideoTrack.setEncoderConfiguration?.(encoderConfig);
        } catch (_) {
            // ignore
        }

        try {
            await localVideoTrack.setOptimizationMode?.('motion');
        } catch (_) {
            // ignore
        }
    }

    await syncRemoteVideoStreamProfile();
}

function bindAgoraNetworkQuality(client) {
    if (!client || client.__adaptiveQualityBound) return;

    client.__adaptiveQualityBound = true;
    client.on('network-quality', (stats) => {
        const { uplink, downlink } = normalizeAgoraNetworkQuality(stats);
        const level = Math.max(uplink, downlink);
        setCallConnectionHint(describeAgoraNetworkQuality(level));
        void applyAdaptiveVideoProfile(level >= 4 ? 'low' : 'standard');
    });
}

async function configureAgoraClientForCall(client) {
    if (!client) return;

    bindAgoraNetworkQuality(client);

    try {
        await client.enableDualStream?.();
    } catch (_) {
        // ignore
    }

    try {
        await client.setLowStreamParameter?.({
            width: 320,
            height: 180,
            framerate: 15,
            bitrate: 220,
        });
    } catch (_) {
        // ignore
    }
}

function getScreenShareErrorMessage(error) {
    const rawMessage = String(error?.message || error?.name || error?.code || '').trim();
    const message = rawMessage.toLowerCase();

    if (message.includes('notallowed') || message.includes('permission denied')) {
        return 'Screen sharing was blocked. Allow screen capture in your browser and try again.';
    }

    if (message.includes('notreadable') || message.includes('abort')) {
        return 'Screen sharing did not start. Try again and keep the capture picker open until you choose a screen.';
    }

    if (message.includes('notfound')) {
        return 'No screen or window was selected to share.';
    }

    return rawMessage
        ? `Unable to start screen sharing: ${rawMessage}`
        : 'Unable to start screen sharing on this browser.';
}

async function switchToNextCamera() {
    if (!localVideoTrack) {
        setCallConnectionHint('Camera preview is not available on this device.');
        return;
    }

    try {
        const cameras = await AgoraRTC.getCameras();
        if (!Array.isArray(cameras) || cameras.length < 2) {
            setCallConnectionHint('Only one camera is available on this device.');
            return;
        }

        const currentLabel = String(localVideoTrack.getTrackLabel?.() || '').trim().toLowerCase();
        let currentIndex = cameras.findIndex((camera) => camera.deviceId === currentCameraDeviceId);
        if (currentIndex === -1 && currentLabel) {
            currentIndex = cameras.findIndex((camera) => String(camera.label || '').trim().toLowerCase() === currentLabel);
        }
        if (currentIndex === -1) {
            currentIndex = 0;
        }

        const nextCamera = cameras[(currentIndex + 1) % cameras.length];
        await localVideoTrack.setDevice(nextCamera.deviceId);
        currentCameraDeviceId = nextCamera.deviceId;
        await applyAdaptiveVideoProfile(currentVideoProfile);
        clearAgoraContainer(localVideo);
        localVideoTrack.play(localVideo);
        setCallConnectionHint(`Switched camera to ${nextCamera.label || 'the next available device'}.`);
        setCallPanelState(localVideo, {
            cameraOn: localVideoEnabled,
            micOn: localAudioEnabled,
            hasVideo: Boolean(localVideoEnabled),
            screenSharing: isScreenSharing,
            statusText: `Switched camera to ${nextCamera.label || 'the next available device'}.`,
        });
    } catch (error) {
        console.error('Camera switch failed:', error);
        setCallConnectionHint('Unable to switch camera on this device.');
    }
}

async function stopScreenShare(options = {}) {
    const { restoreCamera = true, notify = true } = options;

    if (agoraClient && joinedAgoraChannel && screenVideoTrack) {
        try {
            await agoraClient.unpublish(screenVideoTrack);
        } catch (_) {
            // ignore
        }
    }

    if (screenVideoTrack) {
        try {
            screenVideoTrack.stop();
            screenVideoTrack.close();
        } catch (_) {
            // ignore
        }
        screenVideoTrack = null;
    }

    isScreenSharing = false;
    updateShareScreenButton(false);

    if (restoreCamera && agoraClient && joinedAgoraChannel && localVideoTrack) {
        try {
            await agoraClient.publish(localVideoTrack);
        } catch (_) {
            // ignore
        }

        try {
            await localVideoTrack.setEnabled(localVideoEnabled);
        } catch (_) {
            // ignore
        }

        await applyAdaptiveVideoProfile(currentVideoProfile);
    }

    if (notify) {
        setCallConnectionHint('Screen sharing stopped. Camera stream restored.');
    }

    setCallPanelState(localVideo, {
        screenSharing: false,
        cameraOn: localVideoEnabled,
        micOn: localAudioEnabled,
        hasVideo: Boolean(localVideoTrack && localVideoEnabled),
        statusText: localVideoEnabled ? 'Camera preview is ready.' : 'Your camera is off.',
    });
}

async function startScreenShare() {
    if (!agoraClient || !joinedAgoraChannel) {
        setCallConnectionHint('Join the call first before sharing your screen.');
        return;
    }

    if (isScreenSharing) {
        await stopScreenShare();
        return;
    }

    try {
        const createdTrack = await AgoraRTC.createScreenVideoTrack({}, 'disable');
        const nextScreenTrack = Array.isArray(createdTrack) ? createdTrack[0] : createdTrack;
        if (!nextScreenTrack) {
            throw new Error('No screen track was created.');
        }

        screenVideoTrack = nextScreenTrack;
        await screenVideoTrack.setOptimizationMode?.('detail');
        screenVideoTrack.on?.('track-ended', () => {
            void stopScreenShare({ restoreCamera: true, notify: true });
        });

        if (localVideoTrack) {
            try {
                await agoraClient.unpublish(localVideoTrack);
            } catch (_) {
                // ignore
            }
        }

        await agoraClient.publish(screenVideoTrack);
        isScreenSharing = true;
        updateShareScreenButton(true);
        setCallConnectionHint('Screen sharing is live. The instructor can now see your screen.');
        setCallPanelState(localVideo, {
            screenSharing: true,
            cameraOn: localVideoEnabled,
            micOn: localAudioEnabled,
            hasVideo: Boolean(localVideoTrack && localVideoEnabled),
            statusText: 'You are sharing your screen.',
        });
    } catch (error) {
        console.error('Screen share failed:', error);
        await stopScreenShare({ restoreCamera: false, notify: false });
        alert(getScreenShareErrorMessage(error));
    }
}

function setRemoteAudioInteractionNeeded(needed) {
    remoteAudioNeedsInteraction = Boolean(needed);
    updatePlaybackButton();
}

function tryResumeAgoraAudioContext() {
    try {
        window.AgoraRTC?.resumeAudioContext?.();
    } catch (_) {
        // ignore
    }
}

function stopRemoteAudioPlayback() {
    if (!agoraClient?.remoteUsers?.length) return;

    for (const user of agoraClient.remoteUsers) {
        const track = user?.audioTrack;
        if (!track) continue;

        try {
            track.setVolume?.(0);
            track.stop?.();
        } catch (_) {
            // ignore
        }
    }
}

function updatePlaybackButton() {
    if (!enableAudioBtn) return;

    enableAudioBtn.hidden = false;
    enableAudioBtn.classList.toggle('is-off', !remotePlaybackEnabled);
    enableAudioBtn.classList.toggle('is-active', remotePlaybackEnabled);

    const icon = enableAudioBtn.querySelector('.call-btn-icon i');
    if (icon) {
        icon.className = `fa-solid ${remotePlaybackEnabled ? 'fa-volume-high' : 'fa-volume-xmark'}`;
    }

    const stateText = enableAudioBtn.querySelector('.call-btn-text');
    if (stateText) {
        stateText.textContent = remotePlaybackEnabled
            ? (remoteAudioNeedsInteraction ? 'Resume' : 'On')
            : 'Off';
    }
}

function playRemoteAudioTrack(track) {
    if (!track) return;

    if (!remotePlaybackEnabled) {
        try {
            track.setVolume?.(0);
            track.stop?.();
        } catch (_) {
            // ignore
        }
        setRemoteAudioInteractionNeeded(false);
        return;
    }

    tryResumeAgoraAudioContext();

    try {
        track.setVolume?.(100);
        const playback = track.play();
        if (playback && typeof playback.then === 'function') {
            playback
                .then(() => setRemoteAudioInteractionNeeded(false))
                .catch((error) => {
                    console.warn('Agora remote audio playback failed:', error);
                    setRemoteAudioInteractionNeeded(true);
                });
            return;
        }

        setRemoteAudioInteractionNeeded(false);
    } catch (error) {
        console.warn('Agora remote audio playback failed:', error);
        setRemoteAudioInteractionNeeded(true);
    }
}

function replayRemoteAudioTracks() {
    if (!remotePlaybackEnabled) {
        stopRemoteAudioPlayback();
        setRemoteAudioInteractionNeeded(false);
        return;
    }

    if (!agoraClient?.remoteUsers?.length) {
        setRemoteAudioInteractionNeeded(false);
        return;
    }

    let foundTrack = false;
    for (const user of agoraClient.remoteUsers) {
        if (!user?.audioTrack) continue;
        foundTrack = true;
        playRemoteAudioTrack(user.audioTrack);
    }

    if (!foundTrack) {
        setRemoteAudioInteractionNeeded(false);
    }
}

function tryUnlockRemoteAudio() {
    if (!remotePlaybackEnabled || !remoteAudioNeedsInteraction) return;
    replayRemoteAudioTracks();
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
    setCallPanelState(remoteVideo, {
        cameraOn: true,
        micOn: remoteVideo.dataset.micOn !== '0',
        hasVideo: true,
        screenSharing: isLikelyScreenTrack(track),
        statusText: isLikelyScreenTrack(track) ? 'Screen sharing is active.' : 'Live video connected.',
    });
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
        localVideoTrack = await AgoraRTC.createCameraVideoTrack({ encoderConfig: STANDARD_VIDEO_ENCODER_CONFIG });
        tracks.push(localVideoTrack);
        localVideoEnabled = true;
        await localVideoTrack.setOptimizationMode?.('motion');
        try {
            const cameras = await AgoraRTC.getCameras();
            const currentLabel = String(localVideoTrack.getTrackLabel?.() || '').trim().toLowerCase();
            currentCameraDeviceId = cameras.find((camera) => String(camera.label || '').trim().toLowerCase() === currentLabel)?.deviceId || currentCameraDeviceId;
        } catch (_) {
            // ignore
        }
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

function markStudentCallConnected() {
    remoteMediaConnected = true;
    if (mediaSyncInterval) {
        clearInterval(mediaSyncInterval);
        mediaSyncInterval = null;
    }
    setCallStatusLabel('Video Session');
    setCallConnectionHint(describeAgoraNetworkQuality(currentVideoProfile === 'low' ? 4 : 1));
    setCallPanelState(remoteVideo, {
        micOn: remoteVideo.dataset.micOn !== '0',
        cameraOn: remoteVideo.dataset.cameraOn !== '0',
        hasVideo: remoteVideo.classList.contains('has-video'),
    });
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
    bindAgoraNetworkQuality(agoraClient);

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
            setCallPanelState(remoteVideo, {
                cameraOn: false,
                hasVideo: false,
                screenSharing: false,
                statusText: remoteVideo.dataset.micOn === '1'
                    ? 'The instructor is in audio only mode.'
                    : 'The instructor camera is off.',
            });
        }

        if (mediaType === 'audio') {
            setCallPanelState(remoteVideo, {
                micOn: false,
                statusText: remoteVideo.classList.contains('has-video')
                    ? 'The instructor microphone is muted.'
                    : 'Waiting for instructor audio...',
            });
        }
    });

    agoraClient.on('user-left', () => {
        remoteMediaConnected = false;
        clearAgoraContainer(remoteVideo);
        delete remoteVideo.dataset.trackId;
        setCallPanelState(remoteVideo, {
            micOn: false,
            cameraOn: false,
            hasVideo: false,
            screenSharing: false,
            statusText: 'Waiting for instructor to join...',
        });
        if (currentConsultationId) {
            setCallStatusLabel('Waiting for instructor...');
        }
    });

    return agoraClient;
}

async function subscribeToRemoteMedia(user, mediaType) {
    if (!agoraClient || !user || !mediaType) return;

    await agoraClient.subscribe(user, mediaType);

    if (mediaType === 'video' && user.videoTrack) {
        await syncRemoteVideoStreamProfile();
        playRemoteVideoTrack(user.videoTrack);
        markStudentCallConnected();
    }

    if (mediaType === 'audio' && user.audioTrack) {
        playRemoteAudioTrack(user.audioTrack);
        setCallPanelState(remoteVideo, {
            micOn: true,
            statusText: remoteVideo.classList.contains('has-video')
                ? 'Audio is connected.'
                : 'Audio connected. Waiting for video...',
        });
        if (!user.hasVideo && !user.videoTrack) {
            markStudentCallConnected();
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
    await stopScreenShare({ restoreCamera: false, notify: false });

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
    remotePlaybackEnabled = true;
    setRemoteAudioInteractionNeeded(false);
    currentVideoProfile = 'standard';
    currentCameraDeviceId = '';
    clearAgoraContainer(localVideo);
    clearAgoraContainer(remoteVideo);
    delete remoteVideo.dataset.trackId;
    setCallPanelState(localVideo, {
        micOn: true,
        cameraOn: true,
        hasVideo: false,
        screenSharing: false,
        statusText: 'Camera preview will appear here.',
    });
    setCallPanelState(remoteVideo, {
        micOn: false,
        cameraOn: false,
        hasVideo: false,
        screenSharing: false,
        statusText: 'Waiting for instructor to join...',
    });

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
    if (callTimerInterval) {
        clearInterval(callTimerInterval);
        callTimerInterval = null;
    }
    void cleanupAgoraCall();
    currentConsultationId = null;
    lastSignalId = 0;
    callStartAt = null;
    if (callTimer) callTimer.textContent = 'LIVE';
    callAnswered = false;
    remoteMediaConnected = false;
    updateCallToggleButton(toggleCameraBtn, true);
    updateCallToggleButton(toggleMicBtn, true);
    updateShareScreenButton(false);
    setCallConnectionHint(DEFAULT_CALL_HINT);
    resetLocalPreviewPosition();
    setCallStatusLabel('Video Session');
    closeCallModalUI();
    
    // Clear session storage when call ends
    try {
        sessionStorage.removeItem('student_active_consultation_id');
    } catch (e) {
        // ignore storage errors
    }
}

function stopCall() {
    // Show confirmation dialog
    showEndCallConfirmation();
}

function renderCallTimer() {
    if (!callTimer) return;
    const startedAt = Number(callStartAt);
    if (!Number.isFinite(startedAt) || startedAt <= 0) {
        callTimer.textContent = 'LIVE';
        return;
    }

    const totalSeconds = Math.max(0, Math.floor((Date.now() - startedAt) / 1000));
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    const parts = [];

    if (hours > 0) parts.push(`${hours}h`);
    if (minutes > 0 || hours > 0) parts.push(`${minutes}m`);
    parts.push(`${seconds}s`);

    callTimer.textContent = parts.join(' ');
}

function startCallTimer() {
    const parsedStartAt = Number(callStartAt);
    callStartAt = Number.isFinite(parsedStartAt) && parsedStartAt > 0
        ? parsedStartAt
        : Date.now();
    if (callTimerInterval) clearInterval(callTimerInterval);
    renderCallTimer();
    callTimerInterval = setInterval(renderCallTimer, 1000);
}

async function markConsultationAnswered(consultationId) {
    if (!consultationId) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const response = await fetch(`{{ url('/consultations') }}/${consultationId}/answer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
        },
        body: JSON.stringify({}),
    });

    if (!response.ok) {
        return null;
    }

    return response.json();
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
        // First send WebRTC signal immediately to notify instructor in real-time
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
                device_session_id: null,  // null = broadcast to all
            }),
        });
    } catch (e) {
        console.error('Failed to send decline signal:', e);
    }

    try {
        // Then update consultation status
        await fetch(`{{ url('/consultations') }}/${consultationId}/decline-call`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({}),
        });
    } catch (e) {
        console.error('Failed to update consultation status:', e);
    }
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
    const consultationState = data?.consultation || null;
    const sharedStartedAt = Date.parse(String(consultationState?.started_at || ''));

    if (
        consultationState?.status === 'in_progress' &&
        Number.isFinite(sharedStartedAt) &&
        sharedStartedAt > 0 &&
        (!Number.isFinite(Number(callStartAt)) || Number(callStartAt) <= 0)
    ) {
        callStartAt = sharedStartedAt;
        startCallTimer();
    }

    if (consultationState?.status === 'completed' && consultationState?.duration_label && callTimer) {
        callTimer.textContent = consultationState.duration_label;
    }

    if (!data?.signals?.length) return;
    data.signals.forEach((signal) => {
        lastSignalId = Math.max(lastSignalId, signal.id);
        // Skip old decline signals if we're resuming from a refresh
        // (old signals are not relevant to the current session)
        if (isResumedFromRefresh && signal.type === 'disconnect' && signal.payload?.reason === 'declined') {
            isResumedFromRefresh = false;  // Mark that we've caught up with old signals
            return;  // Skip this old decline signal
        }
        handleSignal(signal.type, signal.payload);
    });
}

async function handleSignal(type, payload) {
    if (type === 'disconnect') {
        const reason = String(payload?.reason || '');
        if (currentConsultationId) {
            markStudentCallRecentlyEnded(currentConsultationId);
        }
        const message = reason === 'cancelled_by_instructor'
            ? 'Call ended by Instructor'
            : reason === 'no_answer'
                ? 'Instructor ended this call attempt.'
            : reason === 'call_ended'
                ? 'Call completed'
                : 'Call ended by the other participant.';
        suppressStudentCallEndToasts();
        if (reason === 'call_ended') {
            suppressStudentCompletionToasts();
        }
        actuallyStopCall();
        showStudentCallOutcomeToast(message, reason === 'call_ended' ? 'success' : 'warning');
        if (reason === 'call_ended' || reason === 'no_answer' || reason === 'declined') {
            setTimeout(() => {
                try { pollStudentConsultationUpdates(); } catch (_) { /* ignore */ }
                try { pollStudentNotifications(); } catch (_) { /* ignore */ }
                try { checkIncoming(); } catch (_) { /* ignore */ }
            }, 150);
        }
        return;
    }
}

async function startVideoCall(consultationId, options = {}) {
    if (!consultationId) return;
    if (currentConsultationId && currentConsultationId !== consultationId) {
        actuallyStopCall();
    }

    if (!AGORA_APP_ID) {
        alert('Set AGORA_APP_ID in your .env file before testing Agora calls.');
        return;
    }

    currentConsultationId = consultationId;
    
    // Store consultation ID in session storage to restore after page refresh
    try {
        sessionStorage.setItem('student_active_consultation_id', String(consultationId));
    } catch (e) {
        console.error('Failed to save consultation ID to session storage:', e);
    }
    
    lastSignalId = Math.max(0, Number(options.initialSignalId || 0));
    callAnswered = false;
    callStartAt = null;
    remoteMediaConnected = false;
    setCallStatusLabel('Joining channel...');
    openCallModal();
    setCallConnectionHint('Preparing camera and microphone...');
    updateCallToggleButton(toggleCameraBtn, true);
    updateCallToggleButton(toggleMicBtn, true);
    updateShareScreenButton(false);
    resetLocalPreviewPosition();
    setCallPanelState(localVideo, {
        micOn: true,
        cameraOn: true,
        hasVideo: false,
        screenSharing: false,
        statusText: 'Preparing your camera preview...',
    });
    setCallPanelState(remoteVideo, {
        micOn: false,
        cameraOn: false,
        hasVideo: false,
        screenSharing: false,
        statusText: 'Waiting for instructor to join...',
    });

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
        await configureAgoraClientForCall(client);
        await syncPublishedRemoteUsers();
        setTimeout(() => { void syncPublishedRemoteUsers(); }, 500);
    } catch (error) {
        console.error('Agora join failed:', error);
        actuallyStopCall();
        alert(getAgoraCallErrorMessage(error, 'join'));
        return;
    }

    let answerResponse = null;
    try {
        answerResponse = await markConsultationAnswered(consultationId);
        callAnswered = true;
        const sharedStartedAt = Date.parse(String(answerResponse?.started_at || ''));
        callStartAt = Number.isFinite(sharedStartedAt) && sharedStartedAt > 0
            ? sharedStartedAt
            : Date.now();
        startCallTimer();
        try {
            await sendSignal('answered', {
                started_at: answerResponse?.started_at || null,
            });
        } catch (_) {
            // ignore
        }
    } catch (error) {
        console.error('Failed to acknowledge answered call:', error);
        actuallyStopCall();
        alert('Unable to connect the call right now. Please try answering again.');
        return;
    }

    try {
        const { tracks, failures } = await createLocalAgoraTracks();
        requireMicrophoneTrack(failures);
        await applyAdaptiveVideoProfile(currentVideoProfile);

        clearAgoraContainer(localVideo);
        if (localVideoTrack) {
            await localVideoTrack.setEnabled(true);
            localVideoTrack.play(localVideo);
            setCallPanelState(localVideo, {
                micOn: localAudioEnabled,
                cameraOn: true,
                hasVideo: true,
                screenSharing: isScreenSharing,
                statusText: 'Camera preview is ready.',
            });
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
        void syncPublishedRemoteUsers();
        beginRemoteMediaSync();
        setTimeout(() => { void syncPublishedRemoteUsers(); }, 150);
        setTimeout(() => { void syncPublishedRemoteUsers(); }, 500);
        setTimeout(() => { void syncPublishedRemoteUsers(); }, 1000);

        if (failures.length === 0) {
            setCallStatusLabel('Connecting...');
        } else if (localAudioTrack && !localVideoTrack) {
            setCallStatusLabel('Connecting with microphone only...');
            alert('Camera is unavailable, so the call joined with microphone only.');
        } else if (!localAudioTrack && localVideoTrack) {
            setCallStatusLabel('Connecting with camera only...');
            alert('Microphone is unavailable, so the call joined with camera only.');
        } else {
            setCallStatusLabel('Connecting...');
        }
    } catch (error) {
        console.error('Agora local media failed:', error);
        actuallyStopCall();
        alert(getAgoraCallErrorMessage(error, 'media'));
        return;
    }

    pollTimer = setInterval(pollSignals, 1000);
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
        suppressIncomingEndedMessage();
        
        if (consultationId > 0) {
            try {
                await declineIncomingCall(consultationId);
                // Give signal time to be processed on server
                await new Promise(resolve => setTimeout(resolve, 100));
            } catch (e) {
                console.error('Decline failed:', e);
            }
        }
        
        hideIncomingModal();
        clearShownConsultations();
        lastConsultationId = null;
        
        // Force immediate update to show decline status
        try { pollStudentNotifications(); } catch (_) { /* ignore */ }
        try { pollStudentConsultationUpdates(); } catch (_) { /* ignore */ }
    });
}

if (declineConfirmNo) {
    declineConfirmNo.addEventListener('click', () => {
        hideDeclineConfirmation();
    });
}

if (endCallConfirmYes) {
    endCallConfirmYes.addEventListener('click', async () => {
        suppressStudentCallEndToasts();
        suppressStudentCompletionToasts();
        hideEndCallConfirmation();
        const consultationId = currentConsultationId;
        markStudentCallRecentlyEnded(consultationId);
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
                showStudentCallOutcomeToast('Call completed', 'success');
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
    toggleCameraBtn.addEventListener('click', async () => {
        if (!localVideoTrack) return;
        localVideoEnabled = !localVideoEnabled;
        await localVideoTrack.setEnabled(localVideoEnabled);
        updateCallToggleButton(toggleCameraBtn, localVideoEnabled);
        setCallPanelState(localVideo, {
            cameraOn: localVideoEnabled,
            micOn: localAudioEnabled,
            hasVideo: Boolean(localVideoEnabled),
            screenSharing: isScreenSharing,
            statusText: localVideoEnabled ? 'Camera preview is ready.' : 'Your camera is off.',
        });
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
        updateCallToggleButton(toggleMicBtn, localAudioEnabled);
        setCallPanelState(localVideo, {
            micOn: localAudioEnabled,
            cameraOn: localVideoEnabled,
            hasVideo: Boolean(localVideoEnabled && localVideoTrack),
            screenSharing: isScreenSharing,
            statusText: localAudioEnabled ? 'Microphone is active.' : 'Your microphone is muted.',
        });
    });
}
if (switchCameraBtn) {
    switchCameraBtn.addEventListener('click', () => {
        void switchToNextCamera();
    });
}
if (shareScreenBtn) {
    shareScreenBtn.addEventListener('click', () => {
        void startScreenShare();
    });
}
if (enableAudioBtn) {
    enableAudioBtn.addEventListener('click', () => {
        remotePlaybackEnabled = !remotePlaybackEnabled;

        if (!remotePlaybackEnabled) {
            stopRemoteAudioPlayback();
            setRemoteAudioInteractionNeeded(false);
            updatePlaybackButton();
            return;
        }

        updatePlaybackButton();
        replayRemoteAudioTracks();
    });
}

// Call Menu Handler
const callMenuBtn = document.getElementById('callMenuBtn');
const callMenuDropdown = document.getElementById('callMenuDropdown');
const switchCameraMenuBtn = document.getElementById('switchCameraMenuBtn');
const shareScreenMenuBtn = document.getElementById('shareScreenMenuBtn');
const enableAudioMenuBtn = document.getElementById('enableAudioMenuBtn');

if (callMenuBtn && callMenuDropdown) {
    callMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        callMenuDropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!callMenuBtn.contains(e.target) && !callMenuDropdown.contains(e.target)) {
            callMenuDropdown.classList.remove('active');
        }
    });
}

if (switchCameraMenuBtn && switchCameraBtn) {
    switchCameraMenuBtn.addEventListener('click', () => {
        switchCameraBtn.click();
        callMenuDropdown.classList.remove('active');
    });
}

if (shareScreenMenuBtn && shareScreenBtn) {
    shareScreenMenuBtn.addEventListener('click', () => {
        shareScreenBtn.click();
        callMenuDropdown.classList.remove('active');
    });
}

if (enableAudioMenuBtn && enableAudioBtn) {
    enableAudioMenuBtn.addEventListener('click', () => {
        enableAudioBtn.click();
        callMenuDropdown.classList.remove('active');
    });
}

document.addEventListener('pointerdown', tryUnlockRemoteAudio, true);
document.addEventListener('keydown', tryUnlockRemoteAudio, true);
initDraggableLocalPreview();
updatePlaybackButton();
setCallPanelState(localVideo, {
    micOn: true,
    cameraOn: true,
    hasVideo: false,
    screenSharing: false,
    statusText: 'Camera preview will appear here.',
});
setCallPanelState(remoteVideo, {
    micOn: false,
    cameraOn: false,
    hasVideo: false,
    screenSharing: false,
    statusText: 'Waiting for instructor to join...',
});
if (callModal) {
    callModal.addEventListener('click', (event) => {
        if (event.target === callModal) {
            stopCall();
        }
    });
}

bindJoinCallButtons();

const autoJoinCallButton = document.querySelector('.consultation-item[data-status="in_progress"] .join-call-btn[data-mode*="video"]');
if (autoJoinCallButton?.dataset.consultationId && !wasStudentCallRecentlyEnded(autoJoinCallButton.dataset.consultationId)) {
    startVideoCall(autoJoinCallButton.dataset.consultationId);
}

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
const consultationCategorySelect = document.getElementById('consultationCategory');
const consultationTopicSelect = document.getElementById('consultationType');
const consultationPrioritySelect = document.getElementById('consultationPriority');
const consultationPriorityGroup = document.getElementById('consultationPriorityGroup');
const consultationTypeOtherGroup = document.getElementById('consultationTypeOtherGroup');
const consultationTypeOtherInput = document.getElementById('consultationTypeOther');
const requestNotesField = document.getElementById('studentNotes');
const requestNotesLabel = document.getElementById('studentNotesLabel');

function syncStudentNotesRequirement() {
    const isUrgent = String(consultationPrioritySelect?.value || '').trim().toLowerCase() === 'urgent';

    if (requestNotesField) {
        requestNotesField.required = isUrgent;
        requestNotesField.setCustomValidity('');
    }

    if (requestNotesLabel) {
        requestNotesLabel.textContent = isUrgent
            ? 'Discussion Brief (Required for Urgent requests)'
            : 'Discussion Brief (Optional)';
    }
}

function syncConsultationPriorityVisibility(modeValue = '') {
    const normalizedMode = String(modeValue || '').trim().toLowerCase();
    const showPriority = normalizedMode === 'face-to-face' || normalizedMode === 'video call';

    if (consultationPriorityGroup) {
        consultationPriorityGroup.style.display = showPriority ? '' : 'none';
    }

    if (consultationPrioritySelect) {
        consultationPrioritySelect.required = showPriority;
        consultationPrioritySelect.setCustomValidity('');

        if (!showPriority) {
            consultationPrioritySelect.value = '';
        }
    }

    syncStudentNotesRequirement();
}

function resetStudentRequestForm() {
    requestSelectedInstructorId = null;
    preferredAutoStart = null;

    if (requestFormElement) {
        requestFormElement.reset();
    }

    requestInstructorCards.forEach((card) => {
        card.classList.remove('selected');
        const input = card.querySelector('input[name="instructor_id"]');
        if (input) input.checked = false;
    });

    preferredDayButtons.forEach((btn) => {
        btn.classList.remove('active');
        btn.disabled = true;
        btn.title = 'Choose an instructor first';
    });

    if (preferredTimeDisplay) preferredTimeDisplay.textContent = 'Select a day';

    if (requestConsultationDate) {
        requestConsultationDate.value = '';
        requestConsultationDate.disabled = true;
        requestConsultationDate.setCustomValidity('');
    }

    if (requestDateTrigger) {
        requestDateTrigger.disabled = true;
    }

    if (requestDatePicker) {
        requestDatePicker.set('disable', [() => true]);
        requestDatePicker.clear();
    }

    if (requestConsultationTime) {
        requestConsultationTime.value = '';
    }

    requestModeCards.forEach((card) => {
        card.classList.remove('selected');
        const input = card.querySelector('input[type="radio"][name="consultation_mode"]');
        if (input) input.checked = false;
    });

    if (consultationCategorySelect) {
        consultationCategorySelect.selectedIndex = 0;
    }

    if (consultationTopicSelect) {
        consultationTopicSelect.innerHTML = '<option value="" disabled selected>Select a topic</option>';
    }

    if (consultationPrioritySelect) {
        consultationPrioritySelect.value = '';
    }
    syncConsultationPriorityVisibility('');

    if (consultationTypeOtherGroup) {
        consultationTypeOtherGroup.style.display = 'none';
    }

    if (consultationTypeOtherInput) {
        consultationTypeOtherInput.value = '';
        consultationTypeOtherInput.required = false;
    }

    if (requestNotesField) {
        requestNotesField.value = '';
        requestNotesField.required = false;
        requestNotesField.setCustomValidity('');
    }

    syncStudentNotesRequirement();

    if (requestDateHint) {
        requestDateHint.textContent = 'Choose an instructor first. Available dates are Monday to Saturday only.';
    }

    if (reviewLine1) reviewLine1.textContent = 'Instructor: —';
    if (reviewLine2) reviewLine2.textContent = 'Date & Time: —';
    if (reviewLine3) reviewLine3.textContent = 'Type: —';
    if (reviewLine4) reviewLine4.textContent = 'Mode: —';
    if (reviewLine5) reviewLine5.textContent = 'Notes: —';
}

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
            syncConsultationPriorityVisibility(input.value || title);
        });
    });
}

const selectedRequestModeInput = document.querySelector('#requestModeGrid input[name="consultation_mode"]:checked');
syncConsultationPriorityVisibility(selectedRequestModeInput?.value || '');

if (consultationPrioritySelect) {
    consultationPrioritySelect.addEventListener('change', () => {
        consultationPrioritySelect.setCustomValidity('');
        syncStudentNotesRequirement();
    });
}

                    const notesField = requestNotesField;
                    if (notesField) {
                        notesField.addEventListener('input', () => {
                            notesField.setCustomValidity('');
                            const value = notesField.value.trim() || '—';
                            if (reviewLine5) reviewLine5.textContent = `Notes: ${value}`;
                        });
                    }

                    // Handle form submission to validate all required fields
                    const requestForm = requestFormElement;
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

                            const isUrgent = String(consultationPrioritySelect?.value || '').trim().toLowerCase() === 'urgent';
                            const selectedMode = String(document.querySelector('#requestModeGrid input[name="consultation_mode"]:checked')?.value || '').trim().toLowerCase();
                            const isFaceToFace = selectedMode === 'face-to-face';
                            const notesValue = String(requestNotesField?.value || '').trim();
                            const priorityValue = String(consultationPrioritySelect?.value || '').trim();

                            if (isFaceToFace && !priorityValue) {
                                e.preventDefault();
                                if (consultationPrioritySelect) {
                                    consultationPrioritySelect.required = true;
                                    consultationPrioritySelect.setCustomValidity('Urgency level is required for Face-to-Face consultations.');
                                    consultationPrioritySelect.reportValidity();
                                }
                                return;
                            }

                            if (isUrgent && !notesValue) {
                                e.preventDefault();
                                if (requestNotesField) {
                                    requestNotesField.required = true;
                                    requestNotesField.setCustomValidity('Description is required when urgency level is Urgent.');
                                    requestNotesField.reportValidity();
                                }
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

function suppressStudentCallEndToasts(durationMs = 7000) {
    studentCallEndToastSuppressUntil = Date.now() + durationMs;
}

function shouldSuppressStudentCallEndToasts() {
    return Date.now() < Number(studentCallEndToastSuppressUntil || 0);
}

function suppressStudentCompletionToasts(durationMs = 10000) {
    studentCompletionToastSuppressUntil = Date.now() + durationMs;
}

function shouldSuppressStudentCompletionToasts() {
    return Date.now() < Number(studentCompletionToastSuppressUntil || 0);
}

function isStudentCompletionNotification(notification) {
    const title = String(notification?.title ?? '').toLowerCase();
    const message = String(notification?.message ?? '').toLowerCase();
    return title.includes('consultation complete')
        || title.includes('session completed')
        || message.includes('video call has been completed')
        || message.includes('consultation has been completed')
        || message.includes('consultation video call has been completed');
}

function shouldNotifyStudentStatusTransition(previousStatus, nextStatus) {
    const previous = String(previousStatus || '').toLowerCase();
    const next = String(nextStatus || '').toLowerCase();

    if (!next || previous === next) {
        return false;
    }

    // Returning to approved after a declined/no-answer call attempt is not
    // a fresh approval decision, so avoid showing a misleading notification.
    if (previous === 'in_progress' && next === 'approved') {
        return false;
    }

    return true;
}

function showStudentCallOutcomeToast(message, variant = 'warning') {
    const normalizedMessage = String(message || '').trim();
    const now = Date.now();
    if (
        normalizedMessage
        && lastStudentCallOutcomeToast.message === normalizedMessage
        && (now - Number(lastStudentCallOutcomeToast.shownAt || 0)) < 4000
    ) {
        return;
    }

    lastStudentCallOutcomeToast = {
        message: normalizedMessage,
        shownAt: now,
    };

    const toastMsg = document.createElement('div');
    toastMsg.style.cssText = variant === 'success'
        ? 'position:fixed;top:16px;right:16px;background:#ecfdf5;border:1px solid #10b981;color:#065f46;padding:12px 16px;border-radius:10px;z-index:9999;font-weight:700;box-shadow:0 14px 28px rgba(6,95,70,0.15);'
        : 'position:fixed;top:16px;right:16px;background:#fff3cd;border:1px solid #ffc107;color:#856404;padding:12px 16px;border-radius:8px;z-index:9999;font-weight:600;';
    toastMsg.textContent = normalizedMessage;
    document.body.appendChild(toastMsg);
    setTimeout(() => toastMsg.remove(), 5000);
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
        .slice(0, 4);

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
                const mobileDetailsBtn = consultationItem.querySelector('.cc-mobile-details-btn');
                const currentSummary = String(mobileDetailsBtn?.dataset.summary || '');
                const currentTranscript = String(mobileDetailsBtn?.dataset.transcript || '');
                const nextSummary = String(consultation.summary_text || '');
                const nextTranscript = String(consultation.transcript_text || '');
                const detailsChanged = currentSummary !== nextSummary || currentTranscript !== nextTranscript;

                // During the first poll after page load we only seed the state to avoid
                // showing notifications for statuses that already existed prior to the reload.
                if (studentInitialPoll) {
                    lastConsultationStates[consultation.id] = newStatus;
                    return;
                }

                if (currentDomStatus !== newStatus || detailsChanged) {
                    console.log(`Refreshing consultation item ${consultation.id}: status ${currentDomStatus} -> ${newStatus}, detailsChanged=${detailsChanged}`);
                    updateConsultationItemStatus(consultationItem, consultation);
                    // update stored state as well for future reference
                    lastConsultationStates[consultation.id] = newStatus;

                    // Re-run filter to keep UI in sync
                    if (typeof refreshConsultationList === 'function') {
                        refreshConsultationList(false);
                    }

                    if (currentDomStatus !== newStatus) {
                        if (
                            !shouldSuppressStudentCallEndToasts() &&
                            shouldNotifyStudentStatusTransition(currentDomStatus, newStatus)
                        ) {
                            showStatusChangeNotification(consultation.id, consultation.instructor_name, newStatus);
                        }
                    }
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

function escapeConsultationActionHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function hasStudentSummaryPreview(consultation) {
    const summaryValue = String(consultation?.summary_text ?? consultation?.summary ?? '').trim();
    const transcriptValue = String(consultation?.transcript_text ?? consultation?.transcript ?? '').trim();
    return Boolean(summaryValue || transcriptValue);
}

function buildStudentSummaryDetailsButton(consultation) {
    if (!hasStudentSummaryPreview(consultation)) return '';

    const durationLabel = consultation.duration_minutes !== null && typeof consultation.duration_minutes !== 'undefined'
        ? `${consultation.duration_minutes} min`
        : (consultation.duration || '—');
    const statusLabel = formatConsultationStatusLabel(consultation.status || '');
    const updatedLabel = consultation.updated_at_human || consultation.updated_label || '';

    return `
        <button type="button"
                class="cc-btn cc-btn-view cc-summary-details-btn details-open-btn"
                data-id="${escapeConsultationActionHtml(consultation.id || '')}"
                data-show-status-updated="true"
                data-instructor="${escapeConsultationActionHtml(consultation.instructor_name || consultation.instructor || 'Instructor')}"
                data-type="${escapeConsultationActionHtml(consultation.type_label || consultation.type || '--')}"
                data-mode="${escapeConsultationActionHtml(consultation.consultation_mode || consultation.mode || '--')}"
                data-date="${escapeConsultationActionHtml(consultation.consultation_date || consultation.date || '--')}"
                data-time="${escapeConsultationActionHtml(consultation.time_range || consultation.time || '--')}"
                data-duration="${escapeConsultationActionHtml(durationLabel)}"
                data-status="${escapeConsultationActionHtml(statusLabel)}"
                data-updated="${escapeConsultationActionHtml(updatedLabel)}"
                data-summary="${escapeConsultationActionHtml(consultation.summary_text || consultation.summary || '')}"
                data-transcript="${escapeConsultationActionHtml(consultation.transcript_text || consultation.transcript || '')}">
            View Summary
        </button>
    `;
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
            ${buildStudentSummaryDetailsButton(consultation)}
        `;
    } else if (statusLower === 'declined') {
        actionHtml = `
            <span style="font-size:12px;font-weight:600;color:#b91c1c;">
                Declined
            </span>
            ${buildStudentSummaryDetailsButton(consultation)}
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

function populateHistorySelect(select, values = [], placeholder = 'All') {
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

function getHistoryModeClass(mode) {
    const normalizedMode = normalizeFilterValue(mode);
    if (normalizedMode.includes('audio')) return 'mode-audio';
    if (normalizedMode.includes('video')) return 'mode-video';
    if (normalizedMode.includes('face')) return 'mode-face';
    return 'mode-default';
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

function getAllHistoryTopicOptions(extraTopics = []) {
    const predefinedTopics = Object.values(consultationTopicsByCategory).flat();
    return Array.from(new Set([
        ...predefinedTopics,
        ...extraTopics.filter(Boolean),
    ])).sort((a, b) => a.localeCompare(b));
}

function updateHistoryFilterOptions() {
    const rows = historyRowWraps
        .map((wrap) => wrap.querySelector('.history-row-item'))
        .filter(Boolean);

    const rowCategories = Array.from(new Set(
        rows.map((row) => String(row.dataset.category || '').trim()).filter(Boolean)
    )).sort((a, b) => a.localeCompare(b));
    const rowTopics = Array.from(new Set(
        rows.map((row) => String(row.dataset.topic || '').trim()).filter(Boolean)
    )).sort((a, b) => a.localeCompare(b));
    const rowModes = Array.from(new Set(
        rows.map((row) => String(row.dataset.mode || '').trim()).filter(Boolean)
    )).sort((a, b) => a.localeCompare(b));
    const predefinedCategories = Object.keys(consultationTopicsByCategory);
    const categories = Array.from(new Set([
        ...predefinedCategories,
        ...rowCategories,
    ])).sort((a, b) => a.localeCompare(b));

    const selectedCategory = String(historyCategoryFilter?.value || '').trim();
    const currentTopicValue = String(historyTopicFilter?.value || '').trim();

    let topicOptions = [];
    if (selectedCategory && consultationTopicsByCategory[selectedCategory]) {
        topicOptions = Array.from(new Set([
            ...consultationTopicsByCategory[selectedCategory],
            ...rowTopics.filter((topic) => {
                const matchingRows = rows.filter((row) => String(row.dataset.category || '').trim() === selectedCategory);
                return matchingRows.some((row) => String(row.dataset.topic || '').trim() === topic);
            }),
        ])).sort((a, b) => a.localeCompare(b));
    } else {
        topicOptions = getAllHistoryTopicOptions(rowTopics);
    }

    populateHistorySelect(historyCategoryFilter, categories, 'All Categories');
    populateHistorySelect(historyModeFilter, rowModes, 'All Modes');

    const topicValueToPreserve = topicOptions.includes(currentTopicValue) ? currentTopicValue : '';
    historyTopicFilter.innerHTML = '';
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'All Topics';
    historyTopicFilter.appendChild(defaultOption);

    topicOptions.forEach((value) => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        historyTopicFilter.appendChild(option);
    });

    historyTopicFilter.value = topicValueToPreserve;
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
        updateHistoryFilterOptions();
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

if (historyResetFilters) {
    historyResetFilters.addEventListener('click', () => {
        if (historyYearInput) historyYearInput.value = '';
        if (historyCategoryFilter) historyCategoryFilter.value = '';
        updateHistoryFilterOptions();
        if (historyTopicFilter) historyTopicFilter.value = '';
        if (historyModeFilter) historyModeFilter.value = '';
        if (historySearch) historySearch.value = '';
        currentHistoryYearIndex = -1;
        historyYearFilterEnabled = false;
        selectedMonth = null;
        historySemButtons.forEach((btn) => btn.classList.toggle('active', btn.dataset.sem === 'all'));
        renderMonthSelector('all');
    });
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

updateHistoryFilterOptions();
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
        if (shouldSuppressStudentCompletionToasts()) {
            return;
        }
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
                if (
                    shouldSuppressStudentCallEndToasts()
                    || (shouldSuppressStudentCompletionToasts() && isStudentCompletionNotification(latestUnreadNotification))
                ) {
                    _markShownStudentToast(token);
                } else if (!_hasShownStudentToast(token)) {
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

                    if (
                        currentStatus &&
                        currentStatus !== newStatus &&
                        shouldNotifyStudentStatusTransition(currentStatus, newStatus)
                    ) {
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

function createStudentHistoryRowWrap(data) {
    const wrap = document.createElement('div');
    wrap.className = 'history-row-wrap';

    const modeValue = String(data.mode || '');
    const modeLower = modeValue.toLowerCase();
    const modeClass = getHistoryModeClass(modeValue);
    const dateObj = new Date(`${data.date || ''}T00:00:00`);
    const monthLabel = Number.isNaN(dateObj.getTime()) ? '' : dateObj.toLocaleDateString('en-US', { month: 'long' });
    const yearLabel = Number.isNaN(dateObj.getTime()) ? '' : String(dateObj.getFullYear());
    const academicYear = getAcademicYearFromDate(data.date);
    const typeValue = String(data.type || '--');
    const categoryValue = String(data.category || '');
    const topicValue = String(data.topic || '');
    const searchValue = `${typeValue} ${categoryValue} ${topicValue} ${data.instructor || ''} ${modeValue} ${monthLabel} ${yearLabel}`.toLowerCase();

    wrap.innerHTML = `
        <div class="history-row history-row-item"
             data-category="${escapeHistoryHtml(categoryValue)}"
             data-topic="${escapeHistoryHtml(topicValue)}"
             data-date="${escapeHistoryHtml(data.date || '')}"
             data-month="${escapeHistoryHtml(monthLabel)}"
             data-year="${escapeHistoryHtml(yearLabel)}"
             data-academic-year="${escapeHistoryHtml(academicYear)}"
             data-semester="${escapeHistoryHtml(getSemesterFromDate(data.date))}"
             data-type="${escapeHistoryHtml(typeValue)}"
             data-mode="${escapeHistoryHtml(modeValue)}"
             data-searchable="${escapeHistoryHtml(searchValue)}"
        >
            <div class="date-time">
                <span>${escapeHistoryHtml(data.date || '--')}</span>
                <span>${escapeHistoryHtml(data.time || '--')}</span>
            </div>
            <div class="history-instructor-cell">
                <div class="cc-avatar" aria-hidden="true">${escapeHistoryHtml(String(data.instructor || 'Instructor').split(/\s+/).slice(0, 2).map((part) => part.charAt(0)).join('').toUpperCase() || 'I')}</div>
                <div class="history-instructor-meta">
                    <div class="history-instructor-topline">
                        <div class="history-instructor-name">${escapeHistoryHtml(data.instructor || 'Instructor')}</div>
                        <div class="history-mobile-datetime">
                            <span>${escapeHistoryHtml(data.date || '--')}</span>
                            <span>${escapeHistoryHtml(data.time || '--')}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div>${escapeHistoryHtml(typeValue)}</div>
            <div class="history-mode-cell">
                <span class="mode-pill ${modeClass}">
                    ${escapeHistoryHtml(modeValue || '--')}
                </span>
            </div>
            <div>${escapeHistoryHtml(data.duration || '--')}</div>
            <div>
                <span class="record-pill">Summary</span>
            </div>
            <div class="history-action-cell">
                <a href="#"
                   class="view-link details-open-btn"
                   data-id="${escapeHistoryHtml(data.id || '')}"
                   data-instructor="${escapeHistoryHtml(data.instructor || 'Instructor')}"
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

function upsertStudentHistoryRow(data) {
    if (!historyTable) return;

    const existingWrap = historyRowWraps.find((wrap) => {
        const btn = wrap.querySelector('.details-open-btn');
        return btn?.dataset.id === String(data.id);
    });

    if (existingWrap) {
        const replacementWrap = createStudentHistoryRowWrap(data);
        existingWrap.replaceWith(replacementWrap);

        const wrapIndex = historyRowWraps.indexOf(existingWrap);
        if (wrapIndex >= 0) {
            historyRowWraps[wrapIndex] = replacementWrap;
        }
    } else {
        const wrap = createStudentHistoryRowWrap(data);
        historyTable.appendChild(wrap);
        historyRowWraps.push(wrap);
    }

    updateHistoryFilterOptions();
    filterHistoryRows();
}
</script>
