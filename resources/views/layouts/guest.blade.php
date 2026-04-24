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

            .app-loader {
                position: fixed;
                inset: 0;
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #060a12;
                overflow: hidden;
                transition: opacity 0.35s ease, visibility 0.35s ease;
            }

            .app-loader.is-hidden {
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
            }

            .app-loader::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(ellipse 60% 50% at 50% 50%, rgba(30, 80, 160, 0.22) 0%, transparent 70%);
                pointer-events: none;
            }

            .app-loader::after {
                content: '';
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(74,144,217,0.04) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(74,144,217,0.04) 1px, transparent 1px);
                background-size: 40px 40px;
                pointer-events: none;
            }

            .app-loader-splash {
                position: relative;
                z-index: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 36px;
                padding: 24px;
            }

            .app-loader-brand {
                font-size: 13px;
                font-weight: 700;
                letter-spacing: 8px;
                color: rgba(74,144,217,0.55);
                text-transform: uppercase;
            }

            .app-loader-coin-scene {
                position: relative;
                width: 110px;
                height: 110px;
                perspective: 600px;
            }

            .app-loader-coin {
                width: 100%;
                height: 100%;
                position: relative;
                transform-style: preserve-3d;
                animation: appLoaderCoinFlip 2.4s linear infinite;
                border-radius: 50%;
            }

            .app-loader-face,
            .app-loader-face-back {
                position: absolute;
                inset: 0;
                border-radius: 50%;
                backface-visibility: hidden;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .app-loader-face {
                background: radial-gradient(circle at 35% 35%, #0d2245, #07142a);
                box-shadow:
                    0 0 0 2.5px rgba(74,144,217,0.75),
                    0 0 28px rgba(74,144,217,0.5),
                    0 0 60px rgba(74,144,217,0.18);
            }

            .app-loader-face-back {
                background: radial-gradient(circle at 65% 65%, #0d2245, #07142a);
                transform: rotateY(180deg);
                box-shadow:
                    0 0 0 2.5px rgba(74,144,217,0.75),
                    0 0 28px rgba(74,144,217,0.5),
                    0 0 60px rgba(74,144,217,0.18);
            }

            .app-loader-face::after,
            .app-loader-face-back::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 50%;
                background: linear-gradient(120deg, transparent 25%, rgba(255,255,255,0.18) 50%, transparent 75%);
                animation: appLoaderSweep 2.4s linear infinite;
                pointer-events: none;
            }

            .app-loader-edge {
                position: absolute;
                inset: 0;
                border-radius: 50%;
                transform: translateZ(-5px) scaleX(1.02);
                background: conic-gradient(#071428, #1a4a80, #071428, #1a4a80, #071428);
                z-index: -1;
            }

            .app-loader-logo-svg {
                width: 76%;
                height: 76%;
                flex-shrink: 0;
                filter: drop-shadow(0 0 6px rgba(74,144,217,0.8));
                animation: appLoaderLogoPulse 2.4s ease-in-out infinite;
            }

            .app-loader-orbit-wrap {
                position: absolute;
                inset: -22px;
                pointer-events: none;
            }

            .app-loader-orbit {
                position: absolute;
                inset: 0;
                border-radius: 50%;
                border: 1.5px solid transparent;
            }

            .app-loader-orbit1 {
                border-top-color: rgba(74,144,217,0.8);
                border-bottom-color: rgba(74,144,217,0.12);
                animation: appLoaderOrbitSpin 1.9s linear infinite;
                box-shadow: 0 0 10px rgba(74,144,217,0.35);
            }

            .app-loader-orbit2 {
                inset: -12px;
                border-right-color: rgba(74,144,217,0.45);
                border-left-color: rgba(74,144,217,0.1);
                animation: appLoaderOrbitSpin 3.4s linear infinite reverse;
                transform: rotateX(62deg);
            }

            .app-loader-particle {
                position: absolute;
                top: 50%;
                left: 50%;
                border-radius: 50%;
                background: #4a90d9;
                box-shadow: 0 0 6px #4a90d9;
                animation: appLoaderOrbitParticle linear infinite;
            }

            .app-loader-shadow {
                width: 100px;
                height: 14px;
                border-radius: 50%;
                background: radial-gradient(ellipse, rgba(74,144,217,0.32) 0%, transparent 70%);
                animation: appLoaderShadowPulse 2.4s linear infinite;
                margin-top: -10px;
            }

            .app-loader-label {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 14px;
            }

            .app-loader-text {
                color: #7ab8e8;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 6px;
                text-transform: uppercase;
                animation: appLoaderTextPulse 1.6s ease-in-out infinite;
            }

            .app-loader-progress-wrap {
                width: 170px;
                height: 2px;
                background: rgba(74,144,217,0.12);
                border-radius: 99px;
                overflow: hidden;
            }

            .app-loader-progress-bar {
                height: 100%;
                width: 40%;
                background: linear-gradient(90deg, transparent, #4a90d9, #c0dff7);
                border-radius: 99px;
                animation: appLoaderProgressSlide 1.9s ease-in-out infinite;
                box-shadow: 0 0 10px rgba(74,144,217,0.8);
            }

            @keyframes appLoaderCoinFlip {
                0% { transform: rotateY(0deg) rotateX(10deg); }
                100% { transform: rotateY(360deg) rotateX(10deg); }
            }

            @keyframes appLoaderSweep {
                0% { opacity: 0; transform: translateX(-120%); }
                35% { opacity: 1; }
                65% { opacity: 1; }
                100% { opacity: 0; transform: translateX(120%); }
            }

            @keyframes appLoaderLogoPulse {
                0%, 100% { filter: drop-shadow(0 0 5px rgba(74,144,217,0.6)); }
                50% { filter: drop-shadow(0 0 14px rgba(74,144,217,1)); }
            }

            @keyframes appLoaderOrbitSpin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            @keyframes appLoaderOrbitParticle {
                from { transform: rotate(var(--start)) translateX(var(--r)) scale(1); opacity: .9; }
                to { transform: rotate(calc(var(--start) + 360deg)) translateX(var(--r)) scale(.6); opacity: .3; }
            }

            @keyframes appLoaderShadowPulse {
                0%, 100% { transform: scaleX(1); opacity: .75; }
                50% { transform: scaleX(.3); opacity: .2; }
            }

            @keyframes appLoaderTextPulse {
                0%, 100% { opacity: .4; }
                50% { opacity: 1; }
            }

            @keyframes appLoaderProgressSlide {
                0% { transform: translateX(-130%); }
                100% { transform: translateX(380%); }
            }
        </style>
    </head>
    <body>
        <div class="app-loader" id="appLoader" aria-hidden="true">
            <div class="app-loader-splash">
                <div class="app-loader-brand">CS Platform</div>
                <div class="app-loader-coin-scene" id="appLoaderCoinScene">
                    <div class="app-loader-orbit-wrap">
                        <div class="app-loader-orbit app-loader-orbit1"></div>
                        <div class="app-loader-orbit app-loader-orbit2"></div>
                    </div>
                    <div class="app-loader-coin">
                        <div class="app-loader-edge"></div>
                        <div class="app-loader-face">
                            <svg class="app-loader-logo-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <defs>
                                    <clipPath id="guestAppLoaderHexClip">
                                        <polygon points="50,4 92,27 92,73 50,96 8,73 8,27"/>
                                    </clipPath>
                                    <linearGradient id="guestAppLoaderMain" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#6eb8ff"/>
                                        <stop offset="55%" stop-color="#4a90d9"/>
                                        <stop offset="100%" stop-color="#1a5faa"/>
                                    </linearGradient>
                                    <radialGradient id="guestAppLoaderFill" cx="38%" cy="30%" r="70%">
                                        <stop offset="0%" stop-color="#0e2e5a"/>
                                        <stop offset="100%" stop-color="#071428"/>
                                    </radialGradient>
                                    <linearGradient id="guestAppLoaderShine" x1="0%" y1="0%" x2="60%" y2="100%">
                                        <stop offset="0%" stop-color="rgba(255,255,255,0.14)"/>
                                        <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
                                    </linearGradient>
                                </defs>
                                <polygon points="50,4 92,27 92,73 50,96 8,73 8,27" fill="none" stroke="url(#guestAppLoaderMain)" stroke-width="2.5" opacity="0.9"/>
                                <polygon points="50,10 87,30.5 87,69.5 50,90 13,69.5 13,30.5" fill="url(#guestAppLoaderFill)" clip-path="url(#guestAppLoaderHexClip)"/>
                                <polygon points="50,10 87,30.5 87,69.5 50,90 13,69.5 13,30.5" fill="none" stroke="rgba(74,144,217,0.2)" stroke-width="1"/>
                                <polygon points="50,10 87,30.5 87,69.5 50,90 13,69.5 13,30.5" fill="url(#guestAppLoaderShine)" opacity="0.6"/>
                                <text x="50" y="63" text-anchor="middle" font-family="'Segoe UI', Arial, sans-serif" font-size="36" font-weight="800" letter-spacing="-1" fill="url(#guestAppLoaderMain)">CS</text>
                                <line x1="26" y1="70" x2="74" y2="70" stroke="url(#guestAppLoaderMain)" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/>
                                <circle cx="50" cy="18" r="2.5" fill="url(#guestAppLoaderMain)" opacity="0.7"/>
                            </svg>
                        </div>
                        <div class="app-loader-face-back">
                            <svg class="app-loader-logo-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" style="transform:scaleX(-1)" aria-hidden="true">
                                <defs>
                                    <linearGradient id="guestAppLoaderMainBack" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#6eb8ff"/>
                                        <stop offset="55%" stop-color="#4a90d9"/>
                                        <stop offset="100%" stop-color="#1a5faa"/>
                                    </linearGradient>
                                    <radialGradient id="guestAppLoaderFillBack" cx="38%" cy="30%" r="70%">
                                        <stop offset="0%" stop-color="#0e2e5a"/>
                                        <stop offset="100%" stop-color="#071428"/>
                                    </radialGradient>
                                    <linearGradient id="guestAppLoaderShineBack" x1="0%" y1="0%" x2="60%" y2="100%">
                                        <stop offset="0%" stop-color="rgba(255,255,255,0.14)"/>
                                        <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
                                    </linearGradient>
                                </defs>
                                <polygon points="50,4 92,27 92,73 50,96 8,73 8,27" fill="none" stroke="url(#guestAppLoaderMainBack)" stroke-width="2.5" opacity="0.9"/>
                                <polygon points="50,10 87,30.5 87,69.5 50,90 13,69.5 13,30.5" fill="url(#guestAppLoaderFillBack)"/>
                                <polygon points="50,10 87,30.5 87,69.5 50,90 13,69.5 13,30.5" fill="none" stroke="rgba(74,144,217,0.2)" stroke-width="1"/>
                                <polygon points="50,10 87,30.5 87,69.5 50,90 13,69.5 13,30.5" fill="url(#guestAppLoaderShineBack)" opacity="0.6"/>
                                <text x="50" y="63" text-anchor="middle" font-family="'Segoe UI', Arial, sans-serif" font-size="36" font-weight="800" letter-spacing="-1" fill="url(#guestAppLoaderMainBack)">CS</text>
                                <line x1="26" y1="70" x2="74" y2="70" stroke="url(#guestAppLoaderMainBack)" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/>
                                <circle cx="50" cy="18" r="2.5" fill="url(#guestAppLoaderMainBack)" opacity="0.7"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="app-loader-shadow"></div>
                <div class="app-loader-label">
                    <div class="app-loader-text">Loading</div>
                    <div class="app-loader-progress-wrap">
                        <div class="app-loader-progress-bar"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="auth-shell">
            <section class="auth-content auth-reveal" id="authContent">
                {{ $slot }}
            </section>
        </div>
        <script>
            (function () {
                const loader = document.getElementById('appLoader');
                const particleWrap = document.querySelector('#appLoaderCoinScene .app-loader-orbit-wrap');

                if (particleWrap && !particleWrap.dataset.ready) {
                    particleWrap.dataset.ready = '1';
                    for (let i = 0; i < 7; i += 1) {
                        const particle = document.createElement('div');
                        const angle = (i / 7) * 360;
                        const radius = 62 + Math.random() * 18;
                        const duration = 1.8 + Math.random() * 1.8;
                        const size = 2.5 + Math.random() * 3;
                        particle.className = 'app-loader-particle';
                        particle.style.cssText = `--start:${angle}deg; --r:${radius}px; margin:-${size / 2}px 0 0 -${size / 2}px; width:${size}px; height:${size}px; animation-duration:${duration}s;`;
                        particleWrap.appendChild(particle);
                    }
                }

                if (!loader) return;

                const hideLoader = function () {
                    loader.classList.add('is-hidden');
                    window.setTimeout(function () {
                        loader.remove();
                    }, 420);
                };

                if (document.readyState === 'complete') {
                    window.setTimeout(hideLoader, 120);
                    return;
                }

                window.addEventListener('load', function () {
                    window.setTimeout(hideLoader, 120);
                }, { once: true });
            })();
        </script>
    </body>
</html>
