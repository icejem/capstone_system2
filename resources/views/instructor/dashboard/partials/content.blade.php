
@php
    $userName = auth()->user()->name ?? 'Instructor';
@endphp

<div class="dashboard instructor-cyber-theme">
    <div class="main">
        <div class="content">
            <div class="content-header">
                <div class="dashboard-header-copy">
                    <h1 class="dashboard-header-title">
                        Welcome , <span class="dashboard-header-name">{{ $userName }}</span>
                        <span class="dashboard-header-wave" aria-hidden="true">&#128075;</span>
                    </h1>
                    <p class="dashboard-header-subtitle">
                        Here's what's happening with your consultations today
                        <span class="dashboard-header-date">&mdash; {{ now()->format('F j, Y') }}</span>
                    </p>
                </div>

                <span class="dashboard-header-bits" aria-hidden="true">
                    10110101 01101001 10100110
                    01101011 10110010 01010101
                </span>
            </div>

            {{-- CONSULTATION STATS --}}
            <div class="stats">
                <div class="stat-card stat-card-total">
                    <div class="stat-icon"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i></div>
                    <div class="stat-copy">
                        <div class="stat-count" data-stat="total">{{ $stats['total'] ?? 0 }}</div>
                        <div class="stat-label">Total Consultations</div>
                        <div class="stat-meta stat-meta-positive">{{ ($stats['pending'] ?? 0) > 0 ? '+' . ($stats['pending'] ?? 0) . ' pending review' : 'All requests reviewed' }}</div>
                    </div>
                </div>
                <div class="stat-card stat-card-pending">
                    <div class="stat-icon" style="background: #fef3c7; color: #c2410c;"><i class="fa-solid fa-hourglass-half" aria-hidden="true"></i></div>
                    <div class="stat-copy">
                        <div class="stat-count" data-stat="pending">{{ $stats['pending'] ?? 0 }}</div>
                        <div class="stat-label">Pending Requests</div>
                        <div class="stat-meta">{{ ($stats['pending'] ?? 0) > 0 ? 'Needs your attention' : 'No pending requests' }}</div>
                    </div>
                </div>
                <div class="stat-card stat-card-approved">
                    <div class="stat-icon" style="background: #d1fae5; color: #065f46;"><i class="fa-solid fa-check" aria-hidden="true"></i></div>
                    <div class="stat-copy">
                        <div class="stat-count" data-stat="approved">{{ $stats['approved'] ?? 0 }}</div>
                        <div class="stat-label">Approved Sessions</div>
                        <div class="stat-meta stat-meta-positive">{{ ($stats['approved'] ?? 0) > 0 ? 'Ready to proceed' : 'No approved sessions yet' }}</div>
                    </div>
                </div>
                <div class="stat-card stat-card-completed">
                    <div class="stat-icon" style="background: #cfeef6; color: #155e75;"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                    <div class="stat-copy">
                        <div class="stat-count" data-stat="completed">{{ $stats['completed'] ?? 0 }}</div>
                        <div class="stat-label">Completed Sessions</div>
                        <div class="stat-meta">{{ ($stats['completed'] ?? 0) > 0 ? 'Sessions finished successfully' : 'No completed sessions yet' }}</div>
                    </div>
                </div>
            </div>

            <div class="overview-panels">
                <article class="overview-panel">
                    <div class="overview-panel-header">
                        <h2 class="overview-panel-title">Recent Consultations</h2>
                        <button type="button" class="overview-panel-link" id="overviewViewAllBtn">View All <span aria-hidden="true">→</span></button>
                    </div>
                    <div id="instructorRecentConsultationsList">
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
                                            <span><i class="fa-solid fa-user" aria-hidden="true"></i> {{ $consultation->student?->name ?? 'Student' }}</span>
                                            <span><i class="fa-solid fa-clock" aria-hidden="true"></i> {{ $formatRelativeDay($consultation->consultation_date) }}, {{ $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>

                <article class="overview-panel" id="instructorUpcomingPanel">
                    <div class="overview-panel-header">
                        <h2 class="overview-panel-title">Upcoming Schedule</h2>
                        <button type="button" class="overview-panel-link history-open-btn">View Calendar <span aria-hidden="true">→</span></button>
                    </div>
                    <div id="instructorUpcomingContent">
                        @if ($upcomingConsultations->isEmpty())
                            <div class="overview-empty">No upcoming consultations scheduled.</div>
                        @else
                            <div class="schedule-list">
                                @foreach ($upcomingConsultations as $consultation)
                                    @php
                                        $consultationDate = $parseManilaDate($consultation->consultation_date);
                                        $consultationTitleRaw = (string) ($consultation->type_label ?: 'Consultation Session');
                                        $priorityValue = trim((string) ($consultation->consultation_priority ?? ''));
                                        $priorityFromType = '';
                                        if (preg_match('/\((urgent|normal|low)\)/i', $consultationTitleRaw, $priorityMatch)) {
                                            $priorityFromType = strtolower((string) ($priorityMatch[1] ?? ''));
                                        }
                                        $priorityKey = strtolower($priorityValue !== '' ? $priorityValue : $priorityFromType);
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

            {{-- AVAILABILITY --}}
            <div class="section {{ $errors->has('days') ? '' : 'is-hidden' }}" id="schedule">
                @php
                    $weeklyDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    $availabilityByDay = $availabilities
                        ->groupBy(fn ($slot) => strtolower($slot->available_day ?? ''))
                        ->map(fn ($slots) => $slots->sortBy('start_time')->values());
                @endphp

                @php
                    $semesterLabel = ($selectedSemester ?? 'first') === 'second' ? 'Second Sem' : 'First Sem';
                    $academicYearLabel = $selectedAcademicYear ?: '--';
                @endphp

                <div class="availability-head">
                    <div class="schedule-head-main">
                        <div class="schedule-head-copy">
                            <div class="section-title" style="margin-bottom:0;">Schedule</div>
                            <div class="schedule-head-meta">
                                <div class="schedule-meta-inline">
                                    <span class="schedule-meta-inline-label">Semester:</span>
                                    <span class="schedule-meta-inline-value">{{ $semesterLabel }}</span>
                                </div>
                                <div class="schedule-meta-inline">
                                    <span class="schedule-meta-inline-label">Academic Year:</span>
                                    <span class="schedule-meta-inline-value">{{ $academicYearLabel }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="schedule-head-actions">
                        <button type="button" class="export-btn" id="scheduleExport">Export Schedule</button>
                        <button type="button" class="section-close schedule-head-exit" id="closeScheduleSection">Exit</button>
                    </div>
                </div>

                @if ($errors->has('days'))
                    <div style="margin-bottom:12px;padding:10px 12px;border:1px solid #fecaca;background:#fef2f2;color:#991b1b;border-radius:10px;font-size:13px;">
                        {{ $errors->first('days') }}
                    </div>
                @endif

                <div class="schedule-layout">
                    <div class="schedule-grid">
                        @foreach ($weeklyDays as $day)
                            <div class="schedule-day">{{ ucfirst(substr($day, 0, 3)) }}</div>
                        @endforeach

                        @foreach ($weeklyDays as $day)
                            @php
                                $daySlots = $availabilityByDay->get($day, collect());
                                $slot = $daySlots->first();
                            @endphp
                            <div class="schedule-cell">
                                @if ($slot)
                                    <div class="schedule-slot">
                                        {{ $formatManilaTime12($slot->start_time) }}
                                        <span>to</span>
                                        {{ $formatManilaTime12($slot->end_time) }}
                                    </div>
                                @else
                                <div class="schedule-empty">--</div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
            <div class="section is-hidden" id="feedback">
                <div class="section-head">
                    <div class="section-title" style="margin-bottom:0;">Feedback</div>
                    <button type="button" class="section-close" id="closeFeedbackSection">Exit</button>
                </div>
                <div style="color:var(--muted);font-size:14px;margin:-6px 0 14px;">View feedback from your students</div>

                <div class="feedback-grid">
                    <div class="feedback-stat-card">
                        <div class="feedback-stat-icon" style="background:#fff7ed;color:#c2410c;">★</div>
                        <div class="feedback-stat-value">{{ number_format((float) ($feedbackStats['average_rating'] ?? 0), 1) }}</div>
                        <div class="feedback-stat-label">Average Rating</div>
                    </div>
                    <div class="feedback-stat-card">
                        <div class="feedback-stat-icon" style="background:#ecfeff;color:#0f766e;">💬</div>
                        <div class="feedback-stat-value">{{ $feedbackStats['total_feedback'] ?? 0 }}</div>
                        <div class="feedback-stat-label">Total Feedback</div>
                    </div>
                    <div class="feedback-stat-card">
                        <div class="feedback-stat-icon" style="background:#ecfdf5;color:#047857;">👍</div>
                        <div class="feedback-stat-value">{{ $feedbackStats['positive_rate'] ?? 0 }}%</div>
                        <div class="feedback-stat-label">Positive Rate</div>
                    </div>
                    <div class="feedback-stat-card">
                        <div class="feedback-stat-icon" style="background:#f1f5f9;color:#475569;">📅</div>
                        <div class="feedback-stat-value">{{ $feedbackStats['this_month'] ?? 0 }}</div>
                        <div class="feedback-stat-label">This Month</div>
                    </div>
                </div>

                <div class="feedback-list">
                    <div class="feedback-list-head">Student Feedback</div>
                    @forelse ($feedbacks as $feedback)
                        @php
                            $rating = max(1, min(5, (int) $feedback->rating));
                            $consultationType = $feedback->consultation?->type_label ?? ($feedback->consultation?->consultation_type ?? 'Consultation');
                        @endphp
                        <div class="feedback-item">
                            <div class="feedback-item-top">
                                <div class="feedback-student">{{ $feedback->student?->name ?? 'Student' }}</div>
                                <div class="request-tag">{{ $consultationType }}</div>
                            </div>
                            <div class="feedback-meta">{{ $feedback->created_at?->format('Y-m-d h:i A') ?? '—' }}</div>
                            <div class="feedback-stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</div>
                            <div class="feedback-comment">{{ $feedback->comments ?: 'No comment provided.' }}</div>
                        </div>
                    @empty
                        <div class="feedback-item">
                            <div class="feedback-comment">No feedback yet from students.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- CONSULTATION REQUESTS --}}
            <div class="section is-hidden" id="requests">
                <div class="section-head">
                    <div class="section-title" style="margin-bottom:0;">Consultations</div>
                    <button type="button" class="section-close" id="closeRequestsSection">Exit</button>
                </div>
                <div class="request-filter-row">
                    <div class="request-filter-group">
                        <label class="request-filter-label" for="requestStatusFilterBtn">Select Status:</label>
                        <div class="request-status-filter" id="requestStatusFilterDropdown">
                            <button type="button" id="requestStatusFilterBtn" class="request-status-filter-btn" aria-expanded="false" aria-controls="requestStatusFilterMenu">
                                <span id="requestStatusFilterLabel">Choose a status...</span>
                                <span class="request-status-filter-caret">&#9660;</span>
                            </button>
                            <div id="requestStatusFilterMenu" class="request-status-filter-menu" aria-hidden="true">
                                <button type="button" class="request-status-filter-option" data-status="all" data-label="All">
                                    <span class="request-status-pill all">All</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="pending" data-label="Pending">
                                    <span class="request-status-pill pending">Pending</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="approved" data-label="Approved">
                                    <span class="request-status-pill approved">Approved</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="completed" data-label="Completed">
                                    <span class="request-status-pill completed">Completed</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="incompleted" data-label="Incomplete">
                                    <span class="request-status-pill incompleted">Incomplete</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="decline" data-label="Decline">
                                    <span class="request-status-pill decline">Decline</span>
                                </button>
                                <button type="button" class="request-status-filter-option" data-status="in_progress" data-label="In Progress">
                                    <span class="request-status-pill in_progress">In Progress</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="request-filter-group request-search-wrap">
                        <label class="request-filter-label" for="requestSearchInput">Search:</label>
                        <input
                            type="search"
                            id="requestSearchInput"
                            class="request-search-input"
                            placeholder="Search student, date, type, mode, status..."
                            autocomplete="off"
                        >
                    </div>
                </div>
                <div class="request-table-shell">
                    <div class="request-table-head" role="row">
                        <div>Name</div>
                        <div>Date &amp; Time</div>
                        <div>Session Type</div>
                        <div>Mode</div>
                        <div>Action</div>
                    </div>
                <div class="request-table">
                    @forelse ($consultations as $consultation)
                        @php
                            $status = strtolower($consultation->status ?? '');
                            $modeValue = strtolower((string) $consultation->consultation_mode);
                            $isFace = str_contains($modeValue, 'face');
                            $hasSummary = !empty($consultation->summary_text);
                            $updatedLabel = $consultation->updated_at?->diffForHumans() ?? '--';
                            $studentName = $consultation->student?->name ?? 'Student';
                            $nameParts = preg_split('/\s+/', trim((string) $studentName)) ?: [];
                            $initials = collect($nameParts)->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->implode('');
                            if ($initials === '') {
                                $initials = 'ST';
                            }
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
                            $requestSearchable = strtolower(implode(' ', array_filter([
                                $studentName,
                                (string) ($consultation->student?->student_id ?? ''),
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
                        <div class="request-row-wrap">
                         <div class="request-row"
                                 data-consultation-id="{{ $consultation->id }}"
                                 data-student-id="{{ $consultation->student?->student_id ?? '--' }}"
                                 data-consultation-date="{{ $consultation->consultation_date }}"
                                 data-consultation-time="{{ substr((string) $consultation->consultation_time, 0, 5) }}"
                                 data-consultation-end-time="{{ substr((string) ($consultation->consultation_end_time ?? ''), 0, 5) }}"
                                 data-consultation-priority="{{ strtolower((string) ($consultation->consultation_priority ?? '')) }}"
                                 data-status="{{ strtolower((string) $consultation->status) }}"
                                 data-mode="{{ strtolower((string) $consultation->consultation_mode) }}"
                                 data-mode-label="{{ $consultation->consultation_mode }}"
                                 data-call-attempts="{{ (int) ($consultation->call_attempts ?? 0) }}"
                                 data-started-at="{{ $consultation->started_at?->toIso8601String() ?? '' }}"
                                 data-actual-start-time="{{ $consultation->started_at?->timezone('Asia/Manila')->format('M d, Y g:i A') ?? '--' }}"
                                 data-actual-end-time="{{ $consultation->ended_at?->timezone('Asia/Manila')->format('M d, Y g:i A') ?? '--' }}"
                                 data-updated="{{ $updatedLabel }}"
                                  data-summary="{{ e((string) ($consultation->summary_text ?? '')) }}"
                                  data-transcript="{{ e((string) ($consultation->transcript_text ?? '')) }}"
                                 data-notes="{{ e((string) ($consultation->student_notes ?? '')) }}"
                                 data-search="{{ $requestSearchable }}"
                            >
                            <div class="request-user">
                                        <div class="request-avatar">{{ $initials }}</div>
                                        <div class="request-user-main">
                                            <div class="request-user-top">
                                            <div class="request-user-name">{{ $studentName }}</div>
                                        </div>
                                <div class="request-user-id">ID: {{ $consultation->student?->student_id ?? '--' }}</div>
                                            @php
                                                $studentOnline = in_array($consultation->student?->id ?? 0, (array) ($onlineStudentIds ?? []));
                                                $studentId = $consultation->student?->id;
                                                $lastActiveMinutes = $studentId && isset($consultationActiveMinutes[$studentId]) ? $consultationActiveMinutes[$studentId]['last_active_minutes'] : null;
                                            @endphp
                                            @if ($studentOnline)
                                                <span class="online-badge" aria-hidden="true">● Online</span>
                                            @elseif ($lastActiveMinutes !== null)
                                                <span class="instructor-active-minutes-badge">Active {{ \App\Services\UserSessionService::formatActiveMinutesAgo($lastActiveMinutes) }}</span>
                                            @else
                                                <span class="instructor-active-minutes-badge">Active —</span>
                                            @endif
                                        </div>
                            </div>
                            <div class="request-mobile-details">
                                <button type="button"
                                        class="request-mobile-details-btn details-open-btn"
                                        data-source="request"
                                        data-show-request-meta="true"
                                        data-action-source="requestAction{{ $consultation->id }}"
                                        data-id="{{ $consultation->id }}"
                                        data-student="{{ $studentName }}"
                                        data-student-id="{{ $consultation->student?->student_id ?? '--' }}"
                                        data-type="{{ $consultation->type_label }}"
                                        data-mode="{{ $consultation->consultation_mode }}"
                                        data-date="{{ $consultation->consultation_date }}"
                                        data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                        data-duration="--"
                                        data-actual-start-time="{{ $consultation->started_at?->timezone('Asia/Manila')->format('M d, Y g:i A') ?? '--' }}"
                                        data-actual-end-time="{{ $consultation->ended_at?->timezone('Asia/Manila')->format('M d, Y g:i A') ?? '--' }}"
                                        data-status="{{ strtoupper($consultation->status) }}"
                                        data-updated="{{ $updatedLabel }}"
                                        data-notes="{{ e((string) ($consultation->student_notes ?? '')) }}"
                                        data-summary="{{ e((string) ($consultation->summary_text ?? '')) }}"
                                        data-transcript="{{ e((string) ($consultation->transcript_text ?? '')) }}">
                                    View Details
                                </button>
                            </div>
                            <div class="request-meta request-datetime">
                                <span><i class="fa-regular fa-calendar"></i> {{ $consultation->consultation_date }}</span>
                                <span><i class="fa-regular fa-clock"></i> {{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                            </div>
                            <div class="request-meta request-type">
                                <span class="request-type-title">{{ $consultation->type_label }}</span>
                                @if (!empty($consultation->student_notes))
                                    <div class="request-note-preview" title="{{ $consultation->student_notes }}">
                                        <span class="request-note-label">Note:</span> {{ $consultation->student_notes }}
                                    </div>
                                @endif
                            </div>
                            <div class="request-meta request-mode">
                                <span class="request-tag {{ $isFace ? 'face' : '' }}">{{ $consultation->consultation_mode }}</span>
                                <div class="request-updated-inline">{{ $updatedLabel }}</div>
                            </div>
                            <div class="request-actions" id="requestAction{{ $consultation->id }}">
                                @if ($consultation->status === 'pending')
                                    <form method="POST" action="{{ route('instructor.consultations.approve', $consultation->id) }}">
                                        @csrf
                                        <button type="submit" class="request-btn approve">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('instructor.consultations.decline', $consultation->id) }}">
                                        @csrf
                                        <button type="submit" class="request-btn decline">Decline</button>
                                    </form>
                                @elseif ($consultation->status === 'approved')
                                    @if ($isFace)
                                        <button type="button"
                                                class="request-btn summary summary-open-btn"
                                                data-id="{{ $consultation->id }}"
                                                data-student="{{ $consultation->student?->name ?? 'Student' }}"
                                                data-student-id="{{ $consultation->student?->student_id ?? '' }}"
                                                data-type="{{ $consultation->type_label }}"
                                                data-mode="{{ $consultation->consultation_mode }}"
                                                data-date="{{ $consultation->consultation_date }}"
                                                data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                                data-summary="{{ e((string) ($consultation->summary_text ?? '')) }}"
                                                data-transcript="{{ e((string) ($consultation->transcript_text ?? '')) }}"
                                        >
                                            {{ $hasSummary ? 'View / Edit Summary' : 'Add Summary' }}
                                        </button>
                                    @else
                                        @php
                                            $callAttempts = (int) ($consultation->call_attempts ?? 0);
                                            $canMarkIncomplete = $callAttempts >= 3;
                                        @endphp
                                        @if (! $canMarkIncomplete)
                                            <form method="POST" action="{{ route('instructor.consultations.start', $consultation->id) }}" class="start-session-form">
                                                @csrf
                                                <button type="submit"
                                                        class="request-btn start start-session-btn"
                                                        data-consultation-id="{{ $consultation->id }}">
                                                    {{ $callAttempts > 0 ? 'Call Again' : 'Video Call' }} (Attempt {{ min($callAttempts + 1, 3) }}/3)
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST"
                                              action="{{ route('instructor.consultations.mark-incomplete', $consultation->id) }}"
                                              class="mark-incomplete-form"
                                              style="{{ $canMarkIncomplete ? '' : 'display:none;' }}"
                                              data-consultation-id="{{ $consultation->id }}">
                                            @csrf
                                            <button type="submit" class="request-btn decline mark-incomplete-btn">Mark as Incompleted</button>
                                        </form>
                                    @endif
                                @elseif ($consultation->status === 'in_progress')
                                    <button type="button"
                                            class="request-btn start join-call-btn"
                                            data-consultation-id="{{ $consultation->id }}">
                                        Join Call
                                    </button>
                                    <span class="request-tag">Video call in progress</span>
                                @elseif (in_array($consultation->status, ['completed', 'incompleted'], true))
                                    <button type="button"
                                            class="request-btn summary summary-open-btn"
                                            data-id="{{ $consultation->id }}"
                                            data-student="{{ $consultation->student?->name ?? 'Student' }}"
                                            data-student-id="{{ $consultation->student?->student_id ?? '' }}"
                                            data-type="{{ $consultation->type_label }}"
                                            data-mode="{{ $consultation->consultation_mode }}"
                                            data-date="{{ $consultation->consultation_date }}"
                                            data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                            data-summary="{{ e((string) ($consultation->summary_text ?? '')) }}"
                                            data-transcript="{{ e((string) ($consultation->transcript_text ?? '')) }}"
                                    >
                                        {{ $hasSummary ? 'View / Edit Summary' : 'Add Summary' }}
                                    </button>
                                @elseif ($consultation->status === 'declined')
                                    <button type="button"
                                            class="request-btn summary summary-open-btn"
                                            data-id="{{ $consultation->id }}"
                                            data-student="{{ $consultation->student?->name ?? 'Student' }}"
                                            data-student-id="{{ $consultation->student?->student_id ?? '' }}"
                                            data-type="{{ $consultation->type_label }}"
                                            data-mode="{{ $consultation->consultation_mode }}"
                                            data-date="{{ $consultation->consultation_date }}"
                                            data-time="{{ $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time) }}"
                                            data-summary="{{ e((string) ($consultation->summary_text ?? '')) }}"
                                            data-transcript="{{ e((string) ($consultation->transcript_text ?? '')) }}"
                                    >
                                        {{ $hasSummary ? 'View / Edit Summary' : 'Add Summary' }}
                                    </button>
                                @else
                                    <span class="request-tag">No Action</span>
                                @endif
                                <div class="request-action-status">
                                    <span class="request-status {{ $status }}">{{ strtoupper($consultation->status) }}</span>
                                </div>
                            </div>
                            </div>
                        </div>
                    @empty
                        <div class="request-row-wrap">
                        <div class="request-row">
                            <div class="request-user" style="grid-column:1 / -1;padding:18px 14px;">
                                <div class="request-user-name">No consultation requests</div>
                                <div class="request-user-email">New requests will appear here.</div>
                            </div>
                        </div>
                        </div>
                    @endforelse
                </div>
                </div>

                <!-- Pagination Controls -->
                <div id="requestPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;">
                    <div id="requestPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to {{ min(10, $consultations->count()) }} of {{ $consultations->count() }} requests
                    </div>
                    <div id="requestPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevRequestBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="requestPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextRequestBtn" class="pagination-nav-btn" style="display:none;">
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

            {{-- APPROVED / UPCOMING --}}

                        </div>
    </div>
</div>

<div class="availability-modal" id="availabilityModal" aria-hidden="true">
    <div class="availability-dialog">
        <div class="availability-modal-header">
            <h2 class="availability-modal-title">Set Availability</h2>
            <button type="button" class="availability-close" id="closeAvailabilityModal" aria-label="Close">x</button>
        </div>
        <div class="availability-modal-body">
            <p class="availability-help">Configure your available time slots for consultations (Philippine Time, 24-hour format)</p>

            <form method="POST" action="{{ route('instructor.availability.store') }}" id="availabilityForm">
                @csrf
                @php
                    $currentYear = (int) now()->format('Y');
                    $academicYears = [];
                    for ($i = -50; $i <= 50; $i++) {
                        $year = $currentYear + $i;
                        $academicYears[] = $year . '-' . ($year + 1);
                    }
                    $selectedSemester = old('semester', $selectedSemester ?? 'first');
                    $selectedAcademicYear = old('academic_year', $selectedAcademicYear ?? ($currentYear . '-' . ($currentYear + 1)));
                @endphp
                                <div class="availability-filters">
                    <div class="semester-toggle" role="group" aria-label="Semester choices">
                        <button type="button" class="semester-btn {{ $selectedSemester === 'first' ? 'active' : '' }}" data-semester="first">First Sem</button>
                        <button type="button" class="semester-btn {{ $selectedSemester === 'second' ? 'active' : '' }}" data-semester="second">Second Sem</button>
                    </div>
                    <input type="hidden" name="semester" id="availabilitySemester" value="{{ $selectedSemester }}">
                    <div class="availability-filter-group">
                        <label for="academicYear">Academic Year</label>
                        <div class="year-picker">
                            <button type="button" class="year-btn year-prev" id="yearPrev" aria-label="Previous year">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="11" stroke="#1F3A8A" stroke-width="1.2" fill="#f8fafc"/><path d="M15 12H9" stroke="#1F3A8A" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <input type="hidden" name="academic_year" id="academicYear" value="{{ $selectedAcademicYear }}">
                            <span class="year-display" id="yearDisplay">{{ $selectedAcademicYear }}</span>
                            <button type="button" class="year-btn year-next" id="yearNext" aria-label="Next year">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="11" stroke="#1F3A8A" stroke-width="1.2" fill="#f8fafc"/><path d="M12 9V15M9 12H15" stroke="#1F3A8A" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @php
                    $weeklyDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    $oldDays = old('days');
                @endphp
                <div class="availability-table">
                    @foreach ($weeklyDays as $day)
                        @php
                            $daySlots = $availabilityByDay->get($day, collect())->pluck('start_time')->map(fn ($time) => substr((string) $time, 0, 5))->values();
                            $dayEnds = $availabilityByDay->get($day, collect())->pluck('end_time')->map(fn ($time) => substr((string) $time, 0, 5))->values();
                            $checked = is_array($oldDays)
                                ? in_array($day, $oldDays, true)
                                : $daySlots->isNotEmpty();
                            $oldSlots = collect(old("slot_times.$day", []))->filter();
                            $oldEnds = collect(old("end_times.$day", []))->filter();
                            $defaultSlot = '08:00';
                            $slotValue = $oldSlots->first()
                                ?? $daySlots->first()
                                ?? $defaultSlot;
                            $endValue = $oldEnds->first()
                                ?? $dayEnds->first()
                                ?? '';
                        @endphp
                        <div class="availability-row {{ $checked ? '' : 'is-disabled' }}" data-day="{{ $day }}">
                            <label class="availability-day">
                                <span class="availability-toggle">
                                    <input type="checkbox" name="days[]" value="{{ $day }}" class="day-check" data-day="{{ $day }}" @checked($checked)>
                                    <span class="availability-toggle-slider"></span>
                                </span>
                                <span class="availability-day-name">{{ ucfirst($day) }}</span>
                            </label>
                            <div class="availability-slots">
                                <div class="availability-slot-list">
                                    <div class="availability-slot">
                                        <input
                                            type="time"
                                            name="slot_times[{{ $day }}][0]"
                                            class="availability-time day-time availability-time-start"
                                            data-day="{{ $day }}"
                                            value="{{ $slotValue }}"
                                            @disabled(!$checked)
                                        >
                                        <span class="availability-to">to</span>
                                        <input
                                            type="time"
                                            name="end_times[{{ $day }}][0]"
                                            class="availability-time day-time availability-time-end"
                                            data-day="{{ $day }}"
                                            value="{{ $endValue }}"
                                            data-auto="{{ $endValue ? '0' : '1' }}"
                                            @disabled(!$checked)
                                        >
                                    </div>
                                </div>
                                <div class="availability-unavailable">Not available</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="availability-modal-actions">
                    <button type="button" class="availability-btn" id="cancelAvailabilityModal">Cancel</button>
                    <button type="submit" class="availability-btn primary">Save Availability</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="history" class="section is-hidden" aria-hidden="true">
        <div class="history-modal-header">
            <div class="history-title-wrap">
                <h2 class="history-modal-title">Consultation History</h2>
                <p class="history-modal-subtitle">Manage and track all completed consultations</p>
            </div>
            <button type="button" class="history-close" id="closeHistoryModal" aria-label="Close history">x</button>
        </div>
        <div class="history-modal-body">
            @php
                $completedConsultations = $consultations->where('status', 'completed');
                $historyTypes = $completedConsultations
                    ->pluck('type_label')
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
                                <button type="button" id="instructorSemAll" class="semester-btn" data-sem="all">All</button>
                                <button type="button" id="instructorSem1" class="semester-btn" data-sem="1">1st Sem</button>
                                <button type="button" id="instructorSem2" class="semester-btn" data-sem="2">2nd Sem</button>
                            </div>
                            <div class="history-month-group history-toolbar-item" id="instructorMonthPickerContainer" style="display:none;">
                                <select id="instructorMonthSelect" aria-label="Filter by month">
                                    <option value="">All months</option>
                                </select>
                            </div>
                            <div class="history-year-group history-toolbar-item history-toolbar-item-year">
                                <input type="text" id="instructorHistoryYearInput" placeholder="Academic Year..." aria-label="Filter by academic year">
                            </div>
                            <div class="availability-filter-group history-inline-filter history-toolbar-item">
                                <select id="instructorHistoryCategoryFilter" aria-label="Filter by category">
                                    <option value="">All Categories</option>
                                    <option value="Curricular Activities">Curricular Activities</option>
                                    <option value="Behavior-Related">Behavior-Related</option>
                                    <option value="Co-curricular activities">Co-curricular activities</option>
                                </select>
                            </div>
                            <div class="availability-filter-group history-inline-filter history-toolbar-item">
                                <select id="instructorHistoryTopicFilter" aria-label="Filter by topic">
                                    <option value="">All Topics</option>
                                </select>
                            </div>
                            <div class="availability-filter-group history-inline-filter history-toolbar-item">
                                <select id="instructorHistoryModeFilter" aria-label="Filter by mode">
                                    <option value="">All Modes</option>
                                    <option value="Video Call">Video Call</option>
                                    <option value="Face-to-Face">Face-to-Face</option>
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
                    <!-- kept for spacing parity with student history -->
                </div>
            </div>

            <div class="section" id="consultationHistoryInline" style="margin-top:0;">
                <div class="history-table">
                    <div class="history-row header">
                        <div>Date & Time</div>
                        <div>Student</div>
                        <div>Type</div>
                        <div>Mode</div>
                        <div>Duration</div>
                        <div>Records</div>
                        <div>Actions</div>
                    </div>

                    @forelse ($completedConsultations as $consultation)
                        @php
                            $modeValue = strtolower((string) $consultation->consultation_mode);
                            $isFaceToFace = str_contains($modeValue, 'face');
                            $duration = $consultation->formatted_duration;
                            $studentName = $consultation->student?->name ?? 'Student';
                            $studentId = $consultation->student?->student_id ?? '--';
                            $initialsParts = array_values(array_filter(explode(' ', trim((string) $studentName))));
                            $initials = strtoupper(substr($initialsParts[0] ?? 'S', 0, 1) . substr($initialsParts[1] ?? '', 0, 1));
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
                            $timeRange = $formatManilaRange($consultation->consultation_time, $consultation->consultation_end_time);
                        @endphp
                        <div class="history-row-wrap">
                            <div class="history-row history-row-item"
                                 data-category="{{ strtolower((string) ($consultation->consultation_category ?? '')) }}"
                                 data-topic="{{ strtolower((string) ($consultation->consultation_topic ?? $consultation->consultation_type ?? '')) }}"
                                 data-date="{{ $consultation->consultation_date }}"
                                 data-month="{{ $dateObj->format('F') }}"
                                 data-year="{{ $year }}"
                                 data-academic-year="{{ $academicYear }}"
                                 data-semester="{{ $semester }}"
                                 data-type="{{ strtolower((string) ($consultation->type_label ?? $consultation->consultation_type ?? '')) }}"
                                 data-mode="{{ strtolower((string) $consultation->consultation_mode) }}"
                                 data-priority="{{ (string) $searchPriority }}"
                                 data-searchable="{{ strtolower(($consultation->type_label ?? '') . ' ' . ($consultation->student?->name ?? '') . ' ' . ($consultation->student?->student_id ?? '') . ' ' . $consultation->consultation_mode . ' ' . $dateObj->format('F') . ' ' . $year . ' ' . $formattedDateLong . ' ' . $formattedDateNoComma . ' ' . $formattedDateShort . ' ' . $formattedDateIso . ' ' . $formattedDateSlash . ' ' . $searchPriority) }}"
                            >
                                <div class="date-time">
                                    <span>{{ $consultation->consultation_date }}</span>
                                    <span>{{ $timeRange }}</span>
                                </div>
                                <div class="history-student-cell">
                                    <div class="request-avatar" aria-hidden="true">{{ $initials ?: 'S' }}</div>
                                    <div class="history-student-meta">
                                        <div class="history-student-name">{{ $studentName }}</div>
                                        <div class="history-mobile-datetime">
                                            <span>{{ $consultation->consultation_date }}</span>
                                            <span>{{ $timeRange }}</span>
                                        </div>
                                        <div class="history-student-id">ID: {{ $studentId }}</div>
                                    </div>
                                </div>
                                <div>{{ $consultation->type_label ?? $consultation->consultation_type }}</div>
                                <div class="history-mode-cell">
                                    <span class="badge badge-mode {{ $isFaceToFace ? 'face' : '' }}">
                                        {{ $consultation->consultation_mode }}
                                    </span>
                                </div>
                                <div>{{ $duration }}</div>
                                <div>
                                    @if (! $isFaceToFace)
                                        <span class="record-pill secondary">Action Taken</span>
                                    @endif
                                    <span class="record-pill">Summary</span>
                                </div>
                                <div class="history-action-cell">
                                    <a href="#"
                                       class="view-link details-open-btn"
                                       data-id="{{ $consultation->id }}"
                                       data-student="{{ $studentName }}"
                                       data-student-id="{{ $studentId }}"
                                       data-date="{{ $consultation->consultation_date }}"
                                       data-time="{{ $timeRange }}"
                                       data-type="{{ $consultation->type_label ?? $consultation->consultation_type }}"
                                       data-mode="{{ $consultation->consultation_mode }}"
                                       data-duration="{{ $consultation->formatted_duration }}"
                                       data-actual-start-time="{{ $consultation->started_at?->timezone('Asia/Manila')->format('M d, Y g:i A') ?? '--' }}"
                                       data-actual-end-time="{{ $consultation->ended_at?->timezone('Asia/Manila')->format('M d, Y g:i A') ?? '--' }}"
                                       data-notes="{{ e((string) ($consultation->student_notes ?? '')) }}"
                                       data-summary="{{ e($consultation->summary_text) }}"
                                       data-transcript="{{ e($consultation->transcript_text) }}"
                                    >View Details</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No consultation history found.</div>
                    @endforelse

                    <div class="empty-state" id="instructorHistoryEmptyState" style="display:none;">No matching results.</div>
                </div>

                <div id="instructorHistoryPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;">
                    <div id="instructorHistoryPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to {{ min(10, $completedConsultations->count()) }} of {{ $completedConsultations->count() }} consultations
                    </div>
                    <div id="instructorHistoryPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevInstructorHistoryBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="instructorHistoryPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextInstructorHistoryBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">›</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
</div>

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
        <div class="call-body">
            <div class="call-stage">
                <div class="call-video call-video-remote" id="remoteVideo" data-participant="Student" data-state="waiting">
                    <div class="call-panel-head">
                        <span class="call-participant-chip" data-call-participant-label>Student</span>
                    </div>
                    <div class="call-media-surface" data-call-media></div>
                    <div class="call-video-placeholder">
                        <div class="call-avatar" data-call-video-avatar>S</div>
                        <div class="call-video-status" data-call-video-status>Waiting for student to join...</div>
                    </div>
                    <div class="call-video-footer">
                        <div class="call-video-identity">
                            <span class="call-video-name">Student</span>
                            <span class="call-video-role">Remote participant</span>
                        </div>
                        <span class="call-video-footer-badge">Secure room</span>
                    </div>
                </div>
                <div class="call-video call-video-local" id="localVideo" data-participant="Instructor" data-state="waiting" data-draggable-local>
                    <div class="call-panel-head">
                        <span class="call-participant-chip" data-call-participant-label>You</span>
                    </div>
                    <div class="call-media-surface" data-call-media></div>
                    <div class="call-video-placeholder">
                        <div class="call-avatar" data-call-video-avatar>I</div>
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
                <button type="button" class="call-btn summary" id="addCallSummaryBtn" aria-label="Add summary">
                    <span class="call-btn-icon" aria-hidden="true">
                        <i class="fa-solid fa-file-pen"></i>
                    </span>
                    <span class="call-btn-meta">
                        <span class="call-btn-title">Summary</span>
                        <span class="call-btn-text">Add</span>
                    </span>
                </button>
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
                <button type="button" id="detailsExportBtn" class="details-export-btn">
                    <i class="fa-solid fa-file-pdf" aria-hidden="true"></i>
                    <span>Export PDF</span>
                </button>
                <button type="button" class="details-close" id="closeDetailsModal" aria-label="Close">x</button>
            </div>
        </div>
        <div class="details-body">
                        <div class="details-grid">
                            <div class="details-card" id="detailsDate">Date & Time: --</div>
                            <div class="details-card" id="detailsStudent">Student: --</div>
                            <div class="details-card" id="detailsStudentId">Student ID: --</div>
                            <div class="details-card" id="detailsMode">Mode: --</div>
                            <div class="details-card" id="detailsType">Type: --</div>
                            <div class="details-card" id="detailsDuration">Duration: --</div>
                            <div class="details-card" id="detailsActualStart">Actual Start: --</div>
                            <div class="details-card" id="detailsActualEnd">Actual End: --</div>
                            <div class="details-card" id="detailsStatus">Status: --</div>
                            <div class="details-card" id="detailsUpdated">Updated: --</div>
                        </div>
            <div class="details-summary" id="detailsNotesWrap" style="display:none;">
                <div class="details-summary-title">Student Notes</div>
                <div id="detailsNotesText">No notes provided.</div>
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

<div class="summary-modal" id="summaryModal" aria-hidden="true">
    <div class="summary-dialog">
        <div class="summary-header">
            <div class="summary-title">Consultation Summary</div>
            <button type="button" class="summary-close" id="closeSummaryModal" aria-label="Close">x</button>
        </div>
        <form method="POST" id="summaryForm">
            @csrf
            <div class="summary-body">
                <div class="summary-grid">
                    <div class="summary-card" id="summaryStudent">Student: --</div>
                    <div class="summary-card" id="summaryStudentId">Student ID: --</div>
                    <div class="summary-card" id="summaryDate">Date & Time: --</div>
                    <div class="summary-card" id="summaryType">Type: --</div>
                    <div class="summary-card summary-card-wide" id="summaryMode">Mode: --</div>
                </div>
                <label class="summary-label" for="summaryText">Summary</label>
                <textarea class="summary-textarea" name="summary_text" id="summaryText" placeholder="Write the summary of the discussion..." required></textarea>
                <div id="summaryActionTakenGroup">
                    <label class="summary-label" for="summaryActionTaken">Action Taken</label>
                    <textarea class="summary-textarea summary-textarea-lg" name="action_taken_text" id="summaryActionTaken" placeholder="Write how you resolved or handled the consultation..." required></textarea>
                </div>
            </div>
            <div class="summary-actions">
                <button type="button" class="availability-btn" id="cancelSummaryModal">Cancel</button>
                <button type="submit" class="availability-btn primary">Save Summary</button>
            </div>
        </form>
    </div>
</div>

<div class="toast" id="notifToast">
    <button class="toast-close" id="closeToast">x</button>
    <div class="toast-title" id="toastTitle">New Notification</div>
    <div class="toast-body" id="toastBody">You have a new notification.</div>
</div>


