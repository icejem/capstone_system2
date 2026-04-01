<x-guest-layout>
    <style>
        .auth-title {
            margin: 0;
            font-size: clamp(28px, 2.8vw, 38px);
            font-weight: 800;
            letter-spacing: 0.01em;
            color: #ecf8ff;
            font-family: "Orbitron", "Franklin Gothic Medium", sans-serif;
            text-shadow: 0 4px 16px rgba(24, 211, 255, 0.24);
        }
        .auth-sub { margin: 10px 0 22px; color: #9fc8df; font-size: 14px; }
        .auth-field { margin-bottom: 14px; }
        .auth-label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 800;
            color: #b9def2;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .auth-input {
            width: 100%;
            border: 1px solid rgba(125, 211, 252, 0.34);
            border-radius: 13px;
            padding: 11px 12px;
            font-size: 14px;
            outline: none;
            background: rgba(7, 23, 48, 0.76);
            color: #e8f8ff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .auth-input::placeholder { color: #7fa5bf; }
        .auth-input:focus {
            border-color: #38bdf8;
            background: rgba(9, 30, 61, 0.9);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.22);
        }
        .auth-password-wrap {
            position: relative;
        }
        .auth-password-wrap .auth-input {
            padding-right: 48px;
        }
        .auth-password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border: 0;
            background: transparent;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0.88;
        }
        .auth-password-toggle:hover {
            opacity: 1;
        }
        .auth-password-toggle svg {
            width: 18px;
            height: 18px;
            display: block;
        }
        .auth-password-toggle .eye-off {
            display: none;
        }
        .auth-password-toggle.is-visible .eye-on {
            display: none;
        }
        .auth-password-toggle.is-visible .eye-off {
            display: block;
        }
        .auth-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin: 14px 0 18px; }
        .auth-check { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; color: #a7cce2; }
        .auth-check input { accent-color: #22d3ee; }
        .auth-link { color: #55d8ff; text-decoration: none; font-size: 13px; font-weight: 700; }
        .auth-link:hover { text-decoration: underline; }
        .auth-btn {
            width: 100%;
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 13px;
            padding: 12px;
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #2563eb 55%, #1d4ed8);
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(30, 64, 175, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }
        .auth-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -130%;
            width: 120%;
            height: 100%;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.32), transparent);
            transition: left 0.45s ease;
        }
        .auth-btn:hover {
            filter: brightness(1.04);
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(30, 64, 175, 0.42);
        }
        .auth-btn:hover::before { left: 120%; }
        .auth-error { margin-top: 6px; color: #fecaca; font-size: 12px; }
        .auth-foot { margin-top: 16px; text-align: center; color: #9cc2d8; font-size: 13px; }
        .auth-copy {
            margin-top: 22px;
            padding-top: 14px;
            text-align: center;
            color: #7fa5bf;
            font-size: 12px;
            border-top: 1px solid rgba(125, 211, 252, 0.16);
        }
        .auth-status {
            margin-bottom: 12px;
            border: 1px solid rgba(74, 222, 128, 0.45);
            background: rgba(34, 197, 94, 0.14);
            color: #bbf7d0;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 13px;
        }
    </style>

    <h2 class="auth-title">Welcome Back</h2>
    <p class="auth-sub">Sign in to continue to your consultation command center.</p>

    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="email">Email</label>
            <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-field">
            <label class="auth-label" for="password">Password</label>
            <div class="auth-password-wrap">
                <input id="password" class="auth-input" type="password" name="password" required autocomplete="current-password">
                <button type="button" class="auth-password-toggle" data-toggle-password="password" aria-label="Show password" aria-pressed="false">
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
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="auth-row">
            <label class="auth-check" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="auth-btn">Log In</button>

        <div class="auth-foot">
            No account yet?
            <a class="auth-link" href="{{ route('register') }}">Register</a>
        </div>

        <div class="auth-copy">
            &copy; 2026 PHILCST - College of Computer Studies. All rights reserved.
        </div>
    </form>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.togglePassword);
                if (!target) return;

                const isPassword = target.type === 'password';
                target.type = isPassword ? 'text' : 'password';
                button.classList.toggle('is-visible', isPassword);
                button.setAttribute('aria-pressed', String(isPassword));
                button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
        });
    </script>
</x-guest-layout>
