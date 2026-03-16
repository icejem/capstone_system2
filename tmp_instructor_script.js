
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menuBtn');
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationPanel = document.getElementById('notificationPanel');
    const notifToast = document.getElementById('notifToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastBody = document.getElementById('toastBody');
    const closeToast = document.getElementById('closeToast');
    const openAvailabilityModalTop = document.getElementById('openAvailabilityModalTop');
    const openAvailabilityModal = document.getElementById('openAvailabilityModal');
    const availabilityModal = document.getElementById('availabilityModal');
    const closeAvailabilityModal = document.getElementById('closeAvailabilityModal');
    const cancelAvailabilityModal = document.getElementById('cancelAvailabilityModal');
    const dayChecks = document.querySelectorAll('.day-check');
    const semesterButtons = document.querySelectorAll('.semester-btn');
    const availabilitySemester = document.getElementById('availabilitySemester');
    const historyModal = document.getElementById('historyModal');
    const historyOpenBtns = document.querySelectorAll('.history-open-btn');
    const closeHistoryModal = document.getElementById('closeHistoryModal');
    const historyLink = document.getElementById('historyLink');
    const historyDateRange = document.getElementById('historyDateRange');
    const historyType = document.getElementById('historyType');
            const historyMode = document.getElementById('historyMode');
    const historySearch = document.getElementById('historySearch');
    const historyExport = document.getElementById('historyExport');
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
    const detailsSummaryText = document.getElementById('detailsSummaryText');
    const detailsTranscriptWrap = document.getElementById('detailsTranscriptWrap');
    const detailsTranscriptText = document.getElementById('detailsTranscriptText');

    const requestsSection = document.getElementById('requests');
    const scheduleSection = document.getElementById('schedule');
    const feedbackSection = document.getElementById('feedback');
    const requestsLink = document.getElementById('requestsLink');
    const scheduleLink = document.getElementById('scheduleLink');
    const feedbackLink = document.getElementById('feedbackLink');
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
    const summaryActionBase = 0;

    const latestNotification = 0;
    const unreadCount = 0;

    if (menuBtn) {
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
        if (!notificationPanel) return;
        if (notificationPanel.contains(event.target) || notificationBtn.contains(event.target)) return;
        notificationPanel.classList.remove('active');
    });

    if (unreadCount > 0 && latestNotification && notifToast) {
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

    if (openAvailabilityModalTop) {
        openAvailabilityModalTop.addEventListener('click', showAvailabilityModal);
    }
    if (openAvailabilityModal) {
        openAvailabilityModal.addEventListener('click', showAvailabilityModal);
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
    
    
    if (historyExport) {
        historyExport.addEventListener('click', () => {
            const visibleRows = historyRows.filter((row) => row.style.display !== 'none');
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
        stopTranscript();
        saveTranscript();
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
        transcriptText = '';
        if (callTimer) callTimer.textContent = '00:00';
        closeCallModalUI();
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
        await fetch(`0/${currentConsultationId}/transcript-toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ active: true }),
        });
        transcriptActive = true;
        toggleTranscriptBtn.textContent = 'Stop Transcript';
        speechRecognizer.start();
    }

    async function stopTranscript() {
        if (!transcriptActive) return;
        transcriptActive = false;
        if (toggleTranscriptBtn) toggleTranscriptBtn.textContent = 'Transcript';
        try {
            speechRecognizer?.stop();
        } catch (_) {
            // ignore
        }
        if (currentConsultationId) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            await fetch(`0/${currentConsultationId}/transcript-toggle`, {
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
        await fetch(`0/${currentConsultationId}/transcript`, {
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

    async function startVideoCall(consultationId, role) {
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

        if (role === 'instructor') {
            const offer = await peerConnection.createOffer();
            await peerConnection.setLocalDescription(offer);
            await sendSignal('offer', offer);
        }

        pollTimer = setInterval(pollSignals, 2000);
    }

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
    if (toggleTranscriptBtn) {
        toggleTranscriptBtn.addEventListener('click', () => {
            if (!transcriptActive) {
                startTranscript();
            } else {
                stopTranscript();
            }
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
        startVideoCall(autoCallRow.dataset.consultationId, 'instructor');
    }

    if (historyModal) {
        historyModal.addEventListener('click', (event) => {
            if (event.target === historyModal) {
                hideHistoryModal();
            }
        });
    }

    if (closeRequestsSection && requestsSection) {
        closeRequestsSection.addEventListener('click', () => {
            requestsSection.classList.add('is-hidden');
        });
    }

    if (requestsLink && requestsSection) {
        requestsLink.addEventListener('click', (event) => {
            event.preventDefault();
            requestsSection.classList.remove('is-hidden');
            if (scheduleSection) scheduleSection.classList.add('is-hidden');
            if (feedbackSection) feedbackSection.classList.add('is-hidden');
        });
    }

    if (scheduleLink && scheduleSection) {
        scheduleLink.addEventListener('click', (event) => {
            event.preventDefault();
            scheduleSection.classList.remove('is-hidden');
            scheduleSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            if (feedbackSection) feedbackSection.classList.add('is-hidden');
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }

    if (closeScheduleSection && scheduleSection) {
        closeScheduleSection.addEventListener('click', () => {
            scheduleSection.classList.add('is-hidden');
        });
    }

    if (feedbackLink && feedbackSection) {
        feedbackLink.addEventListener('click', (event) => {
            event.preventDefault();
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
            feedbackSection.classList.add('is-hidden');
        });
    }

    function openSummaryModal(data) {
        if (!summaryModal || !summaryForm) return;
        summaryForm.action = `${summaryActionBase}/${data.id}/summary`;
        if (summaryStudent) summaryStudent.textContent = `Student: ${data.student}`;
        if (summaryStudentId) summaryStudentId.textContent = `Student ID: ${data.studentId || '--'}`;
        if (summaryDate) summaryDate.textContent = `Date & Time: ${data.date} ${data.time}`;
        if (summaryType) summaryType.textContent = `Type: ${data.type}`;
        if (summaryMode) summaryMode.textContent = `Mode: ${data.mode}`;
        if (summaryText) summaryText.value = data.summary || '';
        summaryModal.classList.add('open');
        summaryModal.setAttribute('aria-hidden', 'false');
    }

    function closeSummary() {
        if (!summaryModal) return;
        summaryModal.classList.remove('open');
        summaryModal.setAttribute('aria-hidden', 'true');
    }

    if (summaryOpenBtns.length) {
        summaryOpenBtns.forEach((btn) => {
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
                });
            });
        });
    }

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

    function openDetailsModal(data) {
        if (!detailsModal) return;

        if (detailsSubtitle) detailsSubtitle.textContent = `${data.type} - ${data.mode} Session`;
        if (detailsDate) detailsDate.textContent = `Date & Time: ${data.date} at ${data.time}`;
        if (detailsStudent) detailsStudent.textContent = `Student: ${data.student}`;
        if (detailsStudentId) detailsStudentId.textContent = `Student ID: ${data.studentId || '--'}`;
        if (detailsMode) detailsMode.textContent = `Mode: ${data.mode}`;
        if (detailsType) detailsType.textContent = `Type: ${data.type}`;
        if (detailsSummaryText) {
            detailsSummaryText.textContent = data.summary || 'Summary not yet available.';
        }

        const modeValue = String(data.mode || '').toLowerCase();
        const needsTranscript = modeValue.includes('audio') || modeValue.includes('video');
        if (detailsTranscriptWrap) {
            detailsTranscriptWrap.style.display = needsTranscript ? 'block' : 'none';
        }
        if (detailsTranscriptText) {
            detailsTranscriptText.textContent = data.transcript || 'Transcript not yet available.';
        }

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
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                openDetailsModal({
                    date: btn.dataset.date || '--',
                    time: btn.dataset.time || '--',
                    student: btn.dataset.student || 'Student',
                    studentId: btn.dataset.studentId || '--',
                    mode: btn.dataset.mode || '--',
                    type: btn.dataset.type || '--',
                    duration: btn.dataset.duration || '--',
                    summary: btn.dataset.summary || '',
                    transcript: btn.dataset.transcript || '',
                });
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

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideAvailabilityModal();
            hideHistoryModal();
            closeSummary();
            closeDetails();
        }
    });

    const hasAvailabilityError = 0;
    if (hasAvailabilityError) {
        showAvailabilityModal();
    }

