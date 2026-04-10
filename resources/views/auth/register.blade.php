<x-guest-layout>
    <style>
  *, *::before, *::after { box-sizing: border-box; }
  body { background: #f1f5f9; font-family: 'Inter', system-ui, sans-serif; display: flex; align-items: flex-start; justify-content: center; min-height: 100vh; padding: 32px 16px; }
  .card { background: #fff; border-radius: 20px; padding: 32px 28px; width: 100%; max-width: 480px; box-shadow: 0 4px 32px rgba(15,23,42,.10); }

  /* ── Header ── */
  .auth-badge { display: inline-flex; align-items: center; gap: 7px; background: #f3eeff; color: #6f42c1; border-radius: 99px; padding: 5px 13px; font-size: 12px; font-weight: 700; margin-bottom: 14px; }
  .auth-badge svg { width: 14px; height: 14px; }
  .auth-title { margin: 0 0 4px; font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -.4px; }
  .auth-sub { margin: 0 0 20px; color: #64748b; font-size: 14px; }

  /* ── Progress bar ── */
  .form-progress { display: flex; gap: 6px; margin-bottom: 20px; }
  .form-progress-step { flex: 1; height: 4px; border-radius: 99px; background: #e2e8f0; transition: background .3s; }
  .form-progress-step.done { background: #6f42c1; }
  .form-progress-step.active { background: linear-gradient(90deg,#6f42c1,#a78bfa); }

  /* ── Grid ── */
  .auth-grid { display: grid; gap: 14px; }
  .auth-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

  /* ── Labels ── */
  .auth-label { display: flex; align-items: center; gap: 5px; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #334155; }
  .auth-label .optional-tag { color: #94a3b8; font-weight: 400; font-size: 11px; background: #f1f5f9; padding: 1px 7px; border-radius: 99px; }

  /* ── Input wrap ── */
  .auth-input-wrap { position: relative; }
  .auth-input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; display: flex; }
  .auth-input-icon svg { width: 16px; height: 16px; }
  .auth-input-status { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); display: none; }
  .auth-input-status svg { width: 16px; height: 16px; display: block; }
  .auth-input-status.show-valid { display: flex; color: #16a34a; }
  .auth-input-status.show-invalid { display: flex; color: #dc2626; }

  /* ── Inputs ── */
  .auth-input {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: 12px;
    padding: 10px 40px 10px 38px; font-size: 14px; outline: none;
    background: #f8fafc; color: #0f172a; transition: border-color .18s, box-shadow .18s, background .18s;
    -webkit-appearance: none;
  }
  .auth-input::placeholder { color: #94a3b8; }
  .auth-input:focus { border-color: #6f42c1; box-shadow: 0 0 0 3px rgba(111,66,193,.15); background: #fff; }
  .auth-input.is-invalid { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.12); background: #fff8f8; }
  .auth-input.is-valid { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.12); background: #f0fdf4; }
  .auth-input.no-icon { padding-left: 12px; }
  .auth-input.has-toggle { padding-right: 72px; }
  select.auth-input { padding-right: 12px; cursor: pointer; }

  /* ── Password toggle ── */
  .auth-password-toggle {
    position: absolute; top: 50%; right: 36px; transform: translateY(-50%);
    width: 28px; height: 28px; border-radius: 8px; border: 0; background: transparent;
    color: #64748b; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 0;
  }
  .auth-password-toggle:hover { background: #f1f5f9; color: #334155; }
  .auth-password-toggle svg { width: 16px; height: 16px; display: block; }
  .auth-password-toggle .eye-off { display: none; }
  .auth-password-toggle.is-visible .eye-on { display: none; }
  .auth-password-toggle.is-visible .eye-off { display: block; }

  /* ── Error / Success ── */
  .auth-error { margin-top: 5px; color: #dc2626; font-size: 12px; display: flex; align-items: center; gap: 4px; min-height: 0; overflow: hidden; max-height: 0; transition: max-height .2s, opacity .2s; opacity: 0; }
  .auth-error.visible { max-height: 60px; opacity: 1; }
  .auth-error svg { width: 13px; height: 13px; flex-shrink: 0; }
  .auth-success { margin-top: 5px; color: #16a34a; font-size: 12px; display: flex; align-items: center; gap: 4px; min-height: 0; overflow: hidden; max-height: 0; transition: max-height .2s, opacity .2s; opacity: 0; }
  .auth-success.visible { max-height: 60px; opacity: 1; }
  .auth-success svg { width: 13px; height: 13px; flex-shrink: 0; }

  /* ── Password strength ── */
  .strength-wrap { margin-top: 8px; }
  .strength-bar { height: 4px; border-radius: 99px; background: #e2e8f0; overflow: hidden; }
  .strength-fill { height: 100%; width: 0; border-radius: 99px; transition: width .3s, background .3s; }
  .strength-label { margin-top: 4px; font-size: 11px; font-weight: 700; color: #94a3b8; text-align: right; }

  /* ── Password rules ── */
  .auth-password-rules {
    margin-top: 8px; padding: 10px 12px; border: 1.5px solid #e2e8f0;
    border-radius: 12px; background: #f8fafc; font-size: 12px; color: #475569;
  }
  .auth-password-rules-title { margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #0f172a; }
  .auth-password-rule-list { margin: 0; padding: 0; list-style: none; display: grid; gap: 5px; }
  .auth-password-rule { display: flex; align-items: center; gap: 6px; color: #94a3b8; transition: color .2s; }
  .auth-password-rule::before { content: ''; display: inline-block; width: 14px; height: 14px; border-radius: 50%; background: #e2e8f0; flex-shrink: 0; transition: background .2s; }
  .auth-password-rule.is-met { color: #15803d; }
  .auth-password-rule.is-met::before { background: #22c55e url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 14 14' fill='none'%3E%3Cpath d='M3 7l2.5 2.5L11 4.5' stroke='%23fff' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/12px no-repeat; }

  /* ── Student ID counter ── */
  .id-counter { position: absolute; right: 38px; top: 50%; transform: translateY(-50%); font-size: 11px; color: #94a3b8; pointer-events: none; font-weight: 600; }
  .id-counter.complete { color: #16a34a; }

  /* ── Submit button ── */
  .auth-btn {
    width: 100%; border: none; border-radius: 12px; padding: 12px;
    margin-top: 12px; font-size: 15px; font-weight: 800; color: #fff;
    background: linear-gradient(135deg,#6f42c1,#59339d);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    gap: 8px; letter-spacing: -.1px; position: relative; overflow: hidden;
    transition: filter .2s, transform .1s;
  }
  .auth-btn:hover:not(:disabled) { filter: brightness(1.06); }
  .auth-btn:active:not(:disabled) { transform: scale(.99); }
  .auth-btn:disabled { opacity: .55; cursor: not-allowed; }
  .auth-btn .btn-spinner { width: 18px; height: 18px; border: 2.5px solid rgba(255,255,255,.35); border-top-color: #fff; border-radius: 50%; animation: spin .7s linear infinite; display: none; }
  .auth-btn.loading .btn-text { display: none; }
  .auth-btn.loading .btn-spinner { display: block; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .auth-btn svg { width: 18px; height: 18px; }

  /* ── Legal / Consent ── */
  .auth-consent-wrap { margin-top: 14px; display: grid; gap: 8px; }
  .auth-consent-check {
    display: flex; align-items: flex-start; gap: 9px;
    padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: 12px;
    background: #f8fafc; color: #334155; font-size: 12.5px; line-height: 1.5;
    cursor: pointer; transition: border-color .15s, background .15s;
  }
  .auth-consent-check:has(input:checked) { border-color: #a78bfa; background: #faf5ff; }
  .auth-consent-check input { margin-top: 1px; accent-color: #6f42c1; width: 15px; height: 15px; flex-shrink: 0; cursor: pointer; }
  .auth-legal-link { border: 0; background: transparent; padding: 0; color: #6f42c1; font-weight: 700; text-decoration: underline; cursor: pointer; font: inherit; font-size: 12.5px; }
  .auth-legal-summary { font-size: 11.5px; color: #94a3b8; line-height: 1.55; text-align: center; margin-top: 2px; }

  .auth-divider { height: 1px; background: #f1f5f9; margin: 4px 0; }
  .auth-foot { margin-top: 14px; text-align: center; color: #64748b; font-size: 13px; }
  .auth-link { color: #6f42c1; text-decoration: none; font-size: 13px; font-weight: 700; }
  .auth-link:hover { text-decoration: underline; }

  /* ── Legal Modal ── */
  .legal-modal-shell { position: fixed; inset: 0; z-index: 1500; display: none; align-items: center; justify-content: center; padding: 18px; }
  .legal-modal-shell.active { display: flex; }
  .legal-modal-backdrop { position: absolute; inset: 0; background: rgba(15,23,42,.52); backdrop-filter: blur(4px); }
  .legal-modal-card { position: relative; width: min(760px,100%); max-height: calc(100vh - 36px); overflow: hidden; border-radius: 18px; border: 1px solid rgba(148,163,184,.26); background: #fff; box-shadow: 0 24px 70px rgba(15,23,42,.24); animation: modalIn .22s ease; }
  @keyframes modalIn { from { transform: scale(.96) translateY(8px); opacity: 0; } to { transform: none; opacity: 1; } }
  .legal-modal-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 18px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
  .legal-modal-title { margin: 0; font-size: 17px; font-weight: 800; color: #0f172a; }
  .legal-modal-close { width: 36px; height: 36px; border-radius: 10px; border: 1.5px solid #dbe3f0; background: #fff; color: #475569; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
  .legal-modal-close:hover { background: #f1f5f9; }
  .legal-modal-body { max-height: calc(100vh - 132px); overflow-y: auto; padding: 16px 18px 18px; color: #475569; font-size: 13px; line-height: 1.7; }
  .legal-modal-panel { display: none; }
  .legal-modal-panel.active { display: block; }
  .legal-modal-body h4 { color: #0f172a; margin: 14px 0 4px; font-size: 13px; }
  .legal-modal-body p { margin: 0 0 10px; }
  .legal-modal-body p:last-child { margin-bottom: 0; }
</style>
</head>
<body>
<div class="card">

  <div class="auth-badge">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
    Student Registration
  </div>

  <h2 class="auth-title">Create Your Account</h2>
  <p class="auth-sub">Fill in the details below to access academic consultations.</p>

  <!-- Progress bar (visual, JS-driven) -->
  <div class="form-progress" id="formProgress">
    <div class="form-progress-step active" data-step="0"></div>
    <div class="form-progress-step" data-step="1"></div>
    <div class="form-progress-step" data-step="2"></div>
    <div class="form-progress-step" data-step="3"></div>
    <div class="form-progress-step" data-step="4"></div>
  </div>

  <form method="POST" action="#" novalidate data-live-validate="register">

    <div class="auth-grid">

      <!-- First & Last Name row -->
      <div class="auth-row-2">
        <div>
          <label class="auth-label" for="first_name">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M2 21v-1a10 10 0 0 1 20 0v1"/></svg>
            First Name
          </label>
          <div class="auth-input-wrap">
            <span class="auth-input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M2 21v-1a10 10 0 0 1 20 0v1"/></svg></span>
            <input id="first_name" class="auth-input" type="text" name="first_name" placeholder="e.g. Juan" required autocomplete="given-name" data-label="First name" data-rule="name">
            <span class="auth-input-status" data-status-for="first_name">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
          </div>
          <div class="auth-error" data-error-for="first_name"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
          <div class="auth-success" data-success-for="first_name"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span></span></div>
        </div>
        <div>
          <label class="auth-label" for="last_name">Last Name</label>
          <div class="auth-input-wrap">
            <span class="auth-input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M2 21v-1a10 10 0 0 1 20 0v1"/></svg></span>
            <input id="last_name" class="auth-input" type="text" name="last_name" placeholder="e.g. Dela Cruz" required autocomplete="family-name" data-label="Last name" data-rule="name">
            <span class="auth-input-status" data-status-for="last_name">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </span>
          </div>
          <div class="auth-error" data-error-for="last_name"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
          <div class="auth-success" data-success-for="last_name"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span></span></div>
        </div>
      </div>

      <!-- Middle Name -->
      <div>
        <label class="auth-label" for="middle_name">
          Middle Name <span class="optional-tag">Optional</span>
        </label>
        <div class="auth-input-wrap">
          <span class="auth-input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M2 21v-1a10 10 0 0 1 20 0v1"/></svg></span>
          <input id="middle_name" class="auth-input" type="text" name="middle_name" placeholder="e.g. Santos" autocomplete="additional-name" data-label="Middle name" data-rule="name" data-optional="true">
          <span class="auth-input-status" data-status-for="middle_name">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
        </div>
        <div class="auth-error" data-error-for="middle_name"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
        <div class="auth-success" data-success-for="middle_name"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span></span></div>
      </div>

      <!-- Email -->
      <div>
        <label class="auth-label" for="email">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          Email Address
        </label>
        <div class="auth-input-wrap">
          <span class="auth-input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></span>
          <input id="email" class="auth-input" type="email" name="email" placeholder="yourname@gmail.com" required autocomplete="username" data-label="Email" data-rule="email" inputmode="email">
          <span class="auth-input-status" data-status-for="email">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
        </div>
        <div class="auth-error" data-error-for="email"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
        <div class="auth-success" data-success-for="email"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span></span></div>
      </div>

      <input type="hidden" name="user_type" value="student">

      <!-- Student ID & Year Level row -->
      <div class="auth-row-2">
        <div>
          <label class="auth-label" for="student_id">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/><path d="M12 14H8"/><path d="M16 14h-1"/></svg>
            Student ID
          </label>
          <div class="auth-input-wrap">
            <span class="auth-input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="10" cy="12" r="2"/><path d="M14 10h3M14 14h3"/></svg></span>
            <input id="student_id" class="auth-input" type="text" name="student_id" placeholder="12345678" autocomplete="off" inputmode="numeric" maxlength="8" required data-label="Student ID" data-rule="student_id">
            <span class="id-counter" id="idCounter">0/8</span>
          </div>
          <div class="auth-error" data-error-for="student_id"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
          <div class="auth-success" data-success-for="student_id"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span></span></div>
        </div>
        <div>
          <label class="auth-label" for="yearlevel">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            Year Level
          </label>
          <div class="auth-input-wrap">
            <select id="yearlevel" class="auth-input no-icon" name="yearlevel" required data-label="Year level" data-rule="yearlevel">
              <option value="">Select year level</option>
              <option value="1st Year">1st Year</option>
              <option value="2nd Year">2nd Year</option>
              <option value="3rd Year">3rd Year</option>
              <option value="4th Year">4th Year</option>
            </select>
          </div>
          <div class="auth-error" data-error-for="yearlevel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
          <div class="auth-success" data-success-for="yearlevel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span></span></div>
        </div>
      </div>

      <!-- Password -->
      <div>
        <label class="auth-label" for="password">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Password
        </label>
        <div class="auth-input-wrap">
          <span class="auth-input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
          <input id="password" class="auth-input has-toggle" type="password" name="password" placeholder="Create a strong password" required autocomplete="new-password" data-label="Password" data-rule="password">
          <button type="button" class="auth-password-toggle" data-toggle-password data-target="password" aria-label="Show password" aria-pressed="false">
            <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6.5 0-10-7-10-7a21.77 21.77 0 0 1 5.06-5.94"/><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c6.5 0 10 7 10 7a21.8 21.8 0 0 1-3.32 4.61"/><path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"/><path d="M3 3l18 18"/></svg>
          </button>
          <span class="auth-input-status" data-status-for="password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
        </div>
        <div class="auth-error" data-error-for="password"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
        <div class="auth-success" data-success-for="password"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span></span></div>

        <!-- Strength meter -->
        <div class="strength-wrap" id="strengthWrap" style="display:none">
          <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
          <div class="strength-label" id="strengthLabel">—</div>
        </div>

        <!-- Rules checklist -->
        <div class="auth-password-rules">
          <p class="auth-password-rules-title">Password must contain:</p>
          <ul class="auth-password-rule-list">
            <li class="auth-password-rule" data-password-rule="length">At least 8 characters long</li>
            <li class="auth-password-rule" data-password-rule="lower">At least one lowercase letter (a–z)</li>
            <li class="auth-password-rule" data-password-rule="upper">At least one uppercase letter (A–Z)</li>
            <li class="auth-password-rule" data-password-rule="number">At least one number (0–9)</li>
            <li class="auth-password-rule" data-password-rule="special">At least one special character (!@#$%^&*)</li>
          </ul>
        </div>
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="auth-label" for="password_confirmation">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/><path d="m9 16 2 2 4-4"/></svg>
          Confirm Password
        </label>
        <div class="auth-input-wrap">
          <span class="auth-input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
          <input id="password_confirmation" class="auth-input has-toggle" type="password" name="password_confirmation" placeholder="Re-enter your password" required autocomplete="new-password" data-label="Password confirmation" data-rule="password_confirmation">
          <button type="button" class="auth-password-toggle" data-toggle-password data-target="password_confirmation" aria-label="Show password" aria-pressed="false">
            <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6.5 0-10-7-10-7a21.77 21.77 0 0 1 5.06-5.94"/><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c6.5 0 10 7 10 7a21.8 21.8 0 0 1-3.32 4.61"/><path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"/><path d="M3 3l18 18"/></svg>
          </button>
          <span class="auth-input-status" data-status-for="password_confirmation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
        </div>
        <div class="auth-error" data-error-for="password_confirmation"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
        <div class="auth-success" data-success-for="password_confirmation"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span></span></div>
      </div>

    </div><!-- /.auth-grid -->

    <div class="auth-divider" style="margin-top:16px"></div>

    <div class="auth-consent-wrap">
      <label class="auth-consent-check" for="terms_accepted">
        <input id="terms_accepted" type="checkbox" name="terms_accepted" value="1" data-legal-checkbox="terms">
        <span>I have read and agree to the <button type="button" class="auth-legal-link" data-open-legal="terms">Terms and Conditions</button>.</span>
      </label>
      <div class="auth-error" data-error-for="terms_accepted"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
      <label class="auth-consent-check" for="privacy_accepted">
        <input id="privacy_accepted" type="checkbox" name="privacy_accepted" value="1" data-legal-checkbox="privacy">
        <span>I have read and agree to the <button type="button" class="auth-legal-link" data-open-legal="privacy">Privacy Policy</button>.</span>
      </label>
      <div class="auth-error" data-error-for="privacy_accepted"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span></span></div>
      <div class="auth-legal-summary">Please review both documents carefully before creating your account.</div>
    </div>

    <button type="submit" class="auth-btn" data-submit-register>
      <span class="btn-text" style="display:flex;align-items:center;gap:8px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        Create Account
      </span>
      <div class="btn-spinner"></div>
    </button>

    <div class="auth-foot">
      Already have an account?
      <a class="auth-link" href="#">Sign in instead</a>
    </div>
  </form>
</div><!-- /.card -->

<!-- Legal Modal -->
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
        <h4>Account Information</h4>
        <p>Users must provide accurate and complete information when creating an account. Any false or misleading information may result in restricted access or account suspension.</p>
        <h4>Account Responsibility</h4>
        <p>Users are responsible for keeping their login credentials confidential. Any activity performed under a registered account is considered the responsibility of the account owner.</p>
        <h4>Proper Use of the System</h4>
        <p>The system must only be used for academic and consultation-related purposes. Users must communicate respectfully and avoid inappropriate language, misuse, or unauthorized activities.</p>
        <h4>System Availability</h4>
        <p>The system may occasionally undergo maintenance or updates. Users understand that access may not always be available during these periods.</p>
        <h4>Suspension of Access</h4>
        <p>The system administrator reserves the right to suspend or terminate access if a user violates system policies, disrupts operations, or compromises security.</p>
      </article>
      <article class="legal-modal-panel" data-legal-panel="privacy">
        <p><strong>Privacy Policy</strong></p>
        <h4>Collection of Information</h4>
        <p>The system collects necessary personal information such as name, email address, and account details to support registration, consultation scheduling, and user communication.</p>
        <h4>Use of Information</h4>
        <p>Collected information is used only for academic consultation purposes, system management, and improving user experience within the platform.</p>
        <h4>Protection of Data</h4>
        <p>Personal data is protected through appropriate security measures to prevent unauthorized access, loss, or misuse of information.</p>
        <h4>Access to Information</h4>
        <p>Only authorized administrators and permitted users may access personal information when necessary for system operations.</p>
        <h4>Policy Updates</h4>
        <p>The system administration may update this Privacy Policy when needed. Users will be informed of significant changes affecting personal information handling.</p>
      </article>
    </div>
  </div>
</div>

<script>
(function () {
  'use strict';

  const form = document.querySelector('[data-live-validate="register"]');
  if (!form) return;

  /* ── Refs ── */
  const legalModal      = document.getElementById('legalModal');
  const legalModalTitle = document.getElementById('legalModalTitle');
  const legalPanels     = [...document.querySelectorAll('[data-legal-panel]')];
  const ruleIndicators  = [...form.querySelectorAll('[data-password-rule]')];
  const submitBtn       = form.querySelector('[data-submit-register]');
  const legalCheckboxes = [...form.querySelectorAll('[data-legal-checkbox]')];
  const inputs          = [...form.querySelectorAll('.auth-input[name]')];
  const idCounter       = document.getElementById('idCounter');
  const strengthWrap    = document.getElementById('strengthWrap');
  const strengthFill    = document.getElementById('strengthFill');
  const strengthLabel   = document.getElementById('strengthLabel');
  const progressSteps   = [...document.querySelectorAll('[data-step]')];

  const touched = new WeakMap();

  /* ── Helpers ── */
  const trim = v => v.replace(/\s+/gu, ' ').trim();
  const normName = v => trim(v).replace(/[^\p{L}]/gu, '').toLowerCase();
  const countVowels = v => (v.match(/[aeiouy]/gu) || []).length;
  const namePattern = /^(?=.*\p{L})[\p{L}\s''-]+$/u;
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i;
  const gmailPattern = /^[^\s@]+@gmail\.com$/i;

  const longestConsonantRun = v => {
    let max = 0, cur = 0;
    [...v].forEach(c => { if (/[aeiouy]/iu.test(c)) { cur = 0; } else { max = Math.max(max, ++cur); } });
    return max;
  };

  /* ── Password strength ── */
  const evalRules = v => ({
    length:  v.length >= 8,
    lower:   /[a-z]/.test(v),
    upper:   /[A-Z]/.test(v),
    number:  /\d/.test(v),
    special: /[^A-Za-z0-9]/.test(v),
  });

  const strengthConfig = [
    { max: 0,  pct: '0%',   color: '#e2e8f0', label: '—' },
    { max: 1,  pct: '20%',  color: '#ef4444', label: 'Very weak' },
    { max: 2,  pct: '40%',  color: '#f97316', label: 'Weak' },
    { max: 3,  pct: '60%',  color: '#eab308', label: 'Fair' },
    { max: 4,  pct: '80%',  color: '#3b82f6', label: 'Strong' },
    { max: 5,  pct: '100%', color: '#22c55e', label: 'Very strong 🎉' },
  ];

  const updateStrength = v => {
    const rules  = evalRules(v);
    const score  = Object.values(rules).filter(Boolean).length;
    const cfg    = v.length === 0 ? strengthConfig[0] : (strengthConfig[score] || strengthConfig[5]);
    strengthWrap.style.display = v.length ? 'block' : 'none';
    strengthFill.style.width   = cfg.pct;
    strengthFill.style.background = cfg.color;
    strengthLabel.textContent  = cfg.label;
    strengthLabel.style.color  = cfg.color;
    ruleIndicators.forEach(el => el.classList.toggle('is-met', Boolean(rules[el.dataset.passwordRule])));
  };

  /* ── Progress bar ── */
  const GROUPS = [
    ['first_name','last_name','middle_name'],
    ['email'],
    ['student_id','yearlevel'],
    ['password'],
    ['password_confirmation'],
  ];

  const updateProgress = () => {
    const completedGroups = GROUPS.filter(group =>
      group.every(name => {
        const inp = form.querySelector(`[name="${name}"]`);
        if (!inp) return true;
        if (inp.dataset.optional === 'true') return true;
        const errEl = form.querySelector(`[data-error-for="${name}"] span`);
        return inp.value.trim() !== '' && (!errEl || errEl.textContent === '');
      })
    ).length;
    progressSteps.forEach((step, i) => {
      step.classList.toggle('done',   i < completedGroups);
      step.classList.toggle('active', i === completedGroups && i < progressSteps.length);
    });
  };

  /* ── Show / hide feedback ── */
  const setField = (input, msg, ok = '') => {
    const errEl  = form.querySelector(`[data-error-for="${input.name}"] span`);
    const errWrap = form.querySelector(`[data-error-for="${input.name}"]`);
    const okEl   = form.querySelector(`[data-success-for="${input.name}"] span`);
    const okWrap = form.querySelector(`[data-success-for="${input.name}"]`);
    const status = form.querySelector(`[data-status-for="${input.name}"]`);

    if (errEl)  errEl.textContent  = msg;
    if (errWrap) errWrap.classList.toggle('visible', Boolean(msg));
    if (okEl)   okEl.textContent   = msg ? '' : ok;
    if (okWrap) okWrap.classList.toggle('visible', !msg && Boolean(ok));

    input.classList.toggle('is-invalid', Boolean(msg));
    input.classList.toggle('is-valid',  !msg && Boolean(ok));
    input.setAttribute('aria-invalid', msg ? 'true' : 'false');

    if (status) {
      status.classList.toggle('show-valid',   !msg && Boolean(ok));
      status.classList.toggle('show-invalid', Boolean(msg));
    }
  };

  /* ── Validate one field ── */
  const validate = (input, { showRequired = false } = {}) => {
    const raw   = input.value;
    const val   = trim(raw);
    const label = input.dataset.label || input.name;
    const rule  = input.dataset.rule  || '';
    const opt   = input.dataset.optional === 'true';

    input.setCustomValidity('');
    let msg = '', ok = '';

    if (!val) {
      if (!opt && input.required && showRequired) msg = `${label} is required.`;
    } else if (rule === 'name') {
      const n = normName(val);
      if (!namePattern.test(val))                                  msg = 'Only letters, spaces, hyphens, or apostrophes allowed.';
      else if (n.length < 2)                                       msg = 'Please enter a real name (at least 2 letters).';
      else if (n.length > 50 || val.length > 60)                   msg = "That name is too long.";
      else if (/(\p{L})\1{3,}/u.test(n))                          msg = 'Please enter a real name.';
      else if (/(\p{L}{2,4})\1{2,}/u.test(n))                     msg = 'Please avoid random or repeated text.';
      else if (n.length >= 4  && countVowels(n) === 0)             msg = "This doesn't look like a valid name.";
      else if (n.length >= 8  && (countVowels(n)/n.length) < 0.2)  msg = 'This looks like random text.';
      else if (n.length >= 10 && longestConsonantRun(n) >= 5)      msg = "This doesn't look like a valid name.";
      else ok = '✓ Looks good';
    } else if (rule === 'email') {
      const lower = val.toLowerCase();
      if (!emailPattern.test(lower))    msg = 'Please enter a valid email address (e.g. you@gmail.com).';
      else if (!gmailPattern.test(lower)) msg = 'Only Gmail addresses (@gmail.com) are accepted.';
      else ok = "Gmail address looks good.";
    } else if (rule === 'student_id') {
      if (!/^\d+$/.test(val))      msg = 'Student ID must contain digits only.';
      else if (val.length !== 8)   msg = `Student ID must be exactly 8 digits (${val.length}/8).`;
      else ok = 'Student ID format is valid.';
    } else if (rule === 'yearlevel') {
      if (!val) msg = 'Please select your year level.';
      else ok = `Year ${val} selected.`;
    } else if (rule === 'password') {
      const r = evalRules(raw);
      if      (raw.length === 0)  msg = '';
      else if (!r.length)         msg = 'Password must be at least 8 characters long.';
      else if (!r.lower)          msg = 'Add at least one lowercase letter (a–z).';
      else if (!r.upper)          msg = 'Add at least one uppercase letter (A–Z).';
      else if (!r.number)         msg = 'Add at least one number (0–9).';
      else if (!r.special)        msg = 'Add at least one special character (e.g. !@#$%^&*).';
      else                        ok  = 'Password meets all requirements.';
    } else if (rule === 'password_confirmation') {
      const pw = form.querySelector('[name="password"]');
      if (pw && raw !== pw.value)  msg = 'Passwords do not match. Please check again.';
      else if (raw)                ok  = 'Passwords match!';
    }

    input.setCustomValidity(msg);
    setField(input, msg, ok);
    updateProgress();
    return msg === '';
  };

  /* ── Legal checkbox ── */
  const validateLegal = (cb, { showRequired = false } = {}) => {
    if (!cb) return true;
    const shouldShow = showRequired || touched.get(cb);
    const errEl  = form.querySelector(`[data-error-for="${cb.name}"]`);
    const errSpan = form.querySelector(`[data-error-for="${cb.name}"] span`);
    const name   = cb.dataset.legalCheckbox === 'privacy' ? 'Privacy Policy' : 'Terms and Conditions';
    const msg    = cb.checked || !shouldShow ? '' : `You must accept the ${name} to continue.`;
    if (errEl)   errEl.classList.toggle('visible', Boolean(msg));
    if (errSpan) errSpan.textContent = msg;
    return !msg;
  };

  /* ── Student ID: digits-only live filter & counter ── */
  const studentIdInput = form.querySelector('[name="student_id"]');
  if (studentIdInput && idCounter) {
    studentIdInput.addEventListener('input', () => {
      studentIdInput.value = studentIdInput.value.replace(/\D/g, '').slice(0, 8);
      const len = studentIdInput.value.length;
      idCounter.textContent = `${len}/8`;
      idCounter.classList.toggle('complete', len === 8);
    });
  }

  /* ── Bind input events ── */
  inputs.forEach(input => {
    if (!form.querySelector(`[data-error-for="${input.name}"]`)) return;

    /* Block invalid characters for name fields before they appear */
    input.addEventListener('beforeinput', e => {
      if (input.dataset.rule !== 'name' || !e.data) return;
      if (/[^\p{L}\s''-]/u.test(e.data)) {
        e.preventDefault();
        setField(input, 'Only letters, spaces, hyphens, or apostrophes allowed.');
        setTimeout(() => { if (!input.value.trim()) setField(input, '', ''); }, 1500);
      }
    });

    input.addEventListener('input', () => {
      touched.set(input, true);
      if (input.dataset.rule === 'email') input.value = input.value.replace(/\s/g, '').toLowerCase();
      if (input.dataset.rule === 'password') updateStrength(input.value);
      validate(input, { showRequired: false });
      /* Live-sync confirm field */
      if (input.name === 'password') {
        const conf = form.querySelector('[name="password_confirmation"]');
        if (conf && (conf.value || touched.get(conf))) validate(conf, { showRequired: touched.get(conf) });
      }
    });

    input.addEventListener('blur', () => {
      touched.set(input, true);
      if (input.dataset.rule === 'password') updateStrength(input.value);
      validate(input, { showRequired: true });
      if (input.name === 'password') {
        const conf = form.querySelector('[name="password_confirmation"]');
        if (conf && (conf.value || touched.get(conf))) validate(conf, { showRequired: touched.get(conf) });
      }
    });
  });

  /* ── Legal checkboxes ── */
  legalCheckboxes.forEach(cb => {
    cb.addEventListener('change', () => {
      touched.set(cb, true);
      validateLegal(cb, { showRequired: true });
    });
  });

  /* ── Submit ── */
  form.addEventListener('submit', e => {
    let valid = true;
    inputs.forEach(input => {
      if (!form.querySelector(`[data-error-for="${input.name}"]`)) return;
      touched.set(input, true);
      if (!validate(input, { showRequired: true })) valid = false;
    });
    legalCheckboxes.forEach(cb => {
      touched.set(cb, true);
      if (!validateLegal(cb, { showRequired: true })) valid = false;
    });
    if (!valid) {
      e.preventDefault();
      const first = form.querySelector('.auth-input.is-invalid')
                 || legalCheckboxes.find(cb => !cb.checked) || null;
      if (first) { first.focus(); first.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
      return;
    }
    /* Loading state */
    if (submitBtn) {
      submitBtn.classList.add('loading');
      submitBtn.disabled = true;
    }
  });

  /* ── Password toggles ── */
  document.querySelectorAll('[data-toggle-password]').forEach(btn => {
    btn.addEventListener('click', () => {
      const inp = document.getElementById(btn.dataset.target);
      if (!inp) return;
      const isPassword = inp.type === 'password';
      inp.type = isPassword ? 'text' : 'password';
      btn.classList.toggle('is-visible', isPassword);
      btn.setAttribute('aria-pressed', String(isPassword));
      btn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
    });
  });

  /* ── Legal modal ── */
  const openLegal = panel => {
    if (!legalModal) return;
    legalPanels.forEach(p => p.classList.toggle('active', p.dataset.legalPanel === panel));
    legalModalTitle.textContent = panel === 'privacy' ? 'Privacy Policy' : 'Terms and Conditions';
    legalModal.classList.add('active');
    legalModal.setAttribute('aria-hidden', 'false');
  };
  const closeLegal = () => {
    legalModal?.classList.remove('active');
    legalModal?.setAttribute('aria-hidden', 'true');
  };

  document.querySelectorAll('[data-open-legal]').forEach(b => b.addEventListener('click', () => openLegal(b.dataset.openLegal)));
  document.querySelectorAll('[data-close-legal]').forEach(b => b.addEventListener('click', closeLegal));
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && legalModal?.classList.contains('active')) closeLegal(); });

  /* ── Init ── */
  updateStrength('');
  updateProgress();
})();
</script>
</x-guest-layout>
