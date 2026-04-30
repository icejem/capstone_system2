            <div class="students-card is-hidden" id="consultationsSection">
                <div class="consultations-head">
                    <div class="consultations-head-top">
                        <div>
                            <h2 class="consultations-title">All Consultations</h2>
                            <p class="consultations-subtitle">Manage and track all student consultations</p>
                        </div>
                        <button type="button" class="section-close-btn section-close-trigger" data-close-section="consultations" aria-label="Close consultations section">&times;</button>
                    </div>
                    <div class="consultations-filter-card">
                        <div class="consultations-toolbar-top">
                            <div class="consultation-toolbar-item consultation-toolbar-item-search">
                                <input
                                    type="text"
                                    class="students-search consultation-search-input"
                                    id="consultationSearch"
                                    placeholder="Search date, name, or student ID..."
                                    autocomplete="off"
                                    aria-label="Search consultations"
                                >
                            </div>
                        </div>
                        <div class="consultations-toolbar-scroll">
                            <div class="consultations-toolbar-row">
                                <div class="consultation-semester-toggle" role="group" aria-label="Consultation semester filter">
                                    <button type="button" id="consultationSemAll" class="consultation-semester-btn active" data-sem="all">All</button>
                                    <button type="button" id="consultationSem1" class="consultation-semester-btn" data-sem="1">1st Sem</button>
                                    <button type="button" id="consultationSem2" class="consultation-semester-btn" data-sem="2">2nd Sem</button>
                                </div>
                                <div class="consultation-toolbar-item" id="consultationMonthPickerContainer">
                                    <select class="students-filter" id="consultationMonthSelect" aria-label="Filter by month">
                                        <option value="">All months</option>
                                    </select>
                                </div>
                                <div class="consultation-toolbar-item consultation-toolbar-item-year">
                                    <input type="text" class="students-search" id="consultationYearInput" placeholder="Academic Year..." autocomplete="off" aria-label="Filter by academic year">
                                </div>
                                <div class="consultation-toolbar-item consultation-toolbar-item-request">
                                    <select class="students-filter" id="consultationCategoryFilter" aria-label="Filter by category">
                                        <option value="">All Categories</option>
                                    </select>
                                </div>
                                <div class="consultation-toolbar-item consultation-toolbar-item-request">
                                    <select class="students-filter" id="consultationTopicFilter" aria-label="Filter by topic">
                                        <option value="">All Topics</option>
                                    </select>
                                </div>
                                <div class="consultation-toolbar-item">
                                    <select class="students-filter" id="consultationStatusFilter" aria-label="Filter by status">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="completed">Completed</option>
                                        <option value="incompleted">Incomplete</option>
                                        <option value="declined">Declined</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="consultation-toolbar-actions">
                                    <button type="button" class="stats-export-btn stats-export-reset" id="consultationResetFiltersBtn">
                                        <i class="fa-solid fa-rotate-left"></i> Reset Filters
                                    </button>
                                    <button type="button" class="stats-export-btn stats-export-pdf" id="consultationExportPdfBtn">
                                        <i class="fa-solid fa-file-pdf"></i> Export PDF
                                    </button>
                                    <button type="button" class="stats-export-btn stats-export-excel" id="consultationExportBtn">
                                        <i class="fa-solid fa-download"></i> Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-consultation-shell">
                    <div class="admin-consultation-head" role="row">
                        <div>Student</div>
                        <div>Instructor</div>
                        <div>Date &amp; Time</div>
                        <div>Topic / Type</div>
                        <div>Mode</div>
                        <div>Status</div>
                        <div>Actions</div>
                    </div>
                    <div class="admin-consultation-table" id="consultationTableBody">
                        @forelse ($consultationRows as $row)
                            @php
                                $modeClass = str_contains(strtolower((string) $row['mode']), 'audio')
                                    ? 'mode-audio'
                                    : (str_contains(strtolower((string) $row['mode']), 'video')
                                        ? 'mode-video'
                                        : (str_contains(strtolower((string) $row['mode']), 'face')
                                            ? 'mode-face'
                                            : 'mode-default'));
                            @endphp
                            <div
                                class="admin-consultation-row"
                                data-consultation-id="{{ $row['consultation_id'] }}"
                                data-status="{{ strtolower((string) $row['status']) }}"
                                data-date="{{ $row['date'] }}"
                                data-category="{{ (string) ($row['category'] ?? '') }}"
                                data-topic="{{ (string) ($row['topic'] ?? '') }}"
                                data-type="{{ (string) ($row['type'] ?? '') }}"
                                data-mode="{{ (string) ($row['mode'] ?? '') }}"
                                data-search-all="{{ strtolower($row['code'] . ' ' . $row['student'] . ' ' . $row['student_id'] . ' ' . $row['instructor'] . ' ' . $row['date'] . ' ' . ($row['formatted_date_long'] ?? '') . ' ' . ($row['formatted_date_no_comma'] ?? '') . ' ' . ($row['formatted_date_short'] ?? '') . ' ' . ($row['formatted_date_iso'] ?? '') . ' ' . ($row['formatted_date_slash'] ?? '') . ' ' . $row['time_range'] . ' ' . $row['duration'] . ' ' . $row['type'] . ' ' . ($row['category'] ?? '') . ' ' . ($row['topic'] ?? '') . ' ' . $row['mode'] . ' ' . $row['status'] . ' ' . ($row['priority'] ?? '') . ' ' . $row['summary'] . ' ' . $row['action_taken']) }}"
                            >
                                <div class="admin-consultation-party">
                                    <div class="admin-consultation-primary">{{ $row['student'] }}</div>
                                    <div class="admin-consultation-secondary">ID: {{ $row['student_id'] ?: '--' }}</div>
                                </div>
                                <div class="admin-consultation-party">
                                    <div class="admin-consultation-primary">{{ $row['instructor'] }}</div>
                                    <div class="admin-consultation-secondary">Instructor</div>
                                </div>
                                <div class="admin-consultation-datetime">
                                    <div class="admin-consultation-date">{{ $row['date'] }}</div>
                                    <div class="admin-consultation-time">{{ $row['time_range'] }}</div>
                                </div>
                                <div class="admin-consultation-type">
                                    <span class="admin-consultation-type-text">{{ $row['type'] }}</span>
                                </div>
                                <div class="admin-consultation-mode">
                                    <span class="mode-pill {{ $modeClass }}">{{ $row['mode'] }}</span>
                                </div>
                                <div class="admin-consultation-status">
                                    <span class="status-tag status-{{ strtolower((string) $row['status']) }}">{{ strtoupper((string) $row['status']) }}</span>
                                </div>
                                <div class="admin-consultation-actions">
                                    <a href="#"
                                       class="action-view consultation-view-btn"
                                       data-id="{{ $row['code'] }}"
                                       data-consultation-id="{{ $row['consultation_id'] }}"
                                       data-student="{{ $row['student'] }}"
                                       data-student-id="{{ $row['student_id'] }}"
                                       data-instructor="{{ $row['instructor'] }}"
                                       data-date="{{ $row['date'] }}"
                                       data-time="{{ $row['time_range'] }}"
                                       data-duration="{{ $row['duration'] }}"
                                       data-actual-start-time="{{ $row['actual_start_time'] ?? '--' }}"
                                       data-actual-end-time="{{ $row['actual_end_time'] ?? '--' }}"
                                       data-type="{{ $row['type'] }}"
                                       data-mode="{{ $row['mode'] }}"
                                       data-status="{{ strtoupper((string) $row['status']) }}"
                                       data-summary="{{ $row['summary'] }}"
                                       data-action-taken="{{ $row['action_taken'] }}"
                                    >View</a>
                                </div>
                            </div>
                        @empty
                            <div class="admin-consultation-empty">No consultations found.</div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Consultation Pagination Controls -->
                <div id="consultationPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;padding:0 16px;">
                    <div id="consultationPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to 10 of 0 consultations
                    </div>
                    <div id="consultationPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevConsultationAdminBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">&lsaquo;</span>
                        </button>
                        <div id="consultationPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextConsultationAdminBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">&rsaquo;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.dashboard.partials.system_logs')
    </div>
</div>
