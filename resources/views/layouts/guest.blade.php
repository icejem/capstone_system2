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

            .auth-content {
                position: relative;
                width: min(460px, 100%);
                padding: 0;
            }

            .auth-reveal {
                animation: fadeUp 0.55s ease forwards;
            }

            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @media (max-width: 860px) {
                .auth-content { width: min(460px, 100%); }
            }

            @media (max-width: 620px) {
                .auth-shell { padding: 14px; }
                .auth-content { width: 100%; }
            }
        </style>
    </head>
    <body>
        <div class="auth-shell">
            <section class="auth-content auth-reveal" id="authContent">
                {{ $slot }}
            </section>
        </div>
    </body>
</html>
