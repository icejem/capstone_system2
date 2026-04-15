@extends('layouts.app')

@section('title', 'Notifications')
@section('hide_navigation', '1')

@section('content')
@php
    $userType = auth()->user()->user_type ?? 'student';
    $dashboardRoute = match ($userType) {
        'admin' => route('admin.dashboard'),
        'instructor' => route('instructor.dashboard'),
        default => route('student.dashboard'),
    };
    $brand = match ($userType) {
        'admin' => '#1f3a8a',
        'instructor' => '#17999e',
        default => '#1f3a8a',
    };
    $brandDark = match ($userType) {
        'admin' => '#1e40af',
        'instructor' => '#1a8a94',
        default => '#1e40af',
    };
    $brandSoft = match ($userType) {
        'admin' => '#dbeafe',
        'instructor' => '#e6f3f5',
        default => '#dbeafe',
    };
@endphp

<style>
:root {
    --brand: {{ $brand }};
    --brand-dark: {{ $brandDark }};
    --brand-soft: {{ $brandSoft }};
    --bg: #f6f5fb;
    --surface: #ffffff;
    --text: #1f2937;
    --muted: #6b7280;
    --border: #e5e7eb;
    --shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
}
* { box-sizing: border-box; }
body { margin: 0; font-family: "Inter", "Segoe UI", Tahoma, sans-serif; background: var(--bg); }

.page {
    max-width: 920px;
    margin: 0 auto;
    padding: 24px 20px 40px;
}
.toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
}
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 12px;
    background: #fff;
    box-shadow: var(--shadow);
    color: var(--text);
    text-decoration: none;
    font-weight: 700;
}
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
}
.title {
    font-size: 22px;
    font-weight: 800;
}
.btn {
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    border: none;
    background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    color: #fff;
}
.card {
    background: var(--surface);
    border-radius: 16px;
    padding: 18px;
    box-shadow: var(--shadow);
}
.item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    border-bottom: 1px solid var(--border);
    padding: 12px 0;
}
.item:last-child { border-bottom: none; }
.dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--brand);
    margin-top: 6px;
}
.item.unread { background: var(--brand-soft); border-radius: 10px; padding: 12px; }
.meta { color: var(--muted); font-size: 12px; }
.actions {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-link {
    background: none;
    border: none;
    color: var(--brand);
    font-weight: 700;
    cursor: pointer;
    font-size: 12px;
}
.pagination-wrap {
    margin-top: 18px;
    display: flex;
    justify-content: center;
}
.pagination-wrap nav > div:first-child {
    display: none;
}
.pagination-wrap svg {
    width: 18px;
    height: 18px;
}
.pagination-wrap span,
.pagination-wrap a {
    font-size: 13px;
}
</style>

<div class="page">
    <div class="toolbar">
        <a href="{{ $dashboardRoute }}" class="back-link">
            <span aria-hidden="true">&larr;</span>
            <span>Back to Dashboard</span>
        </a>
    </div>

    <div class="header">
        <div class="title">All Notifications</div>
        <form method="POST" action="{{ route('notifications.markAllRead') }}">
            @csrf
            <button type="submit" class="btn">Mark all read</button>
        </form>
    </div>

    @if (session('success'))
        <div style="margin-bottom:12px;background:#e9f7ef;color:#1e7e34;padding:10px 12px;border-radius:10px;border:1px solid #c6f1d9;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        @forelse ($notifications as $notification)
            <div class="item {{ $notification->is_read ? '' : 'unread' }}">
                <span class="dot"></span>
                <div>
                    <div style="font-weight:700">{{ $notification->title }}</div>
                    <div style="color:var(--muted);margin-top:4px">{{ $notification->message }}</div>
                    <div class="meta">{{ $notification->created_at?->diffForHumans() }}</div>
                </div>
                <div class="actions">
                    @if (!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="btn-link">Mark read</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="meta">No notifications yet.</div>
        @endforelse
    </div>

    @if ($notifications->hasPages())
        <div class="pagination-wrap">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
