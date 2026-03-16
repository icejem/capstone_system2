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
                                    <form method="POST" action="{{ route('admin.instructors.demote', $instructor->id) }}">
                                        @csrf
                                        <button class="btn secondary" type="submit">Move to Student</button>
                                    </form>
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

<script>
const sidebar = document.getElementById('sidebar');
const menuBtn = document.getElementById('menuBtn');
if (menuBtn) {
    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
}
</script>
@endsection
