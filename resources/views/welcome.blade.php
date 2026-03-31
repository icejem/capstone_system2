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

        html { scroll-behavior: smooth; }

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
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 18px 54px;
        }

        .top-nav {
            position: relative;
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto minmax(240px, 1fr);
            align-items: center;
            gap: 18px;
            min-height: 92px;
            padding: 16px 6px 12px;
        }

        .top-nav::after {
            content: "";
            position: absolute;
            left: -18px;
            right: -18px;
            bottom: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.3);
        }

        .brand {
            justify-self: start;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #eff8ff;
            font-weight: 800;
            font-size: 22px;
            line-height: 1.1;
            letter-spacing: 0.01em;
        }

        .brand-icon {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            border: 2px solid rgba(214, 228, 255, 0.72);
            background: rgba(255, 255, 255, 0.92);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .brand-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .top-links {
            justify-self: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 34px;
            min-width: 240px;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .top-links a {
            position: relative;
            color: #eef8ff;
            text-decoration: none;
            padding: 10px 2px 14px;
            transition: color .2s ease, opacity .2s ease;
        }

        .top-links a.nav-link-active::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 0;
            width: 86px;
            max-width: calc(100% + 24px);
            height: 3px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(120, 228, 255, 0.95), rgba(42, 127, 255, 0.9));
            transform: translateX(-50%);
            box-shadow: 0 8px 20px rgba(37, 132, 255, 0.28);
        }

        .top-links a:hover {
            color: #ffffff;
            opacity: 0.86;
        }

        .top-actions {
            justify-self: end;
            display: inline-flex;
            align-items: center;
            gap: 14px;
        }

        .nav-btn {
            min-width: 116px;
            border: 1.5px solid rgba(201, 229, 255, 0.75);
            color: #eff8ff;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.02em;
            text-transform: none;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease, color .2s ease, border-color .2s ease;
        }

        .nav-btn:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(226, 240, 255, 0.94);
            box-shadow: 0 12px 24px rgba(2, 18, 40, 0.22);
        }

        .nav-btn.primary {
            border-color: rgba(85, 165, 255, 0.95);
            background: linear-gradient(135deg, #3c78b1, #235f99);
            color: #f7fbff;
            box-shadow: 0 12px 24px rgba(18, 73, 132, 0.34);
        }

        .nav-btn.primary:hover {
            background: linear-gradient(135deg, #467fb6, #2b689f);
            box-shadow: 0 14px 28px rgba(18, 73, 132, 0.42);
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

        .feature-grid {
            margin: 56px auto 0;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
            max-width: 920px;
        }

        .feature-card {
            position: relative;
            border-radius: 24px;
            padding: 30px 24px 26px;
            text-align: center;
            color: #f4fbff;
            background: linear-gradient(180deg, rgba(67, 137, 199, 0.95), rgba(44, 104, 168, 0.96));
            border: 1px solid rgba(188, 225, 255, 0.26);
            box-shadow: 0 18px 34px rgba(10, 43, 85, 0.24);
            overflow: hidden;
        }

        .feature-card::after {
            content: "";
            position: absolute;
            right: -10px;
            bottom: -10px;
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: rgba(109, 205, 255, 0.42);
        }

        .feature-icon {
            width: 42px;
            height: 42px;
            margin: 0 auto 18px;
            color: #f7fcff;
        }

        .feature-icon svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .feature-title {
            margin: 0 0 8px;
            font-size: 18px;
            line-height: 1.25;
            font-weight: 800;
        }

        .feature-copy {
            margin: 0;
            font-size: 15px;
            line-height: 1.45;
            color: rgba(243, 250, 255, 0.9);
        }

        .about-section {
            position: relative;
            margin: 86px auto 0;
            max-width: 1140px;
            padding: 68px 44px 48px;
            border-radius: 42px;
            background:
                radial-gradient(520px 220px at 50% 100%, rgba(92, 175, 236, 0.3), transparent 75%),
                linear-gradient(180deg, #eaf6ff 0%, #dcefff 100%);
            box-shadow: 0 24px 56px rgba(8, 39, 78, 0.24);
            color: #325071;
            overflow: hidden;
        }

        .about-section::before,
        .about-section::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.42);
        }

        .about-section::before {
            width: 76px;
            height: 76px;
            left: -18px;
            bottom: 54px;
        }

        .about-section::after {
            width: 54px;
            height: 54px;
            right: 18px;
            bottom: 72px;
        }

        .about-head {
            position: relative;
            z-index: 1;
            max-width: 760px;
            margin: 0 auto 34px;
            text-align: center;
        }

        .about-title {
            margin: 0;
            font-family: "Space Grotesk", "Franklin Gothic Medium", sans-serif;
            font-size: clamp(34px, 4vw, 54px);
            line-height: 1.08;
            color: #203d63;
        }

        .about-sub {
            margin: 14px auto 0;
            max-width: 36ch;
            font-size: 17px;
            line-height: 1.55;
            color: #355777;
        }

        .about-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(0, 0.92fr);
            gap: 22px;
            align-items: stretch;
        }

        .info-panel {
            border-radius: 28px;
            padding: 24px 22px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(181, 213, 239, 0.72);
            box-shadow: 0 18px 32px rgba(92, 145, 193, 0.16);
        }

        .info-panel-title {
            margin: 0 0 18px;
            font-size: 20px;
            line-height: 1.2;
            font-weight: 800;
            color: #2d4d72;
        }

        .flow-list {
            display: grid;
            gap: 20px;
        }

        .flow-step {
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 14px;
            align-items: start;
        }

        .step-badge {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #5ba0d6, #3f7fb6);
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
            box-shadow: 0 10px 20px rgba(52, 105, 156, 0.24);
        }

        .step-title {
            margin: 2px 0 4px;
            font-size: 17px;
            line-height: 1.25;
            font-weight: 800;
            color: #315276;
        }

        .step-copy {
            margin: 0;
            font-size: 15px;
            line-height: 1.6;
            color: #567593;
        }

        .faculty-copy {
            margin: 0;
            font-size: 15px;
            line-height: 1.75;
            color: #567593;
        }

        .faculty-points {
            margin: 18px 0 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 12px;
        }

        .faculty-points li {
            display: grid;
            grid-template-columns: 18px 1fr;
            gap: 10px;
            align-items: start;
            font-size: 15px;
            line-height: 1.5;
            color: #41617f;
        }

        .faculty-check {
            color: #317abd;
            font-weight: 800;
            line-height: 1.4;
        }

        .faculty-badge {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-top: 22px;
            padding: 11px 16px;
            border-radius: 999px;
            background: #edf5fb;
            color: #4b6f92;
            font-size: 14px;
            font-weight: 700;
        }

        .faculty-badge img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 50%;
            background: #ffffff;
            padding: 2px;
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
            border: 1px solid rgba(148, 163, 184, 0.24);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
            box-shadow: 0 22px 54px rgba(15, 23, 42, 0.18);
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
            color: #0f172a;
        }

        .auth-close {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid #dbe3f0;
            background: #ffffff;
            color: #475569;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
        }

        .auth-close:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .auth-status {
            margin-bottom: 10px;
            border: 1px solid rgba(34, 197, 94, 0.24);
            background: #f0fdf4;
            color: #166534;
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
            color: #475569;
        }

        .auth-input {
            width: 100%;
            border: 1px solid #dbe3f0;
            border-radius: 11px;
            padding: 11px 12px;
            font-size: 14px;
            color: #0f172a;
            background: #ffffff;
            outline: none;
        }

        .auth-input::placeholder {
            color: #94a3b8;
        }

        .auth-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
        }

        .auth-input.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
        }

        .auth-input.is-valid {
            border-color: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
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
            color: #475569;
            font-size: 13px;
        }

        .auth-check input { accent-color: #2563eb; }

        .auth-link {
            color: #2563eb;
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

        .auth-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            filter: none;
            box-shadow: none;
        }

        .auth-error {
            margin-top: 5px;
            color: #b91c1c;
            font-size: 12px;
            font-weight: 600;
        }

        .auth-success {
            margin-top: 5px;
            color: #15803d;
            font-size: 12px;
            font-weight: 600;
        }

        .auth-success:empty,
        .auth-error:empty {
            display: none;
        }

        .auth-note {
            margin-top: 6px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.45;
        }

        .auth-foot {
            margin-top: 12px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        .auth-consent-wrap {
            margin-top: 12px;
            display: grid;
            gap: 12px;
        }

        .auth-consent-check {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid #dbe3f0;
            border-radius: 12px;
            background: #f8fafc;
            color: #334155;
            font-size: 13px;
            line-height: 1.5;
        }

        .auth-consent-check input {
            margin-top: 2px;
            accent-color: #2563eb;
        }

        .auth-consent-check strong {
            color: #0f172a;
        }

        .auth-legal-link {
            border: 0;
            background: transparent;
            padding: 0;
            color: #2563eb;
            font-weight: 700;
            text-decoration: underline;
            cursor: pointer;
            font: inherit;
        }

        .auth-legal-summary {
            font-size: 12px;
            color: #64748b;
            line-height: 1.55;
            padding: 0 2px;
        }

        .legal-modal-shell {
            position: fixed;
            inset: 0;
            z-index: 1500;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .legal-modal-shell.active {
            display: flex;
        }

        .legal-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.52);
            backdrop-filter: blur(4px);
        }

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

        .legal-modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }

        .legal-modal-tabs {
            display: flex;
            gap: 8px;
            padding: 12px 18px 0;
            background: #f8fafc;
        }

        .legal-modal-tab {
            border: 1px solid #dbe3f0;
            background: #ffffff;
            color: #475569;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .legal-modal-tab.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.18);
        }

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
            max-height: calc(100vh - 150px);
            overflow-y: auto;
            padding: 18px;
            color: #475569;
            font-size: 13px;
            line-height: 1.7;
        }

        .legal-modal-panel {
            display: none;
        }

        .legal-modal-panel.active {
            display: block;
        }

        .legal-modal-body p {
            margin: 0 0 14px;
        }

        .legal-modal-body p:last-child {
            margin-bottom: 0;
        }

        .auth-panel { display: none; }
        .auth-panel.active { display: block; }

        @media (max-width: 980px) {
            .top-nav {
                grid-template-columns: auto 1fr;
            }

            .top-links { display: none; }

            .about-section {
                padding: 54px 28px 34px;
            }

            .about-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .page-wrap { padding: 14px 12px 26px; }
            .top-nav {
                grid-template-columns: 1fr;
                justify-items: center;
                gap: 12px;
                min-height: auto;
                padding: 4px 0 12px;
            }
            .top-nav::after {
                left: -12px;
                right: -12px;
            }
            .brand { font-size: 16px; }
            .brand-icon { width: 44px; height: 44px; }
            .top-actions {
                justify-self: center;
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
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
            .feature-grid {
                grid-template-columns: 1fr;
                gap: 16px;
                margin-top: 38px;
            }
            .feature-card {
                padding: 24px 18px 22px;
            }
            .about-section {
                margin-top: 56px;
                padding: 42px 18px 24px;
                border-radius: 28px;
            }
            .about-head {
                margin-bottom: 26px;
            }
            .about-sub,
            .step-copy,
            .faculty-copy,
            .faculty-points li {
                font-size: 14px;
            }
            .info-panel {
                padding: 20px 16px;
                border-radius: 22px;
            }
            .flow-step {
                grid-template-columns: 36px 1fr;
                gap: 12px;
            }
            .step-badge {
                width: 36px;
                height: 36px;
                border-radius: 12px;
                font-size: 17px;
            }
            .faculty-badge {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
            .auth-modal { padding: 14px; }
            .auth-grid-register { grid-template-columns: 1fr; }
            .auth-span-2 { grid-column: auto; }
        }

        @media (max-width: 860px) {
            .feature-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 620px) {
            .feature-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrap">
        <header class="top-nav">
            <a href="{{ route('home') }}" class="brand" aria-label="Home">
                <span class="brand-icon"><img src="{{ asset('cslogo.jpg') }}" alt="CS Logo"></span>
                <span>College of Computer Studies </span>
            </a>

            <nav class="top-links" aria-label="Primary">
                <a href="#features" class="nav-link-active">Features</a>
                <a href="#about">About</a>
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

        <section class="feature-grid" id="features" aria-label="Platform features">
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M16 3v4"></path>
                        <path d="M8 3v4"></path>
                        <path d="M3 10h18"></path>
                        <path d="M8 14h.01"></path>
                        <path d="M12 14h.01"></path>
                        <path d="M16 14h.01"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Schedule Appointments</h3>
                <p class="feature-copy">Faculty availability booking for consultation sessions in one place.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        <path d="M8 9h8"></path>
                        <path d="M8 13h5"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Virtual Consultations</h3>
                <p class="feature-copy">Video, chat, and secure online consultation support for students and faculty.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 3-6.7"></path>
                        <path d="M3 4v5h5"></path>
                        <path d="M12 7v5l3 2"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Track Progress</h3>
                <p class="feature-copy">Monitor updates and consultation history.</p>
            </article>
        </section>

        <section class="about-section" id="about" aria-labelledby="aboutTitle">
            <div class="about-head">
                <h2 class="about-title" id="aboutTitle">How it Works</h2>
                <p class="about-sub">Browse faculty, select a slot, meet virtually, and provide feedback after every consultation.</p>
            </div>

            <div class="about-grid">
                <article class="info-panel">
                    <h3 class="info-panel-title">Simple Consultation Flow</h3>
                    <div class="flow-list">
                        <div class="flow-step">
                            <div class="step-badge">1</div>
                            <div>
                                <h4 class="step-title">Browse Faculty</h4>
                                <p class="step-copy">Review faculty availability and choose the instructor best suited to your concern.</p>
                            </div>
                        </div>

                        <div class="flow-step">
                            <div class="step-badge">2</div>
                            <div>
                                <h4 class="step-title">Select a Slot</h4>
                                <p class="step-copy">Book an available consultation time with a clear and guided appointment flow.</p>
                            </div>
                        </div>

                        <div class="flow-step">
                            <div class="step-badge">3</div>
                            <div>
                                <h4 class="step-title">Meet and Follow Up</h4>
                                <p class="step-copy">Join virtually, continue the conversation, and monitor updates from your dashboard.</p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="info-panel">
                    <h3 class="info-panel-title">Meet Our Faculty</h3>
                    <p class="faculty-copy">
                        Connect and consult with CCS faculty members for guidance on programming, systems, research, and various school-related concerns.
                    </p>

                    <ul class="faculty-points">
                        <li><span class="faculty-check">&#10003;</span><span>Assistance with academic and school-related concerns</span></li>
                        <li><span class="faculty-check">&#10003;</span><span>Support for capstone, thesis, and project consultations</span></li>
                        <li><span class="faculty-check">&#10003;</span><span>Help with problem-solving and student concerns</span></li>
                        <li><span class="faculty-check">&#10003;</span><span>Easy tracking of consultation records and updates</span></li>
                    </ul>

                    <div class="faculty-badge">
                        <img src="{{ asset('cslogo.jpg') }}" alt="CCS Faculty">
                        <span>College of Computer Studies Faculty</span>
                    </div>
                </article>
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
                    <form method="POST" action="{{ route('register') }}" class="auth-grid-register" novalidate data-live-validate="welcome-register">
                        @csrf
                        <input type="hidden" name="auth_form" value="register">
                        <div>
                            <label class="auth-label" for="registerFirstName">First Name</label>
                            <input id="registerFirstName" class="auth-input @error('first_name') is-invalid @enderror" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" placeholder="First name" maxlength="50" data-label="First name" data-rule="name">
                            @error('first_name')<div class="auth-error">{{ $message }}</div>@enderror
                            <div class="auth-success" data-success-for="first_name"></div>
                        </div>

                        <div>
                            <label class="auth-label" for="registerLastName">Last Name</label>
                            <input id="registerLastName" class="auth-input @error('last_name') is-invalid @enderror" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" placeholder="Last name" maxlength="50" data-label="Last name" data-rule="name">
                            @error('last_name')<div class="auth-error">{{ $message }}</div>@enderror
                            <div class="auth-success" data-success-for="last_name"></div>
                        </div>

                        <div class="auth-span-2">
                            <label class="auth-label" for="registerMiddleName">Middle Name (Optional)</label>
                            <input id="registerMiddleName" class="auth-input @error('middle_name') is-invalid @enderror" type="text" name="middle_name" value="{{ old('middle_name') }}" autocomplete="additional-name" placeholder="Middle name" maxlength="50" data-label="Middle name" data-rule="name" data-optional="true">
                            @error('middle_name')<div class="auth-error">{{ $message }}</div>@enderror
                            <div class="auth-success" data-success-for="middle_name"></div>
                        </div>

                        <div>
                            <label class="auth-label" for="registerEmail">Email</label>
                            <input id="registerEmail" class="auth-input @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@gmail.com" data-label="Email" data-rule="gmail">
                            @error('email')<div class="auth-error">{{ $message }}</div>@enderror
                            <div class="auth-success" data-success-for="email"></div>
                            <div class="auth-note">Use your Gmail address. We'll send a verification link so we know this account is really yours.</div>
                        </div>

                        <div>
                            <label class="auth-label" for="registerPassword">Password</label>
                            <input id="registerPassword" class="auth-input @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" placeholder="Create password" data-label="Password" data-rule="password">
                            @error('password')<div class="auth-error">{{ $message }}</div>@enderror
                            <div class="auth-success" data-success-for="password"></div>
                        </div>

                        <div>
                            <label class="auth-label" for="registerPasswordConfirmation">Confirm Password</label>
                            <input id="registerPasswordConfirmation" class="auth-input @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password" data-label="Password confirmation" data-rule="password_confirmation">
                            @error('password_confirmation')<div class="auth-error">{{ $message }}</div>@enderror
                            <div class="auth-success" data-success-for="password_confirmation"></div>
                        </div>

                        <div>
                            <label class="auth-label" for="registerStudentId">Student ID</label>
                            <input id="registerStudentId" class="auth-input @error('student_id') is-invalid @enderror" type="text" name="student_id" value="{{ old('student_id') }}" placeholder="Enter 8-digit Student ID" inputmode="numeric" pattern="\d{8}" minlength="8" maxlength="8" required data-label="Student ID" data-rule="student_id">
                            @error('student_id')<div class="auth-error">{{ $message }}</div>@enderror
                            <div class="auth-success" data-success-for="student_id"></div>
                        </div>

                        <button type="submit" class="auth-btn auth-span-2" data-submit-register disabled>Create Account</button>

                        <div class="auth-consent-wrap auth-span-2">
                            <label class="auth-consent-check" for="registerTermsAccepted">
                                <input
                                    id="registerTermsAccepted"
                                    type="checkbox"
                                    name="terms_accepted"
                                    value="1"
                                    data-legal-checkbox="terms"
                                    @checked(old('terms_accepted'))
                                >
                                <span>
                                    <strong>I agree</strong> to the
                                    <button type="button" class="auth-legal-link" data-open-legal="terms">Terms and Conditions</button>.
                                </span>
                            </label>
                            @error('terms_accepted')<div class="auth-error">{{ $message }}</div>@enderror
                            <label class="auth-consent-check" for="registerPrivacyAccepted">
                                <input
                                    id="registerPrivacyAccepted"
                                    type="checkbox"
                                    name="privacy_accepted"
                                    value="1"
                                    data-legal-checkbox="privacy"
                                    @checked(old('privacy_accepted'))
                                >
                                <span>
                                    <strong>I agree</strong> to the
                                    <button type="button" class="auth-legal-link" data-open-legal="privacy">Privacy Policy</button>.
                                </span>
                            </label>
                            @error('privacy_accepted')<div class="auth-error">{{ $message }}</div>@enderror
                            <div class="auth-legal-summary">
                                Please review both documents before creating your account.
                            </div>
                        </div>

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

    <div class="legal-modal-shell" id="legalModal" aria-hidden="true">
        <div class="legal-modal-backdrop" data-close-legal></div>
        <div class="legal-modal-card" role="dialog" aria-modal="true" aria-labelledby="legalModalTitle">
            <div class="legal-modal-head">
                <h3 class="legal-modal-title" id="legalModalTitle">Terms and Conditions</h3>
                <button type="button" class="legal-modal-close" data-close-legal aria-label="Close legal document">&times;</button>
            </div>
            <div class="legal-modal-tabs" role="tablist" aria-label="Legal document tabs">
                <button type="button" class="legal-modal-tab active" data-legal-tab="terms">Terms and Conditions</button>
                <button type="button" class="legal-modal-tab" data-legal-tab="privacy">Privacy Policy</button>
            </div>
            <div class="legal-modal-body">
                <article class="legal-modal-panel active" data-legal-panel="terms">
                    <p>By using the Online Faculty-Student Consultation System of the Computer Studies Department, users agree to use the platform only for academic consultation and communication purposes. All students and faculty members must provide accurate account information and maintain the confidentiality of their login credentials. Users are expected to communicate respectfully and avoid any inappropriate, offensive, or unauthorized use of the system.</p>
                    <p>Consultation requests shall be subject to faculty availability, and faculty members reserve the right to approve, reschedule, or decline appointments when necessary. All personal information, messages, and consultation records shall remain confidential and will be used only for academic and administrative purposes.</p>
                    <p>The institution reserves the right to monitor system activity, perform maintenance, and enforce policies to ensure proper use of the platform. Any misuse, unauthorized access, or activities that may disrupt the system are strictly prohibited. Continued use of the system signifies acceptance of these terms and conditions.</p>
                </article>
                <article class="legal-modal-panel" data-legal-panel="privacy">
                    <p>The Online Faculty-Student Consultation System of the Computer Studies Department is committed to protecting the privacy and personal information of all users, including students, faculty members, and administrators.</p>
                    <p>Personal information such as names, email addresses, account credentials, consultation schedules, and communication records collected through the system shall be used solely for academic, administrative, and consultation-related purposes. All collected data will be handled with confidentiality and protected against unauthorized access, disclosure, or misuse.</p>
                    <p>The system may record user activities, including login details, consultation requests, and message history, to maintain security, improve system performance, and ensure proper implementation of institutional policies.</p>
                    <p>Only authorized personnel, including designated administrators and faculty members, shall have access to relevant information necessary for managing consultations and maintaining system operations.</p>
                    <p>The institution implements reasonable technical and administrative measures to safeguard user data; however, users are also responsible for protecting their account credentials and reporting any unauthorized account activity.</p>
                    <p>The system does not share personal information with third parties unless required by institutional policy, legal obligation, or authorized administrative purposes.</p>
                    <p>By using the system, users acknowledge and consent to the collection, use, and protection of their information in accordance with this Privacy Policy.</p>
                </article>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('authModal');
            const loginPanel = document.getElementById('loginPanel');
            const registerPanel = document.getElementById('registerPanel');
            const forgotPanel = document.getElementById('forgotPanel');
            const titleEl = document.getElementById('authModalTitle');
            const legalModal = document.getElementById('legalModal');
            const legalModalTitle = document.getElementById('legalModalTitle');
            const legalOpenButtons = Array.from(document.querySelectorAll('[data-open-legal]'));
            const legalCloseButtons = Array.from(document.querySelectorAll('[data-close-legal]'));
            const legalPanels = Array.from(document.querySelectorAll('[data-legal-panel]'));
            const legalTabButtons = Array.from(document.querySelectorAll('[data-legal-tab]'));

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

            const openLegalPanel = (panelName) => {
                if (!legalModal) return;

                const target = panelName === 'privacy' ? 'privacy' : 'terms';
                legalPanels.forEach((panel) => {
                    panel.classList.toggle('active', panel.dataset.legalPanel === target);
                });
                legalTabButtons.forEach((button) => {
                    button.classList.toggle('active', button.dataset.legalTab === target);
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

            legalTabButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    openLegalPanel(button.dataset.legalTab || 'terms');
                });
            });

            legalCloseButtons.forEach((button) => {
                button.addEventListener('click', closeLegalModal);
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && legalModal?.classList.contains('active')) {
                    closeLegalModal();
                }
            });

            const forcedAuth = @json($authPanel ?? request('auth'));
            const flashAuthForm = @json(session('auth_form'));
            const oldAuthForm = @json(old('auth_form'));
            const hasRegisterErrors = Boolean(@json($errors->any())) && oldAuthForm === 'register';
            const hasLoginErrors = Boolean(@json($errors->any())) && oldAuthForm === 'login';
            const hasForgotErrors = Boolean(@json($errors->any())) && oldAuthForm === 'forgot';

            const registerForm = document.querySelector('[data-live-validate="welcome-register"]');

            if (registerForm) {
                const touchedFields = new WeakMap();
                const registerSubmitButton = registerForm.querySelector('[data-submit-register]');
                const registerFields = Array.from(registerForm.querySelectorAll('.auth-input[name][data-rule]'));
                const legalCheckboxes = Array.from(registerForm.querySelectorAll('[data-legal-checkbox]'));
                const namePattern = /^(?=.*\p{L})[\p{L}\s'-]+$/u;
                const gmailPattern = /^[^\s@]+@gmail\.com$/i;

                const normalizeWhitespace = (value) => value.replace(/\s+/gu, ' ').trim();
                const normalizeName = (value) => normalizeWhitespace(value);

                const getErrorElement = (input) => {
                    const directSibling = input.parentElement?.querySelector('.auth-error');
                    if (directSibling) return directSibling;
                    return registerForm.querySelector(`[data-error-for="${input.name}"]`);
                };

                const getSuccessElement = (input) => registerForm.querySelector(`[data-success-for="${input.name}"]`);

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

                const evaluateName = (input) => {
                    const isOptional = input.dataset.optional === 'true';
                    const value = normalizeName(input.value);

                    if (!value) {
                        return isOptional
                            ? { valid: true, message: '', success: '' }
                            : { valid: false, message: 'Please enter a real name.', success: '' };
                    }

                    if (!namePattern.test(value)) {
                        return { valid: false, message: 'Names should only contain letters, spaces, hyphens, or apostrophes.', success: '' };
                    }

                    const lettersOnly = value.replace(/[^\p{L}]/gu, '').toLowerCase();

                    if (lettersOnly.length < 2) {
                        return { valid: false, message: 'Please enter a real name.', success: '' };
                    }

                    if (lettersOnly.length > 50 || value.length > 60) {
                        return { valid: false, message: "This doesn't look like a valid name.", success: '' };
                    }

                    if (/(\p{L})\1{3,}/u.test(lettersOnly)) {
                        return { valid: false, message: 'Please enter a real name.', success: '' };
                    }

                    if (/(\p{L}{2,4})\1{2,}/u.test(lettersOnly)) {
                        return { valid: false, message: 'Please avoid random or meaningless text.', success: '' };
                    }

                    const vowelCount = countVowels(lettersOnly);
                    if (lettersOnly.length >= 4 && vowelCount === 0) {
                        return { valid: false, message: "This doesn't look like a valid name.", success: '' };
                    }

                    if (lettersOnly.length >= 8 && (vowelCount / lettersOnly.length) < 0.23) {
                        return { valid: false, message: 'Please avoid random or meaningless text.', success: '' };
                    }

                    if (lettersOnly.length >= 10 && longestConsonantRun(lettersOnly) >= 5) {
                        return { valid: false, message: "This doesn't look like a valid name.", success: '' };
                    }

                    return { valid: true, message: '', success: 'Looks good.' };
                };

                const evaluateEmail = (input) => {
                    const value = normalizeWhitespace(input.value).toLowerCase();

                    if (!value) {
                        return { valid: false, message: 'Please enter a valid Gmail address.', success: '' };
                    }

                    if (!gmailPattern.test(value)) {
                        return { valid: false, message: 'Please enter a valid Gmail address.', success: '' };
                    }

                    return { valid: true, message: '', success: "This Gmail looks good. We'll verify it after signup." };
                };

                const evaluateStudentId = (input) => {
                    const value = normalizeWhitespace(input.value);

                    if (!value) {
                        return { valid: false, message: 'Student ID is required.', success: '' };
                    }

                    if (!/^\d{8}$/.test(value)) {
                        return { valid: false, message: 'Student ID must be exactly 8 digits.', success: '' };
                    }

                    return { valid: true, message: '', success: 'Student ID format looks good.' };
                };

                const evaluatePassword = (input) => {
                    const value = input.value;

                    if (!value) {
                        return { valid: false, message: 'Please create a password.', success: '' };
                    }

                    if (value.length < 8) {
                        return { valid: false, message: 'Use at least 8 characters for your password.', success: '' };
                    }

                    return { valid: true, message: '', success: 'Password length looks good.' };
                };

                const evaluatePasswordConfirmation = (input) => {
                    const value = input.value;
                    const passwordInput = registerForm.querySelector('[name="password"]');

                    if (!value) {
                        return { valid: false, message: 'Please confirm your password.', success: '' };
                    }

                    if (passwordInput && value !== passwordInput.value) {
                        return { valid: false, message: 'Passwords do not match yet.', success: '' };
                    }

                    return { valid: true, message: '', success: 'Passwords match.' };
                };

                const evaluateField = (input) => {
                    switch (input.dataset.rule) {
                        case 'name':
                            return evaluateName(input);
                        case 'gmail':
                            return evaluateEmail(input);
                        case 'student_id':
                            return evaluateStudentId(input);
                        case 'password':
                            return evaluatePassword(input);
                        case 'password_confirmation':
                            return evaluatePasswordConfirmation(input);
                        default:
                            return { valid: true, message: '', success: '' };
                    }
                };

                const applyFieldState = (input, result, options = {}) => {
                    const shouldShow = options.force === true || touchedFields.get(input) === true || input.value.trim() !== '';
                    const errorElement = getErrorElement(input);
                    const successElement = getSuccessElement(input);

                    if (!shouldShow) {
                        input.classList.remove('is-invalid', 'is-valid');
                        if (errorElement) errorElement.textContent = '';
                        if (successElement) successElement.textContent = '';
                        return;
                    }

                    input.classList.toggle('is-invalid', !result.valid);
                    input.classList.toggle('is-valid', result.valid && result.success !== '');

                    if (errorElement) {
                        errorElement.textContent = result.valid ? '' : result.message;
                    }

                    if (successElement) {
                        successElement.textContent = result.valid ? result.success : '';
                    }
                };

                const legalConsentsAccepted = () => legalCheckboxes.every((checkbox) => checkbox.checked);

                const evaluateFormForSubmit = () => (
                    registerFields.every((input) => evaluateField(input).valid) && legalConsentsAccepted()
                );

                const updateSubmitState = () => {
                    if (!registerSubmitButton) return;
                    registerSubmitButton.disabled = !evaluateFormForSubmit();
                };

                legalCheckboxes.forEach((checkbox) => {
                    checkbox.addEventListener('change', updateSubmitState);
                });

                registerFields.forEach((input) => {
                    input.addEventListener('input', () => {
                        touchedFields.set(input, true);
                        if (input.dataset.rule === 'gmail') {
                            input.value = input.value.replace(/\s+/gu, '').toLowerCase();
                        }

                        const result = evaluateField(input);
                        applyFieldState(input, result);

                        if (input.name === 'password') {
                            const confirmationInput = registerForm.querySelector('[name="password_confirmation"]');
                            if (confirmationInput) {
                                const confirmationResult = evaluateField(confirmationInput);
                                applyFieldState(confirmationInput, confirmationResult);
                            }
                        }

                        updateSubmitState();
                    });

                    input.addEventListener('blur', () => {
                        touchedFields.set(input, true);
                        applyFieldState(input, evaluateField(input), { force: true });
                        updateSubmitState();
                    });
                });

                registerForm.addEventListener('submit', (event) => {
                    let firstInvalidField = null;

                    registerFields.forEach((input) => {
                        touchedFields.set(input, true);
                        const result = evaluateField(input);
                        applyFieldState(input, result, { force: true });

                        if (!result.valid && !firstInvalidField) {
                            firstInvalidField = input;
                        }
                    });

                    updateSubmitState();

                    const firstMissingConsent = legalCheckboxes.find((checkbox) => !checkbox.checked);
                    if (firstMissingConsent && !firstInvalidField) {
                        firstInvalidField = firstMissingConsent;
                    }

                    if (firstInvalidField) {
                        event.preventDefault();
                        firstInvalidField.focus();
                    }
                });

                updateSubmitState();
            }

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
