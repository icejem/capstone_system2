<x-guest-layout>
    <style>
        .auth-title { margin: 0; font-size: 30px; font-weight: 800; letter-spacing: -.3px; }
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
        }
        .auth-input:focus { border-color: #6f42c1; box-shadow: 0 0 0 4px rgba(111, 66, 193, .2); }
        .auth-error { margin-top: 6px; color: #b91c1c; font-size: 12px; }
        .auth-btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 12px;
            margin-top: 18px;
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #6f42c1, #59339d);
            cursor: pointer;
        }
        .auth-btn:hover { filter: brightness(1.04); }
        .auth-foot { margin-top: 14px; text-align: center; color: #64748b; font-size: 13px; }
        .auth-link { color: #6f42c1; text-decoration: none; font-size: 13px; font-weight: 700; }
        .auth-link:hover { text-decoration: underline; }
    </style>

    <h2 class="auth-title">Create Account</h2>
    <p class="auth-sub">Set up your account to access consultations.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-grid">
            <div>
                <label class="auth-label" for="first_name">First Name</label>
                <input id="first_name" class="auth-input" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus autocomplete="given-name">
                @error('first_name')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="auth-label" for="last_name">Last Name</label>
                <input id="last_name" class="auth-input" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name">
                @error('last_name')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="auth-label" for="middle_name">Middle Name <span style="color: #94a3b8; font-weight: 400;">(Optional)</span></label>
                <input id="middle_name" class="auth-input" type="text" name="middle_name" value="{{ old('middle_name') }}" autocomplete="additional-name">
                @error('middle_name')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="auth-label" for="email">Email</label>
                <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" name="user_type" value="student">

            <!-- Student-only fields -->
            <div id="student-fields" class="student-fields">
                <div>
                    <label class="auth-label" for="student_id">Student ID</label>
                    <input id="student_id" class="auth-input" type="text" name="student_id" value="{{ old('student_id') }}" placeholder="Enter 8-digit Student ID" autocomplete="off" inputmode="numeric" pattern="\d{8}" minlength="8" maxlength="8" required>
                    @error('student_id')
                        <div class="auth-error">{{ $message }}</div>
                    @enderror
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
                <input id="password" class="auth-input" type="password" name="password" required autocomplete="new-password">
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="auth-label" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" class="auth-input" type="password" name="password_confirmation" required autocomplete="new-password">
                @error('password_confirmation')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="auth-btn">Register</button>

        <div class="auth-foot">
            Already registered?
            <a class="auth-link" href="{{ route('login') }}">Log in</a>
        </div>
    </form>

    <script>
        // No dynamic field toggling needed - all fields are now visible by default
    </script>
</x-guest-layout>
