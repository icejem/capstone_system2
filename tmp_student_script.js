
const sidebar = document.getElementById('sidebar');
const menuBtn = document.getElementById('menuBtn');
const overlay = document.getElementById('overlay');
const notificationBtn = document.getElementById('notificationBtn');
const notificationPanel = document.getElementById('notificationPanel');
const markAllReadBtn = document.getElementById('markAllReadBtn');
const notificationList = document.getElementById('notificationList');
const notificationBadge = document.getElementById('notificationBadge');
const feedbackModal = document.getElementById('feedbackModal');
const openFeedbackBtn = document.getElementById('openFeedbackBtn');
const closeFeedbackBtn = document.getElementById('closeFeedbackBtn');
const cancelFeedbackBtn = document.getElementById('cancelFeedbackBtn');
const totalConsultationsCard = document.getElementById('totalConsultationsCard');
const historySection = document.getElementById('history');
const historyLink = document.getElementById('historyLink');
const requestSection = document.getElementById('request-consultation');
const requestConsultationLink = document.getElementById('requestConsultationLink');
const requestCancelBtn = document.getElementById('requestCancelBtn');
const requestCloseBtn = document.getElementById('requestCloseBtn');
const historyModal = document.getElementById('historyModal');
const historyOpenBtns = document.querySelectorAll('.history-open-btn');
const closeHistoryModal = document.getElementById('closeHistoryModal');
const historyDateRange = document.getElementById('historyDateRange');
    const historyType = document.getElementById('historyType');
const historyMode = document.getElementById('historyMode');
const historySearch = document.getElementById('historySearch');
const historyExport = document.getElementById('historyExport');
const historyRows = Array.from(document.querySelectorAll('.history-row-item'));
const latestNotification = 0;
const unreadCount = 0;
const flashSuccess = 0;

if (menuBtn && sidebar) {
    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
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

if (markAllReadBtn) {
    markAllReadBtn.addEventListener('click', () => {
        document.querySelectorAll('.notification-item.unread').forEach((item) => {
            item.classList.remove('unread');
        });
        if (notificationBadge) {
            notificationBadge.textContent = '0';
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

if (totalConsultationsCard && historySection) {
    totalConsultationsCard.addEventListener('click', () => {
        historySection.classList.toggle('is-hidden');
        if (!historySection.classList.contains('is-hidden')) {
            historySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
}


if (requestConsultationLink && requestSection) {
    requestConsultationLink.addEventListener('click', (event) => {
        event.preventDefault();
        requestSection.classList.remove('is-hidden');
        requestSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
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


if (requestCancelBtn && requestSection) {
    requestCancelBtn.addEventListener('click', () => {
        requestSection.classList.add('is-hidden');
    });
}

if (requestCloseBtn && requestSection) {
    requestCloseBtn.addEventListener('click', () => {
        requestSection.classList.add('is-hidden');
    });
}

if (requestSection && window.location.hash === '#request-consultation') {
    requestSection.classList.remove('is-hidden');
    requestSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

if (window.location.hash === '#history') {
    showHistoryModal();
}


function showHistoryModal() {
    if (!historyModal) return;
    historyModal.classList.add('open');
    historyModal.setAttribute('aria-hidden', 'false');
}

function hideHistoryModal() {
    if (!historyModal) return;
    historyModal.classList.remove('open');
    historyModal.setAttribute('aria-hidden', 'true');
}

if (historyOpenBtns.length) {
    historyOpenBtns.forEach((btn) => {
        btn.addEventListener('click', showHistoryModal);
    });
}

if (closeHistoryModal) {
    closeHistoryModal.addEventListener('click', hideHistoryModal);
}

if (historyModal) {
    historyModal.addEventListener('click', (event) => {
        if (event.target === historyModal) {
            hideHistoryModal();
        }
    });
}




if (historyExport) {
    historyExport.addEventListener('click', () => {
        const visibleRows = historyRows.filter((row) => row.style.display !== 'none');
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
    if (detailsSubtitle) detailsSubtitle.textContent = `${data.type} • ${data.mode} Session`;
    if (detailsDate) detailsDate.textContent = `Date & Time: ${data.date} at ${data.time}`;
    if (detailsDuration) detailsDuration.textContent = `Duration: ${data.duration}`;
    if (detailsInstructor) detailsInstructor.textContent = `Instructor: ${data.instructor}`;
    if (detailsMode) detailsMode.textContent = `Mode: ${data.mode}`;

    if (detailsSummaryWrap) {
        detailsSummaryWrap.style.display = 'block';
        if (detailsSummaryText) {
            detailsSummaryText.textContent = data.summary || 'Summary not yet available.';
        }
    }
    if (detailsTranscriptWrap) {
        detailsTranscriptWrap.style.display = 'block';
        if (detailsTranscriptText) {
            detailsTranscriptText.textContent = data.transcript || 'Transcript not yet available.';
        }
    }

    detailsModal.classList.add('open');
    detailsModal.setAttribute('aria-hidden', 'false');
}

async function refreshDetailsData(consultationId) {
    if (!consultationId) return;
    try {
        const response = await fetch(`0/${consultationId}/details`);
        if (!response.ok) return;
        const data = await response.json();
        openDetailsModal({
            type: data.type || '--',
            mode: data.mode || '--',
            date: data.date || '--',
            time: data.time || '--',
            instructor: data.instructor || '--',
            duration: data.duration || '--',
            summary: data.summary || '',
            transcript: data.transcript || '',
        });
    } catch (_) {
        // ignore
    }
}

function closeDetails() {
    if (!detailsModal) return;
    detailsModal.classList.remove('open');
    detailsModal.setAttribute('aria-hidden', 'true');
}

if (detailsOpenBtns.length) {
    detailsOpenBtns.forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            openDetailsModal({
                type: btn.dataset.type || '—',
                mode: btn.dataset.mode || '—',
                date: btn.dataset.date || '—',
                time: btn.dataset.time || '—',
                instructor: btn.dataset.instructor || '—',
                duration: btn.dataset.duration || '—',
                summary: btn.dataset.summary || '',
                transcript: btn.dataset.transcript || '',
            });
            refreshDetailsData(btn.dataset.id);
        });
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


let currentConsultationId = null;
let peerConnection = null;
let localStream = null;
let pollTimer = null;
let lastSignalId = 0;
let callTimerInterval = null;
let callStartAt = null;
let transcriptActive = false;
let transcriptText = '';
let speechRecognizer = null;

function openCallModal() {
    if (!callModal) return;
    callModal.classList.add('open');
    callModal.setAttribute('aria-hidden', 'false');
}

function closeCallModalUI() {
    if (!callModal) return;
    callModal.classList.remove('open');
    callModal.setAttribute('aria-hidden', 'true');
}

function stopCall() {
    if (pollTimer) {
        clearInterval(pollTimer);
        pollTimer = null;
    }
    if (callTimerInterval) {
        clearInterval(callTimerInterval);
        callTimerInterval = null;
    }
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
    }
    if (localStream) {
        localStream.getTracks().forEach((track) => track.stop());
        localStream = null;
    }
    if (localVideo) localVideo.srcObject = null;
    if (remoteVideo) remoteVideo.srcObject = null;
    currentConsultationId = null;
    lastSignalId = 0;
    callStartAt = null;
    if (callTimer) callTimer.textContent = '00:00';
    transcriptActive = false;
    transcriptText = '';
    closeCallModalUI();
}

async function appendTranscriptChunk(role, text) {
    if (!currentConsultationId || !text) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    await fetch(`0/${currentConsultationId}/transcript-append`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({ role, text }),
    });
}

function startCallTimer() {
    callStartAt = Date.now();
    if (callTimer) callTimer.textContent = '00:00';
    if (callTimerInterval) clearInterval(callTimerInterval);
    callTimerInterval = setInterval(() => {
        if (!callStartAt) return;
        const diff = Date.now() - callStartAt;
        const totalSeconds = Math.floor(diff / 1000);
        const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');
        if (callTimer) callTimer.textContent = `${minutes}:${seconds}`;
    }, 1000);
}

async function sendSignal(type, payload) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    await fetch("0", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({
            consultation_id: currentConsultationId,
            type,
            payload,
        }),
    });
}

async function pollSignals() {
    if (!currentConsultationId) return;
    const response = await fetch(`0?consultation_id=${currentConsultationId}&after=${lastSignalId}`);
    if (!response.ok) return;
    const data = await response.json();
    if (!data?.signals?.length) return;
    data.signals.forEach((signal) => {
        lastSignalId = Math.max(lastSignalId, signal.id);
        handleSignal(signal.type, signal.payload);
    });
}

function createPeerConnection() {
    peerConnection = new RTCPeerConnection({
        iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
    });

    peerConnection.onicecandidate = (event) => {
        if (event.candidate) {
            sendSignal('ice', { candidate: event.candidate });
        }
    };

    peerConnection.ontrack = (event) => {
        if (remoteVideo) {
            remoteVideo.srcObject = event.streams[0];
        }
    };

    if (localStream) {
        localStream.getTracks().forEach((track) => {
            peerConnection.addTrack(track, localStream);
        });
    }
}

async function handleSignal(type, payload) {
    if (!peerConnection) {
        createPeerConnection();
    }

    if (type === 'offer') {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(payload));
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        await sendSignal('answer', answer);
    }

    if (type === 'answer') {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(payload));
    }

    if (type === 'ice' && payload?.candidate) {
        try {
            await peerConnection.addIceCandidate(new RTCIceCandidate(payload.candidate));
        } catch (_) {
            // ignore
        }
    }
}

async function startVideoCall(consultationId) {
    if (!consultationId) return;
    if (currentConsultationId && currentConsultationId !== consultationId) {
        stopCall();
    }
    currentConsultationId = consultationId;
    openCallModal();

    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        if (localVideo) localVideo.srcObject = localStream;
        createPeerConnection();
        startCallTimer();
    } catch (error) {
        stopCall();
        return;
    }

    pollTimer = setInterval(pollSignals, 2000);
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
                appendTranscriptChunk('student', chunk);
            }
        }
    };
    recognizer.onerror = () => {
        // ignore
    };
    return recognizer;
}

function startStudentTranscript() {
    if (transcriptActive) return;
    if (!speechRecognizer) {
        speechRecognizer = initSpeechRecognition();
    }
    if (!speechRecognizer) return;
    transcriptActive = true;
    speechRecognizer.start();
}

function stopStudentTranscript() {
    if (!transcriptActive) return;
    transcriptActive = false;
    try {
        speechRecognizer?.stop();
    } catch (_) {
        // ignore
    }
}

async function pollTranscriptStatus() {
    if (!currentConsultationId) return;
    const response = await fetch(`0/${currentConsultationId}/transcript-status`);
    if (!response.ok) return;
    const data = await response.json();
    const shouldBeActive = !!data?.active;
    if (shouldBeActive && !transcriptActive) {
        startStudentTranscript();
    }
    if (!shouldBeActive && transcriptActive) {
        stopStudentTranscript();
    }
}

setInterval(pollTranscriptStatus, 3000);

if (closeCallModal) closeCallModal.addEventListener('click', stopCall);
if (endCallBtn) endCallBtn.addEventListener('click', stopCall);
if (toggleCameraBtn) {
    toggleCameraBtn.addEventListener('click', () => {
        if (!localStream) return;
        const videoTrack = localStream.getVideoTracks()[0];
        if (!videoTrack) return;
        videoTrack.enabled = !videoTrack.enabled;
        toggleCameraBtn.querySelector('.call-btn-text').textContent = videoTrack.enabled ? 'Camera Off' : 'Camera On';
    });
}
if (toggleMicBtn) {
    toggleMicBtn.addEventListener('click', () => {
        if (!localStream) return;
        const audioTrack = localStream.getAudioTracks()[0];
        if (!audioTrack) return;
        audioTrack.enabled = !audioTrack.enabled;
        toggleMicBtn.querySelector('.call-btn-text').textContent = audioTrack.enabled ? 'Mic Off' : 'Mic On';
    });
}
if (callModal) {
    callModal.addEventListener('click', (event) => {
        if (event.target === callModal) {
            stopCall();
        }
    });
}

if (joinCallButtons.length) {
    joinCallButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const mode = btn.dataset.mode || '';
            if (!mode.includes('video')) return;
            startVideoCall(btn.dataset.consultationId);
        });
    });
}

// Request consultation (inline)
const requestInstructorCards = document.querySelectorAll('#requestInstructorGrid .request-card-item');
const requestDateHint = document.getElementById('requestDateHint');
const requestConsultationDate = document.getElementById('requestConsultationDate');
const requestConsultationTime = document.getElementById('requestConsultationTime');
const requestAvailabilities = 0;
const requestBookedSlots = 0;
let requestSelectedInstructorId = null;
let preferredAutoStart = null;
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

            if (requestConsultationTime) {
                requestConsultationTime.value = '';
            }

            updatePreferredDays(requestSelectedInstructorId);
            updateDatePickerForInstructor(requestSelectedInstructorId);
            renderRequestSlotPlaceholder();
        });
    });
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

        renderRequestSlots(slots);
        if (reviewLine2) reviewLine2.textContent = `Date & Time: ${selectedDateKey} --`;
    });
}

function renderRequestSlots(slots) {
    if (!slots.length) {
        if (requestConsultationTime) requestConsultationTime.value = '';
        return;
    }

    const slot = slots[0];
    if (requestConsultationTime) requestConsultationTime.value = requestNormalizeTime(slot.start_time);
    if (reviewLine2) {
        const endValue = slot.end_time || '';
        const timeLabel = endValue
            ? `${requestFormatTime12(slot.start_time)} to ${requestFormatTime12(endValue)}`
            : requestFormatTime12(slot.start_time);
        reviewLine2.textContent = `Date & Time: ${requestConsultationDate.value} ${timeLabel}`;
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
        btn.disabled = !daysAvailable.has(dayName);
        btn.classList.remove('active');
    });
    if (preferredTimeDisplay) preferredTimeDisplay.textContent = 'Select a day';

    if (requestConsultationDate) {
        requestConsultationDate.disabled = daysAvailable.size === 0;
    }

    if (requestDateHint) {
        const label = formatAvailableDays(daysAvailable);
        requestDateHint.textContent = label
            ? `Choose a date that matches available days: ${label}.`
            : 'No available days for this instructor.';
    }

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
        });
    });
}

const typeSelect = document.querySelector('select[name="consultation_type"]');
if (typeSelect) {
    typeSelect.addEventListener('change', () => {
        const value = typeSelect.value || '—';
        if (reviewLine3) reviewLine3.textContent = `Type: ${value}`;
    });
}

const notesField = document.querySelector('textarea[name="student_notes"]');
if (notesField) {
    notesField.addEventListener('input', () => {
        const value = notesField.value.trim() || '—';
        if (reviewLine5) reviewLine5.textContent = `Notes: ${value}`;
    });
}

if (flashSuccess && notifToast) {
    toastTitle.textContent = 'Success';
    toastBody.textContent = flashSuccess;
    notifToast.classList.add('show');
    setTimeout(() => notifToast.classList.remove('show'), 6000);
} else if (unreadCount > 0 && latestNotification && notifToast) {
    toastTitle.textContent = latestNotification.title ?? 'New Notification';
    toastBody.textContent = latestNotification.message ?? 'You have a new notification.';
    notifToast.classList.add('show');
    setTimeout(() => notifToast.classList.remove('show'), 6000);
}

if (closeToast) {
    closeToast.addEventListener('click', () => {
        notifToast.classList.remove('show');
    });
}

