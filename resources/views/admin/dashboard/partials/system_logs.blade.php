            <div class="students-card system-logs-card is-hidden" id="systemLogsSection">
                <div class="system-logs-head">
                    <div>
                        <h2 class="system-logs-title">System Logs</h2>
                        <p class="system-logs-subtitle">User authentication activity, active sessions, devices, and session duration</p>
                    </div>
                    <button type="button" class="section-close-btn section-close-trigger" data-close-section="system-logs" aria-label="Close system logs section">&times;</button>
                </div>

                @php
                    $activeLogCount = $systemLogs->whereNull('logout_at')->count();
                    $recentLoginCount = $systemLogs->filter(function ($session) {
                        return $session->login_at && $session->login_at->greaterThanOrEqualTo(now()->subMinutes(30));
                    })->count();
                    $formatLogDuration = function ($session): string {
                        $minutes = $session->logout_at
                            ? (int) ($session->active_minutes ?? 0)
                            : (int) $session->login_at?->diffInMinutes($session->last_activity_at ?: now());

                        if ($minutes < 1) {
                            return 'Less than 1 min';
                        }

                        $hours = intdiv($minutes, 60);
                        $remainingMinutes = $minutes % 60;

                        if ($hours > 0) {
                            return $hours . 'h ' . $remainingMinutes . 'm';
                        }

                        return $minutes . 'm';
                    };
                    $roleLabel = function (?string $role): string {
                        return match (strtolower((string) $role)) {
                            'admin' => 'Admin',
                            'instructor' => 'Instructor',
                            'student' => 'Student',
                            default => 'User',
                        };
                    };
                @endphp

                <div class="system-logs-summary">
                    <div class="system-log-stat">
                        <span class="system-log-stat-value">{{ $systemLogs->count() }}</span>
                        <span class="system-log-stat-label">Recorded Sessions</span>
                    </div>
                    <div class="system-log-stat">
                        <span class="system-log-stat-value">{{ $activeLogCount }}</span>
                        <span class="system-log-stat-label">Active Sessions</span>
                    </div>
                    <div class="system-log-stat">
                        <span class="system-log-stat-value">{{ $recentLoginCount }}</span>
                        <span class="system-log-stat-label">Recent Logins</span>
                    </div>
                </div>

                <div class="system-log-filters">
                    <input type="search" id="systemLogSearch" class="students-search system-log-search" placeholder="Search user, role, browser, IP, location..." autocomplete="off">
                    <select id="systemLogRoleFilter" class="students-filter">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="instructor">Instructor</option>
                        <option value="student">Student</option>
                    </select>
                    <select id="systemLogStatusFilter" class="students-filter">
                        <option value="">All Sessions</option>
                        <option value="active">Active</option>
                        <option value="ended">Ended</option>
                        <option value="recent">Recent Logins</option>
                    </select>
                    <input type="date" id="systemLogDateFrom" class="students-search system-log-date" aria-label="Date from">
                    <input type="date" id="systemLogDateTo" class="students-search system-log-date" aria-label="Date to">
                </div>

                <div class="admin-consultation-shell system-log-shell">
                    <div class="admin-consultation-head system-log-head-row" role="row">
                        <div>User</div>
                        <div>Role</div>
                        <div>Login</div>
                        <div>Logout</div>
                        <div>Duration</div>
                        <div>Device</div>
                        <div>IP / Location</div>
                        <div>Status</div>
                    </div>
                    <div class="admin-consultation-table system-log-table" id="systemLogTableBody">
                        @forelse ($systemLogs as $session)
                            @php
                                $user = $session->user;
                                $role = strtolower((string) ($user?->user_type ?? 'user'));
                                $name = (string) ($user?->name ?? 'Deleted User');
                                $email = (string) ($user?->email ?? '');
                                $isActive = $session->logout_at === null;
                                $isRecent = $session->login_at && $session->login_at->greaterThanOrEqualTo(now()->subMinutes(30));
                                $loginIso = $session->login_at?->format('Y-m-d') ?? '';
                                $searchText = strtolower(trim($name . ' ' . $email . ' ' . $role . ' ' . ($session->browser ?? '') . ' ' . ($session->operating_system ?? '') . ' ' . ($session->device_type ?? '') . ' ' . ($session->ip_address ?? '') . ' ' . ($session->location ?? '')));
                            @endphp
                            <div
                                class="admin-consultation-row system-log-row"
                                data-search="{{ $searchText }}"
                                data-role="{{ $role }}"
                                data-status="{{ $isActive ? 'active' : 'ended' }}"
                                data-recent="{{ $isRecent ? '1' : '0' }}"
                                data-login-date="{{ $loginIso }}"
                                role="row"
                            >
                                <div class="admin-consultation-party">
                                    <div class="admin-consultation-primary">{{ $name }}</div>
                                    <div class="admin-consultation-secondary">ID: {{ $session->user_id }}{{ $email !== '' ? ' • ' . $email : '' }}</div>
                                </div>
                                <div class="system-log-cell-center">
                                    <span class="system-log-role role-{{ $role }}">{{ $roleLabel($role) }}</span>
                                </div>
                                <div class="admin-consultation-datetime">
                                    <div class="admin-consultation-date">{{ $session->login_at?->format('M d, Y') ?? '--' }}</div>
                                    <div class="admin-consultation-time">{{ $session->login_at?->format('h:i A') ?? '--' }}</div>
                                </div>
                                <div class="admin-consultation-datetime">
                                    <div class="admin-consultation-date">{{ $session->logout_at?->format('M d, Y') ?? 'Still active' }}</div>
                                    <div class="admin-consultation-time">
                                        {{ $session->logout_at?->format('h:i A') ?? 'Online now' }}
                                        @if (($session->logout_reason ?? '') === 'timeout')
                                            <span class="system-log-reason">Timeout</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="admin-consultation-type">
                                    <span class="admin-consultation-type-text">{{ $formatLogDuration($session) }}</span>
                                </div>
                                <div class="admin-consultation-party">
                                    <div class="admin-consultation-primary">{{ $session->device_type ?? 'Unknown Device' }}</div>
                                    <div class="admin-consultation-secondary">{{ $session->browser ?? 'Unknown Browser' }} / {{ $session->operating_system ?? 'Unknown OS' }}</div>
                                </div>
                                <div class="admin-consultation-party">
                                    <div class="admin-consultation-primary">{{ $session->ip_address ?? '--' }}</div>
                                    <div class="admin-consultation-secondary">{{ $session->location ?? 'Unknown' }}</div>
                                </div>
                                <div class="admin-consultation-status">
                                    @if ($isActive)
                                        <span class="system-log-status active">Active</span>
                                    @elseif ($isRecent)
                                        <span class="system-log-status recent">Recent</span>
                                    @else
                                        <span class="system-log-status ended">Ended</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="admin-consultation-empty">No user activity logs found.</div>
                        @endforelse
                    </div>
                </div>

                <div id="systemLogEmptyState" class="system-log-empty" style="display:none;">No matching activity logs.</div>

                <div id="systemLogPaginationContainer" class="system-log-pagination">
                    <div id="systemLogPaginationInfo" class="system-log-page-info">Showing 0 activity logs</div>
                    <div id="systemLogPaginationControls" class="system-log-page-controls">
                        <button type="button" id="prevSystemLogBtn" class="pagination-nav-btn" style="display:none;">&lsaquo;</button>
                        <div id="systemLogPageNumbers" class="system-log-page-numbers"></div>
                        <button type="button" id="nextSystemLogBtn" class="pagination-nav-btn" style="display:none;">&rsaquo;</button>
                    </div>
                </div>
            </div>
