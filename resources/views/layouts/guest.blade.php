<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('cslogo.jpg') }}">
        <link rel="shortcut icon" href="{{ asset('cslogo.jpg') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Orbitron:wght@500;700&display=swap');

            :root {
                --shell-bg-1: #050f22;
                --shell-bg-2: #0a1d3d;
                --shell-bg-3: #081a33;
                --line: rgba(99, 179, 237, 0.34);
                --card-bg: rgba(5, 16, 36, 0.82);
                --panel-bg: rgba(7, 22, 48, 0.78);
                --text-main: #e7f6ff;
                --text-muted: #9cc2d8;
                --accent: #18d3ff;
                --accent-2: #3b82f6;
            }

            * { box-sizing: border-box; }

            body {
                margin: 0;
                font-family: "Manrope", "Trebuchet MS", "Segoe UI", sans-serif;
                color: var(--text-main);
                background:
                    radial-gradient(1200px 620px at -15% -25%, rgba(24, 211, 255, 0.24) 0%, transparent 62%),
                    radial-gradient(1000px 560px at 120% 130%, rgba(59, 130, 246, 0.2) 0%, transparent 63%),
                    linear-gradient(130deg, var(--shell-bg-1), var(--shell-bg-2) 45%, var(--shell-bg-3));
                min-height: 100vh;
                overflow-x: hidden;
            }

            body::before {
                content: "";
                position: fixed;
                inset: 0;
                pointer-events: none;
                background-image:
                    linear-gradient(rgba(120, 204, 255, 0.08) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(120, 204, 255, 0.08) 1px, transparent 1px);
                background-size: 48px 48px;
                mask-image: radial-gradient(circle at center, black 25%, transparent 80%);
                animation: gridShift 18s linear infinite;
            }

            @keyframes gridShift {
                from { transform: translateY(0); }
                to { transform: translateY(48px); }
            }

            .auth-shell {
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 24px;
            }

            .auth-card {
                width: min(1120px, 100%);
                min-height: min(700px, 90vh);
                display: grid;
                grid-template-columns: 1.08fr 1fr;
                border: 1px solid var(--line);
                border-radius: 22px;
                overflow: hidden;
                background: var(--card-bg);
                box-shadow: 0 24px 64px rgba(0, 10, 24, 0.62);
                backdrop-filter: blur(10px);
            }

            .auth-art {
                position: relative;
                isolation: isolate;
                overflow: hidden;
                padding: 42px 36px;
                background:
                    linear-gradient(160deg, rgba(6, 23, 52, 0.95), rgba(8, 37, 84, 0.95));
                color: #effbff;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 24px;
                border-right: 1px solid rgba(130, 197, 255, 0.26);
            }

            .auth-art::before {
                content: "";
                position: absolute;
                inset: -30% -10%;
                z-index: -2;
                background:
                    radial-gradient(circle at 18% 18%, rgba(34, 211, 238, 0.4), transparent 40%),
                    radial-gradient(circle at 84% 16%, rgba(59, 130, 246, 0.34), transparent 35%),
                    linear-gradient(150deg, rgba(56, 189, 248, 0.16), rgba(37, 99, 235, 0.16));
                animation: artDrift 16s ease-in-out infinite alternate;
            }

            .auth-art::after {
                content: "";
                position: absolute;
                inset: 0;
                z-index: -1;
                pointer-events: none;
                background:
                    repeating-linear-gradient(
                        90deg,
                        rgba(125, 211, 252, 0.09) 0,
                        rgba(125, 211, 252, 0.09) 1px,
                        transparent 1px,
                        transparent 12px
                    );
                opacity: 0.6;
            }

            @keyframes artDrift {
                from { transform: translate3d(0, 0, 0) scale(1); }
                to { transform: translate3d(0, -12px, 0) scale(1.04); }
            }

            .brand-pill {
                width: max-content;
                padding: 7px 12px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: #c8f4ff;
                border: 1px solid rgba(125, 211, 252, 0.48);
                background: rgba(10, 54, 99, 0.4);
            }

            .art-title {
                margin: 0;
                font-family: "Orbitron", "Franklin Gothic Medium", sans-serif;
                font-size: clamp(28px, 3.1vw, 44px);
                font-weight: 700;
                line-height: 1.12;
                letter-spacing: 0.02em;
                text-shadow: 0 4px 18px rgba(24, 211, 255, 0.3);
            }

            .art-sub {
                margin: 10px 0 0;
                color: #bddff1;
                font-size: 14px;
                max-width: 36ch;
                line-height: 1.55;
            }

            .art-metrics {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 10px;
                margin-top: 18px;
            }

            .metric-card {
                border: 1px solid rgba(125, 211, 252, 0.34);
                border-radius: 14px;
                padding: 10px 10px 9px;
                background: rgba(8, 39, 78, 0.46);
                box-shadow: inset 0 0 0 1px rgba(125, 211, 252, 0.12);
            }

            .metric-value {
                font-family: "Orbitron", "Franklin Gothic Medium", sans-serif;
                font-size: 18px;
                font-weight: 700;
                color: #d6f7ff;
            }

            .metric-label {
                font-size: 11px;
                color: #a8ccdf;
                margin-top: 4px;
            }

            .get-started-btn {
                width: fit-content;
                border: 1px solid rgba(125, 211, 252, 0.5);
                background: linear-gradient(120deg, rgba(6, 78, 132, 0.7), rgba(37, 99, 235, 0.6));
                color: #eaf8ff;
                border-radius: 12px;
                font-weight: 800;
                font-size: 13px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                padding: 11px 16px;
                cursor: pointer;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                box-shadow: 0 10px 20px rgba(2, 28, 56, 0.35);
            }

            .get-started-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 24px rgba(2, 28, 56, 0.45);
            }

            .art-footer {
                font-size: 12px;
                color: #9cc2d8;
                border-top: 1px solid rgba(125, 211, 252, 0.2);
                padding-top: 14px;
            }

            .auth-content {
                position: relative;
                padding: 34px;
                background:
                    linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
            }

            .auth-content::before {
                content: "";
                position: absolute;
                inset: 0;
                pointer-events: none;
                background:
                    radial-gradient(circle at 100% 0%, rgba(56, 189, 248, 0.08), transparent 42%),
                    radial-gradient(circle at 0% 100%, rgba(59, 130, 246, 0.06), transparent 40%);
            }

            .auth-content > * {
                position: relative;
                z-index: 1;
            }

            .auth-reveal {
                animation: fadeUp 0.55s ease forwards;
            }

            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @media (max-width: 860px) {
                .auth-card { grid-template-columns: 1fr; }
                .auth-art { padding: 24px; }
                .auth-content { padding: 24px; }
                .art-metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            }

            @media (max-width: 620px) {
                .auth-shell { padding: 14px; }
                .auth-card { min-height: auto; }
                .art-metrics { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <div class="auth-shell">
            <div class="auth-card">
                <aside class="auth-art">
                    <div class="auth-reveal">
                        <div class="brand-pill">Consultation Platform</div>
                        <h1 class="art-title">High-Tech Access Console</h1>
                        <p class="art-sub">A secure, professional gateway for students, instructors, and administrators to manage consultations in real time.</p>
                        <div class="art-metrics">
                            <div class="metric-card">
                                <div class="metric-value">24/7</div>
                                <div class="metric-label">Availability</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-value">Live</div>
                                <div class="metric-label">Status Sync</div>
                            </div>
                            <div class="metric-card">
                                <div class="metric-value">Secure</div>
                                <div class="metric-label">Auth Layer</div>
                            </div>
                        </div>
                    </div>
                    <div class="auth-reveal">
                        <button type="button" class="get-started-btn" id="getStartedBtn">Get Started</button>
                    </div>
                    <div class="art-footer">{{ config('app.name', 'Laravel') }} | Cyber Session Portal</div>
                </aside>
                <section class="auth-content" id="authContent">
                    {{ $slot }}
                </section>
            </div>
        </div>
        <script>
            const getStartedBtn = document.getElementById('getStartedBtn');
            if (getStartedBtn) {
                getStartedBtn.addEventListener('click', () => {
                    const target = document.querySelector('#authContent input, #authContent button, #authContent select, #authContent textarea');
                    if (target) {
                        target.focus();
                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        </script>
    </body>
</html>
