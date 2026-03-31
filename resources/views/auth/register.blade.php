<x-guest-layout>
    <style>
        .auth-title { margin: 0; font-size: 30px; font-weight: 800; letter-spacing: -.3px; color: #0f172a; }
        .auth-sub { margin: 8px 0 22px; color: #64748b; font-size: 14px; }
        .auth-grid { display: grid; grid-template-columns: 1fr; gap: 14px; }
        .auth-label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #334155; }
        .auth-input {
            width: 100%;
            border: 1px solid #dbe3f0;
            border-radius: 12px;
            padding: 11px 12px;
            font-size: 14px;
            outline: none;
            background: #fff;
            color: #0f172a;
        }
        .auth-input::placeholder { color: #94a3b8; }
        .auth-input:focus { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, .14); }
        .auth-input.is-invalid { border-color: #dc2626; box-shadow: 0 0 0 4px rgba(220, 38, 38, .12); }
        .auth-input.is-valid { border-color: #16a34a; box-shadow: 0 0 0 4px rgba(34, 197, 94, .12); }
        .auth-error { margin-top: 6px; color: #b91c1c; font-size: 12px; }
        .auth-error:empty { display: none; }
        .auth-success { margin-top: 6px; color: #15803d; font-size: 12px; }
        .auth-success:empty { display: none; }
        .auth-btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 12px;
            margin-top: 18px;
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            cursor: pointer;
        }
        .auth-btn:hover { filter: brightness(1.04); }
        .auth-foot { margin-top: 14px; text-align: center; color: #64748b; font-size: 13px; }
        .auth-link { color: #2563eb; text-decoration: none; font-size: 13px; font-weight: 700; }
        .auth-link:hover { text-decoration: underline; }
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
                <input id="password" class="auth-input @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" data-label="Password" data-rule="password" aria-invalid="@error('password') true @else false @enderror">
                <div class="auth-error" data-error-for="password">@error('password'){{ $message }}@enderror</div>
                <div class="auth-success" data-success-for="password"></div>
            </div>

            <div>
                <label class="auth-label" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" class="auth-input @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" required autocomplete="new-password" data-label="Password confirmation" data-rule="password_confirmation" aria-invalid="@error('password_confirmation') true @else false @enderror">
                <div class="auth-error" data-error-for="password_confirmation">@error('password_confirmation'){{ $message }}@enderror</div>
                <div class="auth-success" data-success-for="password_confirmation"></div>
            </div>
        </div>

        <button type="submit" class="auth-btn">Register</button>

        <div class="auth-foot">
            Already registered?
            <a class="auth-link" href="{{ route('login') }}">Log in</a>
        </div>
    </form>

    <script>
        (function () {
            const form = document.querySelector('[data-live-validate="register"]');

            if (!form) return;

            const touchedFields = new WeakMap();
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
                    if (value.length < 8) {
                        message = 'Use at least 8 characters for your password.';
                    } else {
                        success = 'Password length looks good.';
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
                    validateField(input, { showRequired: true });

                    if (input.name === 'password') {
                        const confirmationInput = form.querySelector('[name="password_confirmation"]');
                        if (confirmationInput && (confirmationInput.value.trim() !== '' || touchedFields.get(confirmationInput) === true)) {
                            validateField(confirmationInput, { showRequired: touchedFields.get(confirmationInput) === true });
                        }
                    }
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

                if (!isValid) {
                    event.preventDefault();
                    const firstInvalidField = form.querySelector('.auth-input.is-invalid');
                    if (firstInvalidField) {
                        firstInvalidField.focus();
                    }
                }
            });
        })();
    </script>
</x-guest-layout>
