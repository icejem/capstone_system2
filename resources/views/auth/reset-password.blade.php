<x-guest-layout>
    <style>
        .auth-card {
            width: min(560px, 100%);
            min-height: auto;
            grid-template-columns: 1fr;
            background: transparent;
            border: none;
            box-shadow: none;
            backdrop-filter: none;
        }

        .auth-art {
            display: none;
        }

        .auth-content {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            min-height: min(100vh, 760px);
            background: transparent;
        }

        .auth-content::before {
            display: none;
        }

        .reset-card {
            width: min(480px, 100%);
            margin: 0 auto;
            padding: 18px 18px 16px;
            border-radius: 18px;
            border: 1px solid rgba(96, 165, 250, 0.42);
            background: linear-gradient(180deg, rgba(10, 28, 56, 0.96) 0%, rgba(9, 24, 48, 0.98) 100%);
            box-shadow: 0 22px 40px rgba(2, 12, 28, 0.4), inset 0 0 0 1px rgba(255, 255, 255, 0.04);
        }

        .reset-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .reset-card-title {
            margin: 0;
            color: #eef6ff;
            font-size: clamp(18px, 2vw, 22px);
            font-weight: 800;
            line-height: 1.1;
        }

        .reset-card-close {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(96, 165, 250, 0.5);
            background: rgba(19, 47, 89, 0.78);
            color: #e7f1ff;
            text-decoration: none;
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
            transition: background 0.18s ease, transform 0.18s ease;
        }

        .reset-card-close:hover {
            background: rgba(37, 99, 235, 0.34);
            transform: translateY(-1px);
        }

        .reset-field + .reset-field {
            margin-top: 12px;
        }

        .reset-label {
            display: block;
            margin-bottom: 6px;
            color: #dbeafe;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .reset-input-wrap {
            position: relative;
        }

        .reset-input {
            width: 100%;
            height: 38px;
            border-radius: 12px;
            border: 1px solid rgba(96, 165, 250, 0.34);
            background: rgba(12, 33, 66, 0.9);
            color: #f3f8ff;
            padding: 0 14px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .reset-input::placeholder {
            color: #8ca9cb;
        }

        .reset-input:focus {
            border-color: #60a5fa;
            background: rgba(14, 38, 75, 0.96);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .reset-input.has-toggle {
            padding-right: 42px;
        }

        .reset-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border: none;
            background: transparent;
            color: #dbeafe;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            opacity: 0.86;
        }

        .reset-toggle:hover {
            opacity: 1;
        }

        .reset-toggle svg {
            width: 18px;
            height: 18px;
            display: block;
        }

        .reset-toggle .eye-off {
            display: none;
        }

        .reset-toggle.is-visible .eye-on {
            display: none;
        }

        .reset-toggle.is-visible .eye-off {
            display: block;
        }

        .reset-error {
            margin-top: 6px;
            color: #fecaca;
            font-size: 12px;
            font-weight: 600;
        }

        .reset-submit {
            width: 100%;
            height: 38px;
            margin-top: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(90deg, #2f66f3 0%, #3b82f6 100%);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.28);
            transition: transform 0.18s ease, filter 0.18s ease;
        }

        .reset-submit:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
        }

        @media (max-width: 620px) {
            .auth-content {
                min-height: auto;
            }

            .reset-card {
                width: 100%;
            }
        }
    </style>

    <form method="POST" action="{{ route('password.store') }}" class="reset-card">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="reset-card-header">
            <h2 class="reset-card-title">Reset Password</h2>
            <a href="{{ route('login') }}" class="reset-card-close" aria-label="Close reset password form">&times;</a>
        </div>

        <div class="reset-field">
            <label class="reset-label" for="email">Email</label>
            <div class="reset-input-wrap">
                <input
                    id="email"
                    class="reset-input"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    placeholder="you@example.com"
                    required
                    autofocus
                    autocomplete="username"
                >
            </div>
            @error('email')
                <div class="reset-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="reset-field">
            <label class="reset-label" for="password"> New Password</label>
            <div class="reset-input-wrap">
                <input
                    id="password"
                    class="reset-input has-toggle"
                    type="password"
                    name="password"
                    placeholder="Enter password"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="reset-toggle" data-toggle-password="password" aria-label="Show password" aria-pressed="false">
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
                <div class="reset-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="reset-field">
            <label class="reset-label" for="password_confirmation">Confirm Password</label>
            <div class="reset-input-wrap">
                <input
                    id="password_confirmation"
                    class="reset-input has-toggle"
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    required
                    autocomplete="new-password"
                >
                <button type="button" class="reset-toggle" data-toggle-password="password_confirmation" aria-label="Show password confirmation" aria-pressed="false">
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
            @error('password_confirmation')
                <div class="reset-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="reset-submit">Reset Password</button>
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
