            <div id="overviewSection">
            <div class="stat-grid" id="overviewStatsCards">
                <div class="stat-card stat-card-students">
                    <div class="stat-icon" style="background:#e0f2fe;color:#075985;"><i class="fa-solid fa-user-graduate" aria-hidden="true"></i></div>
                    <div class="stat-copy">
                        <div class="stat-value" id="adminOverviewStudents">{{ $totalStudents }}</div>
                        <div class="stat-label">Students</div>
                        <div class="stat-meta stat-meta-positive">Registered student accounts</div>
                    </div>
                </div>

                <div class="stat-card stat-card-instructors">
                    <div class="stat-icon" style="background:#ecfdf5;color:#047857;"><i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i></div>
                    <div class="stat-copy">
                        <div class="stat-value" id="adminOverviewInstructors">{{ $totalInstructors }}</div>
                        <div class="stat-label">Instructors</div>
                        <div class="stat-meta stat-meta-positive">Active faculty users</div>
                    </div>
                </div>

                <div class="stat-card stat-card-consultations" id="recent-consultations">
                    <div class="stat-icon" style="background:#fff7ed;color:#c2410c;"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i></div>
                    <div class="stat-copy">
                        <div class="stat-value" id="adminOverviewConsultations">{{ $totalConsultations }}</div>
                        <div class="stat-label">Total Consultations</div>
                        <div class="stat-meta">{{ $totalConsultations > 0 ? 'Tracked consultation records' : 'No consultation records yet' }}</div>
                    </div>
                </div>

                <div class="stat-card stat-card-completed">
                    <div class="stat-icon" style="background:#ede9fe;color:#5b21b6;"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                    <div class="stat-copy">
                        <div class="stat-value" id="adminOverviewCompleted">{{ $completedSessions }}</div>
                        <div class="stat-label">Completed Sessions</div>
                        <div class="stat-meta">{{ $completedSessions > 0 ? 'Finished sessions logged' : 'No completed sessions yet' }}</div>
                    </div>
                </div>
            </div>

                <div class="grid-2">
                <div class="panel admin-recent-panel">
                    <div class="overview-panel-header">
                        <h2 class="overview-panel-title">Recent Consultations</h2>
                        <a href="#" class="overview-panel-link">View All <span aria-hidden="true">&rarr;</span></a>
                    </div>

                    <div id="adminRecentConsultationsList">
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
                                    $priorityValue = trim((string) ($consultation->consultation_priority ?? ''));
                                    $priorityFromType = '';
                                    if (preg_match('/\((urgent|normal|low)\)/i', (string) $consultationTitle, $priorityMatch)) {
                                        $priorityFromType = strtolower((string) ($priorityMatch[1] ?? ''));
                                    }
                                    $priorityKey = strtolower($priorityValue !== '' ? $priorityValue : $priorityFromType);
                                @endphp
                                <div class="recent-item">
                                    <div class="recent-item-top">
                                        <p class="recent-item-title">
                                            {{ $consultationTitle }}
                                        </p>
                                        <span class="recent-status-pill status-{{ $statusKey }}">{{ $statusLabel }}</span>
                                    </div>
                                    <div class="recent-item-meta">
                                        <span><i class="fa-solid fa-users" aria-hidden="true"></i> {{ $consultation->student?->name ?? 'Student' }} with {{ $consultation->instructor?->name ?? 'Instructor' }}</span>
                                        <span><i class="fa-solid fa-clock" aria-hidden="true"></i> {{ $formatRelativeDay($consultation->consultation_date) }}, {{ $formatManilaRangeDash($consultation->consultation_time, $consultation->consultation_end_time) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    </div>
                </div>

                <div class="panel" id="system-overview">
                    <div class="panel-head">System Overview</div>
                    <div class="overview-list">
                        <div class="overview-item">
                            <div>
                                <div class="overview-title">Data Integrity</div>
                                <div class="overview-sub">Records validated and available</div>
                            </div>
                            <span class="overview-state state-green">Healthy</span>
                        </div>
                        <div class="overview-item">
                            <div>
                                <div class="overview-title">Security Status</div>
                                <div class="overview-sub">No threat flags detected</div>
                            </div>
                            <span class="overview-state state-green">Secure</span>
                        </div>
                        <div class="overview-item">
                            <div>
                                <div class="overview-title">Database</div>
                                <div class="overview-sub">Connected and synchronized</div>
                            </div>
                            <span class="overview-state state-blue">Online</span>
                        </div>
                        <div class="overview-item">
                            <div>
                                <div class="overview-title">Pending Actions</div>
                                <div class="overview-sub">{{ $pendingConsultations }} consultations awaiting approval</div>
                            </div>
                            <span class="overview-state state-orange">Attention</span>
                        </div>
                    </div>
                </div>
                </div>

                <div class="stats-workspace is-hidden" id="statistics">
                    <div class="stats-filter-card">
                        <div class="stats-filter-head">
                            <div class="stats-filter-title"><i class="fa-solid fa-filter"></i> Filters</div>
                            <button type="button" class="section-close-btn section-close-trigger" data-close-section="statistics" aria-label="Close statistics section">&times;</button>
                        </div>
                        <div class="stats-search-top">
                            <div class="stats-search-wrap">
                                <input
                                    type="text"
                                    class="stats-search-input"
                                    id="statsSearchInput"
                                    placeholder="Search statistics..."
                                    autocomplete="off"
                                    aria-label="Search statistics"
                                >
                            </div>
                            <button type="button" class="stats-search-clear stats-search-clear-top is-hidden" id="statsSearchClearBtn" aria-label="Clear search">&times;</button>
                        </div>
                        <div class="stats-toolbar-scroll">
                            <div class="stats-toolbar-row">
                                <div class="stats-toolbar-item stats-toolbar-item-semester">
                                <div class="stats-semester-toggle" role="group" aria-label="Statistics semester filter">
                                    <button type="button" class="stats-semester-btn active" data-stats-semester="all">All</button>
                                    <button type="button" class="stats-semester-btn" data-stats-semester="first">1st Sem</button>
                                    <button type="button" class="stats-semester-btn" data-stats-semester="second">2nd Sem</button>
                                </div>
                                </div>
                                <div class="stats-toolbar-item stats-toolbar-item-year">
                                <input
                                    type="text"
                                    class="stats-filter-select"
                                    id="statsAcademicYearSelect"
                                    placeholder="Search Academic Year"
                                    autocomplete="off"
                                    aria-label="Filter statistics by academic year"
                                >
                                </div>
                                <div class="stats-toolbar-item">
                                    <select class="stats-filter-select" id="statsMonthSelect" aria-label="Filter statistics by month"></select>
                                </div>
                                <div class="stats-toolbar-item">
                                    <select class="stats-filter-select" id="statsCategorySelect" aria-label="Filter statistics by category"></select>
                                </div>
                                <div class="stats-toolbar-item">
                                    <select class="stats-filter-select" id="statsTopicSelect" aria-label="Filter statistics by topic"></select>
                                </div>
                                <div class="stats-toolbar-item">
                                    <select class="stats-filter-select" id="statsModeSelect" aria-label="Filter statistics by mode"></select>
                                </div>
                                <div class="stats-toolbar-item">
                                    <select class="stats-filter-select" id="statsInstructorSelect" aria-label="Filter statistics by instructor"></select>
                                </div>
                                <div class="stats-toolbar-actions">
                                    <button type="button" class="stats-export-btn stats-export-reset" id="statsResetBtn">
                                        <i class="fa-solid fa-rotate-left"></i> Reset Filters
                                    </button>
                                    <button type="button" class="stats-export-btn stats-export-pdf" id="statsExportPdfBtn">
                                        <i class="fa-solid fa-file-pdf"></i> Export PDF
                                    </button>
                                    <button type="button" class="stats-export-btn stats-export-excel" id="statsExportExcelBtn">
                                        <i class="fa-solid fa-file-excel"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stats-metric-grid">
                        <div class="stats-metric-card consultations">
                            <div class="stats-metric-label">Total Consultations</div>
                            <div class="stats-metric-value" id="statsTotalConsultations">0</div>
                        </div>
                        <div class="stats-metric-card types">
                            <div class="stats-metric-label">Consultation Types</div>
                            <div class="stats-metric-value" id="statsTypeCount">0</div>
                        </div>
                        <div class="stats-metric-card period">
                            <div class="stats-metric-label">Current Period</div>
                            <div class="stats-metric-subvalue" id="statsCurrentPeriod">1st Sem</div>
                        </div>
                    </div>

                    <div class="stats-distribution">
                        <div class="stats-distribution-head">
                            <div class="stats-distribution-title"><i class="fa-solid fa-chart-bar"></i> Horizontal Bar Chart - Percentage Distribution</div>
                            <div class="stats-distribution-subtitle" id="statsDistributionSubtitle">Month - Semester Academic Year</div>
                        </div>
                        <div class="stats-distribution-body">
                            <div class="stats-bar-summary">
                                <div class="stats-bar-summary-label">Total Consultations</div>
                                <div class="stats-donut-total" id="statsDonutTotal">0</div>
                            </div>
                            <div class="stats-bar-chart" id="statsDonutChart"></div>
                        </div>
                    </div>
                </div>
            </div>

