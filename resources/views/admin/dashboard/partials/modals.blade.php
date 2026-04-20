<div class="details-modal" id="consultationDetailsModal" aria-hidden="true">
    <div class="details-dialog">
        <div class="details-header">
            <div>
                <div class="details-title">Consultation Details</div>
                <div class="details-subtitle" id="detailsSubtitle">Consultation session details</div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <a href="#" id="detailsExportBtn" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;justify-content:center;padding:10px 14px;border-radius:10px;background:#dbeafe;color:#1d4ed8;text-decoration:none;font-size:12px;font-weight:800;">Export PDF</a>
                <button type="button" class="details-close" id="closeConsultationDetailsModal">x</button>
            </div>
        </div>
        <div class="details-body">
            <div class="details-grid">
                <div class="details-card" id="detailsDate">Date & Time: --</div>
                <div class="details-card details-card-student" id="detailsStudent">
                    <span id="detailsStudentText">Student: --</span>
                    <span class="details-card-inline-id" id="detailsStudentInlineId">ID: --</span>
                </div>
                <div class="details-card" id="detailsStudentId">Student ID: --</div>
                <div class="details-card" id="detailsInstructor">Instructor: --</div>
                <div class="details-card" id="detailsMode">Mode: --</div>
                <div class="details-card" id="detailsType">Topic / Type: --</div>
                <div class="details-card" id="detailsDuration">Duration: --</div>
            </div>

            <div class="details-summary">
                <div class="details-summary-title">Summary</div>
                <div class="details-summary-text" id="detailsSummaryText">Summary not yet available.</div>
            </div>

            <div class="details-summary">
                <div class="details-summary-title">Action Taken</div>
                <div class="details-summary-text" id="detailsActionTakenText">Action taken not yet available.</div>
            </div>
        </div>
    </div>
</div>

<div class="manage-modal" id="manageUserModal" aria-hidden="true">
    <div class="manage-dialog">
        <div class="manage-head">
            <div class="manage-title">Manage User</div>
            <button type="button" class="manage-close" id="closeManageUserModal">x</button>
        </div>
        <div class="manage-body">
            <div class="manage-user">
                <div class="manage-avatar" id="manageAvatar">U</div>
                <div>
                    <div class="manage-name" id="manageName">—</div>
                    <div class="manage-email" id="manageEmail">—</div>
                    <div class="manage-meta" id="manageMeta">—</div>
                </div>
            </div>

            <div class="manage-row">
                <div class="manage-row-label">Role</div>
                <div class="manage-row-value" id="manageRole">—</div>
            </div>
            <div class="manage-row">
                <div class="manage-row-label">Joined Date</div>
                <div class="manage-row-value" id="manageJoined">—</div>
            </div>
            <div class="manage-row">
                <div class="manage-row-label">Total Consultations</div>
                <div class="manage-row-value" id="manageConsultations">0</div>
            </div>
            <div class="manage-row">
                <div class="manage-row-label">Current Status</div>
                <div><span class="status-tag status-active" id="manageCurrentStatus">active</span></div>
            </div>

            <div class="manage-actions-label">Change Status</div>
            <div class="manage-actions">
                <button type="button" class="manage-status-btn activate" data-status-value="active">Activate</button>
                <button type="button" class="manage-status-btn deactivate" data-status-value="inactive">Deactivate</button>
                <button type="button" class="manage-status-btn suspend" data-status-value="suspended">Suspend</button>
            </div>
        </div>
    </div>
</div>

<div class="status-confirm-modal" id="statusConfirmModal" aria-hidden="true">
    <div class="status-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="statusConfirmTitle">
        <div class="status-confirm-head">
            <div>
                <div class="status-confirm-kicker">Confirm Status Change</div>
                <div class="status-confirm-title" id="statusConfirmTitle">Update account status?</div>
            </div>
            <button type="button" class="status-confirm-close" id="closeStatusConfirmModal" aria-label="Close">x</button>
        </div>
        <div class="status-confirm-body">
            <p class="status-confirm-message" id="statusConfirmMessage">
                Please confirm this account status update.
            </p>
            <div class="status-confirm-user" id="statusConfirmUser">User: --</div>
        </div>
        <div class="status-confirm-actions">
            <button type="button" class="status-confirm-btn cancel" id="cancelStatusConfirm">Cancel</button>
            <button type="button" class="status-confirm-btn confirm" id="confirmStatusChange">Confirm</button>
        </div>
    </div>
</div>

<div class="add-modal" id="addInstructorModal" aria-hidden="true">
    <div class="add-dialog">
        <div class="add-head">
            <div class="add-title">Add Instructor</div>
            <button type="button" class="add-close" id="closeAddInstructor">x</button>
        </div>
        <div class="add-body">
            @if ($errors->any())
                <div class="add-alert">
                    <div style="font-weight:700;margin-bottom:6px;">Please fix the errors below.</div>
                    <ul style="margin:0;padding-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.instructors.store') }}">
                @csrf
                <div class="add-form-grid">
                    <div class="add-form-row">
                        <div>
                            <label class="add-label" for="add_first_name">First Name</label>
                            <input id="add_first_name" class="add-input" type="text" name="first_name" value="{{ old('first_name') }}" required>
                        </div>
                        <div>
                            <label class="add-label" for="add_last_name">Last Name</label>
                            <input id="add_last_name" class="add-input" type="text" name="last_name" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                    <div class="add-form-row">
                        <div>
                            <label class="add-label" for="add_middle_name">Middle Name</label>
                            <input id="add_middle_name" class="add-input" type="text" name="middle_name" value="{{ old('middle_name') }}">
                        </div>
                        <div>
                            <label class="add-label" for="add_email">Email</label>
                            <input id="add_email" class="add-input" type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    <div class="add-form-row">
                        <div>
                            <label class="add-label" for="add_phone_number">Mobile Number</label>
                            <input id="add_phone_number" class="add-input" type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="09171234567" maxlength="20" required>
                            <div style="margin-top:6px;font-size:12px;color:#64748b;">Used for SMS reminders and consultation notifications.</div>
                        </div>
                        <div>
                            <label class="add-label" for="add_password">Password</label>
                            <input id="add_password" class="add-input" type="password" name="password" required>
                        </div>
                    </div>
                    <div class="add-form-row">
                        <div>
                            <label class="add-label" for="add_password_confirmation">Confirm Password</label>
                            <input id="add_password_confirmation" class="add-input" type="password" name="password_confirmation" required>
                        </div>
                        <div></div>
                    </div>
                </div>
                <div class="add-actions">
                    <button type="button" class="manage-status-btn suspend" id="cancelAddInstructor">Cancel</button>
                    <button type="submit" class="manage-status-btn activate">Create Instructor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="add-modal" id="studentCsvImportModal" aria-hidden="true">
    <div class="add-dialog">
        <div class="add-head">
            <div class="add-title">Import Student CSV</div>
            <button type="button" class="add-close" id="closeStudentCsvImportModal">x</button>
        </div>
        <div class="add-body">
            <div class="add-alert" id="studentCsvImportAlert" style="display:none;"></div>

            <form id="studentCsvImportForm">
                <div class="add-form-grid">
                    <div class="add-form-row">
                        <div>
                            <label class="add-label" for="studentCsvAcademicYear">Academic Year</label>
                            <input id="studentCsvAcademicYear" class="add-input" type="text" name="academic_year" placeholder="e.g. 2026-2027" maxlength="9" required>
                        </div>
                        <div>
                            <label class="add-label" for="studentCsvSemester">Semester</label>
                            <select id="studentCsvSemester" class="add-input" name="semester" required>
                                <option value="">Select semester</option>
                                <option value="first">1st Semester</option>
                                <option value="second">2nd Semester</option>
                            </select>
                        </div>
                    </div>
                    <div class="add-form-row single">
                        <div>
                            <label class="add-label">CSV File</label>
                            <input type="file" id="studentCsvImportInput" accept=".csv,text/csv" style="display:none;">
                            <div class="student-import-file-row">
                                <button type="button" class="manage-status-btn suspend" id="studentCsvChooseFileBtn">Choose File</button>
                                <div class="student-import-file-name" id="studentCsvFileName">No file selected</div>
                            </div>
                            <div class="student-import-help">CSV must contain: <strong>student_id</strong>, <strong>first_name</strong>, <strong>last_name</strong>.</div>
                        </div>
                    </div>
                </div>
                <div class="add-actions">
                    <button type="button" class="manage-status-btn suspend" id="cancelStudentCsvImport">Cancel</button>
                    <button type="submit" class="manage-status-btn activate" id="saveStudentCsvImport">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="admin-notif-toast" id="adminNotifToast" aria-live="polite" aria-atomic="true">
    <div class="admin-notif-toast-head">
        <div>
            <p class="admin-notif-toast-title" id="adminNotifToastTitle">New Notification</p>
            <p class="admin-notif-toast-body" id="adminNotifToastBody">You have a new consultation update.</p>
        </div>
        <button class="admin-notif-toast-close" id="adminNotifToastClose" type="button" aria-label="Close notification">&times;</button>
    </div>
</div>

