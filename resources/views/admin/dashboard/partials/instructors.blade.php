            <div class="students-card is-hidden" id="instructorsSection">
                <div class="students-head">
                    <div class="students-title">Instructor Accounts</div>
                    <div class="students-controls students-controls-instructor">
                        <input type="text" class="students-search" id="instructorSearch" placeholder="Search by name or email...">
                        <select class="students-filter" id="instructorStatusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        <button type="button" class="students-btn" id="openAddInstructor">Add Instructor</button>
                        <button type="button" class="section-close-btn section-close-trigger" data-close-section="instructors" aria-label="Close instructors section">&times;</button>
                    </div>
                </div>

                <div class="table-scroll-shell">
                    <table class="students-table">
                        <thead>
                                <tr>
                                <th>User</th>
                                <th>Joined</th>
                                <th class="consultations-count-head">Consultations</th>
                                <th>Status</th>
                                <th>Online Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="instructorTableBody">
                            @forelse ($instructorRows as $instructor)
                                <tr data-status="{{ $instructor['status'] }}" data-search="{{ strtolower($instructor['name'] . ' ' . $instructor['email']) }}">
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar">{{ strtoupper(substr($instructor['name'], 0, 1)) }}</div>
                                            <div>
                                                <div class="student-name">{{ $instructor['name'] }}</div>
                                                <div class="student-email">{{ $instructor['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $instructor['joined'] }}</td>
                                    <td class="consultations-count-cell">{{ $instructor['consultations'] }}</td>
                                    <td><span class="status-tag status-{{ $instructor['status'] }}">{{ $instructor['status'] }}</span></td>
                                    <td>
                                        @php
                                            $instructorOnline = in_array($instructor['id'], (array) $onlineInstructorIds) || \App\Services\UserSessionService::isUserOnline($instructor['id']);
                                            $lastActiveMinutes = isset($instructorActiveMinutes[$instructor['id']])
                                                ? $instructorActiveMinutes[$instructor['id']]['last_active_minutes']
                                                : \App\Services\UserSessionService::getLastActiveMinutes($instructor['id']);
                                        @endphp
                                        @if ($instructorOnline)
                                            <span class="online-badge">Online</span>
                                        @elseif ($lastActiveMinutes !== null)
                                            <span class="user-active-minutes-badge">Active {{ $lastActiveMinutes }}{{ $lastActiveMinutes === 1 ? ' min' : ' mins' }} ago</span>
                                        @else
                                            <span style="color:var(--muted);font-size:11px;font-weight:700;">Offline</span>
                                        @endif
                                    </td>
                                    <td class="student-action-cell">
                                        <a href="#"
                                           class="manage-link manage-user-btn student-view-details-link"
                                           data-user-id="{{ $instructor['id'] }}"
                                           data-role="Instructor"
                                           data-name="{{ $instructor['name'] }}"
                                           data-email="{{ $instructor['email'] }}"
                                           data-meta="Instructor Account"
                                           data-joined="{{ $instructor['joined'] }}"
                                           data-consultations="{{ $instructor['consultations'] }}"
                                           data-status="{{ $instructor['status'] }}"
                                        ><span class="manage-label-desktop">Manage</span><span class="manage-label-mobile">View</span></a>
                                        <button
                                            type="button"
                                            class="manage-link add-schedule-btn"
                                            data-instructor-id="{{ $instructor['id'] }}"
                                            data-instructor-name="{{ $instructor['name'] }}"
                                            data-availability='@json(($instructorScheduleMap[$instructor['id']] ?? collect())->toArray())'
                                        >
                                            Add Schedule
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="color:var(--muted);text-align:center;">No instructor accounts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Instructor Pagination Controls -->
                <div id="instructorPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;padding:0 16px;">
                    <div id="instructorPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to 10 of 0 instructors
                    </div>
                    <div id="instructorPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevInstructorBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="instructorPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextInstructorBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">›</span>
                        </button>
                    </div>
                </div>
            </div>

