@extends('layouts.app')

@section('title', 'Manage Instructors')

@section('content')
<style>
:root {
    --brand: #6f42c1;
    --brand-dark: #59339d;
    --brand-soft: #ede9fe;
    --bg: #f6f5fb;
    --surface: #ffffff;
    --text: #1f2937;
    --muted: #6b7280;
    --border: #e5e7eb;
    --shadow: 0 14px 32px rgba(79, 70, 229, 0.12);
}
* { box-sizing: border-box; }
body { margin: 0; font-family: "Inter", "Segoe UI", Tahoma, sans-serif; background: var(--bg); }

.dashboard { display: flex; min-height: 100vh; }
.sidebar {
    width: 260px;
    background: linear-gradient(180deg, #ffffff, #f7f4ff);
    box-shadow: 2px 0 14px rgba(0, 0, 0, 0.06);
    padding: 28px 0;
    position: fixed;
    inset: 0 auto 0 0;
    z-index: 20;
    display: flex;
    flex-direction: column;
}
.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 22px;
    margin-bottom: 36px;
    text-decoration: none;
    color: var(--text);
}
.logo-badge {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color: #fff;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 16px;
    box-shadow: 0 6px 14px rgba(111, 66, 193, 0.4);
}
.sidebar-logo-text { font-weight: 800; font-size: 14px; letter-spacing: 0.3px; }
.sidebar-menu { list-style: none; padding: 0; margin: 0; flex: 1; }
.sidebar-menu-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 22px;
    color: #4b5563;
    text-decoration: none;
    font-size: 14px;
    border-left: 4px solid transparent;
    transition: all 0.2s ease;
    border-radius: 0 12px 12px 0;
    margin: 4px 0;
}
.sidebar-menu-link:hover,
.sidebar-menu-link.active {
    background: var(--brand-soft);
    color: var(--brand);
    border-left-color: var(--brand);
    font-weight: 600;
}
.sidebar-logout {
    padding: 18px 22px;
    border-top: 1px solid var(--border);
}
.logout-btn {
    width: 100%;
    border: 1px solid #f5c2c7;
    background: #fdecec;
    color: #b02a37;
    padding: 11px 14px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.main { margin-left: 260px; flex: 1; min-width: 0; }
.topbar {
    background: var(--surface);
    padding: 14px 26px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    z-index: 10;
    backdrop-filter: blur(6px);
}
.topbar-left { display: flex; align-items: center; gap: 12px; font-weight: 700; }
.menu-btn {
    display: none;
    background: var(--brand-soft);
    border: 1px solid #ddd;
    padding: 8px 12px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: var(--brand);
}
.content { padding: 28px; }
.card {
    background: var(--surface);
    border-radius: 16px;
    padding: 20px;
    box-shadow: var(--shadow);
    margin-bottom: 20px;
}
.section-title {
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 14px;
}
.table {
    width: 100%;
    border-collapse: collapse;
}
.table th, .table td {
    text-align: left;
    padding: 10px 12px;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
}
.table th { color: var(--muted); font-weight: 700; }
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    background: var(--brand-soft);
    color: var(--brand);
}
.btn {
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 12px;
    cursor: pointer;
    border: none;
}
.btn.primary {
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color: #fff;
}
.btn.secondary {
    background: #fdecec;
    color: #b02a37;
    border: 1px solid #f5c2c7;
}
.btn.schedule {
    background: #eef2ff;
    color: #3730a3;
    border: 1px solid #c7d2fe;
}
.action-stack {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.availability-modal {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, 0.55);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    z-index: 90;
}
.availability-modal.is-open { display: flex; }
.availability-dialog {
    width: min(760px, 100%);
    background: #fff;
    border-radius: 14px;
    border: 1px solid var(--border);
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.22);
}
.availability-modal-header, .availability-modal-actions {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
}
.availability-modal-actions {
    border-bottom: none;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
.availability-modal-title { margin: 0; font-size: 18px; font-weight: 800; }
.availability-modal-subtitle { margin: 6px 0 0; font-size: 12px; color: var(--muted); }
.availability-modal-body { padding: 14px 16px; max-height: 62vh; overflow: auto; }
.availability-meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-bottom: 12px; }
.availability-meta label, .availability-day-name { font-size: 12px; font-weight: 700; color: var(--muted); }
.availability-meta input, .availability-meta select, .availability-time {
    width: 100%;
    margin-top: 6px;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 8px 10px;
}
.availability-row {
    display: grid;
    grid-template-columns: 120px 1fr 20px 1fr;
    align-items: center;
    gap: 8px;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 8px;
}
.availability-row.is-disabled .availability-time { opacity: 0.5; pointer-events: none; }
.availability-day {
    display: flex;
    align-items: center;
    gap: 8px;
}
@media (max-width: 640px) {
    .availability-meta { grid-template-columns: 1fr; }
    .availability-row { grid-template-columns: 1fr; }
}

@media (max-width: 900px) {
    .sidebar { width: 220px; }
    .main { margin-left: 220px; }
}
@media (max-width: 768px) {
    .sidebar { transform: translateX(-100%); transition: transform 0.25s ease; }
    .sidebar.open { transform: translateX(0); }
    .main { margin-left: 0; }
    .menu-btn { display: inline-flex; }
    .content { padding: 20px; }
}
</style>

<div class="dashboard">
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
            <span class="logo-badge">CS</span>
            <span class="sidebar-logo-text">Consultation Platform</span>
        </a>
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.dashboard') }}" class="sidebar-menu-link">Dashboard</a></li>
            <li><a href="{{ route('admin.instructors') }}" class="sidebar-menu-link active">Manage Instructors</a></li>
        </ul>
        <div class="sidebar-logout">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-btn" id="menuBtn">Menu</button>
                <div>Manage Instructors</div>
            </div>
        </div>

        <div class="content">
            @if (session('success'))
                <div style="margin-bottom:16px;background:#e9f7ef;color:#1e7e34;padding:10px 12px;border-radius:10px;border:1px solid #c6f1d9;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="section-title">Current Instructors</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($instructors as $instructor)
                            <tr>
                                <td>{{ $instructor->name }}</td>
                                <td>{{ $instructor->email }}</td>
                                <td><span class="badge">Instructor</span></td>
                                <td>
                                    <div class="action-stack">
                                        <button
                                            type="button"
                                            class="btn schedule add-schedule-btn"
                                            data-id="{{ $instructor->id }}"
                                            data-name="{{ $instructor->name }}"
                                            data-availability='@json(($instructorAvailabilities[$instructor->id] ?? collect())->toArray())'
                                        >
                                            Add Schedule
                                        </button>
                                        <form method="POST" action="{{ route('admin.instructors.demote', $instructor->id) }}">
                                            @csrf
                                            <button class="btn secondary" type="submit">Move to Student</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No instructors yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card">
                <div class="section-title">Promote Students</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->email }}</td>
                                <td><span class="badge">Student</span></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.instructors.promote', $student->id) }}">
                                        @csrf
                                        <button class="btn primary" type="submit">Make Instructor</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="availability-modal" id="availabilityModal" aria-hidden="true">
    <div class="availability-dialog">
        <div class="availability-modal-header">
            <h2 class="availability-modal-title">Set Availability</h2>
            <p class="availability-modal-subtitle" id="availabilityModalInstructorLabel">Instructor: --</p>
        </div>
        <form method="POST" id="adminScheduleForm">
            @csrf
            <div class="availability-modal-body">
                <div class="availability-meta">
                    <div>
                        <label for="availabilitySemester">Semester</label>
                        <select id="availabilitySemester" name="semester" required>
                            <option value="first">First Sem</option>
                            <option value="second">Second Sem</option>
                        </select>
                    </div>
                    <div>
                        <label for="availabilityAcademicYear">Academic Year</label>
                        <input id="availabilityAcademicYear" name="academic_year" type="text" value="{{ now()->format('Y') . '-' . now()->addYear()->format('Y') }}" pattern="\d{4}-\d{4}" required>
                    </div>
                </div>
                @foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                    <div class="availability-row is-disabled" data-day="{{ $day }}">
                        <label class="availability-day">
                            <input type="checkbox" class="availability-check" name="days[]" value="{{ $day }}">
                            <span class="availability-day-name">{{ ucfirst($day) }}</span>
                        </label>
                        <input class="availability-time availability-start" type="time" name="slot_times[{{ $day }}][]" value="08:00">
                        <span>to</span>
                        <input class="availability-time availability-end" type="time" name="end_times[{{ $day }}][]" value="09:00">
                    </div>
                @endforeach
            </div>
            <div class="availability-modal-actions">
                <button type="button" class="btn secondary" id="cancelAvailabilityModal">Cancel</button>
                <button type="submit" class="btn primary">Save Availability</button>
            </div>
        </form>
    </div>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const menuBtn = document.getElementById('menuBtn');
if (menuBtn) {
    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
}

const availabilityModal = document.getElementById('availabilityModal');
const cancelAvailabilityModal = document.getElementById('cancelAvailabilityModal');
const adminScheduleForm = document.getElementById('adminScheduleForm');
const modalInstructorLabel = document.getElementById('availabilityModalInstructorLabel');
const availabilitySemester = document.getElementById('availabilitySemester');
const availabilityAcademicYear = document.getElementById('availabilityAcademicYear');
const scheduleActionTemplate = @json(route('admin.instructors.schedule.store', ['user' => '__ID__']));
let selectedAvailabilityMap = {};

function updateDayRowState(row, checked) {
    row.classList.toggle('is-disabled', !checked);
    row.querySelectorAll('.availability-time').forEach((input) => {
        input.disabled = !checked;
    });
}

function resetAvailabilityForm() {
    const rows = availabilityModal.querySelectorAll('.availability-row');
    rows.forEach((row) => {
        const check = row.querySelector('.availability-check');
        const start = row.querySelector('.availability-start');
        const end = row.querySelector('.availability-end');
        if (check) check.checked = false;
        if (start) start.value = '08:00';
        if (end) end.value = '09:00';
        updateDayRowState(row, false);
    });
}

function fillAvailabilityFromSelection() {
    resetAvailabilityForm();
    const key = `${availabilitySemester.value}|${availabilityAcademicYear.value}`;
    const slots = Array.isArray(selectedAvailabilityMap[key]) ? selectedAvailabilityMap[key] : [];
    slots.forEach((slot) => {
        const row = availabilityModal.querySelector(`.availability-row[data-day="${slot.day}"]`);
        if (!row) return;
        const check = row.querySelector('.availability-check');
        const start = row.querySelector('.availability-start');
        const end = row.querySelector('.availability-end');
        if (check) check.checked = true;
        if (start && slot.start_time) start.value = slot.start_time;
        if (end && slot.end_time) end.value = slot.end_time;
        updateDayRowState(row, true);
    });
}

document.querySelectorAll('.availability-check').forEach((check) => {
    check.addEventListener('change', (event) => {
        const row = event.target.closest('.availability-row');
        if (!row) return;
        updateDayRowState(row, event.target.checked);
    });
});

document.querySelectorAll('.add-schedule-btn').forEach((button) => {
    button.addEventListener('click', () => {
        const instructorId = button.dataset.id;
        const instructorName = button.dataset.name || 'Instructor';
        selectedAvailabilityMap = {};
        try {
            selectedAvailabilityMap = JSON.parse(button.dataset.availability || '{}');
        } catch (error) {
            selectedAvailabilityMap = {};
        }
        modalInstructorLabel.textContent = `Instructor: ${instructorName}`;
        adminScheduleForm.action = scheduleActionTemplate.replace('__ID__', instructorId);
        fillAvailabilityFromSelection();
        availabilityModal.classList.add('is-open');
        availabilityModal.setAttribute('aria-hidden', 'false');
    });
});

[availabilitySemester, availabilityAcademicYear].forEach((field) => {
    field.addEventListener('change', () => {
        fillAvailabilityFromSelection();
    });
});

function closeAvailabilityModal() {
    availabilityModal.classList.remove('is-open');
    availabilityModal.setAttribute('aria-hidden', 'true');
}

if (cancelAvailabilityModal) {
    cancelAvailabilityModal.addEventListener('click', closeAvailabilityModal);
}

availabilityModal.addEventListener('click', (event) => {
    if (event.target === availabilityModal) {
        closeAvailabilityModal();
    }
});
</script>
@endsection
