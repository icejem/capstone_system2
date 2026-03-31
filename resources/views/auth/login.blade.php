<x-guest-layout>
    <style>
        .auth-title {
            margin: 0;
            font-size: clamp(28px, 2.8vw, 38px);
            font-weight: 800;
            letter-spacing: 0.01em;
            color: #0f172a;
            font-family: "Orbitron", "Franklin Gothic Medium", sans-serif;
        }
        .auth-sub { margin: 10px 0 22px; color: #64748b; font-size: 14px; }
        .auth-field { margin-bottom: 14px; }
        .auth-label {
            display: block;
            margin-bottom: 6px;
            font-size: 12px;
            font-weight: 800;
            color: #475569;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .auth-input {
            width: 100%;
            border: 1px solid #dbe3f0;
            border-radius: 13px;
            padding: 11px 12px;
            font-size: 14px;
            outline: none;
            background: #ffffff;
            color: #0f172a;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .auth-input::placeholder { color: #94a3b8; }
        .auth-input:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
        }
        .auth-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin: 14px 0 18px; }
        .auth-check { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; }
        .auth-check input { accent-color: #2563eb; }
        .auth-link { color: #2563eb; text-decoration: none; font-size: 13px; font-weight: 700; }
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
        .auth-error { margin-top: 6px; color: #b91c1c; font-size: 12px; }
        .auth-foot { margin-top: 16px; text-align: center; color: #64748b; font-size: 13px; }
        .auth-status {
            margin-bottom: 12px;
            border: 1px solid rgba(34, 197, 94, 0.24);
            background: #f0fdf4;
            color: #166534;
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
            <input id="password" class="auth-input" type="password" name="password" required autocomplete="current-password">
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
    </form>
</x-guest-layout>
