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
                    <div class="system-log-toolbar-scroll">
                        <div class="system-log-toolbar-row">
                            <div class="system-log-toolbar-item system-log-toolbar-item-search">
                                <input type="search" id="systemLogSearch" class="students-search system-log-search" placeholder="Search user, role, browser, IP, location..." autocomplete="off" aria-label="Search system logs">
                            </div>
                            <div class="system-log-toolbar-item">
                                <select id="systemLogRoleFilter" class="students-filter" aria-label="Filter system logs by role">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="instructor">Instructor</option>
                                    <option value="student">Student</option>
                                </select>
                            </div>
                            <div class="system-log-toolbar-item">
                                <select id="systemLogStatusFilter" class="students-filter" aria-label="Filter system logs by session status">
                                    <option value="">All Sessions</option>
                                    <option value="active">Active</option>
                                    <option value="ended">Ended</option>
                                    <option value="recent">Recent Logins</option>
                                </select>
                            </div>
                            <div class="system-log-toolbar-item system-log-toolbar-item-date">
                                <input type="date" id="systemLogDateFrom" class="students-search system-log-date" aria-label="Date from">
                            </div>
                            <div class="system-log-toolbar-item system-log-toolbar-item-date">
                                <input type="date" id="systemLogDateTo" class="students-search system-log-date" aria-label="Date to">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="system-log-table-shell">
                    <table class="system-log-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Login</th>
                                <th>Logout</th>
                                <th>Duration</th>
                                <th>Device</th>
                                <th>IP / Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="systemLogTableBody">
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
                                <tr
                                    class="system-log-row"
                                    data-search="{{ $searchText }}"
                                    data-role="{{ $role }}"
                                    data-status="{{ $isActive ? 'active' : 'ended' }}"
                                    data-recent="{{ $isRecent ? '1' : '0' }}"
                                    data-login-date="{{ $loginIso }}"
                                >
                                    <td>
                                        <div class="system-log-user">
                                            <span class="system-log-avatar">{{ strtoupper(substr($name, 0, 1)) }}</span>
                                            <span>
                                                <strong>{{ $name }}</strong>
                                                <small>ID: {{ $session->user_id }}{{ $email !== '' ? ' - ' . $email : '' }}</small>
                                            </span>
                                        </div>
                                    </td>
                                    <td><span class="system-log-role role-{{ $role }}">{{ $roleLabel($role) }}</span></td>
                                    <td>{{ $session->login_at?->format('M d, Y h:i A') ?? '--' }}</td>
                                    <td>
                                        {{ $session->logout_at?->format('M d, Y h:i A') ?? 'Still active' }}
                                        @if (($session->logout_reason ?? '') === 'timeout')
                                            <span class="system-log-reason">Timeout</span>
                                        @endif
                                    </td>
                                    <td>{{ $formatLogDuration($session) }}</td>
                                    <td>
                                        <div class="system-log-device">
                                            <strong>{{ $session->device_type ?? 'Unknown Device' }}</strong>
                                            <small>{{ $session->browser ?? 'Unknown Browser' }} / {{ $session->operating_system ?? 'Unknown OS' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="system-log-device">
                                            <strong>{{ $session->ip_address ?? '--' }}</strong>
                                            <small>{{ $session->location ?? 'Unknown' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($isActive)
                                            <span class="system-log-status active">Active</span>
                                        @elseif ($isRecent)
                                            <span class="system-log-status recent">Recent</span>
                                        @else
                                            <span class="system-log-status ended">Ended</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="system-log-empty">No user activity logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
