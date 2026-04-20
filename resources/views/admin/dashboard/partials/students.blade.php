            <div class="students-card is-hidden" id="studentsSection">
                <div class="students-head">
                    <div class="students-title">Student Accounts</div>
                    <div class="students-controls">
                        <input type="text" class="students-search" id="studentSearch" placeholder="Search by name, email, or ID...">
                        <select class="students-filter" id="studentAcademicYearFilter">
                            <option value="">All Academic Years</option>
                            @foreach (($studentAcademicYearOptions ?? collect()) as $academicYear)
                                <option value="{{ $academicYear }}">{{ $academicYear }}</option>
                            @endforeach
                        </select>
                        <select class="students-filter" id="studentSemesterFilter">
                            <option value="">All Semesters</option>
                            <option value="first">1st Semester</option>
                            <option value="second">2nd Semester</option>
                        </select>
                        <select class="students-filter" id="studentYearLevelFilter">
                            <option value="">All Year Levels</option>
                            @foreach (\App\Models\User::yearLevelLabels() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <select class="students-filter" id="studentStatusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                        <button
                            type="button"
                            class="students-btn"
                            id="studentCsvImportBtn"
                            title="Open student roster CSV import"
                        >
                            Import CSV
                        </button>
                        <button type="button" class="section-close-btn section-close-trigger" data-close-section="students" aria-label="Close students section">&times;</button>
                    </div>
                </div>

                <div class="table-scroll-shell">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Student ID</th>
                                <th>Year Level</th>
                                <th>Joined</th>
                                <th>Consultations</th>
                                <th>Status</th>
                                <th>Online Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            @forelse ($studentRows as $student)
                                <tr
                                    data-status="{{ $student['status'] }}"
                                    data-search="{{ strtolower($student['name'] . ' ' . $student['email'] . ' ' . $student['student_id'] . ' ' . ($student['year_level_label'] ?? '')) }}"
                                    data-year-level="{{ $student['year_level'] ?? '' }}"
                                    data-academic-years="{{ implode('|', $student['academic_years'] ?? []) }}"
                                    data-semesters="{{ implode('|', $student['semesters'] ?? []) }}"
                                    data-period-keys="{{ implode('|', $student['period_keys'] ?? []) }}"
                                >
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar">{{ strtoupper(substr($student['name'], 0, 1)) }}</div>
                                            <div>
                                                <div class="student-name">{{ $student['name'] }}</div>
                                                <div class="student-email">{{ $student['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="student-id-cell">{{ $student['student_id'] }}</td>
                                    <td>{{ $student['year_level_label'] ?? 'Not set' }}</td>
                                    <td>{{ $student['joined'] }}</td>
                                    <td style="font-weight:700">{{ $student['consultations'] }}</td>
                                    <td><span class="status-tag status-{{ $student['status'] }}">{{ $student['status'] }}</span></td>
                                    <td>
                                        @php
                                            $studentOnline = in_array($student['id'], (array) $onlineStudentIds) || \App\Services\UserSessionService::isUserOnline($student['id']);
                                            $lastActiveMinutes = isset($studentActiveMinutes[$student['id']])
                                                ? $studentActiveMinutes[$student['id']]['last_active_minutes']
                                                : \App\Services\UserSessionService::getLastActiveMinutes($student['id']);
                                        @endphp
                                        @if ($studentOnline)
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
                                           data-user-id="{{ $student['id'] }}"
                                           data-role="Student"
                                           data-name="{{ $student['name'] }}"
                                           data-email="{{ $student['email'] }}"
                                           data-meta="Student ID: {{ $student['student_id'] }} | Year Level: {{ $student['year_level_label'] ?? 'Not set' }}"
                                           data-joined="{{ $student['joined'] }}"
                                           data-consultations="{{ $student['consultations'] }}"
                                           data-status="{{ $student['status'] }}"
                                        ><span class="manage-label-desktop">Manage</span><span class="manage-label-mobile">View</span></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="color:var(--muted);text-align:center;">No student accounts found.</td>
                                </tr>
                            @endforelse
                            <tr id="studentEmptyState" style="display:none;">
                                <td colspan="8" style="color:var(--muted);text-align:center;">No students match the selected filters.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Student Pagination Controls -->
                <div id="studentPaginationContainer" style="display:flex;justify-content:space-between;align-items:center;margin-top:20px;gap:16px;flex-wrap:wrap;padding:0 16px;">
                    <div id="studentPaginationInfo" style="font-size:13px;color:var(--muted);font-weight:600;">
                        Showing 1 to 10 of 0 students
                    </div>
                    <div id="studentPaginationControls" style="display:flex;gap:8px;align-items:center;">
                        <button id="prevStudentBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">‹</span>
                        </button>
                        <div id="studentPageNumbers" style="display:flex;gap:4px;">
                            <!-- Page numbers will be generated by JavaScript -->
                        </div>
                        <button id="nextStudentBtn" class="pagination-nav-btn" style="display:none;">
                            <span style="font-size:16px;">›</span>
                        </button>
                    </div>
                </div>
            </div>

