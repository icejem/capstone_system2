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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* ── RESET & ROOT ── */
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        :root{
            --navy:#060e24;
            --navy2:#0a1628;
            --blue:#1d4ed8;
            --blue2:#2563eb;
            --blue3:#3b82f6;
            --accent:#60a5fa;
            --cyan:#22d3ee;
            --purple:#818cf8;
            --white:#ffffff;
            --text:#e2e8f0;
            --muted:#94a3b8;
        }
        html{scroll-behavior:smooth;}
        body{font-family:'Inter',sans-serif;background:var(--navy);color:var(--text);overflow-x:hidden;}

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar{width:6px;}
        ::-webkit-scrollbar-track{background:#0a1628;}
        ::-webkit-scrollbar-thumb{background:#2563eb;border-radius:3px;}

        /* ══════════════════════════════════════
           NAVBAR
        ══════════════════════════════════════ */
        .navbar{
            position:fixed;top:0;left:0;right:0;z-index:1100;
            display:flex;align-items:center;justify-content:space-between;
            padding:0 5.5%;height:60px;
            background:rgba(8,16,39,0.94);
            backdrop-filter:blur(18px);
            border-bottom:1px solid rgba(90,135,222,0.16);
            transition:all 0.3s;
        }
        .nav-logo{display:flex;align-items:center;gap:12px;text-decoration:none;}
        .nav-logo-icon{
            width:34px;height:34px;border-radius:50%;
            background:#fff;
            display:flex;align-items:center;justify-content:center;
            box-shadow:0 0 0 1px rgba(255,255,255,0.18);
            overflow:hidden;flex-shrink:0;
        }
        .nav-logo-icon img{width:100%;height:100%;object-fit:contain;}
        .nav-logo-text strong{display:block;font-size:12px;font-weight:800;color:#fff;line-height:1.1;}
        .nav-logo-text span{font-size:9px;color:#6ca0ff;letter-spacing:0.25px;line-height:1.15;}
        .nav-links{display:flex;align-items:center;gap:34px;}
        .nav-links a{font-size:13px;font-weight:600;color:#9aa7c0;text-decoration:none;transition:color 0.2s;}
        .nav-links a:hover{color:#fff;}
        .nav-cta{display:flex;align-items:center;gap:10px;}
        .btn-outline{
            min-width:78px;padding:8px 18px;border-radius:11px;
            border:1px solid rgba(70,129,233,0.5);
            color:#74abff;font-size:13px;font-weight:700;
            background:transparent;cursor:pointer;transition:all 0.2s;
        }
        .btn-outline:hover{background:rgba(39,93,186,0.16);border-color:#4e8cff;}
        .btn-primary-nav{
            min-width:86px;padding:8px 18px;border-radius:11px;
            background:#2d65ea;
            color:#fff;font-size:13px;font-weight:700;
            border:0;cursor:pointer;transition:all 0.25s;
        }
        .btn-primary-nav:hover{background:#2558d4;}

        /* ── Mobile nav hide ── */
        @media(max-width:820px){.nav-links{display:none;}}
        @media(max-width:500px){
            .navbar{
                height:auto;
                flex-wrap:wrap;
                align-items:flex-start;
                gap:10px 12px;
                padding:10px 4%;
            }
            .nav-logo{
                width:100%;
                align-items:flex-start;
            }
            .nav-logo-text{
                min-width:0;
                max-width:100%;
            }
            .nav-logo-text strong{font-size:11px;}
            .nav-logo-text span{font-size:9px;display:block;}
            .nav-cta{
                width:100%;
                justify-content:flex-end;
            }
            .btn-outline{padding:7px 14px;font-size:12px;}
            .btn-primary-nav{padding:7px 16px;font-size:12px;}
            .hero{
                padding-top:148px;
            }
        }

        /* ══════════════════════════════════════
           HERO
        ══════════════════════════════════════ */
        .hero{
            scroll-margin-top:60px;
            min-height:100vh;
            display:flex;align-items:center;justify-content:center;
            padding:108px 5.5% 46px;
            position:relative;overflow:hidden;
            background:
                radial-gradient(circle at 76% 19%,rgba(55,94,219,0.24),transparent 28%),
                radial-gradient(circle at 37% 54%,rgba(17,56,142,0.16),transparent 28%),
                linear-gradient(180deg,#0b1430 0%,#081023 100%);
        }
        .hero::before{
            content:'';position:absolute;inset:0;
            background-image:
                linear-gradient(rgba(64,101,178,0.07) 1px,transparent 1px),
                linear-gradient(90deg,rgba(64,101,178,0.07) 1px,transparent 1px);
            background-size:48px 48px;
            opacity:.38;
            pointer-events:none;
        }
        .orb{position:absolute;border-radius:50%;filter:blur(100px);pointer-events:none;animation:float 8s ease-in-out infinite;}
        .orb1{width:420px;height:420px;background:rgba(38,95,214,0.16);top:-120px;right:-80px;animation-delay:0s;}
        .orb2{width:320px;height:320px;background:rgba(99,102,241,0.12);bottom:-120px;left:-40px;animation-delay:4s;}
        .orb3{width:220px;height:220px;background:rgba(34,211,238,0.08);top:34%;left:42%;animation-delay:2s;}
        @keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-30px);}}

        .hero-inner{
            display:grid;grid-template-columns:minmax(0,1.02fr) minmax(500px,.98fr);gap:54px;align-items:center;
            max-width:1220px;width:100%;position:relative;z-index:1;
        }
        @media(max-width:860px){
            .hero-inner{grid-template-columns:1fr;text-align:center;}
            .hero-mockup{display:none;}
            .hero-stats{justify-content:center;}
            .hero-btns{justify-content:center;}
        }

        .hero-text{max-width:560px;}
        .hero-tag{
            display:inline-flex;align-items:center;gap:8px;
            background:rgba(27,56,119,0.48);border:1px solid rgba(67,117,208,0.55);
            border-radius:999px;padding:6px 14px;
            font-size:12px;font-weight:700;color:#5b9fff;
            margin-bottom:22px;letter-spacing:0.2px;
        }
        .hero-tag .dot{width:6px;height:6px;border-radius:50%;background:#22d3ee;animation:pulse 2s infinite;}
        @keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.5;transform:scale(1.4);}}

        .hero h1{
            font-size:clamp(52px,6.2vw,74px);font-weight:900;
            line-height:.98;color:#fff;margin-bottom:22px;
            letter-spacing:-2.6px;
        }
        .hero h1 .grad{
            background:linear-gradient(90deg,#5c95ff 0%,#7e8fff 42%,#2ec5f1 100%);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;
            background-clip:text;
        }
        .hero p{
            font-size:16px;font-weight:500;color:#95a1b6;
            line-height:1.6;max-width:510px;margin-bottom:34px;
        }
        .hero-btns{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:40px;}
        .btn-hero{
            min-height:44px;padding:12px 28px;border-radius:14px;font-size:15px;font-weight:800;
            display:inline-flex;align-items:center;gap:8px;
            transition:all 0.25s;cursor:pointer;border:0;
        }
        .btn-hero svg{width:18px;height:18px;}
        .btn-hero.primary{
            background:#2d65ea;color:#fff;
            box-shadow:inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .btn-hero.primary:hover{background:#2558d4;}
        .btn-hero.ghost{
            background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.12);
            color:#e9eef9;
        }
        .btn-hero.ghost:hover{background:rgba(255,255,255,0.08);}

        .hero-stats{display:flex;align-items:flex-start;gap:24px;}
        .h-stat .num{font-size:30px;font-weight:900;color:#fff;line-height:1;}
        .h-stat .lbl{font-size:12px;font-weight:600;color:#8f9cb4;margin-top:6px;}
        .h-divider{width:1px;height:44px;background:rgba(255,255,255,0.12);}

        /* ── Hero Mockup ── */
        .hero-mockup{position:relative;padding-top:18px;}
        .mockup-window{
            background:linear-gradient(180deg,rgba(18,34,86,0.96),rgba(11,24,60,0.98));
            border:1px solid rgba(63,104,190,0.45);
            border-radius:22px;padding:14px;
            box-shadow:0 28px 80px rgba(2,10,28,0.55),inset 0 1px 0 rgba(255,255,255,0.06);
        }
        .mockup-bar{display:flex;align-items:center;gap:7px;margin-bottom:12px;padding:2px 4px;}
        .mockup-dot{width:9px;height:9px;border-radius:50%;}
        .mockup-dot.r{background:#ff6d5a;}
        .mockup-dot.y{background:#ffbf3b;}
        .mockup-dot.g{background:#72d66a;}
        .mockup-bar-title{flex:1;text-align:center;font-size:10px;color:#90a1c6;}
        .video-call-ui{background:#0a142e;border-radius:18px;overflow:hidden;position:relative;border:1px solid rgba(255,255,255,0.04);}
        .vc-main{height:178px;display:flex;align-items:center;justify-content:center;background:linear-gradient(180deg,#25357f,#1d2961);position:relative;}
        .vc-avatar-lg{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#4e82ff,#7b49f0);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;color:#fff;}
        .vc-name-tag{position:absolute;bottom:10px;left:14px;background:rgba(12,18,41,0.88);border-radius:8px;padding:4px 10px;font-size:11px;font-weight:700;color:#fff;}
        .vc-self{position:absolute;bottom:10px;right:10px;width:72px;height:54px;border-radius:10px;background:#11724f;border:1px solid rgba(255,255,255,0.14);display:flex;align-items:center;justify-content:center;}
        .vc-avatar-sm{width:28px;height:28px;border-radius:50%;background:#16a56c;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#fff;}
        .vc-live{position:absolute;top:10px;right:10px;display:flex;align-items:center;gap:5px;background:rgba(144,40,52,0.28);border:1px solid rgba(235,95,95,0.4);border-radius:999px;padding:4px 10px;font-size:10px;font-weight:800;color:#ff8f9d;}
        .vc-live-dot{width:6px;height:6px;background:#ff5b64;border-radius:50%;animation:pulse 1.5s infinite;}
        .vc-controls{display:flex;align-items:center;justify-content:center;gap:10px;padding:14px;background:#0a142e;}
        .vc-btn{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;}
        .vc-btn svg{width:16px;height:16px;}
        .vc-btn.mic{background:rgba(255,255,255,0.12);color:#fff;}
        .vc-btn.cam{background:rgba(255,255,255,0.12);color:#fff;}
        .vc-btn.end{background:#ef4444;color:#fff;width:44px;height:44px;}
        .vc-btn.chat{background:rgba(37,99,235,0.26);color:#75a8ff;}
        .vc-btn.screen{background:rgba(255,255,255,0.12);color:#fff;}
        .mockup-info{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px;}
        .m-info-card{background:#132754;border:1px solid rgba(70,102,168,0.48);border-radius:12px;padding:10px 14px;}
        .m-info-card .m-label{font-size:9px;color:#8f9cb7;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;}
        .m-info-card .m-val{font-size:12px;font-weight:800;color:#fff;margin-top:3px;}

        .float-badge{
            position:absolute;top:0;right:-16px;
            background:linear-gradient(135deg,#315de8,#7034d7);
            border-radius:16px;padding:12px 18px;
            box-shadow:0 18px 38px rgba(67,54,201,0.3);
            border:1px solid rgba(255,255,255,0.08);
            animation:floatbadge 3s ease-in-out infinite;
        }
        @keyframes floatbadge{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
        .float-badge .fb-num{font-size:22px;font-weight:900;color:#fff;line-height:1;}
        .float-badge .fb-lbl{font-size:10px;color:rgba(214,222,255,0.72);font-weight:700;line-height:1.15;margin-top:4px;}
        .float-badge2{
            position:absolute;bottom:-6px;left:-18px;
            background:linear-gradient(135deg,#10875f,#1bc889);
            border-radius:14px;padding:10px 16px;
            box-shadow:0 18px 34px rgba(14,154,110,0.28);
            border:1px solid rgba(255,255,255,0.1);
            display:flex;align-items:center;gap:8px;
            animation:floatbadge 3s ease-in-out infinite;animation-delay:1.5s;
        }
        .float-badge2 svg{width:20px;height:20px;color:#6ee7b7;}
        .float-badge2 div .t{font-size:11px;font-weight:700;color:#fff;}
        .float-badge2 div .s{font-size:9px;color:rgba(200,255,230,0.7);}

        /* ══════════════════════════════════════
           SECTION COMMONS
        ══════════════════════════════════════ */
        section{padding:90px 6%;}
        .section-label{
            display:inline-flex;align-items:center;gap:8px;
            background:rgba(59,130,246,0.08);border:1px solid rgba(59,130,246,0.2);
            border-radius:20px;padding:5px 14px;
            font-size:11px;font-weight:700;color:var(--accent);
            letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;
        }
        .section-title{font-size:clamp(28px,4vw,44px);font-weight:900;color:#fff;line-height:1.15;margin-bottom:14px;letter-spacing:-0.5px;}
        .section-sub{font-size:16px;color:var(--muted);max-width:520px;line-height:1.7;margin-bottom:60px;}
        .text-center{text-align:center;margin:0 auto;}

        /* ══════════════════════════════════════
           FEATURES
        ══════════════════════════════════════ */
        .features{background:linear-gradient(180deg,#080f1e 0%,#060e24 100%);position:relative;}
        .features::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0%,rgba(37,99,235,0.07) 0%,transparent 60%);pointer-events:none;}
        .features-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;position:relative;z-index:1;max-width:940px;margin:0 auto;}
        @media(max-width:900px){.features-grid{grid-template-columns:1fr 1fr;max-width:none;}}
        @media(max-width:580px){.features-grid{grid-template-columns:1fr;}}

        .feat-card{
            background:linear-gradient(160deg,rgba(255,255,255,0.04),rgba(255,255,255,0.01));
            border:1px solid rgba(255,255,255,0.07);
            border-radius:20px;padding:28px;
            transition:all 0.3s;position:relative;overflow:hidden;
        }
        .feat-card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--card-glow,rgba(37,99,235,0.1)),transparent 60%);opacity:0;transition:opacity 0.3s;}
        .feat-card:hover{transform:translateY(-6px);border-color:rgba(59,130,246,0.3);box-shadow:0 20px 40px rgba(0,0,0,0.3);}
        .feat-card:hover::before{opacity:1;}
        .feat-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;}
        .feat-icon svg{width:24px;height:24px;}
        .fi-blue{background:rgba(37,99,235,0.15);color:#60a5fa;border:1px solid rgba(37,99,235,0.25);}
        .fi-purple{background:rgba(139,92,246,0.15);color:#c4b5fd;border:1px solid rgba(139,92,246,0.25);}
        .fi-cyan{background:rgba(34,211,238,0.1);color:#67e8f9;border:1px solid rgba(34,211,238,0.2);}
        .fi-green{background:rgba(16,185,129,0.12);color:#6ee7b7;border:1px solid rgba(16,185,129,0.2);}
        .fi-orange{background:rgba(245,158,11,0.12);color:#fcd34d;border:1px solid rgba(245,158,11,0.2);}
        .fi-pink{background:rgba(236,72,153,0.12);color:#f9a8d4;border:1px solid rgba(236,72,153,0.2);}
        .feat-card h3{font-size:16px;font-weight:700;color:#fff;margin-bottom:10px;}
        .feat-card p{font-size:13px;color:var(--muted);line-height:1.65;}

        /* ══════════════════════════════════════
           HOW IT WORKS
        ══════════════════════════════════════ */
        .how{background:linear-gradient(135deg,#060e24 0%,#0a1628 100%);position:relative;overflow:hidden;}
        .how::before{content:'';position:absolute;right:-200px;top:50%;transform:translateY(-50%);width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,0.08),transparent 70%);pointer-events:none;}
        .steps-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;position:relative;z-index:1;}
        @media(max-width:900px){.steps-grid{grid-template-columns:1fr 1fr;}}
        @media(max-width:500px){.steps-grid{grid-template-columns:1fr;}}
        .steps-grid::before{content:'';position:absolute;top:38px;left:12%;right:12%;height:2px;background:linear-gradient(90deg,transparent,rgba(59,130,246,0.3) 20%,rgba(59,130,246,0.3) 80%,transparent);z-index:0;}
        @media(max-width:900px){.steps-grid::before{display:none;}}
        .step-card{background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.07);border-radius:18px;padding:26px 22px;text-align:center;transition:all 0.3s;position:relative;z-index:1;}
        .step-card:hover{background:rgba(255,255,255,0.05);transform:translateY(-4px);}
        .step-num{width:52px;height:52px;border-radius:50%;margin:0 auto 16px;background:linear-gradient(135deg,#1d4ed8,#3b82f6);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900;color:#fff;box-shadow:0 4px 16px rgba(37,99,235,0.5);}
        .step-card h3{font-size:15px;font-weight:700;color:#fff;margin-bottom:8px;}
        .step-card p{font-size:12px;color:var(--muted);line-height:1.6;}

        /* ══════════════════════════════════════
           CTA
        ══════════════════════════════════════ */
        .cta-section{padding:80px 6%;text-align:center;background:linear-gradient(180deg,#080f1e,#060e24);position:relative;overflow:hidden;}
        .cta-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 50% 50%,rgba(37,99,235,0.12) 0%,transparent 60%);}
        .cta-box{max-width:680px;margin:0 auto;position:relative;z-index:1;background:linear-gradient(135deg,rgba(29,78,216,0.15),rgba(99,102,241,0.1));border:1px solid rgba(59,130,246,0.25);border-radius:28px;padding:60px 40px;box-shadow:0 0 80px rgba(37,99,235,0.12);}
        .cta-box h2{font-size:clamp(28px,4vw,40px);font-weight:900;color:#fff;margin-bottom:14px;letter-spacing:-0.5px;}
        .cta-box p{font-size:16px;color:var(--muted);margin-bottom:32px;line-height:1.6;}
        .cta-btns{display:flex;justify-content:center;gap:14px;flex-wrap:wrap;}
        @media(max-width:500px){.cta-box{padding:40px 20px;}.cta-btns{flex-direction:column;align-items:center;}}

        /* ══════════════════════════════════════
           FOOTER
        ══════════════════════════════════════ */
        footer{background:#040b1a;border-top:1px solid rgba(255,255,255,0.05);padding:50px 6% 30px;}
        .footer-inner{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;margin-bottom:40px;}
        @media(max-width:800px){.footer-inner{grid-template-columns:1fr 1fr;}}
        @media(max-width:500px){.footer-inner{grid-template-columns:1fr;}}
        .footer-brand .logo{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
        .footer-logo-icon{width:36px;height:36px;border-radius:9px;background:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden;}
        .footer-logo-icon img{width:100%;height:100%;object-fit:contain;}
        .footer-brand .logo strong{font-size:14px;font-weight:800;color:#fff;}
        .footer-brand p{font-size:12px;color:var(--muted);line-height:1.7;max-width:220px;}
        .footer-col h4{font-size:12px;font-weight:700;color:#fff;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;}
        .footer-col a{display:block;font-size:12px;color:var(--muted);text-decoration:none;margin-bottom:10px;transition:color 0.2s;}
        .footer-col a:hover{color:var(--accent);}
        .footer-bottom{border-top:1px solid rgba(255,255,255,0.05);padding-top:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
        .footer-bottom p{font-size:11px;color:var(--muted);}
        .social-links{display:flex;gap:10px;}
        .social-btn{width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all 0.2s;cursor:pointer;}
        .social-btn:hover{background:rgba(59,130,246,0.15);border-color:rgba(59,130,246,0.3);color:var(--accent);}
        .social-btn svg{width:15px;height:15px;}

        /* ══════════════════════════════════════
           FADE-IN ANIMATION
        ══════════════════════════════════════ */
        .fade-in{opacity:0;transform:translateY(30px);transition:opacity 0.6s ease,transform 0.6s ease;}
        .fade-in.visible{opacity:1;transform:translateY(0);}

        /* ══════════════════════════════════════
           AUTH MODAL  (original — untouched)
        ══════════════════════════════════════ */
        .modal-shell{position:fixed;inset:0;display:none;align-items:center;justify-content:center;padding:18px;z-index:1200;}
        .modal-shell.active{display:flex;}
        .modal-backdrop{position:absolute;inset:0;background:rgba(2,10,26,0.74);backdrop-filter:blur(6px);}
        .auth-modal{
            position:relative;width:min(480px,100%);max-height:calc(100vh - 28px);
            overflow-y:auto;border-radius:16px;
            border:1px solid rgba(120,206,255,0.4);
            background:linear-gradient(150deg,rgba(4,19,43,0.96),rgba(7,27,58,0.96));
            box-shadow:0 18px 48px rgba(1,8,21,0.6);
            padding:14px;animation:popIn .22s ease;
        }
        .auth-modal.register-mode{width:min(780px,100%);}
        @keyframes popIn{from{opacity:0;transform:scale(.98) translateY(8px);}to{opacity:1;transform:scale(1) translateY(0);}}
        .auth-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;}
        .auth-title{margin:0;font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:#eaf8ff;}
        .auth-close{width:34px;height:34px;border-radius:10px;border:1px solid rgba(134,220,255,0.45);background:rgba(10,39,79,0.6);color:#cde9f8;font-size:20px;font-family:Arial,sans-serif;font-weight:700;line-height:1;cursor:pointer;}
        .auth-close:hover{background:rgba(12,52,101,0.78);color:#f0fbff;}
        .auth-status{
            margin-bottom:10px;
            border:1px solid rgba(96,165,250,0.45);
            background:rgba(30,41,89,0.45);
            color:#dbeafe;
            border-radius:10px;
            padding:8px 10px;
            font-size:13px;
            font-weight:700;
            transition:opacity .5s ease,transform .5s ease;
        }
        .auth-status.is-hiding{opacity:0;transform:translateY(-4px);}
        .auth-status-inner{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;}
        .auth-status-close{
            border:0;
            background:transparent;
            color:inherit;
            font-size:18px;
            font-weight:800;
            line-height:1;
            cursor:pointer;
            padding:0;
            opacity:.82;
        }
        .auth-status-close:hover{opacity:1;}
        .auth-grid{display:grid;gap:8px;}
        .auth-grid-register{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px 10px;}
        .auth-span-2{grid-column:1/-1;}
        .auth-label{font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#afcee2;}
        .auth-input{width:100%;border:1px solid rgba(117,203,255,0.35);border-radius:11px;padding:9px 11px;font-size:14px;color:#e9f8ff;background:rgba(7,24,51,0.78);outline:none;font-family:'Inter',sans-serif;}
        .auth-password-wrap{position:relative;}
        .auth-password-wrap .auth-input{padding-right:42px;}
        .auth-password-toggle{position:absolute;top:50%;right:12px;transform:translateY(-50%);width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;padding:0;border:0;background:transparent;color:#f8fbff;opacity:0.9;cursor:pointer;}
        .auth-password-toggle:hover{opacity:1;}
        .auth-password-toggle svg{width:18px;height:18px;}
        .auth-password-toggle .eye-off{display:none;}
        .auth-password-toggle.is-visible .eye-on{display:none;}
        .auth-password-toggle.is-visible .eye-off{display:block;}
        .auth-input::placeholder{color:#7fa5bf;}
        .auth-input:focus{border-color:#33cfff;box-shadow:0 0 0 4px rgba(51,207,255,0.2);}
        .auth-input.is-invalid{border-color:rgba(248,113,113,0.92);box-shadow:0 0 0 4px rgba(248,113,113,0.16);}
        .auth-row{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:2px;}
        .auth-check{display:inline-flex;align-items:center;gap:8px;color:#a8c9dd;font-size:13px;}
        .auth-check input{accent-color:#0fd1ff;}
        .auth-link{color:#59daff;text-decoration:none;font-size:13px;font-weight:700;}
        .auth-link:hover{text-decoration:underline;}
        .auth-btn{margin-top:6px;width:100%;border:0;border-radius:11px;padding:10px;font-size:13px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:#f4fdff;background:#2563eb;cursor:pointer;box-shadow:none;}
        .auth-btn:hover{background:#1d4ed8;filter:none;}
        .auth-btn:disabled{opacity:0.6;cursor:not-allowed;filter:none;box-shadow:none;}
        .auth-error{margin-top:5px;color:#fecaca;font-size:12px;font-weight:600;}
        .auth-success{margin-top:5px;color:#bbf7d0;font-size:12px;font-weight:600;}
        .auth-success:empty,.auth-error:empty{display:none;}
        .auth-note{margin-top:4px;color:#9fd2ea;font-size:11px;line-height:1.45;}
        .auth-foot{margin-top:6px;text-align:center;color:#99bfd7;font-size:12px;}
        .auth-consent-wrap{margin-top:4px;display:grid;gap:6px;}
        .auth-consent-check{display:flex;align-items:flex-start;gap:8px;padding:7px 0;border:0;border-radius:12px;background:transparent;color:#c9e7f8;font-size:12px;line-height:1.5;}
        .auth-consent-check input{margin-top:2px;accent-color:#33cfff;}
        .auth-consent-check strong{color:#eef8ff;}
        .auth-legal-link{border:0;background:transparent;padding:0;color:#6fe8ff;font-weight:700;text-decoration:underline;cursor:pointer;font:inherit;}
        .auth-legal-summary{font-size:11px;color:#8db3ca;line-height:1.55;padding:0 2px;}
        .auth-panel{display:none;padding:0;margin:0;}
        .auth-panel.active{display:block;}
        @media(max-width:620px){
            .auth-modal{padding:14px;}
            .auth-grid-register{grid-template-columns:1fr;}
            .auth-span-2{grid-column:auto;}
        }

        /* ══════════════════════════════════════
           LEGAL MODAL  (original — untouched)
        ══════════════════════════════════════ */
        .legal-modal-shell{position:fixed;inset:0;z-index:1500;display:none;align-items:center;justify-content:center;padding:18px;}
        .legal-modal-shell.active{display:flex;}
        .legal-modal-backdrop{position:absolute;inset:0;background:rgba(15,23,42,0.52);backdrop-filter:blur(4px);}
        .legal-modal-card{position:relative;width:min(760px,100%);max-height:calc(100vh - 36px);overflow:hidden;border-radius:18px;border:1px solid rgba(148,163,184,0.26);background:#ffffff;box-shadow:0 24px 70px rgba(15,23,42,0.24);}
        .legal-modal-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid #e2e8f0;background:#f8fafc;}
        .legal-modal-title{margin:0;font-size:18px;font-weight:800;color:#0f172a;}
        .legal-modal-close{width:36px;height:36px;border-radius:10px;border:1px solid #dbe3f0;background:#ffffff;color:#475569;font-size:20px;line-height:1;cursor:pointer;}
        .legal-modal-body{max-height:calc(100vh - 196px);overflow-y:auto;padding:16px 18px 18px;color:#475569;font-size:13px;line-height:1.7;}
        .legal-modal-panel{display:none;}
        .legal-modal-panel.active{display:block;}
        .legal-modal-body p{margin:0 0 14px;}
        .legal-modal-body p:last-child{margin-bottom:0;}
        .legal-modal-actions{display:flex;justify-content:flex-end;gap:10px;padding:14px 18px 18px;border-top:1px solid rgba(148,163,184,0.18);background:#f8fbff;}
        .legal-action-btn{min-width:110px;padding:10px 16px;border-radius:12px;font-size:13px;font-weight:800;cursor:pointer;transition:all .2s;border:1px solid transparent;}
        .legal-action-btn-secondary{background:#ffffff;color:#475569;border-color:rgba(148,163,184,0.35);}
        .legal-action-btn-secondary:hover{background:#f8fafc;border-color:rgba(100,116,139,0.5);}
        .legal-action-btn-primary{background:#2563eb;color:#ffffff;box-shadow:0 12px 30px rgba(37,99,235,0.18);}
        .legal-action-btn-primary:hover{background:#1d4ed8;}
    </style>
</head>
<body>

    <!-- ══════════════ NAVBAR ══════════════ -->
    <nav class="navbar" id="top">
        <a href="#home" class="nav-logo">
            <div class="nav-logo-icon">
                <img src="{{ asset('cslogo.jpg') }}" alt="CCS Logo">
            </div>
            <div class="nav-logo-text">
                <strong>College of Computer Studies</strong>
                <span>Philippine College of Science and Technology</span>
            </div>
        </a>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#features">Features</a>
            <a href="#how">How It Works</a>
            <a href="#contact">Get Started</a>
        </div>
        <div class="nav-cta">
            <button type="button" class="btn-outline" data-open-auth="login">Log In</button>
            @if(Route::has('register'))
                <button type="button" class="btn-primary-nav" data-open-auth="register">Register</button>
            @endif
        </div>
    </nav>

    <!-- ══════════════ HERO ══════════════ -->
    <section class="hero" id="home">
        <div class="orb orb1"></div>
        <div class="orb orb2"></div>
        <div class="orb orb3"></div>

        <div class="hero-inner">
            <!-- Left: Text -->
            <div class="hero-text fade-in">
                <h1>
                    Online<br>
                    Faculty - Student<br>
                    <span class="grad">Consultation</span>
                </h1>
                <p>Bridge the gap between students and faculty. Schedule, connect, and consult — all in one secure, real-time platform built for the College of Computer Studies.</p>
                <div class="hero-btns">
                    <button type="button" class="btn-hero primary" data-open-auth="register">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        Start Consultation
                    </button>
                    <a href="#how" class="btn-hero ghost">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/></svg>
                        See How It Works
                    </a>
                </div>
            </div>

            <!-- Right: Mockup -->
            <div class="hero-mockup fade-in" style="animation-delay:0.2s;">
                <div class="float-badge">
                    <div class="fb-num">28</div>
                    <div class="fb-lbl">Sessions This Month</div>
                </div>
                <div class="mockup-window">
                    <div class="mockup-bar">
                        <span class="mockup-dot r"></span>
                        <span class="mockup-dot y"></span>
                        <span class="mockup-dot g"></span>
                        <span class="mockup-bar-title">CCS Consultation · Live Session</span>
                    </div>
                    <div class="video-call-ui">
                        <div class="vc-main">
                            <div class="vc-avatar-lg">P</div>
                            <div class="vc-name-tag">Prof. Marquez · Faculty</div>
                            <div class="vc-self"><div class="vc-avatar-sm">J</div></div>
                            <div class="vc-live"><span class="vc-live-dot"></span> LIVE</div>
                        </div>
                        <div class="vc-controls">
                            <div class="vc-btn mic">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/><path d="M19 10v2a7 7 0 01-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
                            </div>
                            <div class="vc-btn cam">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                            </div>
                            <div class="vc-btn end">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.68 13.31a16 16 0 003.41 2.6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7 2 2 0 011.72 2v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07C9.44 16.29 7.71 14.56 6.37 12.5A19.79 19.79 0 013.3 3.87 2 2 0 015.27 1.7h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L9.25 9.5"/><line x1="23" y1="1" x2="1" y2="23"/></svg>
                            </div>
                            <div class="vc-btn screen">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            </div>
                            <div class="vc-btn chat">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="mockup-info">
                        <div class="m-info-card">
                            <div class="m-label">Topic</div>
                            <div class="m-val">Academic Performance</div>
                        </div>
                        <div class="m-info-card">
                            <div class="m-label">Duration</div>
                            <div class="m-val">00:42:18 ● REC</div>
                        </div>
                    </div>
                </div>
                <div class="float-badge2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <div><div class="t">Session Secured</div><div class="s">End-to-end encrypted</div></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════ FEATURES ══════════════ -->
    <section class="features" id="features">
        <div style="text-align:center;position:relative;z-index:1;">
            <div class="section-label">✦ Platform Features</div>
            <h2 class="section-title">Everything You Need,<br>All in One Place</h2>
            <p class="section-sub text-center">Designed specifically for the College of Computer Studies — connecting faculty and students seamlessly.</p>
        </div>
        <div class="features-grid fade-in">
            <div class="feat-card" style="--card-glow:rgba(37,99,235,0.15);">
                <div class="feat-icon fi-blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                </div>
                <h3>HD Video Consultation</h3>
                <p>Crystal-clear, real-time video calls between students and faculty. Screen sharing, recording, and virtual backgrounds supported.</p>
            </div>
            <div class="feat-card" style="--card-glow:rgba(139,92,246,0.15);">
                <div class="feat-icon fi-purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <h3>Smart Scheduling</h3>
                <p>View faculty availability in real-time. Book, reschedule, or cancel appointments with automated email confirmations.</p>
            </div>
            <div class="feat-card" style="--card-glow:rgba(16,185,129,0.1);">
                <div class="feat-icon fi-green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Secure &amp; Private</h3>
                <p>End-to-end encrypted sessions. Only enrolled CCS students and registered faculty can access the platform.</p>
            </div>
            <div class="feat-card" style="--card-glow:rgba(236,72,153,0.1);">
                <div class="feat-icon fi-pink">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                </div>
                <h3>Smart Notifications</h3>
                <p>Get notified before sessions, receive feedback forms after consultations, and stay updated on schedule changes.</p>
            </div>
        </div>
    </section>

    <!-- ══════════════ HOW IT WORKS ══════════════ -->
    <section class="how" id="how">
        <div style="text-align:center;position:relative;z-index:1;">
            <div class="section-label">✦ Process</div>
            <h2 class="section-title">How It Works</h2>
            <p class="section-sub text-center">Get started in 4 simple steps — no technical expertise required.</p>
        </div>
        <div class="steps-grid fade-in">
            <div class="step-card">
                <div class="step-num">1</div>
                <h3>Create Account</h3>
                <p>Register using your school email. Students and faculty get separate dashboards tailored to their needs.</p>
            </div>
            <div class="step-card">
                <div class="step-num">2</div>
                <h3>Request Consultation</h3>
                <p>Choose your concern type — academic, behavior, or curricular. Pick your preferred faculty and time slot.</p>
            </div>
            <div class="step-card">
                <div class="step-num">3</div>
                <h3>Get Confirmed</h3>
                <p>Faculty reviews and approves your request. You'll receive an instant notification with your session link.</p>
            </div>
            <div class="step-card">
                <div class="step-num">4</div>
                <h3>Start Video Call</h3>
                <p>Join your session right from the browser — no downloads needed. Chat, share screens, and consult live.</p>
            </div>
        </div>
    </section>

    <!-- ══════════════ CTA ══════════════ -->
    <section class="cta-section" id="contact">
        <div class="cta-box fade-in">
            <div class="section-label" style="display:inline-flex;">🚀 Ready to Get Started?</div>
            <h2>Connect with Your Faculty<br>Anytime, Anywhere</h2>
            <p>Join hundreds of CCS students already using Consultation Platform to get the academic support they need — through seamless, professional video consultations.</p>
            <div class="cta-btns">
                <button type="button" class="btn-hero primary" data-open-auth="register">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Sign Up as Student
                </button>
                <button type="button" class="btn-hero ghost" data-open-auth="login">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Log In to Account
                </button>
            </div>
        </div>
    </section>

    <!-- ══════════════ FOOTER ══════════════ -->
    <footer>
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="logo">
                    <div class="footer-logo-icon">
                        <img src="{{ asset('cslogo.jpg') }}" alt="CCS Logo">
                    </div>
                    <strong>CCS Consultation Platform</strong>
                </div>
                <p>Online Faculty–Student Consultation Platform — Philippine College of Science and Technology, College of Computer Studies.</p>
            </div>
            <div class="footer-col">
                <h4>Platform</h4>
                <a href="#">Dashboard</a>
                <a href="#">Request Session</a>
                <a href="#">My Consultations</a>
                <a href="#">History</a>
            </div>
            <div class="footer-col">
                <h4>Support</h4>
                <a href="#">Help Center</a>
                <a href="#">Contact Admin</a>
                <a href="#">System Status</a>
                <a href="#">Privacy Policy</a>
            </div>
            <div class="footer-col">
                <h4>About</h4>
                <a href="#">About PCST</a>
                <a href="#">CCS Department</a>
                <a href="#">Faculty Directory</a>
                <a href="#">Announcements</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 CCS Consultation Platform · Philippine College of Science and Technology. All rights reserved.</p>
            <div class="social-links">
                <div class="social-btn">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </div>
                <div class="social-btn">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                </div>
                <div class="social-btn">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </div>
            </div>
        </div>
    </footer>

    <!-- ══════════════ AUTH MODAL (original — fully preserved) ══════════════ -->
    <div class="modal-shell" id="authModal" aria-hidden="true">
        <div class="modal-backdrop" data-close-auth></div>
        <div class="auth-modal" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">
            <div class="auth-head">
                <h2 class="auth-title" id="authModalTitle">Account Access</h2>
                <button type="button" class="auth-close" data-close-auth aria-label="Close">&times;</button>
            </div>

            @if(session('status') && in_array(session('auth_form'), ['forgot', 'login'], true))
                <div class="auth-status" data-auth-status role="status">
                    <div class="auth-status-inner">
                        <span>{{ session('status') }}</span>
                        <button type="button" class="auth-status-close" data-auth-status-close aria-label="Dismiss status message">&times;</button>
                    </div>
                </div>
            @endif

            <!-- LOGIN PANEL -->
            <section class="auth-panel" id="loginPanel">
                <form method="POST" action="{{ route('login') }}" class="auth-grid" id="loginForm" autocomplete="off" data-server-email="{{ old('email') }}">
                    @csrf
                    <input type="hidden" name="auth_form" value="login">
                    <input type="hidden" name="device_fingerprint" id="loginDeviceFingerprint" value="{{ old('device_fingerprint') }}">
                    <div>
                        <label class="auth-label" for="loginEmail">Email</label>
                        <input id="loginEmail" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" placeholder="you@example.com">
                        @error('email')<div class="auth-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="auth-label" for="loginPassword">Password</label>
                        <div class="auth-password-wrap">
                            <input id="loginPassword" class="auth-input" type="password" name="password" required autocomplete="off" placeholder="Enter password">
                            <button type="button" class="auth-password-toggle" data-toggle-password data-target="loginPassword" aria-label="Show password">
                                <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0112 19C5 19 1 12 1 12a21.76 21.76 0 015.06-5.94"/><path d="M9.9 4.24A10.94 10.94 0 0112 5c7 0 11 7 11 7a21.8 21.8 0 01-4.31 5.07"/><path d="M14.12 14.12A3 3 0 019.88 9.88"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        @error('password')<div class="auth-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="auth-row">
                        <label class="auth-check" for="remember_me">
                            <input type="hidden" name="remember" value="0">
                            <input id="remember_me" type="checkbox" name="remember" value="1" @checked(old('remember') == 1)>
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="auth-link" data-switch-auth="forgot">Forgot password?</a>
                    </div>
                    <button type="submit" class="auth-btn">Login</button>
                    @if(Route::has('register'))
                        <div class="auth-foot">No account yet? <a href="#" class="auth-link" data-switch-auth="register">Register</a></div>
                    @endif
                </form>
            </section>

            <!-- REGISTER PANEL -->
            @if(Route::has('register'))
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
                        <label class="auth-label" for="registerPhoneNumber">Mobile Number</label>
                        <input id="registerPhoneNumber" class="auth-input @error('phone_number') is-invalid @enderror" type="text" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="tel" placeholder="09171234567" maxlength="20" data-label="Mobile number" data-rule="phone_number">
                        <div class="auth-error" data-error-for="phone_number">@error('phone_number'){{ $message }}@enderror</div>
                        <div class="auth-success" data-success-for="phone_number"></div>
                        <div class="auth-note">Used for SMS reminders and consultation notifications.</div>
                    </div>
                    <div>
                        <label class="auth-label" for="registerPassword">Password</label>
                        <input id="registerPassword" class="auth-input @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" placeholder="Create password" data-label="Password" data-rule="password">
                        <div class="auth-error" data-error-for="password">@error('password'){{ $message }}@enderror</div>
                        <div class="auth-success" data-success-for="password"></div>
                        <div class="auth-note">Password must be at least 8 characters and include uppercase, lowercase, number, and special character.</div>
                    </div>
                    <div>
                        <label class="auth-label" for="registerPasswordConfirmation">Confirm Password</label>
                        <input id="registerPasswordConfirmation" class="auth-input @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password" data-label="Password confirmation" data-rule="password_confirmation">
                        <div class="auth-error" data-error-for="password_confirmation">@error('password_confirmation'){{ $message }}@enderror</div>
                        <div class="auth-success" data-success-for="password_confirmation"></div>
                    </div>
                    <div>
                        <label class="auth-label" for="registerStudentId">Student ID</label>
                        <input id="registerStudentId" class="auth-input @error('student_id') is-invalid @enderror" type="text" name="student_id" value="{{ old('student_id') }}" placeholder="Enter 8-digit Student ID" inputmode="numeric" pattern="\d{8}" minlength="8" maxlength="8" required data-label="Student ID" data-rule="student_id">
                        @error('student_id')<div class="auth-error">{{ $message }}</div>@enderror
                        <div class="auth-success" data-success-for="student_id"></div>
                    </div>
                    <div>
                        <label class="auth-label" for="registerYearLevel">Year Level</label>
                        <select id="registerYearLevel" class="auth-input @error('year_level') is-invalid @enderror" name="year_level" required data-label="Year level" data-rule="year_level">
                            <option value="">Select Year Level</option>
                            @foreach (\App\Models\User::yearLevelLabels() as $value => $label)
                                <option value="{{ $value }}" @selected(old('year_level') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('year_level')<div class="auth-error">{{ $message }}</div>@enderror
                        <div class="auth-success" data-success-for="year_level"></div>
                    </div>
                    <button type="submit" class="auth-btn auth-span-2" data-submit-register disabled>Create Account</button>
                    <div class="auth-consent-wrap auth-span-2">
                        <label class="auth-consent-check" for="registerTermsAccepted">
                            <input id="registerTermsAccepted" type="checkbox" name="terms_accepted" value="1" data-legal-checkbox="terms" @checked(old('terms_accepted'))>
                            <span><strong>I agree</strong> to the <button type="button" class="auth-legal-link" data-open-legal="terms">Terms and Conditions</button>.</span>
                        </label>
                        @error('terms_accepted')<div class="auth-error">{{ $message }}</div>@enderror
                        <label class="auth-consent-check" for="registerPrivacyAccepted">
                            <input id="registerPrivacyAccepted" type="checkbox" name="privacy_accepted" value="1" data-legal-checkbox="privacy" @checked(old('privacy_accepted'))>
                            <span><strong>I agree</strong> to the <button type="button" class="auth-legal-link" data-open-legal="privacy">Privacy Policy</button>.</span>
                        </label>
                        @error('privacy_accepted')<div class="auth-error">{{ $message }}</div>@enderror
                        <div class="auth-legal-summary">Please review both documents before creating your account.</div>
                    </div>
                    <div class="auth-foot auth-span-2">Already registered? <a href="#" class="auth-link" data-switch-auth="login">Login</a></div>
                </form>
            </section>
            @endif

            <!-- FORGOT PANEL -->
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
                    <div class="auth-foot">Back to <a href="#" class="auth-link" data-switch-auth="login">Login</a></div>
                </form>
            </section>
        </div>
    </div>

    <!-- ══════════════ LEGAL MODAL (original — fully preserved) ══════════════ -->
    <div class="legal-modal-shell" id="legalModal" aria-hidden="true">
        <div class="legal-modal-backdrop" data-close-legal></div>
        <div class="legal-modal-card" role="dialog" aria-modal="true" aria-labelledby="legalModalTitle">
            <div class="legal-modal-head">
                <h3 class="legal-modal-title" id="legalModalTitle">Terms and Conditions</h3>
                <button type="button" class="legal-modal-close" data-close-legal aria-label="Close legal document">&times;</button>
            </div>
            <div class="legal-modal-body">
                <article class="legal-modal-panel active" data-legal-panel="terms">
                    <p><strong>User Terms and Conditions</strong></p>
                    <p><strong>Account Information</strong><br>Users must provide accurate and complete information when creating an account in the system. Any false or misleading information may result in restricted access or account suspension.</p>
                    <p><strong>Account Responsibility</strong><br>Users are responsible for keeping their login credentials confidential. Any activity performed using a registered account is considered the responsibility of the account owner.</p>
                    <p><strong>Proper Use of the System</strong><br>The system must only be used for academic and consultation-related purposes. Users must communicate respectfully and avoid inappropriate language, misuse, or unauthorized activities within the platform.</p>
                    <p><strong>System Availability</strong><br>The system may occasionally undergo maintenance, updates, or temporary interruptions. Users understand that access may not always be available during these periods.</p>
                    <p><strong>Suspension of Access</strong><br>The system administrator reserves the right to suspend or terminate access if a user violates system policies, disrupts operations, or performs actions that may affect security and reliability.</p>
                </article>
                <article class="legal-modal-panel" data-legal-panel="privacy">
                    <p><strong>Privacy Policy</strong></p>
                    <p><strong>Collection of Information</strong><br>The system collects necessary personal information such as name, email address, and account details to support registration, consultation scheduling, and communication between users.</p>
                    <p><strong>Use of Information</strong><br>Collected information is used only for academic consultation purposes, system management, and improving user experience within the platform.</p>
                    <p><strong>Protection of Data</strong><br>Personal data stored in the system is protected through appropriate security measures to prevent unauthorized access, loss, or misuse of information.</p>
                    <p><strong>Access to Information</strong><br>Only authorized administrators and permitted users may access personal information when necessary for system operations and academic transactions.</p>
                    <p><strong>Policy Updates</strong><br>The system administration may update this Privacy Policy when needed. Users will be informed of significant changes affecting the handling of personal information.</p>
                </article>
            </div>
            <div class="legal-modal-actions">
                <button type="button" class="legal-action-btn legal-action-btn-secondary" data-legal-decision="disagree">Disagree</button>
                <button type="button" class="legal-action-btn legal-action-btn-primary" data-legal-decision="agree">Agree</button>
            </div>
        </div>
    </div>

    <!-- ══════════════ JAVASCRIPT ══════════════ -->
    <script>
    (function () {
        // ── Auth Modal ──
        const modal = document.getElementById('authModal');
        const loginPanel = document.getElementById('loginPanel');
        const loginForm = document.getElementById('loginForm');
        const loginEmailInput = document.getElementById('loginEmail');
        const loginPasswordInput = document.getElementById('loginPassword');
        const rememberMeCheckbox = document.getElementById('remember_me');
        const loginFingerprintField = document.getElementById('loginDeviceFingerprint');
        const registerPanel = document.getElementById('registerPanel');
        const forgotPanel = document.getElementById('forgotPanel');
        const titleEl = document.getElementById('authModalTitle');
        const legalModal = document.getElementById('legalModal');
        const legalModalTitle = document.getElementById('legalModalTitle');
        const legalOpenButtons = Array.from(document.querySelectorAll('[data-open-legal]'));
        const legalCloseButtons = Array.from(document.querySelectorAll('[data-close-legal]'));
        const legalPanels = Array.from(document.querySelectorAll('[data-legal-panel]'));
        const legalDecisionButtons = Array.from(document.querySelectorAll('[data-legal-decision]'));

        if (!modal || !loginPanel || !titleEl) return;

        if (loginFingerprintField && !loginFingerprintField.value) {
            try {
                const storageKey = 'consultation_platform_device_key';
                let deviceKey = window.localStorage.getItem(storageKey);

                if (!deviceKey) {
                    if (window.crypto?.randomUUID) {
                        deviceKey = window.crypto.randomUUID();
                    } else {
                        deviceKey = `${Date.now()}-${Math.random().toString(36).slice(2)}`;
                    }

                    window.localStorage.setItem(storageKey, deviceKey);
                }

                const screenPart = window.screen ? `${window.screen.width}x${window.screen.height}x${window.screen.colorDepth}` : 'unknown-screen';
                const timezonePart = Intl.DateTimeFormat().resolvedOptions().timeZone || 'unknown-timezone';
                const platformPart = navigator.platform || 'unknown-platform';
                loginFingerprintField.value = [deviceKey, navigator.userAgent, platformPart, screenPart, timezonePart].join('|');
            } catch (error) {
                loginFingerprintField.value = navigator.userAgent || '';
            }
        }

        const openButtons = Array.from(document.querySelectorAll('[data-open-auth]'));
        const closeButtons = Array.from(document.querySelectorAll('[data-close-auth]'));
        const switchButtons = Array.from(document.querySelectorAll('[data-switch-auth]'));

        const showPanel = (panel) => {
            const isRegister = panel === 'register' && registerPanel;
            const isForgot   = panel === 'forgot'   && forgotPanel;
            loginPanel.classList.toggle('active', !isRegister && !isForgot);
            if (registerPanel) registerPanel.classList.toggle('active', Boolean(isRegister));
            if (forgotPanel)   forgotPanel.classList.toggle('active',   Boolean(isForgot));
            const authModalCard = modal.querySelector('.auth-modal');
            if (authModalCard) authModalCard.classList.toggle('register-mode', Boolean(isRegister));
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

        openButtons.forEach(b => b.addEventListener('click', () => showPanel(b.getAttribute('data-open-auth') || 'login')));
        switchButtons.forEach(b => b.addEventListener('click', (e) => { e.preventDefault(); showPanel(b.getAttribute('data-switch-auth') || 'login'); }));
        closeButtons.forEach(b => b.addEventListener('click', hideModal));
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.classList.contains('active')) hideModal(); });

        // ── Legal Modal ──
        let activeLegalPanel = null;

        const openLegalPanel = (panelName) => {
            if (!legalModal) return;
            const target = panelName === 'privacy' ? 'privacy' : 'terms';
            activeLegalPanel = target;
            legalPanels.forEach(p => p.classList.toggle('active', p.dataset.legalPanel === target));
            if (legalModalTitle) legalModalTitle.textContent = target === 'privacy' ? 'Privacy Policy' : 'Terms and Conditions';
            legalModal.classList.add('active');
            legalModal.setAttribute('aria-hidden', 'false');
        };
        const closeLegalModal = () => {
            if (!legalModal) return;
            legalModal.classList.remove('active');
            legalModal.setAttribute('aria-hidden', 'true');
        };
        legalOpenButtons.forEach(b => b.addEventListener('click', () => openLegalPanel(b.dataset.openLegal || 'terms')));
        legalCloseButtons.forEach(b => b.addEventListener('click', closeLegalModal));
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && legalModal?.classList.contains('active')) closeLegalModal(); });

        // ── Register Live Validation ──
        const registerForm = document.querySelector('[data-live-validate="welcome-register"]');
        if (registerForm) {
            const touchedFields = new WeakMap();
            const registerSubmitButton = registerForm.querySelector('[data-submit-register]');
            const registerFields = Array.from(registerForm.querySelectorAll('.auth-input[name][data-rule]'));
            const legalCheckboxes = Array.from(registerForm.querySelectorAll('[data-legal-checkbox]'));
            const namePattern = /^(?=.*\p{L})[\p{L}\s'-]+$/u;
            const gmailPattern = /^[^\s@]+@gmail\.com$/i;
            const validYearLevels = new Set(['1st', '2nd', '3rd', '4th']);

            const normalizeWhitespace = (v) => v.replace(/\s+/gu, ' ').trim();
            const normalizeName = (v) => normalizeWhitespace(v);
            const getErrorElement = (input) => input.parentElement?.querySelector('.auth-error') || registerForm.querySelector(`[data-error-for="${input.name}"]`);
            const getSuccessElement = (input) => registerForm.querySelector(`[data-success-for="${input.name}"]`);
            const countVowels = (v) => (v.match(/[aeiouy]/gu) || []).length;
            const longestConsonantRun = (v) => {
                let longest = 0, current = 0;
                Array.from(v).forEach(c => { if (/[aeiouy]/iu.test(c)) { current = 0; return; } current += 1; if (current > longest) longest = current; });
                return longest;
            };
            const evaluateName = (input) => {
                const isOptional = input.dataset.optional === 'true';
                const value = normalizeName(input.value);
                if (!value) return isOptional ? { valid: true, message: '', success: '' } : { valid: false, message: 'Please enter a real name.', success: '' };
                if (!namePattern.test(value)) return { valid: false, message: 'Names should only contain letters, spaces, hyphens, or apostrophes.', success: '' };
                const lettersOnly = value.replace(/[^\p{L}]/gu, '').toLowerCase();
                if (lettersOnly.length < 2) return { valid: false, message: 'Please enter a real name.', success: '' };
                if (lettersOnly.length > 50 || value.length > 60) return { valid: false, message: "This doesn't look like a valid name.", success: '' };
                if (/(\p{L})\1{3,}/u.test(lettersOnly)) return { valid: false, message: 'Please enter a real name.', success: '' };
                if (/(\p{L}{2,4})\1{2,}/u.test(lettersOnly)) return { valid: false, message: 'Please avoid random or meaningless text.', success: '' };
                const vowelCount = countVowels(lettersOnly);
                if (lettersOnly.length >= 4 && vowelCount === 0) return { valid: false, message: "This doesn't look like a valid name.", success: '' };
                if (lettersOnly.length >= 8 && (vowelCount / lettersOnly.length) < 0.23) return { valid: false, message: 'Please avoid random or meaningless text.', success: '' };
                if (lettersOnly.length >= 10 && longestConsonantRun(lettersOnly) >= 5) return { valid: false, message: "This doesn't look like a valid name.", success: '' };
                return { valid: true, message: '', success: '' };
            };
            const evaluateEmail = (input) => {
                const value = normalizeWhitespace(input.value).toLowerCase();
                if (!value) return { valid: false, message: 'Please enter a valid Gmail address.', success: '' };
                if (!gmailPattern.test(value)) return { valid: false, message: 'Please enter a valid Gmail address.', success: '' };
                return { valid: true, message: '', success: '' };
            };
            const evaluatePhoneNumber = (input) => {
                const digits = String(input.value || '').replace(/\D+/g, '');
                if (!digits) return { valid: false, message: 'Please enter your mobile number for SMS reminders.', success: '' };
                if (!/^(09\d{9}|9\d{9}|639\d{9})$/.test(digits)) {
                    return { valid: false, message: 'Enter a valid Philippine mobile number (e.g. 09171234567).', success: '' };
                }
                return { valid: true, message: '', success: '' };
            };
            const evaluateStudentId = (input) => {
                const value = normalizeWhitespace(input.value);
                if (!value) return { valid: false, message: 'Student ID is required.', success: '' };
                if (!/^\d{8}$/.test(value)) return { valid: false, message: 'Student ID must be exactly 8 digits.', success: '' };
                return { valid: true, message: '', success: '' };
            };
            const evaluateYearLevel = (input) => {
                const value = normalizeWhitespace(input.value);
                if (!value) return { valid: false, message: 'Please select your year level.', success: '' };
                if (!validYearLevels.has(value)) return { valid: false, message: 'Please choose a valid year level from the list.', success: '' };
                return { valid: true, message: '', success: '' };
            };
            const evaluatePassword = (input) => {
                const value = input.value;
                if (!value) return { valid: false, message: 'Please create a password.', success: '' };
                if (value.length < 8) return { valid: false, message: 'Use at least 8 characters for your password.', success: '' };
                if (!/[a-z]/.test(value)) return { valid: false, message: 'Add at least one lowercase letter.', success: '' };
                if (!/[A-Z]/.test(value)) return { valid: false, message: 'Add at least one uppercase letter.', success: '' };
                if (!/\d/.test(value)) return { valid: false, message: 'Add at least one number.', success: '' };
                if (!/[^A-Za-z0-9]/.test(value)) return { valid: false, message: 'Add at least one special character.', success: '' };
                return { valid: true, message: '', success: '' };
            };
            const evaluatePasswordConfirmation = (input) => {
                const value = input.value;
                const passwordInput = registerForm.querySelector('[name="password"]');
                if (!value) return { valid: false, message: 'Please confirm your password.', success: '' };
                if (passwordInput && value !== passwordInput.value) return { valid: false, message: 'Passwords do not match yet.', success: '' };
                return { valid: true, message: '', success: '' };
            };
            const evaluateField = (input) => {
                switch (input.dataset.rule) {
                    case 'name': return evaluateName(input);
                    case 'gmail': return evaluateEmail(input);
                    case 'phone_number': return evaluatePhoneNumber(input);
                    case 'student_id': return evaluateStudentId(input);
                    case 'year_level': return evaluateYearLevel(input);
                    case 'password': return evaluatePassword(input);
                    case 'password_confirmation': return evaluatePasswordConfirmation(input);
                    default: return { valid: true, message: '', success: '' };
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
                input.classList.remove('is-valid');
                if (errorElement) errorElement.textContent = result.valid ? '' : result.message;
                if (successElement) successElement.textContent = '';
            };
            const legalConsentsAccepted = () => legalCheckboxes.every(c => c.checked);
            const evaluateFormForSubmit = () => registerFields.every(input => evaluateField(input).valid) && legalConsentsAccepted();
            const updateSubmitState = () => { if (registerSubmitButton) registerSubmitButton.disabled = !evaluateFormForSubmit(); };

            const checkboxForPanel = (panelName) => legalCheckboxes.find(c => c.dataset.legalCheckbox === panelName) || null;

            legalCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('click', (event) => {
                    event.preventDefault();
                    openLegalPanel(checkbox.dataset.legalCheckbox || 'terms');
                });
                checkbox.addEventListener('keydown', (event) => {
                    if (event.key === ' ' || event.key === 'Enter') {
                        event.preventDefault();
                        openLegalPanel(checkbox.dataset.legalCheckbox || 'terms');
                    }
                });
            });

            legalDecisionButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    if (!activeLegalPanel) {
                        closeLegalModal();
                        return;
                    }

                    const checkbox = checkboxForPanel(activeLegalPanel);

                    if (checkbox) {
                        checkbox.checked = button.dataset.legalDecision === 'agree';
                    }

                    updateSubmitState();
                    closeLegalModal();
                });
            });
            registerFields.forEach(input => {
                input.addEventListener('input', () => {
                    touchedFields.set(input, true);
                    if (input.dataset.rule === 'gmail') input.value = input.value.replace(/\s+/gu, '').toLowerCase();
                    if (input.dataset.rule === 'phone_number') input.value = input.value.replace(/[^\d+]/g, '').slice(0, 13);
                    applyFieldState(input, evaluateField(input));
                    if (input.name === 'password') {
                        const confInput = registerForm.querySelector('[name="password_confirmation"]');
                        if (confInput) applyFieldState(confInput, evaluateField(confInput));
                    }
                    updateSubmitState();
                });
                input.addEventListener('blur', () => {
                    touchedFields.set(input, true);
                    applyFieldState(input, evaluateField(input), { force: true });
                    updateSubmitState();
                });
            });
            registerForm.addEventListener('submit', (e) => {
                let firstInvalidField = null;
                registerFields.forEach(input => {
                    touchedFields.set(input, true);
                    const result = evaluateField(input);
                    applyFieldState(input, result, { force: true });
                    if (!result.valid && !firstInvalidField) firstInvalidField = input;
                });
                updateSubmitState();
                const firstMissingConsent = legalCheckboxes.find(c => !c.checked);
                if (firstMissingConsent && !firstInvalidField) firstInvalidField = firstMissingConsent;
                if (firstInvalidField) { e.preventDefault(); firstInvalidField.focus(); }
            });
            updateSubmitState();
        }

        // ── Auto-open modal on page load (from Laravel session/errors) ──
        const forcedAuth       = @json($authPanel ?? request('auth'));
        const flashAuthForm    = @json(session('auth_form'));
        const oldAuthForm      = @json(old('auth_form'));
        const hasRegisterErrors = Boolean(@json($errors->any())) && oldAuthForm === 'register';
        const hasLoginErrors    = Boolean(@json($errors->any())) && oldAuthForm === 'login';
        const hasForgotErrors   = Boolean(@json($errors->any())) && oldAuthForm === 'forgot';

        if (loginForm && loginEmailInput && loginPasswordInput && rememberMeCheckbox) {
            const rememberedEmailKey = 'consultation_platform_remembered_email';
            const rememberEnabledKey = 'consultation_platform_remember_enabled';
            const serverEmail = String(loginForm.dataset.serverEmail || '').trim();
            const rememberedEmail = String(window.localStorage.getItem(rememberedEmailKey) || '').trim();
            const rememberEnabled = window.localStorage.getItem(rememberEnabledKey) === '1';
            const shouldKeepServerEmail = hasLoginErrors || serverEmail !== '';

            if (shouldKeepServerEmail) {
                rememberMeCheckbox.checked = rememberMeCheckbox.checked || rememberEnabled;
            } else if (rememberEnabled && rememberedEmail !== '') {
                loginEmailInput.value = rememberedEmail;
                rememberMeCheckbox.checked = true;
                loginPasswordInput.value = '';
            } else {
                loginEmailInput.value = '';
                loginPasswordInput.value = '';
                rememberMeCheckbox.checked = false;
            }

            loginForm.addEventListener('submit', () => {
                const normalizedEmail = loginEmailInput.value.trim().toLowerCase();

                if (rememberMeCheckbox.checked && normalizedEmail !== '') {
                    window.localStorage.setItem(rememberedEmailKey, normalizedEmail);
                    window.localStorage.setItem(rememberEnabledKey, '1');
                } else {
                    window.localStorage.removeItem(rememberedEmailKey);
                    window.localStorage.removeItem(rememberEnabledKey);
                }
            });
        }

        if (hasRegisterErrors) {
            showPanel('register');
        } else if (hasForgotErrors) {
            showPanel('forgot');
        } else if (hasLoginErrors) {
            showPanel('login');
        } else if (Boolean(@json(session('status'))) && (flashAuthForm === 'forgot' || flashAuthForm === 'login')) {
            showPanel(flashAuthForm);
        } else if (forcedAuth === 'register' || forcedAuth === 'login' || forcedAuth === 'forgot') {
            showPanel(forcedAuth);
        }

        const closeAuthStatus = (status) => {
            if (!status || status.dataset.dismissed === 'true') return;

            status.dataset.dismissed = 'true';
            status.classList.add('is-hiding');

            window.setTimeout(() => {
                status.remove();
            }, 500);
        };

        document.querySelectorAll('[data-auth-status]').forEach((status) => {
            const closeButton = status.querySelector('[data-auth-status-close]');

            if (closeButton) {
                closeButton.addEventListener('click', () => closeAuthStatus(status));
            }

            window.setTimeout(() => {
                closeAuthStatus(status);
            }, 4000);
        });
    })();

    // ── Password toggle ──
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            const input = targetId ? document.getElementById(targetId) : null;
            if (!input) return;
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            button.classList.toggle('is-visible', !showing);
            button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        });
    });

    // ── Scroll fade-in ──
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

    // ── Navbar scroll effect ──
    window.addEventListener('scroll', () => {
        const nb = document.querySelector('.navbar');
        if (nb) nb.style.background = window.scrollY > 50 ? 'rgba(6,14,36,0.97)' : 'rgba(6,14,36,0.85)';
    });
    </script>
</body>
</html>
