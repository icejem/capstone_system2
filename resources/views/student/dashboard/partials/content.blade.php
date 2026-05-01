<div class="dashboard student-cyber-theme">
    <div class="main">
        <div class="content">
            <div class="content-header">
                <div class="dashboard-header-copy">
                    <h1 class="dashboard-header-title">
                        Welcome, <span class="dashboard-header-name">{{ $userName }}</span>
                        <span class="dashboard-header-wave" aria-hidden="true"><i class="fa-solid fa-exclamation"></i></span>
                    </h1>
                    <p class="dashboard-header-subtitle">
                        Here's what's happening with your consultations today
                        <span class="dashboard-header-date">— {{ now()->format('F j, Y') }}</span>
                    </p>
                </div>

                <span class="dashboard-header-bits" aria-hidden="true">
                    10110101 01101001 10100110
                    01101011 10110010 01010101
                </span>

            </div>

            <section class="dashboard-overview">
                <div class="stats overview-metrics student-stats">
                    <article class="stat-card stat-card-total clickable" id="totalConsultationsCard" title="View consultation history">
                        <div class="stat-icon"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i></div>
                        <div class="stat-copy">
                            <div class="stat-count" id="studentOverviewTotal">{{ $totalConsultationsCount }}</div>
                            <div class="stat-label">Total Consultations</div>
                            <div class="stat-meta stat-meta-positive">{{ $pendingRequestsCount > 0 ? '+' . $pendingRequestsCount . ' pending review' : 'All requests reviewed' }}</div>
                        </div>
                    </article>
                    <article class="stat-card stat-card-pending">
                        <div class="stat-icon"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i></div>
                        <div class="stat-copy">
                            <div class="stat-count" id="studentOverviewPending">{{ $pendingRequestsCount }}</div>
                            <div class="stat-label">Pending Requests</div>
                            <div class="stat-meta">{{ $pendingRequestsCount > 0 ? 'Needs your attention' : 'No pending items' }}</div>
                        </div>
                    </article>
                    <article class="stat-card stat-card-approved">
                        <div class="stat-icon"><i class="fa-solid fa-check" aria-hidden="true"></i></div>
                        <div class="stat-copy">
                            <div class="stat-count" id="studentOverviewApproved">{{ $approvedSessionsCount }}</div>
                            <div class="stat-label">Approved Sessions</div>
                            <div class="stat-meta stat-meta-positive">{{ $approvedSessionsCount > 0 ? 'Ready to proceed' : 'No approved sessions yet' }}</div>
                        </div>
                    </article>
                    <article class="stat-card stat-card-completed">
                        <div class="stat-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                        <div class="stat-copy">
                            <div class="stat-count" id="studentOverviewCompleted">{{ $completedSessionsCount }}</div>
                            <div class="stat-label">Completed Sessions</div>
                            <div class="stat-meta">{{ $completedSessionsCount > 0 ? 'Sessions finished successfully' : 'No completed sessions yet' }}</div>
                        </div>
                    </article>
                </div>

                <div class="overview-panels">
                    <article class="overview-panel">
                        <div class="overview-panel-header">
                            <h2 class="overview-panel-title">Recent Consultations</h2>
                            <button type="button" class="overview-panel-link" id="overviewViewAllBtn">View All <span aria-hidden="true">→</span></button>
                        </div>
                        <div id="studentRecentConsultationsList">
                            @if ($recentConsultations->isEmpty())
                                <div class="overview-empty">No recent consultations yet.</div>
                            @else
                                <div class="recent-list">
                                    @foreach ($recentConsultations as $consultation)
                                        @php
                                            $statusKey = strtolower((string) ($consultation->status ?? 'pending'));
                                            $statusLabel = match ($statusKey) {
                                                'incompleted' => 'Incomplete',
                                                default => ucwords(str_replace('_', ' ', $statusKey)),
                                            };
                                            $consultationTitle = $consultation->type_label ?: 'Consultation Session';
                                        @endphp
                                        <div class="recent-item">
                                            <div class="recent-item-top">
                                                <p class="recent-item-title">{{ $consultationTitle }}</p>
                                                <span class="recent-status-pill status-{{ $statusKey }}">{{ $statusLabel }}</span>
                                            </div>
                                            <div class="recent-item-meta">
                                                <span><i class="fa-solid fa-user" aria-hidden="true"></i> {{ $consultation->instructor?->name ?? 'Instructor' }}</span>
                                                <span><i class="fa-solid fa-clock" aria-hidden="true"></i> {{ $formatRelativeDay($consultation->consultation_date) }}, {{ $formatManilaRangeSpaced($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>

                    <article class="overview-panel" id="studentUpcomingPanel">
                        <div class="overview-panel-header">
                            <h2 class="overview-panel-title">Upcoming Schedule</h2>
                            <button type="button" class="overview-panel-link history-open-btn">View Calendar <span aria-hidden="true">→</span></button>
                        </div>
                        <div id="studentUpcomingContent">
                            @if ($upcomingConsultations->isEmpty())
                                <div class="overview-empty">No upcoming consultations scheduled.</div>
                            @else
                                <div class="schedule-list">
                                    @foreach ($upcomingConsultations as $consultation)
                                        @php
                                            $consultationDate = $parseManilaDate($consultation->consultation_date);
                                            $consultationTitleRaw = (string) ($consultation->type_label ?: 'Consultation Session');
                                            $priorityKey = null;
                                            if (preg_match('/\((urgent|normal|low)\)/i', $consultationTitleRaw, $priorityMatch)) {
                                                $priorityKey = strtolower((string) ($priorityMatch[1] ?? ''));
                                            }
                                            $consultationTitle = trim((string) preg_replace('/\s*\((urgent|normal|low)\)\s*/i', ' ', $consultationTitleRaw));
                                        @endphp
                                        <div class="schedule-item">
                                            <div class="schedule-date-chip">
                                                <span class="schedule-date-day">{{ $consultationDate ? $consultationDate->format('d') : '--' }}</span>
                                                <span class="schedule-date-month">{{ $consultationDate ? strtoupper($consultationDate->format('M')) : '---' }}</span>
                                            </div>
                                            <div>
                                                <div class="schedule-title-row">
                                                    <p class="schedule-title">{{ $consultationTitle }}</p>
                                                </div>
                                                <p class="schedule-time"><i class="fa-solid fa-clock" aria-hidden="true"></i> {{ $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </article>
                </div>
            </section>

            <!-- Success modal (shown after successful consultation request) -->
            <div class="success-modal-overlay" id="successModalOverlay" aria-hidden="{{ $flashSuccess ? 'false' : 'true' }}" style="display: {{ $successModalDisplay }};">
                <div class="success-modal" role="dialog" aria-modal="true" aria-labelledby="successModalTitle">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:68px;height:68px;border-radius:50%;background:#eef2ff;display:grid;place-items:center;">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12.5L11.5 15L15 10.5" stroke="#3746d6" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div style="flex:1;">
                            <h3 id="successModalTitle">Submission Successful!</h3>
                            <p id="successModalMessage">{{ $flashSuccess ?? 'Your consultation request was submitted successfully.' }}</p>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;margin-top:18px;">
                        <button class="done-btn" id="successModalDone">Done</button>
                    </div>
                </div>
            </div>
            <div class="section is-hidden" id="request-consultation">
                <div class="request-card">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:6px;">
                        <div class="request-title">Request Consultation</div>
                        <button type="button" class="feedback-cancel" id="requestCloseBtn">X</button>
                    </div>
                    <div class="request-subtitle">Fill in the details to schedule a consultation with your instructor.</div>

                    <form method="POST" action="{{ route('student.consultation.store') }}">
                        @csrf
                        <div class="request-layout">
                            <div class="request-main-pane">
                                <div class="request-section">
                                    <span class="request-label">1. Select Instructor</span>
                                    <div class="request-grid" id="requestInstructorGrid">
                                        @forelse ($instructors as $instructor)
                                            @php
                                                $requestInstructorOnline = in_array($instructor->id, (array) ($onlineInstructorIds ?? []), true);
                                                $requestInstructorLastActive = $instructorActiveMinutes[$instructor->id]['last_active_minutes'] ?? null;
                                                $requestInstructorSummary = $instructorConsultationSummaries[$instructor->id] ?? [
                                                    'pending_count' => 0,
                                                    'approved_count' => 0,
                                                    'in_progress_count' => 0,
                                                    'upcoming_count' => 0,
                                                    'next_consultation_label' => 'No upcoming consultations',
                                                ];
                                                $requestInstructorStatusClass = $requestInstructorOnline
                                                    ? 'is-online'
                                                    : ($requestInstructorLastActive !== null ? 'is-recent' : 'is-offline');
                                                $requestInstructorStatusLabel = $requestInstructorOnline
                                                    ? 'Online now'
                                                    : ($requestInstructorLastActive !== null
                                                        ? 'Active ' . \App\Services\UserSessionService::formatActiveMinutesAgo($requestInstructorLastActive)
                                                        : 'Offline');
                                            @endphp
                                            <label class="request-card-item request-card-item-instructor">
                                                <input type="radio" name="instructor_id" value="{{ $instructor->id }}" required>
                                                <div class="request-avatar">{{ strtoupper(substr($instructor->name, 0, 1)) }}</div>
                                                <div class="request-card-text">
                                                    <div class="request-card-headline">
                                                        <div class="request-card-name">{{ $instructor->name }}</div>
                                                        <span class="request-card-status {{ $requestInstructorStatusClass }}">{{ $requestInstructorStatusLabel }}</span>
                                                    </div>
                                                    <div class="request-card-meta">
                                                        <span>{{ $requestInstructorSummary['upcoming_count'] }} upcoming</span>
                                                        <span>&bull;</span>
                                                        <span>{{ $requestInstructorSummary['in_progress_count'] }} in progress</span>
                                                    </div>
                                                </div>
                                                <div class="request-card-hover" aria-hidden="true">
                                                    <div class="request-card-hover-title">Instructor Activity</div>
                                                    <div class="request-card-hover-row">
                                                        <span>Upcoming</span>
                                                        <strong>{{ $requestInstructorSummary['upcoming_count'] }}</strong>
                                                    </div>
                                                    <div class="request-card-hover-row">
                                                        <span>In Progress</span>
                                                        <strong>{{ $requestInstructorSummary['in_progress_count'] }}</strong>
                                                    </div>
                                                </div>
                                            </label>
                                        @empty
                                            <div style="color:var(--muted);font-size:13px;">No instructors found.</div>
                                        @endforelse
                                    </div>
                                    <div id="requestInstructorPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;gap:12px;flex-wrap:wrap;">
                                        <div id="requestInstructorPaginationInfo" style="font-size:12px;color:var(--muted);font-weight:600;"></div>
                                        <div id="requestInstructorPaginationControls" style="display:flex;gap:8px;align-items:center;">
                                            <button id="prevRequestInstructorBtn" class="pagination-nav-btn" style="display:none;">
                                                <span style="font-size:16px;">‹</span>
                                            </button>
                                            <div id="requestInstructorPageNumbers" style="display:flex;gap:4px;"></div>
                                            <button id="nextRequestInstructorBtn" class="pagination-nav-btn" style="display:none;">
                                                <span style="font-size:16px;">›</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="request-section">
                                    <span class="request-label">2. Schedule & Mode</span>
                                    <div class="preferred-row">
                                        <div class="preferred-group">
                                            <div class="preferred-label">Preferred Day</div>
                                            <div class="preferred-days" id="preferredDays">
                                                <button type="button" class="preferred-day-btn" data-day="monday" disabled>Mon</button>
                                                <button type="button" class="preferred-day-btn" data-day="tuesday" disabled>Tue</button>
                                                <button type="button" class="preferred-day-btn" data-day="wednesday" disabled>Wed</button>
                                                <button type="button" class="preferred-day-btn" data-day="thursday" disabled>Thu</button>
                                                <button type="button" class="preferred-day-btn" data-day="friday" disabled>Fri</button>
                                                <button type="button" class="preferred-day-btn" data-day="saturday" disabled>Sat</button>
                                            </div>
                                        </div>
                                        <div class="preferred-group">
                                            <div class="preferred-label">Preferred Time Slot</div>
                                            <div class="preferred-time" id="preferredTimeDisplay">Select a day</div>
                                        </div>
                                        <div class="preferred-group">
                                            <div class="preferred-label">Consultation Date</div>
                                            <div class="preferred-date-wrap">
                                                <input type="date" name="consultation_date" id="requestConsultationDate" class="preferred-date-input" required min="{{ date('Y-m-d') }}" disabled>
                                                <button type="button" class="preferred-date-trigger" id="requestDateTrigger" aria-label="Open calendar" disabled>
                                                    <i class="fa-regular fa-calendar"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hint" id="requestDateHint">Choose an instructor first. Available dates are Monday to Saturday only.</div>
                                    <input type="hidden" name="consultation_time" id="requestConsultationTime">

                                    <div class="request-mode-grid" id="requestModeGrid" style="margin-top:12px;">
                                        <label class="request-mode-card">
                                            <input type="radio" name="consultation_mode" value="Video Call" required>
                                            <div class="mode-body">
                                                <div class="mode-icon"><i class="fa-solid fa-video" aria-hidden="true"></i></div>
                                                <div class="mode-title">Video</div>
                                                <div class="mode-desc">Virtual meeting</div>
                                            </div>
                                        </label>
                                        <label class="request-mode-card">
                                            <input type="radio" name="consultation_mode" value="Face-to-Face" required>
                                            <div class="mode-body">
                                                <div class="mode-icon"><i class="fa-solid fa-user-group" aria-hidden="true"></i></div>
                                                <div class="mode-title">In-Person</div>
                                                <div class="mode-desc">Face-to-face</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="request-section">
                                    <span class="request-label">3. Topic & Details</span>
                                    <div class="request-form-grid">
                                        <div class="request-form-group">
                                            <label>Main Category</label>
                                            <select id="consultationCategory" name="consultation_category" required>
                                                <option value="" disabled selected>Select category</option>
                                                <option value="Curricular Activities">CURRICULAR ACTIVITIES</option>
                                                <option value="Behavior-Related">Behavior-Related</option>
                                                <option value="Co-curricular activities">Co-curricular activities</option>
                                            </select>
                                        </div>

                                        <div class="request-form-group">
                                            <label>Topic</label>
                                            <select id="consultationType" name="consultation_type" required>
                                                <option value="" disabled selected>Select a topic</option>
                                            </select>
                                        </div>

                                        <div class="request-form-group" id="consultationPriorityGroup">
                                            <label>Urgency Level</label>
                                            <select id="consultationPriority" name="consultation_priority">
                                                <option value="" selected disabled>Select urgency level</option>
                                                <option value="Normal">Normal</option>
                                                <option value="Urgent">Urgent</option>
                                                <option value="Low">Low</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="request-form-group" id="consultationTypeOtherGroup" style="display:none; margin-top:10px;">
                                        <label>Other Topic</label>
                                        <input type="text" id="consultationTypeOther" name="consultation_type_other" maxlength="255" placeholder="Specify your topic...">
                                    </div>
                                    <div class="request-form-group" id="studentNotesGroup" style="margin-top:10px;">
                                        <label id="studentNotesLabel" for="studentNotes">Discussion Brief (Optional)</label>
                                        <textarea id="studentNotes" name="student_notes" rows="4" placeholder="Briefly describe what you'd like to discuss..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <aside class="request-summary-pane">
                                <div class="request-summary-card">
                                    <div class="request-summary-title">Summary</div>
                                    <div class="request-summary-subtitle">Review your request</div>
                                    <div class="request-summary-lines">
                                        <div class="meta" id="reviewLine1">Instructor: —</div>
                                        <div class="meta" id="reviewLine2">Date & Time: —</div>
                                        <div class="meta" id="reviewLine3">Type: —</div>
                                        <div class="meta" id="reviewLine4">Mode: —</div>
                                        <div class="meta" id="reviewLine5">Notes: —</div>
                                    </div>
                                    <div class="request-actions request-actions-sticky">
                                        <button type="submit" class="btn primary">Confirm & Submit</button>
                                        <button type="button" class="btn secondary" id="requestCancelBtn">Cancel</button>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </form>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Populate consultation topics based on selected category
                    const topicsByCategory = {
                        'Curricular Activities': [
                            'Thesis/Project',
                            'Grades',
                            'Requirements not submitted',
                            'Lack of quizzes/assignments',
                            'Other curricular concern'
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

                    const categoryEl = document.getElementById('consultationCategory');
                    const typeEl = document.getElementById('consultationType');
                    const priorityEl = document.getElementById('consultationPriority');
                    const reviewLine3 = document.getElementById('reviewLine3');
                    const otherGroupEl = document.getElementById('consultationTypeOtherGroup');
                    const otherInputEl = document.getElementById('consultationTypeOther');

                    function populateTopics(category) {
                        typeEl.innerHTML = '<option value="" disabled selected>Select a topic</option>';
                        if (!category || !topicsByCategory[category]) return;
                        topicsByCategory[category].forEach(function (t) {
                            const opt = document.createElement('option');
                            opt.value = t;
                            opt.textContent = t;
                            typeEl.appendChild(opt);
                        });

                        const hasOthers = topicsByCategory[category].some(function (t) {
                            return String(t).trim().toLowerCase() === 'others';
                        });

                        if (!hasOthers) {
                            const othersOpt = document.createElement('option');
                            othersOpt.value = 'Others';
                            othersOpt.textContent = 'Others';
                            typeEl.appendChild(othersOpt);
                        }
                    }

                    function toggleOtherInput() {
                        if (!otherGroupEl || !otherInputEl || !typeEl) return;
                        const isOthers = (typeEl.value || '') === 'Others';
                        otherGroupEl.style.display = isOthers ? 'block' : 'none';
                        otherInputEl.required = isOthers;
                        if (!isOthers) otherInputEl.value = '';
                    }

                    function updateReviewLine3() {
                        const category = categoryEl?.value || '';
                        const rawTopic = typeEl?.value || '';
                        const topic = rawTopic === 'Others'
                            ? (otherInputEl?.value || '').trim()
                            : rawTopic;
                        const priority = priorityEl?.value || '';
                        let display = '';
                        if (category) display += category;
                        if (rawTopic) display += (display ? ' - ' : '') + (topic || 'Others');
                        if (priority) display += ' (' + priority + ')';
                        if (reviewLine3) reviewLine3.textContent = `Type: ${display || '—'}`;
                    }

                    if (categoryEl && typeEl) {
                        categoryEl.addEventListener('change', function (e) {
                            populateTopics(e.target.value);
                            toggleOtherInput();
                            updateReviewLine3();
                        });
                        // initialize if preselected
                        if (categoryEl.value) populateTopics(categoryEl.value);
                    }

                    if (typeEl) {
                        typeEl.addEventListener('change', function () {
                            toggleOtherInput();
                            updateReviewLine3();
                        });
                    }

                    if (priorityEl) {
                        priorityEl.addEventListener('change', function () {
                            updateReviewLine3();
                        });
                    }

                    if (otherInputEl) {
                        otherInputEl.addEventListener('input', function () {
                            updateReviewLine3();
                        });
                    }

                    toggleOtherInput();
                    updateReviewLine3();
                    const overlay = document.getElementById('successModalOverlay');
                    const doneBtn = document.getElementById('successModalDone');
                    const flashMsg = {!! json_encode($flashSuccess) !!};

                    // Show success modal if there's a flash message
                    if (flashMsg && overlay) {
                        const msgEl = document.getElementById('successModalMessage');
                        if (msgEl) msgEl.textContent = String(flashMsg);
                        overlay.style.display = 'flex';
                        overlay.setAttribute('aria-hidden', 'false');
                    }

                    if (doneBtn && overlay) {
                        doneBtn.addEventListener('click', function () {
                            overlay.style.display = 'none';
                            overlay.setAttribute('aria-hidden', 'true');
                        });
                    }
                });
            </script>
          <style>
/* ===== Consultation Card Styles ===== */
.consultation-item {
    margin-bottom: 5px;
}

.consultation-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: box-shadow 0.2s;
    gap: 24px;
}

.consultation-card:hover {
    box-shadow: 0 4px 16px rgba(60,80,140,0.11);
}

/* Status left border accent */
.consultation-card.status-pending   { border-left: 4px solid #f5a623; }
.consultation-card.status-approved  { border-left: 4px solid #4a90e2; }
.consultation-card.status-in_progress { border-left: 4px solid #7ed321; }
.consultation-card.status-completed { border-left: 4px solid #9b9b9b; }
.consultation-card.status-cancelled { border-left: 4px solid #d0021b; }
.consultation-card.status-incompleted { border-left: 4px solid #d97706; }

/* ---- Card Section Columns ---- */
.cc-col {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    padding: 0 20px;
}

.cc-col:not(:last-child) {
    border-right: 1.5px solid #ececf3;
}

.cc-col-instructor {
    min-width: 190px;
    max-width: 220px;
    padding-left: 0;
}

.cc-col-type   { min-width: 170px; max-width: 220px; }
.cc-col-mode   { min-width: 120px; max-width: 160px; }
.cc-col-status { min-width: 100px; max-width: 130px; }
.cc-col-action {
    min-width: 140px;
    max-width: 180px;
    padding-right: 0;
    align-items: flex-start;
}

/* ---- Instructor block ---- */
.cc-instructor-name {
    font-weight: 800;
    font-size: 15px;
    color: #1a1a2e;
    line-height: 1.2;
}

.cc-instructor-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.online-badge {
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

.instructor-active-minutes-badge {
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
}

.cc-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
    margin-top: 3px;
}

.cc-meta span {
    font-size: 12.5px;
    color: #555;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* ---- Section label ---- */
.cc-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #a0a4b8;
    margin-bottom: 2px;
}

.cc-value {
    font-weight: 700;
    font-size: 14px;
    color: #1a1a2e;
    line-height: 1.3;
}

/* ---- Mode pill ---- */
.cc-mode-pill {
    display: inline-block;
    font-weight: 700;
    font-size: 13px;
    color: #3a4a7a;
    background: #fff;
    border: 1.5px solid #c5cde8;
    border-radius: 8px;
    padding: 5px 14px;
    width: fit-content;
}

/* ---- Status badge ---- */
.cc-status-badge {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 800;
    letter-spacing: 0.08em;
    border-radius: 8px;
    padding: 5px 13px;
    text-transform: uppercase;
    border: 2px solid transparent;
}

.cc-status-badge.status-pending {
    color: #c07000;
    background: #fffbef;
    border-color: #f5a623;
}

.cc-status-badge.status-approved {
    color: #1a60bb;
    background: #eef4ff;
    border-color: #4a90e2;
}

.cc-status-badge.status-in_progress {
    color: #2d7a00;
    background: #f0fff0;
    border-color: #7ed321;
}

.cc-status-badge.status-completed {
    color: #555;
    background: #f5f5f5;
    border-color: #bbb;
}

.cc-status-badge.status-incompleted {
    color: #92400e;
    background: #fffbeb;
    border-color: #f59e0b;
}

.cc-status-badge.status-cancelled {
    color: #b00020;
    background: #fff0f0;
    border-color: #d0021b;
}

/* ---- Action area ---- */
.cc-awaiting {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 12.5px;
    font-weight: 700;
    color: #6b7280;
    margin-bottom: 6px;
}

/* Spinner */
.cc-spinner {
    width: 15px;
    height: 15px;
    border: 2.5px solid #d1d5db;
    border-top-color: #4a90e2;
    border-radius: 50%;
    animation: cc-spin 0.8s linear infinite;
    display: inline-block;
    flex-shrink: 0;
}

@keyframes cc-spin {
    to { transform: rotate(360deg); }
}

/* Buttons */
.cc-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-weight: 700;
    font-size: 12.5px;
    border-radius: 9px;
    padding: 7px 16px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.18s;
    text-decoration: none;
    white-space: nowrap;
}

.cc-btn-cancel {
    color: #1a1a2e;
    background: #fff;
    border-color: #1a1a2e;
}

.cc-btn-cancel:hover {
    background: #1a1a2e;
    color: #fff;
}

.cc-btn-join {
    color: #fff;
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    border-color: #4a90e2;
    box-shadow: 0 2px 8px rgba(74,144,226,0.25);
    font-size: 13px;
    padding: 8px 18px;
}

.cc-btn-join:hover {
    background: linear-gradient(135deg, #357abd 0%, #2860a0 100%);
    box-shadow: 0 4px 14px rgba(74,144,226,0.35);
}

.cc-btn-view {
    color: #4a4a6a;
    background: #f4f5fa;
    border-color: #d0d4e8;
}

.cc-btn-view:hover {
    background: #e8eaf8;
    border-color: #4a90e2;
    color: #4a90e2;
}

.cc-completed-check {
    font-size: 12px;
    font-weight: 600;
    color: #888;
    margin-bottom: 6px;
}

.cc-btn-feedback {
    align-self: flex-start;
    justify-content: flex-start;
}

.myc-filter-row {
    margin: 0 0 16px;
    display: flex;
    gap: 14px;
    align-items: flex-end;
    flex-wrap: wrap;
    max-width: 100%;
}

#my-consultations .myc-top-panel {
    margin-bottom: 18px;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
}

#my-consultations {
    background: #ffffff;
    border-radius: 16px;
    padding: 22px;
    box-shadow: var(--shadow);
    margin-bottom: 24px;
}

#my-consultations .history-modal-header {
    padding: 0 0 14px;
    margin-bottom: 14px;
    border-bottom: 0;
    background: transparent;
    align-items: center;
    gap: 14px;
}

#my-consultations .history-title-wrap {
    min-width: 0;
    flex: 1 1 auto;
}

#my-consultations .history-modal-title {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: 0;
    color: #000000;
}

#my-consultations .history-close {
    width: auto;
    min-width: 58px;
    height: 34px;
    padding: 0 14px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    background: #ffffff;
}

#my-consultations .myc-filter-row {
    margin-bottom: 0;
}

.myc-filter-group {
    display: grid;
    gap: 8px;
    min-width: 260px;
    flex: 1 1 300px;
    max-width: 360px;
}

.myc-filter-label {
    font-size: 14px;
    font-weight: 700;
    color: var(--muted);
}

.myc-status-filter {
    position: relative;
    width: 100%;
}

.myc-status-filter-btn {
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

.myc-status-filter-caret {
    color: #111827;
    font-size: 13px;
    line-height: 1;
}

.myc-status-filter-menu {
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

.myc-status-filter-menu.open {
    display: grid;
    gap: 10px;
}

.myc-status-filter-option {
    border: none;
    background: transparent;
    text-align: left;
    padding: 0;
    cursor: pointer;
}

.myc-status-pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}

.myc-status-pill.all { background: #eef2ff; color: #4338ca; }
.myc-status-pill.pending { background: #fef3c7; color: #92400e; }
.myc-status-pill.approved { background: #d1fae5; color: #166534; }
.myc-status-pill.in_progress { background: #ede9fe; color: #5b21b6; }
.myc-status-pill.completed { background: #cfeef6; color: #155e75; }
.myc-status-pill.incompleted { background: #fef3c7; color: #92400e; }
.myc-status-pill.decline { background: #fee2e2; color: #991b1b; }

.myc-search-wrap {
    display: grid;
    gap: 8px;
}

.myc-search-input {
    width: 100%;
    border: 2px solid #d1d5db;
    border-radius: 10px;
    background: #fff;
    color: #111827;
    font-size: 14px;
    font-weight: 600;
    padding: 12px 14px;
}

.myc-search-input:focus {
    outline: none;
    border-color: #5b6bff;
    box-shadow: 0 0 0 3px rgba(91, 107, 255, 0.15);
}

/* ===== Professional Table Layout (My Consultations) ===== */
.myc-table-wrap {
    border: 1px solid #dbe1ea;
    border-radius: 14px;
    background: #ffffff;
    overflow: hidden;
}

.myc-table-head {
    display: grid;
    width: 100%;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.12fr) minmax(0, 1.6fr) minmax(0, 1.05fr) minmax(0, 1fr);
    gap: 0;
    align-items: center;
    background: #eef2f7;
    border-bottom: 1px solid #dbe1ea;
}

.myc-table-head > div {
    padding: 12px 14px;
    font-size: 11px;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: #425066;
    font-weight: 800;
}

.consultation-list {
    display: block;
}

.consultation-item {
    margin: 0;
}

.consultation-card {
    display: grid;
    width: 100%;
    min-width: 0;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.12fr) minmax(0, 1.6fr) minmax(0, 1.05fr) minmax(0, 1fr);
    align-items: center;
    gap: 0;
    padding: 0;
    border: 0;
    border-bottom: 1px solid #edf1f6;
    border-radius: 0;
    background: #fff;
    box-shadow: none;
}

.consultation-card::before {
    display: none;
}

.consultation-card:hover {
    background: #ffffff;
    box-shadow: none;
    border-color: var(--border);
    transform: none;
}

.consultation-card.status-pending,
.consultation-card.status-approved,
.consultation-card.status-in_progress,
.consultation-card.status-completed,
.consultation-card.status-cancelled,
.consultation-card.status-incompleted,
.consultation-card.status-declined {
    border-left: 0;
}

.cc-col {
    border-right: 0 !important;
    padding: 12px 14px;
    min-width: 0;
    overflow-wrap: anywhere;
}

.cc-col-instructor {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
}

.cc-avatar {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    background: linear-gradient(135deg, #7489ff 0%, #5b6bff 100%);
    color: #fff;
    font-size: 13px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.cc-instructor-name {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    overflow-wrap: anywhere;
}

.cc-date {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
}

.cc-time {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
}

.cc-value {
    font-size: 14px;
    font-weight: 600;
    color: #1e293b;
    overflow-wrap: anywhere;
}

.cc-mode-pill {
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid #bfd3f5;
    background: #eaf1ff;
    color: #214a93;
}

.cc-status-badge {
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.08em;
    padding: 6px 12px;
}

.cc-updated {
    font-size: 12px;
    color: #64748b;
    font-style: italic;
    white-space: normal;
}

#my-consultations .cc-col-mode {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
}

.cc-col-action {
    align-items: flex-start;
}

#my-consultations .cc-col-status {
    display: none;
}

#my-consultations .cc-col-updated {
    display: none;
}

.cc-btn {
    border-radius: 8px;
    padding: 7px 12px;
    font-size: 12px;
}

@media (max-width: 1240px) {
    #my-consultations .myc-table-wrap {
        border: none;
        background: transparent;
        box-shadow: none;
        overflow: visible;
    }

    #my-consultations .myc-table-head {
        display: none;
    }

    #my-consultations .consultation-list {
        display: grid;
        gap: 12px;
    }

    #my-consultations .consultation-card {
        display: grid;
        width: 100%;
        min-width: 0;
        grid-template-columns: minmax(0, 1fr) minmax(132px, 156px);
        grid-template-areas:
            "instructor action"
            "date type"
            "mode mode";
        gap: 10px 14px;
        padding: 14px 16px;
        border: 1px solid #dfe7f4;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        background: #ffffff;
        align-items: start;
    }

    #my-consultations .consultation-card:hover {
        background: #ffffff;
        border-color: #dfe7f4;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        transform: none;
    }

    #my-consultations .cc-col {
        padding: 0;
        border-right: none !important;
        min-width: 0;
    }

    #my-consultations .cc-col::before {
        display: block;
        margin-bottom: 3px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    #my-consultations .cc-col-instructor {
        grid-area: instructor;
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: start;
        gap: 10px;
    }

    #my-consultations .cc-col-instructor::before,
    #my-consultations .cc-col-action::before {
        display: none;
        content: none;
    }

    #my-consultations .cc-col-date {
        grid-area: date;
    }

    #my-consultations .cc-col-date::before {
        content: "Date & Time";
    }

    #my-consultations .cc-col-type {
        grid-area: type;
    }

    #my-consultations .cc-col-type::before {
        content: "Session Type";
    }

    #my-consultations .cc-col-mode {
        grid-area: mode;
    }

    #my-consultations .cc-col-mode::before {
        content: "Mode";
    }

    #my-consultations .cc-col-action {
        grid-area: action;
        width: 100%;
        justify-self: end;
        align-self: start;
        display: grid;
        gap: 8px;
        justify-items: stretch;
    }

    #my-consultations .cc-col-action form,
    #my-consultations .cc-col-action .cc-btn,
    #my-consultations .cc-col-action button {
        width: 100%;
        margin: 0;
    }

    #my-consultations .cc-awaiting,
    #my-consultations .cc-completed-check {
        white-space: normal;
        justify-content: center;
        text-align: center;
    }

    #my-consultations .cc-instructor-meta {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    #my-consultations .cc-instructor-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
        min-width: 0;
    }

    #my-consultations .cc-mobile-details {
        display: none !important;
    }

    #my-consultations .cc-instructor-name,
    #my-consultations .cc-date,
    #my-consultations .cc-value {
        font-size: 13px;
        line-height: 1.35;
    }

    #my-consultations .cc-mode-pill,
    #my-consultations .cc-status-badge {
        font-size: 11px;
        padding: 6px 10px;
    }

    #my-consultations .cc-updated {
        font-size: 11px;
        line-height: 1.4;
    }

    #my-consultations .cc-btn {
        min-height: 36px;
        padding: 8px 10px;
        font-size: 11px;
    }
}

@media (max-width: 768px) {
    #my-consultations {
        padding: 14px;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    #my-consultations .myc-top-panel {
        margin-bottom: 14px;
        padding: 0;
    }

    #my-consultations .history-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        padding: 0 0 10px;
        border-radius: 0;
        border-bottom: 0;
        background: transparent;
        color: #111827;
        box-shadow: none;
    }

    #my-consultations .history-title-wrap {
        min-width: 0;
        gap: 4px;
    }

    #my-consultations .history-modal-title {
        font-size: 16px;
        color: #111827;
        line-height: 1.2;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    #my-consultations .history-close {
        flex: 0 0 auto;
        min-width: 56px;
        height: 34px;
        padding: 0 12px;
        border-radius: 11px;
        font-size: 11px;
        border: 1px solid #dbe3f0;
        background: #ffffff;
        color: #1f3a8a;
        box-shadow: none;
        align-self: flex-start;
    }

    .myc-filter-row {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }

    .myc-filter-group {
        min-width: 0;
        max-width: none;
        width: 100%;
        flex: 0 0 auto;
        gap: 6px;
    }

    .myc-filter-label {
        font-size: 12px;
        font-weight: 700;
        margin: 0;
    }

    .myc-search-wrap {
        gap: 6px;
    }

    .myc-status-filter-btn,
    .myc-search-input {
        min-height: 44px;
        padding: 10px 12px;
        font-size: 13px;
        border-radius: 12px;
    }

    .myc-search-input {
        height: 44px;
    }

    .myc-status-filter-menu {
        padding: 8px 10px;
        border-radius: 12px;
    }

    .myc-table-wrap {
        border: none;
        background: transparent;
        box-shadow: none;
        overflow: visible;
    }

    .myc-table-head {
        display: none;
    }

    .consultation-list {
        gap: 12px;
    }

    .consultation-card {
        min-width: 0;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto !important;
        gap: 12px;
        padding: 14px;
        border: 1px solid #dfe7f4;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        background: #ffffff;
        align-items: center;
    }

    #my-consultations .consultation-card > :not(.cc-col-instructor):not(.cc-mobile-details) {
        display: none !important;
    }

    #my-consultations .cc-col-instructor,
    #my-consultations .cc-mobile-details {
        display: flex !important;
    }

    #my-consultations .cc-col {
        width: 100%;
        padding: 0;
        border-right: none !important;
        align-items: flex-start;
        gap: 4px;
    }

    #my-consultations .cc-col::before {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    #my-consultations .cc-col-instructor::before {
        display: none;
        content: none;
    }

    #my-consultations .cc-col-instructor {
        min-width: 0;
        max-width: none;
        display: grid !important;
        grid-template-columns: auto 1fr;
        align-items: start;
        gap: 10px;
    }

    #my-consultations .cc-instructor-meta {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    #my-consultations .cc-instructor-label { display: none; }

    #my-consultations .cc-instructor-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
        min-width: 0;
    }

    #my-consultations .cc-mobile-meta {
        display: none !important;
    }

    #my-consultations .cc-mobile-details {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        align-self: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    #my-consultations .cc-mobile-details-btn {
        padding: 8px 12px;
        font-size: 11px;
    }

    .cc-instructor-name,
    .cc-date,
    .cc-value {
        font-size: 13px;
    }

    .cc-mode-pill,
    .cc-status-badge {
        font-size: 11px;
    }

    .cc-updated {
        white-space: normal;
        font-size: 11px;
    }

    @media (max-width: 380px) {
        #my-consultations .history-modal-header {
            display: flex;
            align-items: flex-start;
        }

        #my-consultations .history-close {
            margin-left: auto;
        }
    }

    }
}
</style>

<div id="my-consultations" class="is-hidden">
<div class="myc-top-panel">
<div class="history-modal-header">
    <div class="history-title-wrap">
        <h2 class="history-modal-title">My Consultations</h2>
    </div>
    <button type="button" class="history-close" id="exitMyConsultationsBtn" aria-label="Close my consultations">Exit</button>
</div>

<div class="myc-filter-row">
    <div class="myc-filter-group">
        <label class="myc-filter-label" for="myConsultationStatusFilterBtn">Select Status:</label>
        <div class="myc-status-filter" id="myConsultationStatusFilterDropdown">
            <button type="button" id="myConsultationStatusFilterBtn" class="myc-status-filter-btn" aria-expanded="false" aria-controls="myConsultationStatusFilterMenu">
                <span id="myConsultationStatusFilterLabel">Choose a status...</span>
                <span class="myc-status-filter-caret">&#9660;</span>
            </button>
            <div id="myConsultationStatusFilterMenu" class="myc-status-filter-menu" aria-hidden="true">
                <button type="button" class="myc-status-filter-option" data-status="all" data-label="All">
                    <span class="myc-status-pill all">All</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="pending" data-label="Pending">
                    <span class="myc-status-pill pending">Pending</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="approved" data-label="Approved">
                    <span class="myc-status-pill approved">Approved</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="in_progress" data-label="In Progress">
                    <span class="myc-status-pill in_progress">In Progress</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="completed" data-label="Completed">
                    <span class="myc-status-pill completed">Completed</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="incompleted" data-label="Incomplete">
                    <span class="myc-status-pill incompleted">Incomplete</span>
                </button>
                <button type="button" class="myc-status-filter-option" data-status="decline" data-label="Decline">
                    <span class="myc-status-pill decline">Decline</span>
                </button>
            </div>
        </div>
    </div>
    <div class="myc-filter-group myc-search-wrap">
        <label class="myc-filter-label" for="myConsultationSearch">Search:</label>
        <input
            type="search"
            id="myConsultationSearch"
            class="myc-search-input"
            placeholder="Search instructor, date, type, mode, status..."
            autocomplete="off"
        >
    </div>
</div>
</div>

<div class="myc-table-wrap">
<div class="myc-table-head" role="row">
    <div>Instructor</div>
    <div>Date &amp; Time</div>
    <div>Session Type</div>
    <div>Mode</div>
    <div>Action</div>
</div>

<div class="consultation-list" id="consultationList">
        @foreach ($consultations as $consultation)
            @php
                $instructorOnline = in_array($consultation->instructor?->id ?? 0, (array) $onlineInstructorIds);
                $instructorId = $consultation->instructor?->id;
                $lastActiveMinutes = $instructorId && isset($instructorActiveMinutes[$instructorId])
                ? $instructorActiveMinutes[$instructorId]['last_active_minutes']
                : null;
            $statusSlug = strtolower($consultation->status);
                $statusLabel = ucwords(str_replace('_', ' ', $statusSlug));
                $instructorName = $consultation->instructor?->name ?? 'Instructor';
                $initialsParts = array_values(array_filter(explode(' ', trim((string) $instructorName))));
                $initials = strtoupper(substr($initialsParts[0] ?? 'I', 0, 1) . substr($initialsParts[1] ?? '', 0, 1));
                $updatedLabel = $consultation->updated_at?->diffForHumans() ?? '--';
                $durationLabel = $consultation->formatted_duration;
                $consultationDateObj = \Illuminate\Support\Carbon::parse($consultation->consultation_date);
                $formattedDateLong = $consultationDateObj->format('F j, Y');
                $formattedDateNoComma = $consultationDateObj->format('F j Y');
                $formattedDateShort = $consultationDateObj->format('M j, Y');
                $formattedDateIso = $consultationDateObj->format('Y-m-d');
                $formattedDateSlash = $consultationDateObj->format('m/d/Y');
                $priorityValue = trim((string) ($consultation->consultation_priority ?? ''));
                $priorityFromType = '';
                if (preg_match('/\((urgent|normal|low)\)/i', (string) ($consultation->type_label ?? ''), $priorityMatch)) {
                    $priorityFromType = $priorityMatch[1];
                }
                $searchPriority = $priorityValue !== '' ? $priorityValue : $priorityFromType;
                $myConsultationSearchable = strtolower(implode(' ', array_filter([
                    $instructorName,
                    (string) ($consultation->type_label ?? ''),
                    (string) ($consultation->consultation_category ?? ''),
                    (string) ($consultation->consultation_type ?? ''),
                    (string) ($consultation->consultation_mode ?? ''),
                    (string) ($consultation->status ?? ''),
                    $formattedDateLong,
                    $formattedDateNoComma,
                    $formattedDateShort,
                    $formattedDateIso,
                    $formattedDateSlash,
                    $searchPriority,
                ])));
            @endphp

        <div class="consultation-item" data-consultation-index="{{ $loop->index }}" data-status="{{ $statusSlug }}" data-search="{{ $myConsultationSearchable }}">
            <div class="consultation-card status-{{ $statusSlug }}" data-consultation-id="{{ $consultation->id }}">

                {{-- -- INSTRUCTOR -- --}}
                <div class="cc-col cc-col-instructor">
                    <div class="cc-avatar" aria-hidden="true">{{ $initials ?: 'I' }}</div>
                    <div class="cc-instructor-meta">
                        <span class="cc-instructor-label">Instructor</span>
                        <div class="cc-instructor-row">
                            <span class="cc-instructor-name">
                                {{ $instructorName }}
                            </span>
                            @if ($instructorOnline)
                                <span class="online-badge" aria-hidden="true">● Online</span>
                            @elseif ($lastActiveMinutes !== null)
                                <span class="instructor-active-minutes-badge">
                                    Active {{ \App\Services\UserSessionService::formatActiveMinutesAgo($lastActiveMinutes) }}
                                </span>
                            @endif
                        </div>
                        <div class="cc-mobile-meta">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i>
                            <span>{{ $updatedLabel }}</span>
                        </div>
                    </div>
                </div>

                <div class="cc-mobile-details">
                    <button type="button"
                            class="cc-mobile-details-btn"
                            data-id="{{ $consultation->id }}"
                            data-show-status-updated="true"
                            data-instructor="{{ $instructorName }}"
                            data-type="{{ $consultation->type_label }}"
                            data-mode="{{ $consultation->consultation_mode }}"
                            data-date="{{ $consultation->consultation_date }}"
                            data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                            data-duration="{{ $durationLabel }}"
                            data-status="{{ $statusLabel }}"
                            data-updated="{{ $updatedLabel }}"
                            data-summary="{{ e($consultation->summary_text) }}"
                            data-transcript="{{ e($consultation->transcript_text) }}"
                            data-action-source="consultationAction{{ $consultation->id }}">
                        View Details
                    </button>
                </div>

                <div class="cc-col cc-col-date">
                    <div class="cc-date">{{ $consultation->consultation_date }}</div>
                    <div class="cc-time">{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}</div>
                </div>

                {{-- -- TYPE -- --}}
                <div class="cc-col cc-col-type">
                    <div class="cc-value">{{ $consultation->type_label }}</div>
                </div>

                <div class="cc-col cc-col-mode">
                    <div class="cc-mode-pill">{{ $consultation->consultation_mode }}</div>
                    <div class="cc-updated">{{ $updatedLabel }}</div>
                </div>

                {{-- -- STATUS -- --}}
                <div class="cc-col cc-col-status">
                    <span class="cc-status-badge status-{{ $statusSlug }}">
                        {{ strtoupper($consultation->status) }}
                    </span>
                </div>

                {{-- -- ACTION -- --}}
                <div class="cc-col cc-col-action {{ $consultation->status === 'completed' ? 'cc-col-action-completed' : '' }}" id="consultationAction{{ $consultation->id }}">
                    @if ($consultation->status === 'pending')
                        <div class="cc-awaiting">
                            <span class="cc-spinner" aria-hidden="true"></span>
                            <span>Awaiting</span>
                        </div>
                        <form method="POST"
                              action="{{ route('student.consultation.cancel', $consultation) }}"
                              class="student-cancel-form"
                              data-consultation-id="{{ $consultation->id }}"
                              style="margin:0">
                            @csrf
                            <button type="submit"
                                    class="cc-btn cc-btn-cancel">
                                Cancel
                            </button>
                        </form>

                    @elseif ($consultation->status === 'approved')
                        <div class="cc-awaiting">
                            <span class="cc-spinner" aria-hidden="true"></span>
                            <span>Starting soon</span>
                        </div>

                    @elseif ($consultation->status === 'in_progress')
                        <button class="cc-btn cc-btn-join join-call-btn"
                                data-consultation-id="{{ $consultation->id }}"
                                data-mode="{{ strtolower((string) $consultation->consultation_mode) }}">
                            🎯 Join Now
                        </button>

                    @elseif ($consultation->status === 'completed')
                        <div class="cc-completed-check">✓ Completed</div>
                        <button type="button"
                                class="cc-btn cc-btn-feedback feedback-open-btn"
                                data-id="{{ $consultation->id }}"
                                data-instructor="{{ $consultation->instructor?->name ?? 'Instructor' }}"
                                data-type="{{ $consultation->type_label }}"
                                data-mode="{{ $consultation->consultation_mode }}"
                                data-date="{{ $consultation->consultation_date }}"
                                data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                data-duration="{{ $durationLabel }}"
                                data-summary="{{ e($consultation->summary_text) }}"
                                data-transcript="{{ e($consultation->transcript_text) }}">
                            💬 Feedback
                        </button>

                    @elseif ($consultation->status === 'incompleted')
                        <span style="font-size:12px;font-weight:700;color:#92400e;">
                            Incomplete
                        </span>
                        @if (filled($consultation->summary_text) || filled($consultation->transcript_text))
                            <button type="button"
                                    class="cc-btn cc-btn-view cc-summary-details-btn details-open-btn"
                                    data-id="{{ $consultation->id }}"
                                    data-show-status-updated="true"
                                    data-instructor="{{ $consultation->instructor?->name ?? 'Instructor' }}"
                                    data-type="{{ $consultation->type_label }}"
                                    data-mode="{{ $consultation->consultation_mode }}"
                                    data-date="{{ $consultation->consultation_date }}"
                                    data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                    data-duration="{{ $durationLabel }}"
                                    data-status="{{ $statusLabel }}"
                                    data-updated="{{ $updatedLabel }}"
                                    data-summary="{{ e($consultation->summary_text) }}"
                                    data-transcript="{{ e($consultation->transcript_text) }}">
                                View Summary
                            </button>
                        @endif

                    @elseif ($consultation->status === 'declined')
                        <span style="font-size:12px;font-weight:600;color:#b91c1c;">
                            Declined
                        </span>
                        @if (filled($consultation->summary_text) || filled($consultation->transcript_text))
                            <button type="button"
                                    class="cc-btn cc-btn-view cc-summary-details-btn details-open-btn"
                                    data-id="{{ $consultation->id }}"
                                    data-show-status-updated="true"
                                    data-instructor="{{ $consultation->instructor?->name ?? 'Instructor' }}"
                                    data-type="{{ $consultation->type_label }}"
                                    data-mode="{{ $consultation->consultation_mode }}"
                                    data-date="{{ $consultation->consultation_date }}"
                                    data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                    data-duration="{{ $durationLabel }}"
                                    data-status="{{ $statusLabel }}"
                                    data-updated="{{ $updatedLabel }}"
                                    data-summary="{{ e($consultation->summary_text) }}"
                                    data-transcript="{{ e($consultation->transcript_text) }}">
                                View Summary
                            </button>
                        @endif

                    @else
                        <span style="font-size:12px;font-weight:600;color:#888;">
                            {{ ucfirst($consultation->status) }}
                        </span>
                    @endif
                </div>

            </div>
        </div>
    @endforeach
</div>
</div>

<!-- Pagination Controls -->
                <div id="consultationPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;">
                    <div id="consultationPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to {{ min(10, $consultations->count()) }} of {{ $consultations->count() }} consultations
                    </div>
                    <div id="consultationPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevConsultationBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="consultationPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextConsultationBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">›</span>
                        </button>
                    </div>
                </div>

                <style>
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
                </style>
            </div>
    </div>
</div>
</div>

<div id="history" class="section is-hidden" aria-hidden="true">
        <div class="history-modal-header">
            <div class="history-title-wrap">
                <h2 class="history-modal-title">Consultation History</h2>
                <p class="history-modal-subtitle">Manage and track all completed consultations</p>
            </div>
            <button type="button" class="history-close" id="closeHistoryModal" aria-label="Close history">&times;</button>
        </div>
        <div class="history-modal-body">
            @php
                $completedConsultations = $consultations->where('status', 'completed');
                $historyTypes = $completedConsultations
                    ->pluck('type_label')
                    ->filter()
                    ->unique()
                    ->values();
                $historyModes = $completedConsultations
                    ->pluck('consultation_mode')
                    ->filter()
                    ->unique()
                    ->values();
                $historyAcademicYears = $completedConsultations
                    ->pluck('consultation_date')
                    ->filter()
                    ->map(function ($date) {
                        try {
                            $parsed = \Illuminate\Support\Carbon::parse($date);
                        } catch (\Exception $e) {
                            return null;
                        }
                        $month = (int) $parsed->format('n');
                        $year = (int) $parsed->format('Y');
                        if ($month >= 8) {
                            return $year . '-' . ($year + 1);
                        }
                        if ($month <= 5) {
                            return ($year - 1) . '-' . $year;
                        }
                        return null;
                    })
                    ->filter()
                    ->unique()
                    ->values();
            @endphp

            <div class="history-header">
                <div class="history-filter-layout">
                    <div class="history-toolbar-top">
                        <div class="availability-filter-group history-inline-filter history-toolbar-item history-toolbar-item-search">
                            <input type="search" id="historySearch" placeholder="Search consultations..." aria-label="Search consultation history">
                        </div>
                    </div>
                    <div class="history-toolbar-scroll">
                        <div class="history-toolbar-row">
                            <div class="semester-toggle history-toolbar-semester">
                                <button type="button" id="semAll" class="semester-btn" data-sem="all">All</button>
                                <button type="button" id="sem1" class="semester-btn" data-sem="1">1st Sem</button>
                                <button type="button" id="sem2" class="semester-btn" data-sem="2">2nd Sem</button>
                            </div>
                            <div class="history-month-group history-toolbar-item" id="monthPickerContainer" style="display:none;">
                                <select id="historyMonthSelect" aria-label="Filter by month">
                                    <option value="">All months</option>
                                </select>
                            </div>
                            <div class="history-year-group history-toolbar-item history-toolbar-item-year">
                                <input type="text" id="historyYearInput" placeholder="Academic Year..." aria-label="Filter by academic year">
                            </div>
                            <div class="availability-filter-group history-inline-filter history-toolbar-item">
                                <select id="historyCategoryFilter" aria-label="Filter by category">
                                    <option value="">All Categories</option>
                                </select>
                            </div>
                            <div class="availability-filter-group history-inline-filter history-toolbar-item">
                                <select id="historyTopicFilter" aria-label="Filter by topic">
                                    <option value="">All Topics</option>
                                </select>
                            </div>
                            <div class="availability-filter-group history-inline-filter history-toolbar-item">
                                <select id="historyModeFilter" aria-label="Filter by mode">
                                    <option value="">All Modes</option>
                                </select>
                            </div>
                            <div class="history-toolbar-actions">
                                <button class="export-btn reset-filter-btn" type="button" id="historyResetFilters">
                                    <i class="fa-solid fa-rotate-left" aria-hidden="true"></i> Reset Filters
                                </button>
                                <button class="export-btn" type="button" id="historyExport">
                                    <i class="fa-solid fa-download" aria-hidden="true"></i> Export CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="filters" aria-hidden="true" style="display:none;">
                <div class="filters-grid">
                    <!-- moved semester and academic year into header for compact layout -->
                </div>
            </div>

            <div class="history-table">
                <div class="history-row header">
                    <div>Date & Time</div>
                    <div>Instructor</div>
                    <div>Type</div>
                    <div>Mode</div>
                    <div>Duration</div>
                    <div>Records</div>
                    <div>Actions</div>
                </div>

                @forelse ($consultations->where('status', 'completed') as $consultation)
                    @php
                        $modeValue = strtolower((string) $consultation->consultation_mode);
                        $modeClass = str_contains($modeValue, 'audio')
                            ? 'mode-audio'
                            : (str_contains($modeValue, 'video')
                                ? 'mode-video'
                                : (str_contains($modeValue, 'face')
                                    ? 'mode-face'
                                    : 'mode-default'));
                        $isFaceToFace = str_contains($modeValue, 'face');
                        $duration = $consultation->duration_minutes ?? null;
                        $instructorName = $consultation->instructor?->name ?? 'Instructor';
                        $initialsParts = array_values(array_filter(explode(' ', trim((string) $instructorName))));
                        $initials = strtoupper(substr($initialsParts[0] ?? 'I', 0, 1) . substr($initialsParts[1] ?? '', 0, 1));
                        $dateObj = \Illuminate\Support\Carbon::parse($consultation->consultation_date);
                        $month = (int) $dateObj->format('n');
                        $year = (int) $dateObj->format('Y');
                        $academicYear = $month >= 8 ? $year . '-' . ($year + 1) : ($year - 1) . '-' . $year;
                        $semester = $month >= 8 || $month <= 5 ? ($month >= 8 ? 'first' : 'second') : '';
                        $formattedDateLong = $dateObj->format('F j, Y');
                        $formattedDateNoComma = $dateObj->format('F j Y');
                        $formattedDateShort = $dateObj->format('M j, Y');
                        $formattedDateIso = $dateObj->format('Y-m-d');
                        $formattedDateSlash = $dateObj->format('m/d/Y');
                        $priorityValue = trim((string) ($consultation->consultation_priority ?? ''));
                        $priorityFromType = '';
                        if (preg_match('/\((urgent|normal|low)\)/i', (string) ($consultation->type_label ?? ''), $priorityMatch)) {
                            $priorityFromType = $priorityMatch[1];
                        }
                        $searchPriority = $priorityValue !== '' ? $priorityValue : $priorityFromType;
                    @endphp
                    <div class="history-row-wrap">
                        <div class="history-row history-row-item"
                             data-category="{{ (string) ($consultation->consultation_category ?? '') }}"
                             data-topic="{{ (string) ($consultation->consultation_type ?? '') }}"
                             data-date="{{ $consultation->consultation_date }}"
                             data-month="{{ $dateObj->format('F') }}"
                             data-year="{{ $year }}"
                             data-academic-year="{{ $academicYear }}"
                             data-semester="{{ $semester }}"
                             data-type="{{ (string) $consultation->type_label }}"
                             data-mode="{{ (string) $consultation->consultation_mode }}"
                             data-priority="{{ (string) $searchPriority }}"
                             data-instructor="{{ (string) ($consultation->instructor?->name ?? '') }}"
                             data-time="{{ (string) substr($consultation->consultation_time, 0, 5) }}"
                             data-searchable="{{ strtolower($consultation->type_label . ' ' . ($consultation->consultation_category ?? '') . ' ' . ($consultation->consultation_type ?? '') . ' ' . ($consultation->instructor?->name ?? '') . ' ' . $consultation->consultation_mode . ' ' . $dateObj->format('F') . ' ' . $year . ' ' . $formattedDateLong . ' ' . $formattedDateNoComma . ' ' . $formattedDateShort . ' ' . $formattedDateIso . ' ' . $formattedDateSlash . ' ' . $searchPriority) }}"
                        >
                        <div class="date-time">
                            <span>{{ $consultation->consultation_date }}</span>
                            <span>{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                        </div>
                        <div class="history-instructor-cell">
                            <div class="cc-avatar" aria-hidden="true">{{ $initials ?: 'I' }}</div>
                            <div class="history-instructor-meta">
                                <div class="history-instructor-topline">
                                    <div class="history-instructor-name">{{ $instructorName }}</div>
                                    <div class="history-mobile-datetime">
                                        <span>{{ $consultation->consultation_date }}</span>
                                        <span>{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>{{ $consultation->type_label }}</div>
                        <div class="history-mode-cell">
                            <span class="mode-pill {{ $modeClass }}">
                                {{ $consultation->consultation_mode }}
                            </span>
                        </div>
                        <div>{{ $duration !== null ? $duration . ' min' : '—' }}</div>
                        <div>
                            @if (! $isFaceToFace)
                                <span class="record-pill secondary">Action Taken</span>
                            @endif
                            <span class="record-pill">Summary</span>
                        </div>
                        <div class="history-action-cell">
                            <a href="#"
                               class="view-link details-open-btn"
                               data-show-status-updated="false"
                               data-type="{{ $consultation->type_label }}"
                               data-mode="{{ $consultation->consultation_mode }}"
                               data-id="{{ $consultation->id }}"
                               data-date="{{ $consultation->consultation_date }}"
                               data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                               data-instructor="{{ $instructorName }}"
                               data-duration="{{ $consultation->formatted_duration }}"
                               data-summary="{{ e($consultation->summary_text) }}"
                               data-transcript="{{ e($consultation->transcript_text) }}"
                            >View Details</a>
                        </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">No consultation history found.</div>
                @endforelse
                <div class="empty-state" id="historyEmptyState" style="display:none;">No matching results.</div>
            </div>

            <!-- History Pagination Controls -->
            <div id="historyPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;">
                <div id="historyPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                    Showing 1 to {{ min(10, $consultations->where('status', 'completed')->count()) }} of {{ $consultations->where('status', 'completed')->count() }} consultations
                </div>
                <div id="historyPaginationControls" style="display:flex;gap:8px;align-items:center;">
                    <button id="prevHistoryBtn" class="pagination-nav-btn" style="display:none;">
                        <span style="font-size:16px;">‹</span>
                    </button>
                    <div id="historyPageNumbers" style="display:flex;gap:4px;">
                        <!-- Page numbers will be generated by JavaScript -->
                    </div>
                    <button id="nextHistoryBtn" class="pagination-nav-btn" style="display:none;">
                        <span style="font-size:16px;">›</span>
                    </button>
                </div>
            </div>
        </div>
</div>

<!-- Decline Confirmation Modal -->
<div id="declineConfirmModal" style="display:none;position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);z-index:1300;background:#fff;border-radius:12px;padding:28px;box-shadow:0 20px 50px rgba(0,0,0,0.3);width:340px;max-width:90%;text-align:center;">
    <div style="font-weight:700;font-size:16px;color:#111827;margin-bottom:12px;">Decline Call?</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:24px;">Are you sure you want to decline this incoming call?</div>
    <div style="display:flex;gap:12px;justify-content:center;">
        <button id="declineConfirmNo" type="button" style="background:#e5e7eb;color:#111827;border:none;border-radius:8px;padding:10px 20px;font-weight:600;cursor:pointer;">No</button>
        <button id="declineConfirmYes" type="button" style="background:#ef4444;color:#fff;border:none;border-radius:8px;padding:10px 20px;font-weight:600;cursor:pointer;">Yes, Decline</button>
    </div>
</div>

<!-- Decline Confirmation Overlay -->
<div id="declineConfirmOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1299;"></div>

<!-- End Call Confirmation Modal -->
<div id="endCallConfirmModal" style="display:none;position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);z-index:1300;background:#fff;border-radius:12px;padding:28px;box-shadow:0 20px 50px rgba(0,0,0,0.3);width:340px;max-width:90%;text-align:center;">
    <div style="font-weight:700;font-size:16px;color:#111827;margin-bottom:12px;">Leave Call?</div>
    <div style="font-size:14px;color:#6b7280;margin-bottom:24px;">Are you sure you want to leave this call?</div>
    <div style="display:flex;gap:12px;justify-content:center;">
        <button id="endCallConfirmNo" type="button" style="background:#e5e7eb;color:#111827;border:none;border-radius:8px;padding:10px 20px;font-weight:600;cursor:pointer;">No</button>
        <button id="endCallConfirmYes" type="button" style="background:#ef4444;color:#fff;border:none;border-radius:8px;padding:10px 20px;font-weight:600;cursor:pointer;">Yes, Leave</button>
    </div>
</div>

<!-- End Call Confirmation Overlay -->
<div id="endCallConfirmOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1299;"></div>

<!-- Incoming call modal -->
<div class="incoming-call-modal" id="incomingCallModal" aria-hidden="true" style="display:none;">
    <div class="incoming-call-card">
        <div class="incoming-call-top">
            <span class="incoming-call-label">Incoming consultation</span>
            <button id="closeIncomingBtn" type="button" title="Close" class="incoming-call-close">×</button>
        </div>
        <div id="incomingAvatar" class="incoming-call-avatar">SM</div>
        <div id="incomingInstructorName" class="incoming-call-name">Instructor Name</div>
        <div id="incomingCallBadge" class="incoming-call-badge">Incoming Video Call</div>
        <p class="incoming-call-copy">Your instructor is inviting you to join a private consultation room.</p>
        <div id="incomingButtonsContainer" class="incoming-call-actions">
            <button id="declineIncomingBtn" type="button" class="incoming-call-action decline" aria-label="Decline call">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
            <button id="acceptIncomingBtn" type="button" class="incoming-call-action accept" aria-label="Accept call">
                <i class="fa-solid fa-phone" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>

<div class="call-modal" id="callModal" aria-hidden="true">
    <div class="call-dialog">
        <div class="call-header">
            <div class="call-title-wrap">
                <div class="call-kicker">
                    <span class="call-kicker-dot"></span>
                    <span>Consultation Room</span>
                </div>
                <div class="call-title" id="callStatusLabel">Video Session</div>
                <div class="call-hint" id="callConnectionHint">Private consultation room with adaptive video and audio.</div>
            </div>
            <div class="call-header-actions">
                <div class="call-live-pill" aria-label="Live call status">
                    <span class="call-live-label">DURATION</span>
                    <span class="call-live-time" id="callTimer">00:00:00</span>
                    <span class="call-live-rec"><span class="call-live-rec-dot"></span>REC</span>
                </div>
                <button type="button" class="call-close" id="closeCallModal" aria-label="Close">&times;</button>
            </div>
        </div>
        <div id="callDebugPanel" style="display:none;padding:8px 12px;background:rgba(15,23,42,0.92);border-top:1px solid rgba(148,163,184,0.25);font:11px/1.35 'Courier New',monospace;color:#bfdbfe;white-space:pre-wrap;"></div>
        <div class="call-session-reminder" id="callSessionReminder" aria-live="polite" hidden>
            <div class="call-session-reminder-text">Reminder: 5 minutes remaining before this video call ends.</div>
            <button type="button" class="call-session-reminder-close" id="closeCallReminderBtn" aria-label="Close reminder">&times;</button>
        </div>
        <div class="call-body">
            <div class="call-stage">
                <div class="call-video call-video-remote" id="remoteVideo" data-participant="Instructor" data-state="waiting">
                    <div class="call-panel-head">
                        <span class="call-participant-chip" data-call-participant-label>Instructor</span>
                    </div>
                    <div class="call-media-surface" data-call-media></div>
                    <div class="call-video-placeholder">
                        <div class="call-avatar" data-call-video-avatar>I</div>
                        <div class="call-video-status" data-call-video-status>Waiting for instructor to join...</div>
                    </div>
                    <div class="call-video-footer">
                        <div class="call-video-identity">
                            <span class="call-video-name">Instructor</span>
                            <span class="call-video-role">Remote participant</span>
                        </div>
                        <span class="call-video-footer-badge">Secure room</span>
                    </div>
                </div>
                <div class="call-video call-video-local" id="localVideo" data-participant="Student" data-state="waiting" data-draggable-local>
                    <div class="call-panel-head">
                        <span class="call-participant-chip" data-call-participant-label>You</span>
                    </div>
                    <div class="call-media-surface" data-call-media></div>
                    <div class="call-video-placeholder">
                        <div class="call-avatar" data-call-video-avatar>S</div>
                        <div class="call-video-status" data-call-video-status>Camera preview will appear here.</div>
                    </div>
                    <div class="call-video-footer">
                        <div class="call-video-identity">
                            <span class="call-video-name">You</span>
                            <span class="call-video-role">Local preview</span>
                        </div>
                        <span class="call-video-footer-badge">Preview</span>
                    </div>
                </div>
            </div>
            <div class="call-actions-shell">
                <div class="call-actions">
                <button type="button" class="call-btn" id="toggleCameraBtn">
                    <span class="call-btn-icon" aria-hidden="true">
                        <i class="fa-solid fa-video"></i>
                    </span>
                    <span class="call-btn-meta">
                        <span class="call-btn-title">Camera</span>
                        <span class="call-btn-text">On</span>
                    </span>
                </button>
                <button type="button" class="call-btn" id="toggleMicBtn">
                    <span class="call-btn-icon" aria-hidden="true">
                        <i class="fa-solid fa-microphone"></i>
                    </span>
                    <span class="call-btn-meta">
                        <span class="call-btn-title">Microphone</span>
                        <span class="call-btn-text">On</span>
                    </span>
                </button>
                <button type="button" class="call-btn" id="switchCameraBtn">
                    <span class="call-btn-icon" aria-hidden="true">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </span>
                    <span class="call-btn-meta">
                        <span class="call-btn-title">Camera</span>
                        <span class="call-btn-text">Switch</span>
                    </span>
                </button>
                <button type="button" class="call-btn" id="shareScreenBtn">
                    <span class="call-btn-icon" aria-hidden="true">
                        <i class="fa-solid fa-display"></i>
                    </span>
                    <span class="call-btn-meta">
                        <span class="call-btn-title">Screen</span>
                        <span class="call-btn-text">Share</span>
                    </span>
                </button>
                <button type="button" class="call-btn" id="enableAudioBtn">
                    <span class="call-btn-icon" aria-hidden="true">
                        <i class="fa-solid fa-volume-high"></i>
                    </span>
                    <span class="call-btn-meta">
                        <span class="call-btn-title">Speaker</span>
                        <span class="call-btn-text">On</span>
                    </span>
                </button>
                <div class="call-menu-wrapper">
                    <button type="button" class="call-btn call-menu-btn" id="callMenuBtn" aria-label="More options">
                        <span class="call-btn-icon" aria-hidden="true">
                            <i class="fa-solid fa-ellipsis"></i>
                        </span>
                    </button>
                    <div class="call-menu-dropdown" id="callMenuDropdown">
                        <button type="button" class="call-menu-item" id="switchCameraMenuBtn">
                            <span class="call-menu-icon"><i class="fa-solid fa-arrows-rotate"></i></span>
                            <span class="call-menu-label">Switch Camera</span>
                        </button>
                        <button type="button" class="call-menu-item" id="shareScreenMenuBtn">
                            <span class="call-menu-icon"><i class="fa-solid fa-display"></i></span>
                            <span class="call-menu-label">Share Screen</span>
                        </button>
                        <button type="button" class="call-menu-item" id="enableAudioMenuBtn">
                            <span class="call-menu-icon"><i class="fa-solid fa-volume-high"></i></span>
                            <span class="call-menu-label">Speaker</span>
                        </button>
                    </div>
                </div>
                <button type="button" class="call-btn end" id="endCallBtn" aria-label="End call">
                    <span class="call-btn-icon" aria-hidden="true">
                        <i class="fa-solid fa-phone-slash"></i>
                    </span>
                    <span class="call-btn-meta">
                        <span class="call-btn-title">Session</span>
                        <span class="call-btn-text">Leave</span>
                    </span>
                </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="details-modal" id="detailsModal" aria-hidden="true">
    <div class="details-dialog">
        <div class="details-header">
            <div>
                <div class="details-title">Consultation Details</div>
                <div class="details-subtitle" id="detailsSubtitle">Completed session</div>
            </div>
            <div class="details-header-actions">
                <a href="#" id="detailsExportBtn" class="details-export-btn" target="_blank" rel="noopener">
                    <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                    <span>Export PDF</span>
                </a>
                <button type="button" class="details-close" id="closeDetailsModal">x</button>
            </div>
        </div>
        <div class="details-body">
            <div class="details-grid">
                <div class="details-card" id="detailsDate">Date & Time: —</div>
                <div class="details-card" id="detailsInstructor">Instructor: —</div>
                <div class="details-card" id="detailsMode">Mode: —</div>
                <div class="details-card" id="detailsType">Type: —</div>
                <div class="details-card" id="detailsDuration">Duration: —</div>
                <div class="details-card" id="detailsActualStart">Actual Start: —</div>
                <div class="details-card" id="detailsActualEnd">Actual End: —</div>
                <div class="details-card" id="detailsStatus">Status: —</div>
                <div class="details-card" id="detailsUpdated">Updated: —</div>
            </div>
            <div class="details-summary" id="detailsActionsWrap" style="display:none;">
                <div class="details-summary-title">Available Actions</div>
                <div class="details-actions-content" id="detailsActionsContent"></div>
            </div>
            <div class="details-summary" id="detailsSummaryWrap">
                <div class="details-summary-title">Consultation Summary</div>
                <div id="detailsSummaryText">Summary not yet available.</div>
            </div>
            <div class="details-summary" id="detailsTranscriptWrap">
                <div class="details-summary-title">Action Taken</div>
                <div id="detailsTranscriptText">Action taken not yet available.</div>
            </div>
        </div>
    </div>
</div>

<div class="overlay" id="overlay"></div>

<div class="modal" id="feedbackModal">
    <div class="modal-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <strong>Submit Feedback</strong>
            <button id="closeFeedbackBtn" style="background:none;border:none;cursor:pointer;font-size:18px">✕</button>
        </div>
        <form id="feedbackForm" method="POST" action="{{ route('student.dashboard.submit') }}">
            @csrf
            <input type="hidden" name="consultation_id" id="feedbackConsultationId" value="">
            <div style="display:block;margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px">Instructor</label>
                <div id="feedbackInstructorName" style="width:100%;padding:11px;border:1px solid var(--border);border-radius:10px;background:#f9fafb;color:#6b7280;font-weight:600;">—</div>
            </div>
            <div style="display:block;margin-bottom:12px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px">Type of Consultation</label>
                <div id="feedbackConsultationType" style="width:100%;padding:11px;border:1px solid var(--border);border-radius:10px;background:#f9fafb;color:#6b7280;font-weight:600;">—</div>
            </div>
            <label style="display:block;margin-bottom:12px">
                Rating (1-5)
                <input type="number" name="rating" min="1" max="5" value="5" style="width:100%;padding:11px;border:1px solid var(--border);border-radius:10px;margin-top:6px">
            </label>
            <label style="display:block;margin-bottom:12px">
                Comments
                <textarea name="comments" rows="4" style="width:100%;padding:11px;border:1px solid var(--border);border-radius:10px;margin-top:6px"></textarea>
            </label>
            <div class="modal-actions">
                <button type="button" class="action-btn secondary" id="cancelFeedbackBtn">Cancel</button>
                <button type="submit" class="action-btn">Submit</button>
            </div>
        </form>
    </div>
</div>

<div class="toast" id="notifToast">
    <button class="toast-close" id="closeToast">x</button>
    <div class="toast-title" id="toastTitle">New Notification</div>
    <div class="toast-body" id="toastBody">You have a new notification.</div>
</div>


