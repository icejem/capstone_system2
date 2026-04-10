<x-guest-layout>
    <style>
        .auth-title { margin: 0; font-size: 30px; font-weight: 800; letter-spacing: -.3px; }
        .auth-sub { margin: 6px 0 16px; color: #64748b; font-size: 14px; }
        .auth-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
        .auth-label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #334155; }
        .auth-input {
            width: 100%;
            border: 1px solid #dbe3f0;
            border-radius: 12px;
            padding: 10px 11px;
            font-size: 14px;
            outline: none;
            background: #fff;
            color: #0f172a;
        }
        .auth-input::placeholder { color: #94a3b8; }
        .auth-input:focus { border-color: #6f42c1; box-shadow: 0 0 0 4px rgba(111, 66, 193, .2); }
        .auth-input.is-invalid { border-color: #dc2626; box-shadow: 0 0 0 4px rgba(220, 38, 38, .12); }
        .auth-input.is-valid { border-color: #16a34a; box-shadow: 0 0 0 4px rgba(34, 197, 94, .12); }
        .auth-input-wrap { position: relative; }
        .auth-input.has-toggle { padding-right: 44px; }
        .auth-password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 0;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            opacity: .85;
        }
        .auth-password-toggle:hover { opacity: 1; }
        .auth-password-toggle svg { width: 18px; height: 18px; display: block; }
        .auth-password-toggle .eye-off { display: none; }
        .auth-password-toggle.is-visible .eye-on { display: none; }
        .auth-password-toggle.is-visible .eye-off { display: block; }
        .auth-error { margin-top: 6px; color: #b91c1c; font-size: 12px; }
        .auth-error:empty { display: none; }
        .auth-success { margin-top: 6px; color: #15803d; font-size: 12px; }
        .auth-success:empty { display: none; }
        .auth-password-rules {
            margin-top: 8px;
            padding: 10px 12px;
            border: 1px solid #dbe3f0;
            border-radius: 12px;
            background: #f8fafc;
            font-size: 12px;
            color: #475569;
        }
        .auth-password-rules-title { margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #0f172a; }
        .auth-password-rule-list { margin: 0; padding-left: 18px; display: grid; gap: 4px; }
        .auth-password-rule.is-met { color: #15803d; }
        .auth-btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 11px;
            margin-top: 10px;
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #6f42c1, #59339d);
            cursor: pointer;
        }
        .auth-btn:hover { filter: brightness(1.04); }
        .auth-foot { margin-top: 8px; text-align: center; color: #64748b; font-size: 13px; }
        .auth-link { color: #6f42c1; text-decoration: none; font-size: 13px; font-weight: 700; }
        .auth-link:hover { text-decoration: underline; }
        .auth-consent-wrap { margin-top: 8px; display: grid; gap: 8px; }
        .auth-consent-check {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 10px 12px;
            border: 1px solid #dbe3f0;
            border-radius: 12px;
            background: #f8fafc;
            color: #334155;
            font-size: 12px;
            line-height: 1.5;
        }
        .auth-consent-check input { margin-top: 2px; accent-color: #2563eb; }
        .auth-consent-check strong { color: #0f172a; }
        .auth-legal-link { border: 0; background: transparent; padding: 0; color: #2563eb; font-weight: 700; text-decoration: underline; cursor: pointer; font: inherit; }
        .auth-legal-summary { font-size: 12px; color: #64748b; line-height: 1.55; }
        .legal-modal-shell {
            position: fixed;
            inset: 0;
            z-index: 1500;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }
        .legal-modal-shell.active { display: flex; }
        .legal-modal-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.52); backdrop-filter: blur(4px); }
        .legal-modal-card {
            position: relative;
            width: min(760px, 100%);
            max-height: calc(100vh - 36px);
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, 0.26);
            background: #ffffff;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.24);
        }
        .legal-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .legal-modal-title { margin: 0; font-size: 18px; font-weight: 800; color: #0f172a; }
        .legal-modal-close {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 1px solid #dbe3f0;
            background: #ffffff;
            color: #475569;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
        }
        .legal-modal-body {
            max-height: calc(100vh - 132px);
            overflow-y: auto;
            padding: 16px 18px 18px;
            color: #475569;
            font-size: 13px;
            line-height: 1.7;
        }
        .legal-modal-panel { display: none; }
        .legal-modal-panel.active { display: block; }
        .legal-modal-body p { margin: 0 0 14px; }
        .legal-modal-body p:last-child { margin-bottom: 0; }
    </style>

    <h2 class="auth-title">Create Account</h2>
    <p class="auth-sub">Set up your account to access consultations.</p>

    <form method="POST" action="{{ route('register') }}" novalidate data-live-validate="register">
        @csrf

        <div class="auth-grid">
            <div>
                <label class="auth-label" for="first_name">First Name</label>
                <input id="first_name" class="auth-input @error('first_name') is-invalid @enderror" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus autocomplete="given-name" data-label="First name" data-rule="name" aria-invalid="@error('first_name') true @else false @enderror">
                <div class="auth-error" data-error-for="first_name">@error('first_name'){{ $message }}@enderror</div>
                <div class="auth-success" data-success-for="first_name"></div>
            </div>

            <div>
                <label class="auth-label" for="last_name">Last Name</label>
                <input id="last_name" class="auth-input @error('last_name') is-invalid @enderror" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" data-label="Last name" data-rule="name" aria-invalid="@error('last_name') true @else false @enderror">
                <div class="auth-error" data-error-for="last_name">@error('last_name'){{ $message }}@enderror</div>
                <div class="auth-success" data-success-for="last_name"></div>
            </div>

            <div>
                <label class="auth-label" for="middle_name">Middle Name <span style="color: #94a3b8; font-weight: 400;">(Optional)</span></label>
                <input id="middle_name" class="auth-input @error('middle_name') is-invalid @enderror" type="text" name="middle_name" value="{{ old('middle_name') }}" autocomplete="additional-name" data-label="Middle name" data-rule="name" data-optional="true" aria-invalid="@error('middle_name') true @else false @enderror">
                <div class="auth-error" data-error-for="middle_name">@error('middle_name'){{ $message }}@enderror</div>
                <div class="auth-success" data-success-for="middle_name"></div>
            </div>

            <div>
                <label class="auth-label" for="email">Email</label>
                <input id="email" class="auth-input @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" data-label="Email" data-rule="email" aria-invalid="@error('email') true @else false @enderror">
                <div class="auth-error" data-error-for="email">@error('email'){{ $message }}@enderror</div>
                <div class="auth-success" data-success-for="email"></div>
            </div>

            <input type="hidden" name="user_type" value="student">

            <!-- Student-only fields -->
            <div id="student-fields" class="student-fields">
                <div>
                    <label class="auth-label" for="student_id">Student ID</label>
                    <input id="student_id" class="auth-input @error('student_id') is-invalid @enderror" type="text" name="student_id" value="{{ old('student_id') }}" placeholder="Enter 8-digit Student ID" autocomplete="off" inputmode="numeric" pattern="\d{8}" minlength="8" maxlength="8" required data-label="Student ID" data-rule="student_id" aria-invalid="@error('student_id') true @else false @enderror">
                    <div class="auth-error" data-error-for="student_id">@error('student_id'){{ $message }}@enderror</div>
                    <div class="auth-success" data-success-for="student_id"></div>
                </div>

                <div>
                    <label class="auth-label" for="yearlevel">Year Level</label>
                    <select id="yearlevel" class="auth-input" name="yearlevel">
                        <option value="">Select Year Level</option>
                        <option value="1st Year" @selected(old('yearlevel') === '1st Year')>1st Year</option>
                        <option value="2nd Year" @selected(old('yearlevel') === '2nd Year')>2nd Year</option>
                        <option value="3rd Year" @selected(old('yearlevel') === '3rd Year')>3rd Year</option>
                        <option value="4th Year" @selected(old('yearlevel') === '4th Year')>4th Year</option>
                    </select>
                    @error('yearlevel')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div>
                <label class="auth-label" for="password">Password</label>
                <div class="auth-input-wrap">
                    <input id="password" class="auth-input has-toggle @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" data-label="Password" data-rule="password" aria-invalid="@error('password') true @else false @enderror">
                    <button type="button" class="auth-password-toggle" data-toggle-password data-target="password" aria-label="Show password" aria-pressed="false">
                        <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6.5 0-10-7-10-7a21.77 21.77 0 0 1 5.06-5.94"></path>
                            <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c6.5 0 10 7 10 7a21.8 21.8 0 0 1-3.32 4.61"></path>
                            <path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"></path>
                            <path d="M3 3l18 18"></path>
                        </svg>
                    </button>
                </div>
                <div class="auth-error" data-error-for="password">@error('password'){{ $message }}@enderror</div>
                <div class="auth-success" data-success-for="password"></div>
                <div class="auth-password-rules" data-password-rules>
                    <p class="auth-password-rules-title">Password requirements</p>
                    <ul class="auth-password-rule-list">
                        <li class="auth-password-rule" data-password-rule="length">At least 8 characters long</li>
                        <li class="auth-password-rule" data-password-rule="lower">At least one lowercase letter</li>
                        <li class="auth-password-rule" data-password-rule="upper">At least one uppercase letter</li>
                        <li class="auth-password-rule" data-password-rule="number">At least one number</li>
                        <li class="auth-password-rule" data-password-rule="special">At least one special character like `!@#$%^&*`</li>
                    </ul>
                </div>
            </div>

            <div>
                <label class="auth-label" for="password_confirmation">Confirm Password</label>
                <div class="auth-input-wrap">
                    <input id="password_confirmation" class="auth-input has-toggle @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" required autocomplete="new-password" data-label="Password confirmation" data-rule="password_confirmation" aria-invalid="@error('password_confirmation') true @else false @enderror">
                    <button type="button" class="auth-password-toggle" data-toggle-password data-target="password_confirmation" aria-label="Show password confirmation" aria-pressed="false">
                        <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-6.5 0-10-7-10-7a21.77 21.77 0 0 1 5.06-5.94"></path>
                            <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c6.5 0 10 7 10 7a21.8 21.8 0 0 1-3.32 4.61"></path>
                            <path d="M14.12 14.12A3 3 0 1 1 9.88 9.88"></path>
                            <path d="M3 3l18 18"></path>
                        </svg>
                    </button>
                </div>
                <div class="auth-error" data-error-for="password_confirmation">@error('password_confirmation'){{ $message }}@enderror</div>
                <div class="auth-success" data-success-for="password_confirmation"></div>
            </div>
        </div>

        <button type="submit" class="auth-btn" data-submit-register>Register</button>

        <div class="auth-consent-wrap">
            <label class="auth-consent-check" for="terms_accepted">
                <input id="terms_accepted" type="checkbox" name="terms_accepted" value="1" data-legal-checkbox="terms" @checked(old('terms_accepted'))>
                <span><strong>I agree</strong> to the <button type="button" class="auth-legal-link" data-open-legal="terms">Terms and Conditions</button>.</span>
            </label>
            <div class="auth-error" data-error-for="terms_accepted">@error('terms_accepted'){{ $message }}@enderror</div>
            <label class="auth-consent-check" for="privacy_accepted">
                <input id="privacy_accepted" type="checkbox" name="privacy_accepted" value="1" data-legal-checkbox="privacy" @checked(old('privacy_accepted'))>
                <span><strong>I agree</strong> to the <button type="button" class="auth-legal-link" data-open-legal="privacy">Privacy Policy</button>.</span>
            </label>
            <div class="auth-error" data-error-for="privacy_accepted">@error('privacy_accepted'){{ $message }}@enderror</div>
            <div class="auth-legal-summary">Please review both documents before creating your account.</div>
        </div>

        <div class="auth-foot">
            Already registered?
            <a class="auth-link" href="{{ route('login') }}">Log in</a>
        </div>
    </form>

    <div class="legal-modal-shell" id="legalModal" aria-hidden="true">
        <div class="legal-modal-backdrop" data-close-legal></div>
        <div class="legal-modal-card" role="dialog" aria-modal="true" aria-labelledby="legalModalTitle">
            <div class="legal-modal-head">
                <h3 class="legal-modal-title" id="legalModalTitle">Terms and Conditions</h3>
                <button type="button" class="legal-modal-close" data-close-legal aria-label="Close legal document">&times;</button>
            </div>
            <div class="legal-modal-body">
                <article class="legal-modal-panel active" data-legal-panel="terms">
                    <p><strong>User Terms and Conditions</strong></p>
                    <p><strong>Account Information</strong><br>Users must provide accurate and complete information when creating an account in the system. Any false or misleading information may result in restricted access or account suspension.</p>
                    <p><strong>Account Responsibility</strong><br>Users are responsible for keeping their login credentials confidential. Any activity performed using a registered account is considered the responsibility of the account owner.</p>
                    <p><strong>Proper Use of the System</strong><br>The system must only be used for academic and consultation-related purposes. Users must communicate respectfully and avoid inappropriate language, misuse, or unauthorized activities within the platform.</p>
                    <p><strong>System Availability</strong><br>The system may occasionally undergo maintenance, updates, or temporary interruptions. Users understand that access may not always be available during these periods.</p>
                    <p><strong>Suspension of Access</strong><br>The system administrator reserves the right to suspend or terminate access if a user violates system policies, disrupts operations, or performs actions that may affect security and reliability.</p>
                </article>
                <article class="legal-modal-panel" data-legal-panel="privacy">
                    <p><strong>Privacy Policy</strong></p>
                    <p><strong>Collection of Information</strong><br>The system collects necessary personal information such as name, email address, and account details to support registration, consultation scheduling, and communication between users.</p>
                    <p><strong>Use of Information</strong><br>Collected information is used only for academic consultation purposes, system management, and improving user experience within the platform.</p>
                    <p><strong>Protection of Data</strong><br>Personal data stored in the system is protected through appropriate security measures to prevent unauthorized access, loss, or misuse of information.</p>
                    <p><strong>Access to Information</strong><br>Only authorized administrators and permitted users may access personal information when necessary for system operations and academic transactions.</p>
                    <p><strong>Policy Updates</strong><br>The system administration may update this Privacy Policy when needed. Users will be informed of significant changes affecting the handling of personal information.</p>
                </article>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.querySelector('[data-live-validate="register"]');
            const legalModal = document.getElementById('legalModal');
            const legalModalTitle = document.getElementById('legalModalTitle');
            const legalOpenButtons = Array.from(document.querySelectorAll('[data-open-legal]'));
            const legalCloseButtons = Array.from(document.querySelectorAll('[data-close-legal]'));
            const legalPanels = Array.from(document.querySelectorAll('[data-legal-panel]'));
            const passwordRuleIndicators = Array.from(form.querySelectorAll('[data-password-rule]'));

            if (!form) return;

            const touchedFields = new WeakMap();
            const submitButton = form.querySelector('[data-submit-register]');
            const legalCheckboxes = Array.from(form.querySelectorAll('[data-legal-checkbox]'));
            const namePattern = /^(?=.*\p{L})[\p{L}\s'-]+$/u;
            const gmailPattern = /^[^\s@]+@gmail\.com$/i;
            const normalizeWhitespace = (value) => value.replace(/\s+/gu, ' ').trim();
            const normalizeName = (value) => normalizeWhitespace(value).replace(/[^\p{L}]/gu, '').toLowerCase();
            const countVowels = (value) => (value.match(/[aeiouy]/gu) || []).length;
            const longestConsonantRun = (value) => {
                let longest = 0;
                let current = 0;

                Array.from(value).forEach((character) => {
                    if (/[aeiouy]/iu.test(character)) {
                        current = 0;
                        return;
                    }

                    current += 1;
                    if (current > longest) {
                        longest = current;
                    }
                });

                return longest;
            };
            const inputs = Array.from(form.querySelectorAll('.auth-input[name]'));
            const evaluatePasswordRules = (value) => ({
                length: value.length >= 8,
                lower: /[a-z]/.test(value),
                upper: /[A-Z]/.test(value),
                number: /\d/.test(value),
                special: /[^A-Za-z0-9]/.test(value),
            });
            const updatePasswordRuleIndicators = (value) => {
                const results = evaluatePasswordRules(value);

                passwordRuleIndicators.forEach((indicator) => {
                    const ruleName = indicator.dataset.passwordRule;
                    indicator.classList.toggle('is-met', Boolean(results[ruleName]));
                });
            };

            const setFieldState = (input, message, success = '') => {
                const errorEl = form.querySelector(`[data-error-for="${input.name}"]`);
                const successEl = form.querySelector(`[data-success-for="${input.name}"]`);
                if (errorEl) {
                    errorEl.textContent = message;
                }
                if (successEl) {
                    successEl.textContent = message ? '' : success;
                }

                input.classList.toggle('is-invalid', Boolean(message));
                input.classList.toggle('is-valid', !message && Boolean(success));
                input.setAttribute('aria-invalid', message ? 'true' : 'false');
            };

            const validateField = (input, options = {}) => {
                const showRequired = options.showRequired === true;
                const value = normalizeWhitespace(input.value);
                const label = input.dataset.label || input.name;
                const rule = input.dataset.rule || '';
                const optional = input.dataset.optional === 'true';
                let message = '';
                let success = '';

                input.setCustomValidity('');

                if (!value) {
                    if (!optional && input.required && showRequired) {
                        message = `${label} is required.`;
                    }
                } else if (rule === 'name') {
                    const normalized = normalizeName(value);
                    const vowelCount = countVowels(normalized);

                    if (!namePattern.test(value)) {
                        message = 'Names should only contain letters, spaces, hyphens, or apostrophes.';
                    } else if (normalized.length < 2) {
                        message = 'Please enter a real name.';
                    } else if (normalized.length > 50 || value.length > 60) {
                        message = "This doesn't look like a valid name.";
                    } else if (/(\p{L})\1{3,}/u.test(normalized)) {
                        message = 'Please enter a real name.';
                    } else if (/(\p{L}{2,4})\1{2,}/u.test(normalized)) {
                        message = 'Please avoid random or meaningless text.';
                    } else if (normalized.length >= 4 && vowelCount === 0) {
                        message = "This doesn't look like a valid name.";
                    } else if (normalized.length >= 8 && (vowelCount / normalized.length) < 0.23) {
                        message = 'Please avoid random or meaningless text.';
                    } else if (normalized.length >= 10 && longestConsonantRun(normalized) >= 5) {
                        message = "This doesn't look like a valid name.";
                    } else {
                        success = 'Looks good.';
                    }
                } else if (rule === 'email') {
                    if (!gmailPattern.test(value.toLowerCase())) {
                        message = 'Please enter a valid Gmail address.';
                    } else {
                        success = "This Gmail looks good. We'll verify it after signup.";
                    }
                } else if (rule === 'student_id') {
                    if (!/^\d{8}$/.test(value)) {
                        message = 'Student ID must be exactly 8 digits.';
                    } else {
                        success = 'Student ID format looks good.';
                    }
                } else if (rule === 'password') {
                    const passwordChecks = evaluatePasswordRules(input.value);

                    if (input.value.length < 8) {
                        message = 'Password must be at least 8 characters long.';
                    } else if (!passwordChecks.lower) {
                        message = 'Password must include at least one lowercase letter (a-z).';
                    } else if (!passwordChecks.upper) {
                        message = 'Password must include at least one uppercase letter (A-Z).';
                    } else if (!passwordChecks.number) {
                        message = 'Password must include at least one number (0-9).';
                    } else if (!passwordChecks.special) {
                        message = 'Password must include at least one special character (e.g., !@#$%^&*).';
                    } else {
                        success = 'Password meets all requirements.';
                    }
                } else if (rule === 'password_confirmation') {
                    const passwordInput = form.querySelector('[name="password"]');
                    if (passwordInput && value !== passwordInput.value) {
                        message = 'Passwords do not match.';
                    } else {
                        success = 'Passwords match.';
                    }
                }

                input.setCustomValidity(message);
                setFieldState(input, message, success);

                return message === '';
            };

            const validateLegalCheckbox = (checkbox, options = {}) => {
                if (!checkbox) return true;

                const showRequired = options.showRequired === true;
                const shouldShow = showRequired || touchedFields.get(checkbox) === true;
                const errorEl = form.querySelector(`[data-error-for="${checkbox.name}"]`);
                const label = checkbox.dataset.legalCheckbox === 'privacy'
                    ? 'Privacy Policy'
                    : 'Terms and Conditions';
                const message = checkbox.checked || !shouldShow
                    ? ''
                    : `Please read and accept the ${label}.`;

                if (errorEl) {
                    errorEl.textContent = message;
                }

                return message === '';
            };

            const updateSubmitState = () => {
                if (!submitButton) return;

                const fieldsValid = inputs.every((input) => {
                    if (!form.querySelector(`[data-error-for="${input.name}"]`)) {
                        return true;
                    }

                    return validateField(input, { showRequired: false });
                });

                const legalValid = legalCheckboxes.every((checkbox) => validateLegalCheckbox(checkbox, { showRequired: false }));
                submitButton.disabled = !(fieldsValid && legalValid);
            };

            const openLegalPanel = (panelName) => {
                if (!legalModal) return;

                const target = panelName === 'privacy' ? 'privacy' : 'terms';
                legalPanels.forEach((panel) => {
                    panel.classList.toggle('active', panel.dataset.legalPanel === target);
                });
                if (legalModalTitle) {
                    legalModalTitle.textContent = target === 'privacy' ? 'Privacy Policy' : 'Terms and Conditions';
                }
                legalModal.classList.add('active');
                legalModal.setAttribute('aria-hidden', 'false');
            };

            const closeLegalModal = () => {
                if (!legalModal) return;
                legalModal.classList.remove('active');
                legalModal.setAttribute('aria-hidden', 'true');
            };

            legalOpenButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    openLegalPanel(button.dataset.openLegal || 'terms');
                });
            });

            legalCloseButtons.forEach((button) => {
                button.addEventListener('click', closeLegalModal);
            });

            inputs.forEach((input) => {
                if (!form.querySelector(`[data-error-for="${input.name}"]`)) {
                    return;
                }

                input.addEventListener('beforeinput', (event) => {
                    if (input.dataset.rule !== 'name' || !event.data || !/[^\p{L}\s'.-]/u.test(event.data)) {
                        return;
                    }

                    event.preventDefault();
                    touchedFields.set(input, true);
                    input.setCustomValidity('Names should only contain letters, spaces, hyphens, or apostrophes.');
                    setFieldState(input, 'Names should only contain letters, spaces, hyphens, or apostrophes.');
                });

                input.addEventListener('input', () => {
                    touchedFields.set(input, true);

                    if (input.dataset.rule === 'email') {
                        input.value = input.value.replace(/\s+/gu, '').toLowerCase();
                    }

                    if (input.dataset.rule === 'password') {
                        updatePasswordRuleIndicators(input.value);
                    }

                    validateField(input, { showRequired: touchedFields.get(input) === true });

                    if (input.name === 'password') {
                        const confirmationInput = form.querySelector('[name="password_confirmation"]');
                        if (confirmationInput && (confirmationInput.value.trim() !== '' || touchedFields.get(confirmationInput) === true)) {
                            validateField(confirmationInput, { showRequired: touchedFields.get(confirmationInput) === true });
                        }
                    }
                });

                input.addEventListener('blur', () => {
                    touchedFields.set(input, true);

                    if (input.dataset.rule === 'password') {
                        updatePasswordRuleIndicators(input.value);
                    }

                    validateField(input, { showRequired: true });

                    if (input.name === 'password') {
                        const confirmationInput = form.querySelector('[name="password_confirmation"]');
                        if (confirmationInput && (confirmationInput.value.trim() !== '' || touchedFields.get(confirmationInput) === true)) {
                            validateField(confirmationInput, { showRequired: touchedFields.get(confirmationInput) === true });
                        }
                    }
                });
            });

            legalCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    touchedFields.set(checkbox, true);
                    validateLegalCheckbox(checkbox, { showRequired: true });
                    updateSubmitState();
                });
            });

            form.addEventListener('submit', (event) => {
                let isValid = true;

                inputs.forEach((input) => {
                    if (!form.querySelector(`[data-error-for="${input.name}"]`)) {
                        return;
                    }

                    touchedFields.set(input, true);

                    if (!validateField(input, { showRequired: true })) {
                        isValid = false;
                    }
                });

                legalCheckboxes.forEach((checkbox) => {
                    touchedFields.set(checkbox, true);
                    if (!validateLegalCheckbox(checkbox, { showRequired: true })) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                    const firstInvalidField = form.querySelector('.auth-input.is-invalid')
                        || legalCheckboxes.find((checkbox) => !checkbox.checked)
                        || null;
                    if (firstInvalidField) {
                        firstInvalidField.focus();
                    }
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && legalModal?.classList.contains('active')) {
                    closeLegalModal();
                }
            });

            document.querySelectorAll('[data-toggle-password]').forEach((button) => {
                button.addEventListener('click', () => {
                    const targetId = button.getAttribute('data-target');
                    const input = targetId ? document.getElementById(targetId) : null;

                    if (!input) return;

                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    button.classList.toggle('is-visible', isPassword);
                    button.setAttribute('aria-pressed', String(isPassword));
                    button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                });
            });

            updatePasswordRuleIndicators(form.querySelector('[name="password"]')?.value || '');
            updateSubmitState();
        })();
    </script>
</x-guest-layout>
