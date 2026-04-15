
<x-guest-layout>
    <style>
        /* ─── Layout ─────────────────────────────────────────── */
        .auth-title { margin: 0; font-size: 30px; font-weight: 800; letter-spacing: -.3px; }
        .auth-sub { margin: 6px 0 16px; color: #64748b; font-size: 14px; }
        .auth-form-shell { display: grid; gap: 18px; max-width: 760px; margin: 0 auto; }

        /* ─── Panel ──────────────────────────────────────────── */
        .auth-panel {
            display: grid;
            gap: 18px;
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }
        .auth-panel-title { margin: 0 0 4px; font-size: 15px; font-weight: 800; color: #0f172a; }
        .auth-panel-sub   { margin: 0; font-size: 12px; color: #64748b; line-height: 1.55; max-width: 92%; }

        /* ─── Grid ───────────────────────────────────────────── */
        .auth-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }

        /* ─── Label row ──────────────────────────────────────── */
        .auth-label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #334155; }
        .auth-label-row {
            display: flex; align-items: center;
            justify-content: space-between; gap: 8px; margin-bottom: 6px;
        }
        .auth-label-row .auth-label { margin-bottom: 0; }

        /* ─── Badges ─────────────────────────────────────────── */
        .auth-badge {
            display: inline-flex; align-items: center; padding: 3px 9px;
            border-radius: 999px; background: #eef2ff; color: #4338ca;
            font-size: 11px; font-weight: 700; white-space: nowrap;
        }
        .auth-badge.optional { background: #f1f5f9; color: #64748b; }
        .auth-badge.profile  { background: #f0fdf4; color: #15803d; }

        /* ─── Input wrapper & input ──────────────────────────── */
        .auth-input-wrap { position: relative; }
        .auth-input {
            width: 100%; border: 1.5px solid #dbe3f0; border-radius: 12px;
            padding: 12px 12px; font-size: 14px; outline: none;
            background: #fff; color: #0f172a; box-sizing: border-box;
            transition: border-color .18s, box-shadow .18s, background .18s;
        }
        .auth-input::placeholder { color: #94a3b8; }
        .auth-input:focus        { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99,102,241,.14); }
        .auth-input.has-icon     { padding-left: 40px; }
        .auth-input.has-toggle   { padding-right: 44px; }
        .auth-input.has-icon.has-toggle { padding-left: 40px; padding-right: 44px; }

        /* State styles */
        .auth-input.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239,68,68,.12);
            background: #fff8f8;
            animation: shake .3s ease;
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%      { transform: translateX(-4px); }
            75%      { transform: translateX(4px); }
        }

        /* ─── Input left icon ────────────────────────────────── */
        .auth-input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 18px; height: 18px; color: #94a3b8; pointer-events: none;
            transition: color .18s;
        }
        .auth-input-wrap:focus-within .auth-input-icon { color: #6366f1; }
        .auth-input-wrap:has(.is-invalid) .auth-input-icon { color: #ef4444; }

        /* ─── Right status icon inside input ─────────────────── */
        .auth-status-icon {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            width: 18px; height: 18px; pointer-events: none;
            opacity: 0; transition: opacity .2s;
        }
        .auth-input.has-toggle ~ .auth-status-icon { right: 40px; }
        .auth-status-icon.is-error   { opacity: 1; color: #ef4444; }

        /* ─── Password toggle ────────────────────────────────── */
        .auth-password-toggle {
            position: absolute; top: 50%; right: 12px; transform: translateY(-50%);
            width: 22px; height: 22px; display: inline-flex; align-items: center;
            justify-content: center; padding: 0; border: 0; background: transparent;
            color: #64748b; cursor: pointer; opacity: .8; transition: opacity .15s, color .15s;
        }
        .auth-password-toggle:hover { opacity: 1; color: #6366f1; }
        .auth-password-toggle svg { width: 18px; height: 18px; display: block; }
        .auth-password-toggle .eye-off { display: none; }
        .auth-password-toggle.is-visible .eye-on  { display: none; }
        .auth-password-toggle.is-visible .eye-off { display: block; }

        /* ─── Feedback messages ──────────────────────────────── */
        .auth-feedback-wrap { min-height: 20px; margin-top: 5px; }

        .auth-error {
            display: none; align-items: flex-start; gap: 5px;
            color: #dc2626; font-size: 12px; font-weight: 600; line-height: 1.5;
            animation: fadeIn .18s ease;
        }
        .auth-error.visible { display: flex; }
        .auth-error-icon { flex-shrink: 0; width: 14px; height: 14px; margin-top: 1px; }

        .auth-success {
            display: none; align-items: center; gap: 5px;
            color: #15803d; font-size: 12px; font-weight: 600;
            animation: fadeIn .18s ease;
        }
        .auth-success.visible { display: flex; }
        .auth-success-icon { flex-shrink: 0; width: 14px; height: 14px; }

        .auth-helper {
            color: #64748b; font-size: 12px; line-height: 1.5;
            transition: opacity .2s;
        }
        /* Hide helper when there's active feedback */
        .auth-helper.has-feedback { display: none; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-3px); } to { opacity: 1; transform: translateY(0); } }

        /* ─── Student ID counter ─────────────────────────────── */
        .auth-id-counter {
            font-size: 11px; font-weight: 700; color: #94a3b8;
            text-align: right; margin-top: 4px; transition: color .2s;
        }
        .auth-id-counter.is-complete { color: #15803d; }
        .auth-id-counter.is-incomplete { color: #f59e0b; }

        /* ─── Password strength ──────────────────────────────── */
        .auth-strength-wrap { margin-top: 10px; display: none; }
        .auth-strength-wrap.visible { display: block; }
        .auth-strength-track {
            height: 5px; border-radius: 99px; background: #e2e8f0; overflow: hidden;
        }
        .auth-strength-fill {
            height: 100%; width: 0; border-radius: 99px;
            transition: width .35s ease, background .35s ease;
        }
        .auth-strength-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 5px;
        }
        .auth-strength-label { font-size: 11px; font-weight: 700; color: #94a3b8; }
        .auth-strength-tip   { font-size: 11px; color: #94a3b8; }

        [data-strength="1"] .auth-strength-fill  { width: 25%; background: #ef4444; }
        [data-strength="1"] .auth-strength-label { color: #dc2626; }
        [data-strength="2"] .auth-strength-fill  { width: 50%; background: #f97316; }
        [data-strength="2"] .auth-strength-label { color: #ea580c; }
        [data-strength="3"] .auth-strength-fill  { width: 75%; background: #eab308; }
        [data-strength="3"] .auth-strength-label { color: #ca8a04; }
        [data-strength="4"] .auth-strength-fill  { width: 100%; background: #22c55e; }
        [data-strength="4"] .auth-strength-label { color: #16a34a; }

        /* ─── Password rules ─────────────────────────────────── */
        .auth-password-rules {
            margin-top: 10px; padding: 12px 14px;
            border: 1.5px solid #e2e8f0; border-radius: 12px; background: #f8fafc;
        }
        .auth-password-rules-title { margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #0f172a; }
        .auth-rule-list { margin: 0; padding: 0; list-style: none; display: grid; gap: 6px; }
        .auth-rule-item {
            display: flex; align-items: center; gap: 7px;
            font-size: 12px; color: #94a3b8; transition: color .2s;
        }
        .auth-rule-item .rule-dot {
            flex-shrink: 0; width: 16px; height: 16px;
            border-radius: 50%; border: 1.5px solid #cbd5e1;
            display: inline-flex; align-items: center; justify-content: center;
            transition: background .2s, border-color .2s;
        }
        .auth-rule-item .rule-dot svg { display: none; width: 10px; height: 10px; color: #fff; }
        .auth-rule-item.is-met { color: #15803d; }
        .auth-rule-item.is-met .rule-dot { background: #22c55e; border-color: #22c55e; }
        .auth-rule-item.is-met .rule-dot svg { display: block; }
        .auth-rule-item.is-fail { color: #dc2626; }
        .auth-rule-item.is-fail .rule-dot { background: #fee2e2; border-color: #ef4444; }

        /* ─── Submit button ──────────────────────────────────── */
        .auth-btn {
            width: 100%; border: none; border-radius: 14px; padding: 14px;
            margin-top: 12px; font-size: 15px; font-weight: 800; color: #fff;
            background: linear-gradient(135deg, #6f42c1 0%, #4f46e5 100%);
            cursor: pointer; transition: filter .15s, transform .12s, box-shadow .15s;
            letter-spacing: .01em;
        }
        .auth-btn:hover:not(:disabled) {
            filter: brightness(1.08); transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99,102,241,.32);
        }
        .auth-btn:active:not(:disabled) { transform: translateY(0); box-shadow: none; }
        .auth-btn:disabled { opacity: .5; cursor: not-allowed; }

        /* ─── Banner ─────────────────────────────────────────── */
        .auth-banner {
            display: none; gap: 10px; align-items: flex-start;
            padding: 12px 14px; border-radius: 14px;
            border: 1.5px solid #fecaca; background: #fef2f2;
            color: #b91c1c; font-size: 12px; font-weight: 700; line-height: 1.55;
        }
        .auth-banner.active { display: flex; }
        .auth-banner-icon { flex-shrink: 0; width: 16px; height: 16px; margin-top: 1px; }

        /* ─── Consent ────────────────────────────────────────── */
        .auth-consent-wrap { display: grid; gap: 8px; }
        .auth-consent-check {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 14px; border: 1.5px solid #e2e8f0; border-radius: 14px;
            background: #f8fafc; font-size: 12px; color: #334155; line-height: 1.6;
            cursor: pointer; transition: border-color .15s, background .15s;
        }
        .auth-consent-check:hover { border-color: #6366f1; background: #fafafe; }
        .auth-consent-check.is-invalid { border-color: #fecaca; background: #fff8f8; }
        .auth-consent-check input { margin-top: 3px; accent-color: #6366f1; cursor: pointer; flex-shrink: 0; }
        .auth-consent-check strong { color: #0f172a; }
        .auth-legal-link {
            border: 0; background: transparent; padding: 0;
            color: #6366f1; font-weight: 700; text-decoration: underline;
            cursor: pointer; font: inherit;
        }
        .auth-legal-link:hover { color: #4338ca; }
        .auth-legal-summary { font-size: 12px; color: #64748b; line-height: 1.6; margin-top: 4px; }
        .auth-foot { margin-top: 12px; text-align: center; color: #64748b; font-size: 13px; }
        .auth-link { color: #6f42c1; text-decoration: none; font-size: 13px; font-weight: 700; }
        .auth-link:hover { text-decoration: underline; }

        /* ─── Legal Modal ────────────────────────────────────── */
        .legal-modal-shell {
            position: fixed; inset: 0; z-index: 1500;
            display: none; align-items: center; justify-content: center; padding: 18px;
        }
        .legal-modal-shell.active { display: flex; }
        .legal-modal-backdrop { position: absolute; inset: 0; background: rgba(15,23,42,.52); backdrop-filter: blur(4px); }
        .legal-modal-card {
            position: relative; width: min(720px,100%);
            max-height: calc(100vh - 36px); overflow: hidden;
            border-radius: 20px; border: 1px solid rgba(148,163,184,.26);
            background: #fff; box-shadow: 0 24px 70px rgba(15,23,42,.22);
        }
        .legal-modal-head {
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
        }
        .legal-modal-title { margin: 0; font-size: 17px; font-weight: 800; color: #0f172a; }
        .legal-modal-close {
            width: 36px; height: 36px; border-radius: 10px; border: 1px solid #dbe3f0;
            background: #fff; color: #475569; font-size: 20px; line-height: 1;
            cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
            transition: background .15s, color .15s, border-color .15s;
        }
        .legal-modal-close:hover { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
        .legal-modal-body {
            max-height: calc(100vh - 120px); overflow-y: auto;
            padding: 18px 20px 22px; color: #475569; font-size: 13px; line-height: 1.7;
        }
        .legal-modal-panel { display: none; }
        .legal-modal-panel.active { display: block; }
        .legal-modal-body p { margin: 0 0 14px; }
        .legal-modal-body p:last-child { margin-bottom: 0; }
    </style>

    <h2 class="auth-title">Create Account</h2>
    <p class="auth-sub">Fill out each field — errors appear instantly so you can fix them right away.</p>

    <form method="POST" action="{{ route('register') }}" novalidate data-live-validate="register" autocomplete="off">
        @csrf

        <div class="auth-form-shell">

            {{-- ── Error Banner ──────────────────────────────────── --}}
            <div class="auth-banner" data-register-banner aria-live="polite">
                <svg class="auth-banner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span data-banner-text></span>
            </div>

            {{-- ── Student Information ───────────────────────────── --}}
            <div class="auth-panel">
                <h3 class="auth-panel-title">Student Information</h3>
                <p class="auth-panel-sub">Use your real name and details exactly as they appear on your school records.</p>

                <div class="auth-grid">

                    {{-- First Name --}}
                    <div>
                        <div class="auth-label-row">
                            <label class="auth-label" for="first_name">First Name</label>
                            <span class="auth-badge">Required</span>
                        </div>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input id="first_name" name="first_name" type="text"
                                class="auth-input has-icon @error('first_name') is-invalid @enderror"
                                value="{{ old('first_name') }}" required autofocus
                                autocomplete="given-name" maxlength="60"
                                data-label="First name" data-rule="name"
                                placeholder="e.g. Maria"
                                aria-describedby="first_name_fb"
                                aria-invalid="@error('first_name') true @else false @enderror">
                            {{-- Status icon --}}
                            <svg class="auth-status-icon" data-status-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <polyline class="icon-check" points="20 6 9 17 4 12" style="display:none"/>
                                <g class="icon-x" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></g>
                            </svg>
                        </div>
                        <div class="auth-feedback-wrap" id="first_name_fb" aria-live="polite">
                            <div class="auth-error" data-error-for="first_name">
                                <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>@error('first_name'){{ $message }}@enderror</span>
                            </div>
                            <div class="auth-success" data-success-for="first_name">
                                <svg class="auth-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span></span>
                            </div>
                            <div class="auth-helper" data-helper-for="first_name">Enter your given name as it appears on your school records.</div>
                        </div>
                    </div>

                    {{-- Last Name --}}
                    <div>
                        <div class="auth-label-row">
                            <label class="auth-label" for="last_name">Last Name</label>
                            <span class="auth-badge">Required</span>
                        </div>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input id="last_name" name="last_name" type="text"
                                class="auth-input has-icon @error('last_name') is-invalid @enderror"
                                value="{{ old('last_name') }}" required
                                autocomplete="family-name" maxlength="60"
                                data-label="Last name" data-rule="name"
                                placeholder="e.g. Santos"
                                aria-describedby="last_name_fb"
                                aria-invalid="@error('last_name') true @else false @enderror">
                            <svg class="auth-status-icon" data-status-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <polyline class="icon-check" points="20 6 9 17 4 12" style="display:none"/>
                                <g class="icon-x" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></g>
                            </svg>
                        </div>
                        <div class="auth-feedback-wrap" id="last_name_fb" aria-live="polite">
                            <div class="auth-error" data-error-for="last_name">
                                <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>@error('last_name'){{ $message }}@enderror</span>
                            </div>
                            <div class="auth-success" data-success-for="last_name">
                                <svg class="auth-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span></span>
                            </div>
                            <div class="auth-helper" data-helper-for="last_name">Your surname or family name as recorded in school.</div>
                        </div>
                    </div>

                    {{-- Middle Name --}}
                    <div>
                        <div class="auth-label-row">
                            <label class="auth-label" for="middle_name">Middle Name</label>
                            <span class="auth-badge optional">Optional</span>
                        </div>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input id="middle_name" name="middle_name" type="text"
                                class="auth-input has-icon @error('middle_name') is-invalid @enderror"
                                value="{{ old('middle_name') }}"
                                autocomplete="additional-name" maxlength="60"
                                data-label="Middle name" data-rule="name" data-optional="true"
                                placeholder="e.g. Reyes (leave blank if none)"
                                aria-describedby="middle_name_fb"
                                aria-invalid="@error('middle_name') true @else false @enderror">
                            <svg class="auth-status-icon" data-status-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <polyline class="icon-check" points="20 6 9 17 4 12" style="display:none"/>
                                <g class="icon-x" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></g>
                            </svg>
                        </div>
                        <div class="auth-feedback-wrap" id="middle_name_fb" aria-live="polite">
                            <div class="auth-error" data-error-for="middle_name">
                                <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>@error('middle_name'){{ $message }}@enderror</span>
                            </div>
                            <div class="auth-success" data-success-for="middle_name">
                                <svg class="auth-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span></span>
                            </div>
                            <div class="auth-helper" data-helper-for="middle_name">Leave this blank if you don't use a middle name.</div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <div class="auth-label-row">
                            <label class="auth-label" for="email">Email Address</label>
                            <span class="auth-badge">Required</span>
                        </div>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/></svg>
                            <input id="email" name="email" type="email"
                                class="auth-input has-icon @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required
                                autocomplete="username" maxlength="254"
                                placeholder="you@gmail.com"
                                data-label="Email" data-rule="email"
                                aria-describedby="email_fb"
                                aria-invalid="@error('email') true @else false @enderror">
                            <svg class="auth-status-icon" data-status-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <polyline class="icon-check" points="20 6 9 17 4 12" style="display:none"/>
                                <g class="icon-x" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></g>
                            </svg>
                        </div>
                        <div class="auth-feedback-wrap" id="email_fb" aria-live="polite">
                            <div class="auth-error" data-error-for="email">
                                <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>@error('email'){{ $message }}@enderror</span>
                            </div>
                            <div class="auth-success" data-success-for="email">
                                <svg class="auth-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span></span>
                            </div>
                            <div class="auth-helper" data-helper-for="email">Use an active Gmail address — not a school lab or shared account.</div>
                        </div>
                    </div>

                    <input type="hidden" name="user_type" value="student">

                    {{-- Student ID --}}
                    <div>
                        <div class="auth-label-row">
                            <label class="auth-label" for="student_id">Student ID</label>
                            <span class="auth-badge">Required</span>
                        </div>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <input id="student_id" name="student_id" type="text"
                                class="auth-input has-icon @error('student_id') is-invalid @enderror"
                                value="{{ old('student_id') }}" required
                                autocomplete="off" inputmode="numeric"
                                maxlength="8" placeholder="e.g. 20240001"
                                data-label="Student ID" data-rule="student_id"
                                aria-describedby="student_id_fb"
                                aria-invalid="@error('student_id') true @else false @enderror">
                            <svg class="auth-status-icon" data-status-icon viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <polyline class="icon-check" points="20 6 9 17 4 12" style="display:none"/>
                                <g class="icon-x" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></g>
                            </svg>
                        </div>
                        {{-- Live digit counter --}}
                        <div class="auth-id-counter" data-id-counter aria-live="polite">0 / 8 digits</div>
                        <div class="auth-feedback-wrap" id="student_id_fb" aria-live="polite">
                            <div class="auth-error" data-error-for="student_id">
                                <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>@error('student_id'){{ $message }}@enderror</span>
                            </div>
                            <div class="auth-success" data-success-for="student_id">
                                <svg class="auth-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span></span>
                            </div>
                            <div class="auth-helper" data-helper-for="student_id">Numbers only. Must be exactly 8 digits.</div>
                        </div>
                    </div>

                    {{-- Year Level --}}
                    <div>
                        <div class="auth-label-row">
                            <label class="auth-label" for="yearlevel">Year Level</label>
                            <span class="auth-badge profile">Profile</span>
                        </div>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 10l-6-6H8l-6 6 10 6 10-6z"/><path d="M2 16l10 6 10-6"/><path d="M6 10v4c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2v-4"/></svg>
                            <select id="yearlevel" name="yearlevel"
                                class="auth-input has-icon @error('yearlevel') is-invalid @enderror"
                                data-label="Year level" data-rule="yearlevel"
                                aria-describedby="yearlevel_fb"
                                aria-invalid="@error('yearlevel') true @else false @enderror">
                                <option value="">— Select your year level —</option>
                                <option value="1st Year" @selected(old('yearlevel') === '1st Year')>1st Year</option>
                                <option value="2nd Year" @selected(old('yearlevel') === '2nd Year')>2nd Year</option>
                                <option value="3rd Year" @selected(old('yearlevel') === '3rd Year')>3rd Year</option>
                                <option value="4th Year" @selected(old('yearlevel') === '4th Year')>4th Year</option>
                            </select>
                        </div>
                        <div class="auth-feedback-wrap" id="yearlevel_fb" aria-live="polite">
                            <div class="auth-error" data-error-for="yearlevel">
                                <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>@error('yearlevel'){{ $message }}@enderror</span>
                            </div>
                            <div class="auth-success" data-success-for="yearlevel">
                                <svg class="auth-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span></span>
                            </div>
                            <div class="auth-helper" data-helper-for="yearlevel">Personalizes your experience. You can change this later in your profile.</div>
                        </div>
                    </div>

                </div>{{-- /auth-grid --}}
            </div>{{-- /panel --}}

            {{-- ── Account Security ──────────────────────────────── --}}
            <div class="auth-panel">
                <h3 class="auth-panel-title">Account Security</h3>
                <p class="auth-panel-sub">Create a strong, unique password. Every requirement is checked in real time as you type.</p>

                <div class="auth-grid">

                    {{-- Password --}}
                    <div>
                        <div class="auth-label-row">
                            <label class="auth-label" for="password">Password</label>
                            <span class="auth-badge">Required</span>
                        </div>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><circle cx="12" cy="16" r="1"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input id="password" name="password" type="password"
                                class="auth-input has-icon has-toggle @error('password') is-invalid @enderror"
                                required autocomplete="new-password" maxlength="128"
                                data-label="Password" data-rule="password"
                                aria-describedby="password_fb"
                                aria-invalid="@error('password') true @else false @enderror">
                            <button type="button" class="auth-password-toggle" data-toggle-password data-target="password" aria-label="Show password" aria-pressed="false">
                                <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6.5 0-10-7-10-7a21.77 21.77 0 0 1 5.06-5.94"/><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c6.5 0 10 7 10 7a21.8 21.8 0 0 1-3.32 4.61"/><path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"/><path d="M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <div class="auth-feedback-wrap" id="password_fb" aria-live="polite">
                            <div class="auth-error" data-error-for="password">
                                <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>@error('password'){{ $message }}@enderror</span>
                            </div>
                            <div class="auth-success" data-success-for="password">
                                <svg class="auth-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span></span>
                            </div>
                        </div>

                        {{-- Strength meter --}}
                        <div class="auth-strength-wrap" data-strength-wrap>
                            <div class="auth-strength-track"><div class="auth-strength-fill" data-strength-fill></div></div>
                            <div class="auth-strength-row">
                                <span class="auth-strength-label" data-strength-label>—</span>
                                <span class="auth-strength-tip" data-strength-tip></span>
                            </div>
                        </div>

                        {{-- Requirements checklist --}}
                        <div class="auth-password-rules">
                            <p class="auth-password-rules-title">Password requirements</p>
                            <ul class="auth-rule-list">
                                <li class="auth-rule-item" data-password-rule="length">
                                    <span class="rule-dot"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="10 3 5 9 2 6"/></svg></span>
                                    At least 8 characters
                                </li>
                                <li class="auth-rule-item" data-password-rule="max">
                                    <span class="rule-dot"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="10 3 5 9 2 6"/></svg></span>
                                    No more than 128 characters
                                </li>
                                <li class="auth-rule-item" data-password-rule="lower">
                                    <span class="rule-dot"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="10 3 5 9 2 6"/></svg></span>
                                    One lowercase letter (a–z)
                                </li>
                                <li class="auth-rule-item" data-password-rule="upper">
                                    <span class="rule-dot"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="10 3 5 9 2 6"/></svg></span>
                                    One uppercase letter (A–Z)
                                </li>
                                <li class="auth-rule-item" data-password-rule="number">
                                    <span class="rule-dot"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="10 3 5 9 2 6"/></svg></span>
                                    One number (0–9)
                                </li>
                                <li class="auth-rule-item" data-password-rule="special">
                                    <span class="rule-dot"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="10 3 5 9 2 6"/></svg></span>
                                    One special character (!@#$%^&amp;*…)
                                </li>
                                <li class="auth-rule-item" data-password-rule="no_sequence">
                                    <span class="rule-dot"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="10 3 5 9 2 6"/></svg></span>
                                    No common sequences (123456, qwerty…)
                                </li>
                                <li class="auth-rule-item" data-password-rule="no_personal">
                                    <span class="rule-dot"><svg viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="10 3 5 9 2 6"/></svg></span>
                                    Does not contain your name or email
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <div class="auth-label-row">
                            <label class="auth-label" for="password_confirmation">Confirm Password</label>
                            <span class="auth-badge">Required</span>
                        </div>
                        <div class="auth-input-wrap">
                            <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><circle cx="12" cy="16" r="1"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="auth-input has-icon has-toggle @error('password_confirmation') is-invalid @enderror"
                                required autocomplete="new-password" maxlength="128"
                                data-label="Password confirmation" data-rule="password_confirmation"
                                aria-describedby="password_confirmation_fb"
                                aria-invalid="@error('password_confirmation') true @else false @enderror">
                            <button type="button" class="auth-password-toggle" data-toggle-password data-target="password_confirmation" aria-label="Show confirmation" aria-pressed="false">
                                <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6.5 0-10-7-10-7a21.77 21.77 0 0 1 5.06-5.94"/><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c6.5 0 10 7 10 7a21.8 21.8 0 0 1-3.32 4.61"/><path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"/><path d="M3 3l18 18"/></svg>
                            </button>
                        </div>
                        <div class="auth-feedback-wrap" id="password_confirmation_fb" aria-live="polite">
                            <div class="auth-error" data-error-for="password_confirmation">
                                <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>@error('password_confirmation'){{ $message }}@enderror</span>
                            </div>
                            <div class="auth-success" data-success-for="password_confirmation">
                                <svg class="auth-success-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span></span>
                            </div>
                            <div class="auth-helper" data-helper-for="password_confirmation">Re-type your password exactly. Pasting is disabled for security.</div>
                        </div>
                    </div>

                </div>{{-- /auth-grid --}}
            </div>{{-- /panel --}}

            {{-- ── Consent ───────────────────────────────────────── --}}
            <div class="auth-panel">
                <h3 class="auth-panel-title">Consent &amp; Agreements</h3>
                <p class="auth-panel-sub">Read and accept both documents before your account can be created.</p>

                <div class="auth-consent-wrap">
                    <label class="auth-consent-check" for="terms_accepted">
                        <input id="terms_accepted" type="checkbox" name="terms_accepted" value="1" data-legal-checkbox="terms" @checked(old('terms_accepted'))>
                        <span><strong>I have read and agree</strong> to the <button type="button" class="auth-legal-link" data-open-legal="terms">Terms and Conditions</button>.</span>
                    </label>
                    <div class="auth-error" data-error-for="terms_accepted" aria-live="polite">
                        <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>@error('terms_accepted'){{ $message }}@enderror</span>
                    </div>

                    <label class="auth-consent-check" for="privacy_accepted">
                        <input id="privacy_accepted" type="checkbox" name="privacy_accepted" value="1" data-legal-checkbox="privacy" @checked(old('privacy_accepted'))>
                        <span><strong>I have read and agree</strong> to the <button type="button" class="auth-legal-link" data-open-legal="privacy">Privacy Policy</button>.</span>
                    </label>
                    <div class="auth-error" data-error-for="privacy_accepted" aria-live="polite">
                        <svg class="auth-error-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>@error('privacy_accepted'){{ $message }}@enderror</span>
                    </div>

                    <p class="auth-legal-summary">Both documents must be accepted before your account is created.</p>
                </div>
            </div>

        </div>{{-- /form-shell --}}

        <button type="submit" class="auth-btn" data-submit-register disabled>Create My Account</button>

        <div class="auth-foot">
            Already have an account?
            <a class="auth-link" href="{{ route('login') }}">Log in here</a>
        </div>
    </form>

    {{-- ── Legal Modal ───────────────────────────────────────── --}}
    <div class="legal-modal-shell" id="legalModal" aria-hidden="true">
        <div class="legal-modal-backdrop" data-close-legal></div>
        <div class="legal-modal-card" role="dialog" aria-modal="true" aria-labelledby="legalModalTitle">
            <div class="legal-modal-head">
                <h3 class="legal-modal-title" id="legalModalTitle">Terms and Conditions</h3>
                <button type="button" class="legal-modal-close" data-close-legal aria-label="Close">&times;</button>
            </div>
            <div class="legal-modal-body">
                <article class="legal-modal-panel active" data-legal-panel="terms">
                    <p><strong>User Terms and Conditions</strong></p>
                    <p><strong>Account Information</strong><br>Users must provide accurate and complete information when creating an account. Any false or misleading information may result in restricted access or account suspension.</p>
                    <p><strong>Account Responsibility</strong><br>Users are responsible for keeping their login credentials confidential. Any activity performed under a registered account is the responsibility of the account owner.</p>
                    <p><strong>Proper Use of the System</strong><br>The system must only be used for academic and consultation-related purposes. Users must communicate respectfully and avoid inappropriate language, misuse, or unauthorized activities.</p>
                    <p><strong>System Availability</strong><br>The system may occasionally undergo maintenance or temporary interruptions. Users understand that access may not always be available during these periods.</p>
                    <p><strong>Suspension of Access</strong><br>The administrator reserves the right to suspend or terminate access if a user violates system policies or performs actions that affect security and reliability.</p>
                </article>
                <article class="legal-modal-panel" data-legal-panel="privacy">
                    <p><strong>Privacy Policy</strong></p>
                    <p><strong>Collection of Information</strong><br>The system collects personal information such as name, email address, and account details to support registration, consultation scheduling, and communication between users.</p>
                    <p><strong>Use of Information</strong><br>Collected information is used only for academic consultation purposes, system management, and improving the user experience within the platform.</p>
                    <p><strong>Protection of Data</strong><br>Personal data is protected through appropriate security measures to prevent unauthorized access, loss, or misuse.</p>
                    <p><strong>Access to Information</strong><br>Only authorized administrators and permitted users may access personal information when necessary for system operations.</p>
                    <p><strong>Policy Updates</strong><br>The system administration may update this Privacy Policy when needed. Users will be informed of significant changes affecting the handling of personal information.</p>
                </article>
            </div>
        </div>
    </div>

    <script>
    (function () {
        'use strict';

        const form = document.querySelector('[data-live-validate="register"]');
        if (!form) return;

        // ── DOM refs ──────────────────────────────────────────────────────────
        const legalModal      = document.getElementById('legalModal');
        const legalModalTitle = document.getElementById('legalModalTitle');
        const submitBtn       = form.querySelector('[data-submit-register]');
        const banner          = form.querySelector('[data-register-banner]');
        const bannerText      = form.querySelector('[data-banner-text]');
        const legalOpenBtns   = Array.from(document.querySelectorAll('[data-open-legal]'));
        const legalCloseBtns  = Array.from(document.querySelectorAll('[data-close-legal]'));
        const legalPanels     = Array.from(document.querySelectorAll('[data-legal-panel]'));
        const legalCheckboxes = Array.from(form.querySelectorAll('[data-legal-checkbox]'));
        const ruleItems       = Array.from(form.querySelectorAll('[data-password-rule]'));
        const inputs          = Array.from(form.querySelectorAll('.auth-input[name]'));
        const strengthWrap    = form.querySelector('[data-strength-wrap]');
        const strengthLabel   = form.querySelector('[data-strength-label]');
        const strengthTip     = form.querySelector('[data-strength-tip]');
        const idCounter       = form.querySelector('[data-id-counter]');

        // ── Tracking ──────────────────────────────────────────────────────────
        // "started" = user has typed at least 1 character (show errors right away)
        // "blurred" = user has left the field (show "required" errors too)
        const fieldState = new WeakMap(); // { started: bool, blurred: bool }

        const getState  = (el) => fieldState.get(el) || { started: false, blurred: false };
        const setState  = (el, patch) => fieldState.set(el, { ...getState(el), ...patch });

        // ── Patterns & constants ──────────────────────────────────────────────
        const NAME_PATTERN     = /^(?=.*\p{L})[\p{L}\s''-]+$/u;
        const GMAIL_PATTERN    = /^[a-zA-Z0-9](?:[a-zA-Z0-9._%+\-]{0,61}[a-zA-Z0-9])?@gmail\.com$/i;
        const VALID_YEAR_LEVELS = new Set(['1st Year','2nd Year','3rd Year','4th Year']);
        const COMMON_SEQUENCES = [
            'password','12345678','123456789','abcdefgh','qwertyui','asdfghjk',
            'zxcvbnm','iloveyou','sunshine','princess','dragon','monkey',
            '11111111','22222222','33333333','00000000','99999999',
        ];
        const STRENGTH_LABELS = ['','Weak — try adding more variety','Fair — getting better','Good — almost there!','Strong — great password!'];
        const STRENGTH_TIPS   = ['','Add uppercase, numbers & symbols','Add uppercase or special chars','Add a special character or more length',''];

        // ── Utilities ─────────────────────────────────────────────────────────
        const trim          = (v) => v.replace(/\s+/gu,' ').trim();
        const normName      = (v) => v.replace(/[^\p{L}]/gu,'').toLowerCase();
        const countVowels   = (v) => (v.match(/[aeiouy]/gu)||[]).length;
        const toTitleCase   = (v) => trim(v).split(/\s+/).map((w) => w.replace(/^(\p{L})/u,(c)=>c.toUpperCase())).join(' ');

        const longestConsonantRun = (v) => {
            let max=0, cur=0;
            for (const ch of v) {
                if (/[aeiouy]/iu.test(ch)) { cur=0; continue; }
                if (++cur>max) max=cur;
            }
            return max;
        };

        const isAllSameDigit = (v) => /^(\d)\1+$/.test(v);
        const isSequential   = (v) => {
            const ASC='01234567890123456789', DESC='98765432109876543210';
            return ASC.includes(v) || DESC.includes(v);
        };

        // ── Password rule evaluator ───────────────────────────────────────────
        const evalPwdRules = (value) => ({
            length:  value.length >= 8,
            max:     value.length <= 128,
            lower:   /[a-z]/.test(value),
            upper:   /[A-Z]/.test(value),
            number:  /\d/.test(value),
            special: /[^A-Za-z0-9]/.test(value),
        });

        const evalPwdAdvisories = (value) => {
            const fn    = (form.querySelector('[name="first_name"]')?.value || '').toLowerCase();
            const ln    = (form.querySelector('[name="last_name"]')?.value || '').toLowerCase();
            const email = (form.querySelector('[name="email"]')?.value || '').toLowerCase();
            const local = email.split('@')[0];
            const vl    = value.toLowerCase();

            return {
                no_sequence: !COMMON_SEQUENCES.some((s) => vl.includes(s)),
                no_personal:
                    !(fn.length >= 3 && vl.includes(fn.slice(0, 4))) &&
                    !(ln.length >= 3 && vl.includes(ln.slice(0, 4))) &&
                    !(local.length >= 4 && vl.includes(local.slice(0, 5))),
            };
        };

        const pwdStrength = (value) => {
            if (!value) return 0;
            const r = evalPwdRules(value);
            const core = [r.length,r.lower,r.upper,r.number,r.special].filter(Boolean).length;
            if (core <= 1) return 1;
            if (core === 2 || core === 3) return 2;
            if (core === 4) return 3;
            if (core === 5 && value.length >= 12) return 4;
            return 3;
        };

        // ── Update password requirement checklist ─────────────────────────────
        const updatePwdUI = (value) => {
            const rules = {
                ...evalPwdRules(value),
                ...evalPwdAdvisories(value),
            };
            const started = value.length > 0;

            ruleItems.forEach((el) => {
                const key = el.dataset.passwordRule;
                const met = Boolean(rules[key]);
                const isAdvisory = key === 'no_sequence' || key === 'no_personal';
                el.classList.toggle('is-met',  met);
                // Only show fail state after user has started typing
                el.classList.toggle('is-fail', started && !met && !isAdvisory);
            });

            if (strengthWrap) {
                if (!started) {
                    strengthWrap.classList.remove('visible');
                    strengthWrap.dataset.strength = '0';
                } else {
                    const lvl = pwdStrength(value);
                    strengthWrap.classList.add('visible');
                    strengthWrap.dataset.strength = String(lvl);
                    if (strengthLabel) strengthLabel.textContent = STRENGTH_LABELS[lvl] || '';
                    if (strengthTip)   strengthTip.textContent   = STRENGTH_TIPS[lvl]   || '';
                }
            }
        };

        // ── Set field state (error / success / clear) ─────────────────────────
        const applyFieldState = (input, message) => {
            const wrap    = input.closest('.auth-input-wrap');
            const errEl   = form.querySelector(`[data-error-for="${input.name}"]`);
            const sucEl   = form.querySelector(`[data-success-for="${input.name}"]`);
            const helper  = form.querySelector(`[data-helper-for="${input.name}"]`);
            const statIco = wrap?.querySelector('[data-status-icon]');

            // Error element
            if (errEl) {
                const span = errEl.querySelector('span') || errEl;
                span.textContent = message;
                errEl.classList.toggle('visible', Boolean(message));
            }

            // Success element
            if (sucEl) {
                const span = sucEl.querySelector('span') || sucEl;
                span.textContent = '';
                sucEl.classList.remove('visible');
            }

            // Helper — hide when there's any feedback showing
            if (helper) {
                helper.classList.toggle('has-feedback', Boolean(message));
            }

            // Input border
            input.classList.toggle('is-invalid', Boolean(message));
            input.classList.remove('is-valid');
            input.setAttribute('aria-invalid', message ? 'true' : 'false');
            input.setCustomValidity(message);

            // Right status icon
            if (statIco) {
                const checkEl = statIco.querySelector('.icon-check');
                const xEl     = statIco.querySelector('.icon-x');
                statIco.classList.toggle('is-error',   Boolean(message));
                statIco.classList.remove('is-success');
                statIco.style.color = message ? '#ef4444' : '';
                if (checkEl) checkEl.style.display = 'none';
                if (xEl)     xEl.style.display     = message ? '' : 'none';
            }
        };

        // ── Core field validator ──────────────────────────────────────────────
        /**
         * showRequired: show "field is required" message
         * started:      user has typed something → show format errors
         */
        const validateField = (input, { showRequired = false, started = false } = {}) => {
            const rawValue = input.value;
            const value    = trim(rawValue);
            const label    = input.dataset.label || input.name;
            const rule     = input.dataset.rule  || '';
            const optional = input.dataset.optional === 'true';
            let msg = '';

            if (!value) {
                // Empty field
                if (!optional && input.required && showRequired) {
                    msg = `${label} is required — please fill this in.`;
                }
                applyFieldState(input, msg);
                return msg === '';
            }

            // ── Name ──────────────────────────────────────────────────────────
            if (rule === 'name') {
                const norm   = normName(value);
                const vowels = countVowels(norm);

                if (!NAME_PATTERN.test(value)) {
                    msg = 'Only letters, spaces, hyphens, or apostrophes are allowed.';
                } else if (norm.length < 2) {
                    msg = `${label} must have at least 2 letters.`;
                } else if (value.length > 60) {
                    msg = `${label} is too long (max 60 characters).`;
                } else if (/^[^a-z\p{L}]/iu.test(value)) {
                    msg = `${label} must start with a letter.`;
                } else if (/(\p{L})\1{3,}/u.test(norm)) {
                    msg = 'Avoid repeating the same letter 4 or more times in a row.';
                } else if (/(\p{L}{2,4})\1{2,}/u.test(norm)) {
                    msg = 'This looks like repeated text — please enter your real name.';
                } else if (norm.length >= 4 && vowels === 0) {
                    msg = `${label} doesn't look like a real name (no vowels found).`;
                } else if (norm.length >= 8 && (vowels / norm.length) < 0.20) {
                    msg = 'Please avoid random or meaningless text.';
                } else if (norm.length >= 10 && longestConsonantRun(norm) >= 5) {
                    msg = `${label} doesn't look like a real name.`;
                }
            }

            // ── Email ─────────────────────────────────────────────────────────
            else if (rule === 'email') {
                const lower = value.toLowerCase();
                const hasAt = lower.includes('@');

                // Only start validating once they've typed past the @ sign
                if (!hasAt && started) {
                    msg = 'Keep typing — your email needs @gmail.com at the end.';
                } else if (hasAt) {
                    if (!GMAIL_PATTERN.test(lower)) {
                        if (!lower.endsWith('@gmail.com') && lower.includes('@')) {
                            const domain = lower.split('@')[1] || '';
                            if (domain && domain !== 'gmail.com' && !domain.startsWith('gmail')) {
                                msg = `Only Gmail addresses are accepted. Did you mean to use @gmail.com?`;
                            } else {
                                msg = 'Please enter a valid Gmail address (e.g. you@gmail.com).';
                            }
                        } else {
                            msg = 'Please enter a valid Gmail address (e.g. you@gmail.com).';
                        }
                    } else if (/\.{2,}/.test(lower.split('@')[0])) {
                        msg = 'Email address cannot have consecutive dots (e.g. jo..hn).';
                    } else if (lower.split('@')[0].length < 2) {
                        msg = 'The username part of your email is too short.';
                    }
                }
                // Still typing before @, no feedback yet
            }

            // ── Student ID ────────────────────────────────────────────────────
            else if (rule === 'student_id') {
                const digits = rawValue.replace(/\D/g,'');
                if (digits.length > 0 && digits.length < 8) {
                    msg = `${8 - digits.length} more digit${8-digits.length===1?'':'s'} needed — ID must be exactly 8 digits.`;
                } else if (digits.length === 8) {
                    if (isAllSameDigit(digits)) {
                        msg = 'Student ID cannot be all the same digit (e.g. 00000000).';
                    } else if (isSequential(digits)) {
                        msg = 'Student ID cannot be a simple sequence (e.g. 12345678 or 87654321).';
                    }
                } else if (digits.length === 0 && showRequired) {
                    msg = 'Student ID is required.';
                }
            }

            // ── Year level ────────────────────────────────────────────────────
            else if (rule === 'yearlevel') {
                if (!VALID_YEAR_LEVELS.has(value)) {
                    msg = 'Please choose a valid year level from the list.';
                }
            }

            // ── Password ──────────────────────────────────────────────────────
            else if (rule === 'password') {
                const r = evalPwdRules(rawValue);
                if (!r.length)       msg = 'Password must be at least 8 characters long.';
                else if (!r.max)     msg = 'Password is too long — maximum 128 characters.';
                else if (!r.lower)   msg = 'Add at least one lowercase letter (a–z).';
                else if (!r.upper)   msg = 'Add at least one uppercase letter (A–Z).';
                else if (!r.number)  msg = 'Add at least one number (0–9).';
                else if (!r.special) msg = 'Add at least one special character (e.g. !@#$%).';
                else {
                    pwdStrength(rawValue);
                }
            }

            // ── Confirm password ──────────────────────────────────────────────
            else if (rule === 'password_confirmation') {
                const pwdInput = form.querySelector('[name="password"]');
                if (!rawValue && showRequired) {
                    msg = 'Please confirm your password.';
                } else if (rawValue && pwdInput) {
                    if (rawValue !== pwdInput.value) {
                        msg = 'Passwords don\'t match — re-check what you typed.';
                    }
                }
            }

            applyFieldState(input, msg);
            return msg === '';
        };

        // ── Checkbox validator ────────────────────────────────────────────────
        const validateCheckbox = (checkbox, showRequired=false) => {
            const { blurred } = getState(checkbox);
            const shouldShow  = showRequired || blurred;
            const errEl       = form.querySelector(`[data-error-for="${checkbox.name}"]`);
            const wrapper     = checkbox.closest('.auth-consent-check');
            const doc         = checkbox.dataset.legalCheckbox === 'privacy' ? 'Privacy Policy' : 'Terms and Conditions';
            const msg         = checkbox.checked || !shouldShow
                ? '' : `Please accept the ${doc} to continue.`;
            if (errEl) {
                const span = errEl.querySelector('span') || errEl;
                span.textContent = msg;
                errEl.classList.toggle('visible', Boolean(msg));
            }
            if (wrapper) wrapper.classList.toggle('is-invalid', Boolean(msg));
            return msg === '';
        };

        // ── Submit button state ───────────────────────────────────────────────
        const isFieldReady = (input) => {
            const optional = input.dataset.optional === 'true';
            const rule     = input.dataset.rule || '';
            const value    = trim(input.value);
            const filled   = value !== '';

            if (!optional && input.required && !filled) {
                // student_id has live counter, treat empty as not ready
                return false;
            }
            if (rule === 'yearlevel' && !filled) return true; // optional select
            return validateField(input, { showRequired: false, started: filled });
        };

        const updateSubmitState = () => {
            if (!submitBtn) return;
            const fieldsOk = inputs.every((inp) => isFieldReady(inp));
            const legalOk  = legalCheckboxes.every((cb) => cb.checked);
            submitBtn.disabled = !(fieldsOk && legalOk);
        };

        // ── Banner ────────────────────────────────────────────────────────────
        const updateBanner = (msg='') => {
            if (!banner) return;
            if (bannerText) bannerText.textContent = msg;
            banner.classList.toggle('active', Boolean(msg));
        };

        // ── Live digit counter for Student ID ─────────────────────────────────
        const updateIdCounter = (input) => {
            if (!idCounter || input.name !== 'student_id') return;
            const digits = input.value.replace(/\D/g,'').length;
            idCounter.textContent = `${digits} / 8 digits`;
            idCounter.classList.toggle('is-complete',   digits === 8);
            idCounter.classList.toggle('is-incomplete', digits > 0 && digits < 8);
        };

        // ── Wire inputs ───────────────────────────────────────────────────────
        inputs.forEach((input) => {
            // Block invalid characters on name fields immediately (beforeinput)
            if (input.dataset.rule === 'name') {
                input.addEventListener('beforeinput', (e) => {
                    if (!e.data || !/[^\p{L}\s''.,-]/u.test(e.data)) return;
                    e.preventDefault();
                    setState(input, { started: true });
                    applyFieldState(input, 'Only letters, spaces, hyphens, or apostrophes are allowed.');
                });
            }

            // Block paste on confirm password
            if (input.name === 'password_confirmation') {
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    setState(input, { started: true });
                    applyFieldState(input, 'Please type your password again — pasting is not allowed here.');
                });
            }

            // ── input event: real-time feedback as user types ──────────────────
            input.addEventListener('input', () => {
                setState(input, { started: true });

                // Sanitize
                if (input.dataset.rule === 'email')      input.value = input.value.replace(/\s+/gu,'').toLowerCase();
                if (input.dataset.rule === 'student_id') input.value = input.value.replace(/\D+/g,'').slice(0,8);
                if (input.dataset.rule === 'name')       input.value = input.value.replace(/\s{2,}/gu,' ');

                updateIdCounter(input);

                if (input.dataset.rule === 'password') updatePwdUI(input.value);

                // Show format errors IMMEDIATELY while typing
                // Only skip the "required" error (empty field) — that shows on blur
                const value = trim(input.value);
                const fieldMeta = getState(input);
                const started = fieldMeta.started || value.length > 0;
                validateField(input, {
                    showRequired: started || fieldMeta.blurred,
                    started,
                });
                updateBanner('');
                updateSubmitState();

                // Cross-validate confirm password live
                if (input.name === 'password') {
                    const conf = form.querySelector('[name="password_confirmation"]');
                    if (conf) {
                        const confValue = trim(conf.value);
                        const confMeta = getState(conf);
                        validateField(conf, {
                            showRequired: confMeta.started || confMeta.blurred || confValue.length > 0,
                            started: confMeta.started || confValue.length > 0,
                        });
                    }
                }
            });

            // ── change event (for select) ──────────────────────────────────────
            input.addEventListener('change', () => {
                setState(input, { started: true, blurred: true });
                const value = trim(input.value);
                validateField(input, { showRequired: true, started: value.length > 0 });
                updateSubmitState();
            });

            // ── blur: show "required" errors now that user left the field ──────
            input.addEventListener('blur', () => {
                setState(input, { blurred: true });

                // Auto title-case name fields on blur
                if (input.dataset.rule === 'name' && input.value.trim()) {
                    input.value = toTitleCase(trim(input.value));
                }
                if (input.dataset.rule === 'password') updatePwdUI(input.value);

                const value   = trim(input.value);
                const started = value.length > 0;
                validateField(input, { showRequired: true, started });
                updateSubmitState();

                if (input.name === 'password') {
                    const conf = form.querySelector('[name="password_confirmation"]');
                    if (conf && trim(conf.value) !== '') {
                        validateField(conf, { showRequired: getState(conf).blurred, started: true });
                        updateSubmitState();
                    }
                }
            });
        });

        // ── Legal checkboxes ──────────────────────────────────────────────────
        legalCheckboxes.forEach((cb) => {
            cb.addEventListener('change', () => {
                setState(cb, { started: true, blurred: true });
                validateCheckbox(cb);
                updateBanner('');
                updateSubmitState();
            });
        });

        // ── Password toggle ───────────────────────────────────────────────────
        document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.target ? document.getElementById(btn.dataset.target) : null;
                if (!target) return;
                const reveal = target.type === 'password';
                target.type  = reveal ? 'text' : 'password';
                btn.classList.toggle('is-visible', reveal);
                btn.setAttribute('aria-pressed', String(reveal));
                btn.setAttribute('aria-label',  reveal ? 'Hide password' : 'Show password');
            });
        });

        // ── Legal modal ───────────────────────────────────────────────────────
        const openLegal  = (panel) => {
            if (!legalModal) return;
            legalPanels.forEach((p) => p.classList.toggle('active', p.dataset.legalPanel===panel));
            if (legalModalTitle) legalModalTitle.textContent = panel==='privacy' ? 'Privacy Policy' : 'Terms and Conditions';
            legalModal.classList.add('active');
            legalModal.setAttribute('aria-hidden','false');
        };
        const closeLegal = () => {
            if (!legalModal) return;
            legalModal.classList.remove('active');
            legalModal.setAttribute('aria-hidden','true');
        };
        legalOpenBtns.forEach((b) => b.addEventListener('click', () => openLegal(b.dataset.openLegal||'terms')));
        legalCloseBtns.forEach((b) => b.addEventListener('click', closeLegal));
        document.addEventListener('keydown', (e) => { if (e.key==='Escape' && legalModal?.classList.contains('active')) closeLegal(); });

        // ── Form submit guard ─────────────────────────────────────────────────
        form.addEventListener('submit', (e) => {
            let valid = true;
            updateBanner('');

            inputs.forEach((inp) => {
                setState(inp, { started: true, blurred: true });
                if (!validateField(inp, { showRequired: true, started: true })) valid = false;
            });
            legalCheckboxes.forEach((cb) => {
                setState(cb, { blurred: true });
                if (!validateCheckbox(cb, true)) valid = false;
            });

            if (!valid) {
                e.preventDefault();
                updateBanner('Some fields need attention — please fix the highlighted items below.');
                const firstBad = form.querySelector('.auth-input.is-invalid')
                    || form.querySelector('.auth-consent-check.is-invalid input')
                    || null;
                if (firstBad) { firstBad.scrollIntoView({ behavior:'smooth', block:'center' }); firstBad.focus(); }
            }
        });

        // ── Init ──────────────────────────────────────────────────────────────
        updatePwdUI('');
        updateIdCounter(form.querySelector('[name="student_id"]') || { name:'', value:'' });

        // Restore server-side errors (after failed submit)
        inputs.forEach((inp) => {
            if (inp.classList.contains('is-invalid')) {
                setState(inp, { started: true, blurred: true });
            }
        });
        if (form.querySelector('.auth-input.is-invalid')) {
            updateBanner('Some fields need attention — please fix the highlighted items below.');
        }
        updateSubmitState();
    })();
    </script>
</x-guest-layout>
