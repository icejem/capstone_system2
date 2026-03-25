<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Consultation Platform') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('cslogo.jpg') }}">
    <link rel="shortcut icon" href="{{ asset('cslogo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-1: #07122b;
            --bg-2: #0b1e40;
            --ink-1: #f1fbff;
            --ink-2: #9ec3db;
            --line: rgba(112, 195, 255, 0.24);
            --brand-1: #0fd1ff;
            --brand-2: #2a7fff;
            --panel: rgba(6, 22, 52, 0.82);
            --panel-2: rgba(7, 26, 58, 0.9);
            --danger: #ef4444;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Manrope", "Segoe UI", sans-serif;
            color: var(--ink-1);
            min-height: 100vh;
            background:
                radial-gradient(900px 520px at -10% -10%, rgba(15, 209, 255, 0.25) 0%, transparent 62%),
                radial-gradient(800px 460px at 110% 110%, rgba(42, 127, 255, 0.24) 0%, transparent 62%),
                linear-gradient(130deg, var(--bg-1), var(--bg-2));
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(128, 200, 255, 0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(128, 200, 255, 0.07) 1px, transparent 1px);
            background-size: 42px 42px;
            opacity: 0.6;
            animation: gridPan 22s linear infinite;
        }

        @keyframes gridPan {
            from { transform: translateY(0); }
            to { transform: translateY(42px); }
        }

        .page-wrap {
            position: relative;
            max-width: 1160px;
            margin: 0 auto;
            padding: 22px 18px 54px;
        }

        .top-nav {
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 12px;
            border: none;
            background: transparent;
            border-radius: 0;
            padding: 10px 0;
            box-shadow: none;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }

        .brand {
            justify-self: start;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #ffffff;
            font-weight: 900;
            font-size: 24px;
            letter-spacing: 0.03em;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            border: 1px solid rgba(214, 228, 255, 0.7);
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .top-links {
            display: inline-flex;
            align-items: center;
            gap: 20px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            justify-self: center;
        }

        .top-links a {
            color: #0b3f6b;
            text-decoration: none;
            padding: 7px 10px;
            border-radius: 9px;
        }

        .top-links a:hover {
            background: rgba(7, 85, 143, 0.12);
            color: #042f57;
        }

        .top-actions {
            justify-self: end;
        }

        .top-links a {
            text-decoration: none;
            color: inherit;
            transition: color .2s ease;
        }

        .top-links a:hover { color: #e8fbff; }

        .top-actions {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nav-btn {
            border: 1px solid rgba(0, 89, 173, 0.4);
            color: #093e6f;
            background: #ffffff;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: none;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease;
        }

        .nav-btn:hover {
            transform: translateY(-1px);
            background: #eaf4ff;
            box-shadow: 0 10px 18px rgba(14, 68, 122, 0.25);
        }

        .nav-btn.primary {
            border-color: #064a97;
            background: #1762b4;
            color: #ffffff;
            box-shadow: 0 10px 18px rgba(4, 83, 139, 0.35);
        }

        .nav-btn.primary:hover {
            background: #0f4f8f;
            box-shadow: 0 12px 22px rgba(2, 62, 107, 0.45);
        }

        .nav-btn:hover {
            transform: translateY(-1px);
            background: rgba(10, 50, 100, 0.7);
            box-shadow: 0 10px 18px rgba(2, 18, 40, 0.35);
        }

        .nav-btn.primary {
            border-color: transparent;
            background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
            color: #f4fdff;
            box-shadow: 0 12px 22px rgba(10, 66, 145, 0.46);
        }

        .hero {
            margin-top: 54px;
            display: block;
            text-align: center;
        }

        .hero-title {
            margin: 0;
            font-family: "Space Grotesk", "Franklin Gothic Medium", sans-serif;
            font-size: clamp(30px, 4.6vw, 58px);
            line-height: 1.15;
            letter-spacing: 0.01em;
            color: #eaf8ff;
            text-shadow: 0 8px 22px rgba(12, 176, 219, 0.16);
            max-width: 18ch;
            margin-inline: auto;
        }

        .hero-sub {
            margin: 10px auto 26px;
            color: var(--ink-2);
            max-width: 34ch;
            font-size: 18px;
            line-height: 1.35;
        }

        .hero-cta {
            display: inline-flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .modal-shell {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            z-index: 50;
        }

        .modal-shell.active { display: flex; }

        .modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2, 10, 26, 0.74);
            backdrop-filter: blur(6px);
        }

        .auth-modal {
            position: relative;
            width: min(480px, 100%);
            max-height: calc(100vh - 36px);
            overflow-y: auto;
            border-radius: 16px;
            border: 1px solid rgba(120, 206, 255, 0.4);
            background: linear-gradient(150deg, rgba(4, 19, 43, 0.96), rgba(7, 27, 58, 0.96));
            box-shadow: 0 18px 48px rgba(1, 8, 21, 0.6);
            padding: 18px;
            animation: popIn .22s ease;
        }

        .auth-modal.register-mode {
            width: min(700px, 100%);
        }

        @keyframes popIn {
            from { opacity: 0; transform: scale(.98) translateY(8px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .auth-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .auth-title {
            margin: 0;
            font-family: "Space Grotesk", "Franklin Gothic Medium", sans-serif;
            font-size: 24px;
            color: #eaf8ff;
        }

        .auth-close {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid rgba(134, 220, 255, 0.45);
            background: rgba(10, 39, 79, 0.6);
            color: #cde9f8;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
        }

        .auth-status {
            margin-bottom: 10px;
            border: 1px solid rgba(74, 222, 128, 0.5);
            background: rgba(34, 197, 94, 0.12);
            color: #bbf7d0;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 700;
        }

        .auth-grid { display: grid; gap: 10px; }
        .auth-grid-register {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 12px;
        }
        .auth-span-2 { grid-column: 1 / -1; }

        .auth-label {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #afcee2;
        }

        .auth-input {
            width: 100%;
            border: 1px solid rgba(117, 203, 255, 0.35);
            border-radius: 11px;
            padding: 11px 12px;
            font-size: 14px;
            color: #e9f8ff;
            background: rgba(7, 24, 51, 0.78);
            outline: none;
        }

        .auth-input:focus {
            border-color: #33cfff;
            box-shadow: 0 0 0 4px rgba(51, 207, 255, 0.2);
        }

        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 2px;
        }

        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #a8c9dd;
            font-size: 13px;
        }

        .auth-check input { accent-color: #0fd1ff; }

        .auth-link {
            color: #59daff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .auth-link:hover { text-decoration: underline; }

        .auth-btn {
            margin-top: 10px;
            width: 100%;
            border: 0;
            border-radius: 11px;
            padding: 12px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: #f4fdff;
            background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(14, 74, 162, 0.42);
        }

        .auth-btn:hover { filter: brightness(1.05); }

        .auth-error {
            margin-top: 5px;
            color: #fecaca;
            font-size: 12px;
            font-weight: 600;
        }

        .auth-foot {
            margin-top: 12px;
            text-align: center;
            color: #99bfd7;
            font-size: 13px;
        }

        .auth-panel { display: none; }
        .auth-panel.active { display: block; }

        @media (max-width: 980px) {
            .top-links { display: none; }
        }

        @media (max-width: 620px) {
            .page-wrap { padding: 14px 12px 26px; }
            .top-nav { padding: 9px 0 12px; }
            .brand { font-size: 16px; }
            .brand-icon { width: 36px; height: 36px; }
            .top-actions { gap: 6px; }
            .nav-btn { padding: 8px 10px; font-size: 11px; }
            .hero-title {
                font-size: 38px;
                max-width: 14ch;
            }
            .hero-sub {
                font-size: 17px;
                max-width: 30ch;
            }
            .auth-modal { padding: 14px; }
            .auth-grid-register { grid-template-columns: 1fr; }
            .auth-span-2 { grid-column: auto; }
        }
    </style>
</head>
<body>
    <div class="page-wrap">
        <header class="top-nav">
            <a href="{{ route('home') }}" class="brand" aria-label="Home">
                <span class="brand-icon"><img src="{{ asset('cslogo.jpg') }}" alt="CS Logo"></span>
                <span>Computer Studies </span>
            </a>

            <nav class="top-links" aria-label="Primary">
                <a href="#">Features</a>
                <a href="#">About</a>
            </nav>

            <div class="top-actions">
                <button type="button" class="nav-btn" data-open-auth="login">Login</button>
                @if (Route::has('register'))
                    <button type="button" class="nav-btn primary" data-open-auth="register">Register</button>
                @endif
            </div>
        </header>

        <section class="hero">
            <div>
                <h2 class="hero-title">Online Faculty-Student<br>Consultation System</h2>
                <p class="hero-sub">Seamlessly schedule academic sessions with your CCS Faculty. Connect, Learn, and Succeed.</p>
                <div class="hero-cta">
                    <button type="button" class="nav-btn primary" data-open-auth="register">Get Started</button>
                </div>
            </div>
        </section>
    </div>

    <div class="modal-shell" id="authModal" aria-hidden="true">
        <div class="modal-backdrop" data-close-auth></div>

        <div class="auth-modal" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">
            <div class="auth-head">
                <h2 class="auth-title" id="authModalTitle">Account Access</h2>
                <button type="button" class="auth-close" data-close-auth aria-label="Close">×</button>
            </div>

            @if (session('status'))
                <div class="auth-status">{{ session('status') }}</div>
            @endif

            <section class="auth-panel" id="loginPanel">
                <form method="POST" action="{{ route('login') }}" class="auth-grid">
                    @csrf
                    <input type="hidden" name="auth_form" value="login">
                    <div>
                        <label class="auth-label" for="loginEmail">Email</label>
                        <input id="loginEmail" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@example.com">
                        @error('email')<div class="auth-error">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="auth-label" for="loginPassword">Password</label>
                        <input id="loginPassword" class="auth-input" type="password" name="password" required autocomplete="current-password" placeholder="Enter password">
                        @error('password')<div class="auth-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="auth-row">
                        <label class="auth-check" for="remember_me">
                            <input type="hidden" name="remember" value="0">
                            <input id="remember_me" type="checkbox" name="remember" value="1">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="auth-link" data-switch-auth="forgot">Forgot password?</a>
                    </div>

                    <button type="submit" class="auth-btn">Login</button>

                    @if (Route::has('register'))
                        <div class="auth-foot">
                            No account yet?
                            <a href="#" class="auth-link" data-switch-auth="register">Register</a>
                        </div>
                    @endif
                </form>
            </section>

            @if (Route::has('register'))
                <section class="auth-panel" id="registerPanel">
                    <form method="POST" action="{{ route('register') }}" class="auth-grid-register">
                        @csrf
                        <input type="hidden" name="auth_form" value="register">
                        <div>
                            <label class="auth-label" for="registerFirstName">First Name</label>
                            <input id="registerFirstName" class="auth-input" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" placeholder="First name">
                            @error('first_name')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="auth-label" for="registerLastName">Last Name</label>
                            <input id="registerLastName" class="auth-input" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" placeholder="Last name">
                            @error('last_name')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="auth-span-2">
                            <label class="auth-label" for="registerMiddleName">Middle Name (Optional)</label>
                            <input id="registerMiddleName" class="auth-input" type="text" name="middle_name" value="{{ old('middle_name') }}" autocomplete="additional-name" placeholder="Middle name">
                            @error('middle_name')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="auth-label" for="registerEmail">Email</label>
                            <input id="registerEmail" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@example.com">
                            @error('email')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="auth-label" for="registerPassword">Password</label>
                            <input id="registerPassword" class="auth-input" type="password" name="password" required autocomplete="new-password" placeholder="Create password">
                            @error('password')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="auth-label" for="registerPasswordConfirmation">Confirm Password</label>
                            <input id="registerPasswordConfirmation" class="auth-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                            @error('password_confirmation')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="auth-label" for="registerStudentId">Student ID</label>
                            <input id="registerStudentId" class="auth-input" type="text" name="student_id" value="{{ old('student_id') }}" placeholder="Enter 8-digit Student ID" inputmode="numeric" pattern="\d{8}" minlength="8" maxlength="8" required>
                            @error('student_id')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="auth-btn auth-span-2">Create Account</button>

                        <div class="auth-foot auth-span-2">
                            Already registered?
                            <a href="#" class="auth-link" data-switch-auth="login">Login</a>
                        </div>
                    </form>
                </section>
            @endif

            <section class="auth-panel" id="forgotPanel">
                <form method="POST" action="{{ route('password.email') }}" class="auth-grid">
                    @csrf
                    <input type="hidden" name="auth_form" value="forgot">
                    <div>
                        <label class="auth-label" for="forgotEmail">Email</label>
                        <input id="forgotEmail" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com">
                        @error('email')<div class="auth-error">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="auth-btn">Send Reset Link</button>

                    <div class="auth-foot">
                        Back to
                        <a href="#" class="auth-link" data-switch-auth="login">Login</a>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('authModal');
            const loginPanel = document.getElementById('loginPanel');
            const registerPanel = document.getElementById('registerPanel');
            const forgotPanel = document.getElementById('forgotPanel');
            const titleEl = document.getElementById('authModalTitle');

            if (!modal || !loginPanel || !titleEl) return;

            const openButtons = Array.from(document.querySelectorAll('[data-open-auth]'));
            const closeButtons = Array.from(document.querySelectorAll('[data-close-auth]'));
            const switchButtons = Array.from(document.querySelectorAll('[data-switch-auth]'));

            const showPanel = (panel) => {
                const isRegister = panel === 'register' && registerPanel;
                const isForgot = panel === 'forgot' && forgotPanel;
                loginPanel.classList.toggle('active', !isRegister && !isForgot);
                if (registerPanel) registerPanel.classList.toggle('active', Boolean(isRegister));
                if (forgotPanel) forgotPanel.classList.toggle('active', Boolean(isForgot));
                const authModalCard = modal.querySelector('.auth-modal');
                if (authModalCard) {
                    authModalCard.classList.toggle('register-mode', Boolean(isRegister));
                }
                titleEl.textContent = isRegister ? 'Create Account' : (isForgot ? 'Reset Password' : 'Welcome Back');
                modal.classList.add('active');
                modal.setAttribute('aria-hidden', 'false');

                const activePanel = isRegister ? registerPanel : (isForgot ? forgotPanel : loginPanel);
                const firstInput = activePanel ? activePanel.querySelector('input') : null;
                if (firstInput) firstInput.focus();
            };

            const hideModal = () => {
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    showPanel(button.getAttribute('data-open-auth') || 'login');
                });
            });

            switchButtons.forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    showPanel(button.getAttribute('data-switch-auth') || 'login');
                });
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', hideModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('active')) {
                    hideModal();
                }
            });

            const forcedAuth = @json($authPanel ?? request('auth'));
            const flashAuthForm = @json(session('auth_form'));
            const oldAuthForm = @json(old('auth_form'));
            const hasRegisterErrors = Boolean(@json($errors->any())) && oldAuthForm === 'register';
            const hasLoginErrors = Boolean(@json($errors->any())) && oldAuthForm === 'login';
            const hasForgotErrors = Boolean(@json($errors->any())) && oldAuthForm === 'forgot';
            if (hasRegisterErrors) {
                showPanel('register');
            } else if (hasForgotErrors) {
                showPanel('forgot');
            } else if (hasLoginErrors || Boolean(@json(session('status')))) {
                showPanel(flashAuthForm === 'forgot' ? 'forgot' : 'login');
            } else if (forcedAuth === 'register' || forcedAuth === 'login' || forcedAuth === 'forgot') {
                showPanel(forcedAuth);
            }
        })();
    </script>
</body>
</html>
