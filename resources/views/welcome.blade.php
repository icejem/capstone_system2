<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'CCS ConsultHub') }}</title>
<link rel="icon" type="image/jpeg" href="{{ asset('cslogo.jpg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}html{scroll-behavior:smooth}body{font-family:'Inter',sans-serif;background:linear-gradient(180deg,#060e24 0%,#09152d 100%);color:#e2e8f0;overflow-x:hidden}body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(59,130,246,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.04) 1px,transparent 1px);background-size:56px 56px;pointer-events:none}a{text-decoration:none}.fade-in{opacity:0;transform:translateY(24px);transition:.6s ease}.fade-in.visible{opacity:1;transform:none}
.navbar{position:fixed;inset:0 0 auto 0;height:74px;padding:0 6%;display:flex;align-items:center;justify-content:space-between;background:rgba(6,14,36,.85);backdrop-filter:blur(18px);border-bottom:1px solid rgba(59,130,246,.14);z-index:50}.brand{display:flex;align-items:center;gap:14px;color:#fff}.brand-badge{width:52px;height:52px;border-radius:14px;overflow:hidden;box-shadow:0 0 18px rgba(37,99,235,.35)}.brand-badge img{width:100%;height:100%;object-fit:cover}.brand strong{display:block;font-size:14px;font-weight:800}.brand span{display:block;font-size:11px;color:#93c5fd;letter-spacing:.08em;text-transform:uppercase}.nav-links,.nav-actions{display:flex;align-items:center}.nav-links{gap:28px}.nav-links a{font-size:13px;font-weight:600;color:#94a3b8}.nav-links a:hover{color:#fff}.nav-actions{gap:10px}
.btn-ghost,.btn-solid,.btn-main,.auth-btn{border:0;border-radius:12px;font-family:inherit;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;justify-content:center}.btn-ghost{padding:10px 18px;background:transparent;border:1px solid rgba(59,130,246,.38);color:#93c5fd}.btn-ghost:hover{background:rgba(59,130,246,.12)}.btn-solid,.auth-btn{padding:11px 22px;background:linear-gradient(135deg,#2563eb,#3b82f6);color:#fff;box-shadow:0 10px 22px rgba(37,99,235,.35)}.btn-solid:hover,.auth-btn:hover{transform:translateY(-2px)}
.hero{min-height:100vh;padding:124px 6% 70px;display:flex;align-items:center}.hero-grid{max-width:1180px;width:100%;margin:0 auto;display:grid;grid-template-columns:1.05fr .95fr;gap:52px;align-items:center}.tag{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:999px;background:rgba(59,130,246,.12);border:1px solid rgba(59,130,246,.24);font-size:12px;font-weight:700;color:#93c5fd;margin-bottom:20px}.tag::before{content:'';width:7px;height:7px;border-radius:50%;background:#22d3ee}.hero h1{font-size:clamp(38px,5vw,60px);line-height:1.05;font-weight:900;color:#fff;letter-spacing:-.04em;margin-bottom:18px}.hero h1 span{background:linear-gradient(135deg,#60a5fa,#818cf8,#22d3ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}.hero p{font-size:17px;line-height:1.75;color:#94a3b8;max-width:540px;margin-bottom:30px}.hero-actions{display:flex;gap:14px;flex-wrap:wrap}.stats{display:flex;gap:24px;flex-wrap:wrap;margin-top:34px}.stat strong{display:block;font-size:28px;color:#fff}.stat span{display:block;margin-top:4px;font-size:11px;color:#94a3b8;letter-spacing:.08em;text-transform:uppercase}
.mockup{position:relative}.badge{position:absolute;right:-14px;top:-14px;padding:12px 16px;border-radius:16px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);box-shadow:0 14px 28px rgba(37,99,235,.4)}.badge strong{display:block;font-size:22px;color:#fff}.badge span{font-size:10px;color:#dbeafe}.window{padding:16px;border-radius:22px;background:linear-gradient(160deg,rgba(14,27,58,.96),rgba(10,22,48,.98));border:1px solid rgba(59,130,246,.2);box-shadow:0 32px 70px rgba(0,0,0,.45)}.bar{display:flex;gap:6px;align-items:center;margin-bottom:14px}.dot{width:10px;height:10px;border-radius:50%}.d1{background:#ff5f57}.d2{background:#febc2e}.d3{background:#28c840}.bar-title{flex:1;text-align:center;font-size:11px;color:#94a3b8}.call{border-radius:16px;overflow:hidden;background:#0b1633}.call-main{height:230px;display:flex;align-items:center;justify-content:center;position:relative;background:linear-gradient(135deg,#1a2a6c,#0d1b4b)}.call-avatar{width:84px;height:84px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#2563eb,#7c3aed);font-size:30px;font-weight:800;color:#fff}.call-name{position:absolute;left:12px;bottom:12px;padding:5px 10px;border-radius:999px;background:rgba(0,0,0,.55);font-size:11px;font-weight:700;color:#fff}.call-self{position:absolute;right:10px;bottom:10px;width:84px;height:64px;border-radius:10px;background:linear-gradient(135deg,#064e3b,#065f46);border:2px solid rgba(255,255,255,.18);display:grid;place-items:center;color:#fff;font-weight:800}.call-live{position:absolute;right:10px;top:10px;padding:4px 10px;border-radius:999px;background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.35);font-size:10px;font-weight:800;color:#fecaca}.call-controls{display:flex;justify-content:center;gap:10px;padding:12px;background:rgba(0,0,0,.3)}.call-btn{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:rgba(255,255,255,.1);color:#fff}.call-btn.end{background:#ef4444}.mini-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px}.mini{padding:10px 12px;border-radius:12px;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.14)}.mini label{display:block;font-size:9px;text-transform:uppercase;letter-spacing:.12em;color:#94a3b8}.mini strong{display:block;margin-top:4px;font-size:12px;color:#fff}
section{padding:88px 6%}.section-head{text-align:center;margin-bottom:46px}.pill{display:inline-flex;padding:6px 14px;border-radius:999px;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#93c5fd;margin-bottom:14px}.section-head h2{font-size:clamp(30px,4vw,44px);line-height:1.12;font-weight:900;color:#fff;letter-spacing:-.03em}.section-head p{max-width:560px;margin:14px auto 0;font-size:16px;line-height:1.7;color:#94a3b8}.grid-3,.grid-4{display:grid;gap:20px}.grid-3{grid-template-columns:repeat(3,1fr)}.grid-4{grid-template-columns:repeat(4,1fr)}.card,.step{padding:28px;border-radius:22px;background:linear-gradient(160deg,rgba(255,255,255,.04),rgba(255,255,255,.01));border:1px solid rgba(255,255,255,.08);transition:.3s}.card:hover,.step:hover{transform:translateY(-6px);border-color:rgba(59,130,246,.28);box-shadow:0 20px 40px rgba(0,0,0,.28)}.icon{width:52px;height:52px;border-radius:14px;display:grid;place-items:center;margin-bottom:18px}.icon.blue{background:rgba(37,99,235,.15);color:#60a5fa}.icon.purple{background:rgba(139,92,246,.15);color:#c4b5fd}.icon.cyan{background:rgba(34,211,238,.1);color:#67e8f9}.icon.green{background:rgba(16,185,129,.1);color:#6ee7b7}.icon.orange{background:rgba(245,158,11,.1);color:#fcd34d}.icon.pink{background:rgba(236,72,153,.1);color:#f9a8d4}.card h3,.step h3{font-size:17px;font-weight:800;color:#fff;margin-bottom:10px}.card p,.step p{font-size:13px;line-height:1.7;color:#94a3b8}.step-num{width:52px;height:52px;margin:0 auto 16px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#1d4ed8,#3b82f6);font-size:20px;font-weight:900;color:#fff}
.cta{background:linear-gradient(180deg,#080f1e,#060e24)}.cta-box{max-width:720px;margin:0 auto;padding:56px 38px;border-radius:28px;text-align:center;background:linear-gradient(135deg,rgba(29,78,216,.15),rgba(99,102,241,.1));border:1px solid rgba(59,130,246,.24);box-shadow:0 0 70px rgba(37,99,235,.12)}.cta-box h2{font-size:clamp(28px,4vw,40px);font-weight:900;color:#fff;line-height:1.14}.cta-box p{margin:16px auto 30px;max-width:560px;font-size:16px;line-height:1.7;color:#94a3b8}.cta-actions{display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
footer{padding:42px 6% 24px;background:#040b1a;border-top:1px solid rgba(255,255,255,.05)}.footer-main{display:grid;grid-template-columns:2fr 1fr 1fr;gap:34px;margin-bottom:28px}.footer-brand{display:flex;gap:12px}.footer-brand .brand-badge{width:44px;height:44px;border-radius:12px;flex-shrink:0}.footer-brand strong{display:block;font-size:14px;font-weight:800;color:#fff}.footer-brand p,.footer-bottom{font-size:12px;color:#94a3b8;line-height:1.7}.footer-links h4{font-size:12px;font-weight:800;color:#fff;letter-spacing:.1em;text-transform:uppercase;margin-bottom:14px}.footer-links a{display:block;margin-bottom:10px;font-size:12px;color:#94a3b8}.footer-links a:hover{color:#93c5fd}.footer-bottom{padding-top:18px;border-top:1px solid rgba(255,255,255,.05);display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap}
.modal-shell{position:fixed;inset:0;display:none;align-items:center;justify-content:center;padding:20px;z-index:200}.modal-shell.active{display:flex}.modal-backdrop{position:absolute;inset:0;background:rgba(2,10,26,.82);backdrop-filter:blur(8px)}.auth-modal{position:relative;width:min(520px,100%);max-height:calc(100vh - 40px);overflow-y:auto;border-radius:22px;border:1px solid rgba(59,130,246,.26);background:linear-gradient(160deg,rgba(15,32,80,.98),rgba(10,22,48,.99));box-shadow:0 30px 60px rgba(0,0,0,.48)}.auth-modal.register-mode{width:min(760px,100%)}.auth-top{padding:26px 28px 18px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between;gap:14px}.auth-top-main{display:flex;align-items:center;gap:14px}.auth-top-main .brand-badge{width:50px;height:50px}.auth-top-main strong{display:block;font-size:17px;font-weight:800;color:#fff}.auth-top-main span{display:block;margin-top:2px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#93c5fd}.auth-close{width:38px;height:38px;border-radius:12px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.04);color:#fff;font-size:20px;cursor:pointer}.auth-body{padding:30px 28px}.auth-status{margin-bottom:18px;padding:10px 12px;border-radius:12px;border:1px solid rgba(34,197,94,.35);background:rgba(34,197,94,.12);color:#bbf7d0;font-size:13px;font-weight:700}.auth-panel{display:none}.auth-panel.active{display:block}.auth-title{font-size:20px;font-weight:900;color:#fff;margin-bottom:8px}.auth-subtitle{font-size:15px;line-height:1.6;color:#94a3b8;margin-bottom:24px}.auth-grid{display:grid;gap:16px}.auth-grid-register{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 18px}.auth-span-2{grid-column:1 / -1}.field{display:grid;gap:8px}.field label{font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#b7cbe7}.field input{width:100%;padding:14px 16px;border-radius:14px;border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.05);color:#fff;font-size:14px;outline:none}.field input::placeholder{color:rgba(148,163,184,.75)}.field input:focus{border-color:rgba(96,165,250,.8);box-shadow:0 0 0 4px rgba(37,99,235,.18)}.auth-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}.auth-check{display:inline-flex;align-items:center;gap:8px;color:#cbd5e1;font-size:13px}.auth-check input{accent-color:#3b82f6}.auth-link{font-size:13px;font-weight:700;color:#60a5fa}.auth-link:hover{text-decoration:underline}.auth-error{font-size:12px;font-weight:600;color:#fecaca}.auth-foot{margin-top:14px;text-align:center;color:#94a3b8;font-size:13px}.auth-copy{margin-top:24px;padding-top:16px;border-top:1px solid rgba(255,255,255,.06);text-align:center;color:#7f90ae;font-size:12px}
@media(max-width:1040px){.hero-grid,.grid-3{grid-template-columns:1fr}.grid-4{grid-template-columns:repeat(2,1fr)}.footer-main{grid-template-columns:1fr 1fr}.mockup{max-width:620px;margin:0 auto}}@media(max-width:760px){.navbar{height:auto;padding:14px 5%;gap:12px;flex-wrap:wrap}.nav-links{display:none}.hero{padding:126px 5% 60px}section{padding:80px 5%}.hero-actions,.stats,.cta-actions{justify-content:center}.footer-main{grid-template-columns:1fr}.auth-top,.auth-body{padding-left:20px;padding-right:20px}.auth-grid-register,.grid-4,.mini-grid{grid-template-columns:1fr}.auth-span-2{grid-column:auto}}@media(max-width:520px){.nav-actions{width:100%;justify-content:flex-end}.brand-copy strong{font-size:12px}.brand-copy span{font-size:10px}}
</style>
</head>
<body>
<nav class="navbar"><a href="{{ route('home') }}" class="brand" aria-label="Home"><span class="brand-badge"><img src="{{ asset('cslogo.jpg') }}" alt="CCS logo"></span><span class="brand-copy"><strong>College of Computer Studies</strong><span>Philippine College of Science and Technology</span></span></a><div class="nav-links"><a href="#features">Features</a><a href="#how">How It Works</a><a href="#contact">Contact</a></div><div class="nav-actions"><button type="button" class="btn-ghost" data-open-auth="login">Log In</button>@if (Route::has('register'))<button type="button" class="btn-solid" data-open-auth="register">Get Started</button>@endif</div></nav>
<section class="hero"><div class="hero-grid"><div class="fade-in"><div class="tag">Philippine College of Science and Technology</div><h1>Online Faculty Student<br><span>Consultation</span><br>with Video Call</h1><p>Bridge the gap between students and faculty. Schedule, connect, and consult in one secure, real-time platform built for the College of Computer Studies.</p><div class="hero-actions">@if (Route::has('register'))<button type="button" class="btn-solid" data-open-auth="register">Start Consultation</button>@endif<a href="#how" class="btn-hero btn-ghost">See How It Works</a></div><div class="stats"><div class="stat"><strong>500+</strong><span>Students Served</span></div><div class="stat"><strong>50+</strong><span>Faculty Members</span></div><div class="stat"><strong>98%</strong><span>Satisfaction Rate</span></div></div></div><div class="mockup fade-in"><div class="badge"><strong>28</strong><span>Sessions This Month</span></div><div class="window"><div class="bar"><span class="dot d1"></span><span class="dot d2"></span><span class="dot d3"></span><span class="bar-title">CCS ConsultHub - Live Session</span></div><div class="call"><div class="call-main"><div class="call-avatar">P</div><div class="call-name">Prof. Marquez - Faculty</div><div class="call-self">J</div><div class="call-live">LIVE</div></div><div class="call-controls"><div class="call-btn">M</div><div class="call-btn">C</div><div class="call-btn end">X</div><div class="call-btn">S</div><div class="call-btn">T</div></div></div><div class="mini-grid"><div class="mini"><label>Topic</label><strong>Academic Performance</strong></div><div class="mini"><label>Duration</label><strong>00:42:18 - REC</strong></div></div></div></div></div></section>
<section class="features" id="features"><div class="section-head"><div class="pill">Platform Features</div><h2>Everything You Need,<br>All in One Place</h2><p>Designed specifically for the College of Computer Studies, connecting faculty and students seamlessly.</p></div><div class="grid-3 fade-in"><div class="card"><div class="icon blue">V</div><h3>HD Video Consultation</h3><p>Crystal-clear, real-time video calls between students and faculty with a browser-based experience.</p></div><div class="card"><div class="icon purple">S</div><h3>Smart Scheduling</h3><p>View faculty availability in real time and request consultation sessions with better coordination.</p></div><div class="card"><div class="icon cyan">C</div><h3>In-Session Chat</h3><p>Stay connected during your consultation with messaging and session context built into the platform.</p></div><div class="card"><div class="icon green">P</div><h3>Secure and Private</h3><p>Protected faculty-student sessions designed for academic support and campus consultation workflows.</p></div><div class="card"><div class="icon orange">R</div><h3>Session Records</h3><p>Review your consultation history, request updates, and keep track of past sessions in one place.</p></div><div class="card"><div class="icon pink">N</div><h3>Smart Notifications</h3><p>Get updated on requests, approvals, schedule changes, and consultation reminders at the right time.</p></div></div></section>
<section class="steps" id="how"><div class="section-head"><div class="pill">Process</div><h2>How It Works</h2><p>Get started in four simple steps with a faculty-student consultation flow built for CCS.</p></div><div class="grid-4 fade-in"><div class="step"><div class="step-num">1</div><h3>Create Account</h3><p>Register with your school details and access a student or faculty experience tailored to your role.</p></div><div class="step"><div class="step-num">2</div><h3>Request Consultation</h3><p>Choose your concern, preferred faculty member, and the time slot that works best for your schedule.</p></div><div class="step"><div class="step-num">3</div><h3>Get Confirmed</h3><p>Receive approval updates, reminders, and instructions directly inside the platform.</p></div><div class="step"><div class="step-num">4</div><h3>Start Video Call</h3><p>Join the session from your browser and communicate live with your faculty member in real time.</p></div></div></section>
<section class="cta" id="contact"><div class="cta-box fade-in"><div class="pill">Ready to Get Started?</div><h2>Connect with Your Faculty<br>Anytime, Anywhere</h2><p>Join the CCS consultation platform and access a smoother, more professional way to schedule and attend academic sessions.</p><div class="cta-actions">@if (Route::has('register'))<button type="button" class="btn-solid" data-open-auth="register">Sign Up as Student</button>@endif<button type="button" class="btn-ghost" data-open-auth="login">Log In to Continue</button></div></div></section>
<footer><div class="footer-main"><div class="footer-brand"><span class="brand-badge"><img src="{{ asset('cslogo.jpg') }}" alt="CCS logo"></span><div><strong>CCS ConsultHub</strong><p>Online faculty-student consultation platform with video call for the Philippine College of Science and Technology.</p></div></div><div class="footer-links"><h4>Platform</h4><a href="#features">Features</a><a href="#how">How It Works</a><a href="#contact">Contact</a></div><div class="footer-links"><h4>Access</h4><a href="#" data-open-auth="login">Login</a>@if (Route::has('register'))<a href="#" data-open-auth="register">Register</a>@endif<a href="{{ route('password.request') }}">Forgot Password</a></div></div><div class="footer-bottom"><span>&copy; 2026 CCS ConsultHub - Philippine College of Science and Technology. All rights reserved.</span><span>Built for the College of Computer Studies</span></div></footer>
<div class="modal-shell" id="authModal" aria-hidden="true">
    <div class="modal-backdrop" data-close-auth></div>
    <div class="auth-modal" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">
        <div class="auth-top">
            <div class="auth-top-main">
                <span class="brand-badge"><img src="{{ asset('cslogo.jpg') }}" alt="CCS logo"></span>
                <span><strong>College of Computer Studies</strong><span>Philippine College of Science and Technology</span></span>
            </div>
            <button type="button" class="auth-close" data-close-auth aria-label="Close">&times;</button>
        </div>
        <div class="auth-body">
            @if (session('status'))
                <div class="auth-status">{{ session('status') }}</div>
            @endif

            <section class="auth-panel" id="loginPanel">
                <h2 class="auth-title" id="authModalTitle">Welcome Back</h2>
                <p class="auth-subtitle">Sign in to your account to continue your consultation journey.</p>
                <form method="POST" action="{{ route('login') }}" class="auth-grid">
                    @csrf
                    <input type="hidden" name="auth_form" value="login">
                    <div class="field">
                        <label for="loginEmail">Email Address</label>
                        <input id="loginEmail" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@example.com">
                        @error('email')<div class="auth-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="field">
                        <label for="loginPassword">Password</label>
                        <input id="loginPassword" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
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
                        <div class="auth-foot">No account yet? <a href="#" class="auth-link" data-switch-auth="register">Register</a></div>
                    @endif
                </form>
                <div class="auth-copy">&copy; 2026 PCST - College of Computer Studies. All rights reserved.</div>
            </section>

            @if (Route::has('register'))
                <section class="auth-panel" id="registerPanel">
                    <h2 class="auth-title">Create Account</h2>
                    <p class="auth-subtitle">Set up your student account first, then log in to continue to the platform.</p>
                    <form method="POST" action="{{ route('register') }}" class="auth-grid-register">
                        @csrf
                        <input type="hidden" name="auth_form" value="register">
                        <div class="field">
                            <label for="registerFirstName">First Name</label>
                            <input id="registerFirstName" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" placeholder="First name">
                            @error('first_name')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="registerLastName">Last Name</label>
                            <input id="registerLastName" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" placeholder="Last name">
                            @error('last_name')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field auth-span-2">
                            <label for="registerMiddleName">Middle Name (Optional)</label>
                            <input id="registerMiddleName" type="text" name="middle_name" value="{{ old('middle_name') }}" autocomplete="additional-name" placeholder="Middle name">
                            @error('middle_name')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="registerEmail">Email</label>
                            <input id="registerEmail" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@example.com">
                            @error('email')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="registerStudentId">Student ID</label>
                            <input id="registerStudentId" type="text" name="student_id" value="{{ old('student_id') }}" placeholder="Enter 8-digit Student ID" inputmode="numeric" pattern="\d{8}" minlength="8" maxlength="8" required>
                            @error('student_id')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="registerPassword">Password</label>
                            <input id="registerPassword" type="password" name="password" required autocomplete="new-password" placeholder="Create password">
                            @error('password')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label for="registerPasswordConfirmation">Confirm Password</label>
                            <input id="registerPasswordConfirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                            @error('password_confirmation')<div class="auth-error">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="auth-btn auth-span-2">Create Account</button>
                        <div class="auth-foot auth-span-2">Already registered? <a href="#" class="auth-link" data-switch-auth="login">Login</a></div>
                    </form>
                </section>
            @endif

            <section class="auth-panel" id="forgotPanel">
                <h2 class="auth-title">Reset Password</h2>
                <p class="auth-subtitle">Enter your email address and we will send your password reset link.</p>
                <form method="POST" action="{{ route('password.email') }}" class="auth-grid">
                    @csrf
                    <input type="hidden" name="auth_form" value="forgot">
                    <div class="field">
                        <label for="forgotEmail">Email</label>
                        <input id="forgotEmail" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com">
                        @error('email')<div class="auth-error">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="auth-btn">Send Reset Link</button>
                    <div class="auth-foot">Back to <a href="#" class="auth-link" data-switch-auth="login">Login</a></div>
                </form>
            </section>
        </div>
    </div>
</div>
<script>
(function(){const observer=new IntersectionObserver((entries)=>{entries.forEach((entry)=>{if(entry.isIntersecting)entry.target.classList.add('visible')})},{threshold:.1});document.querySelectorAll('.fade-in').forEach((element)=>observer.observe(element));const navbar=document.querySelector('.navbar');window.addEventListener('scroll',()=>{if(navbar)navbar.style.background=window.scrollY>50?'rgba(6,14,36,.97)':'rgba(6,14,36,.85)'});})();
(function(){const modal=document.getElementById('authModal');const loginPanel=document.getElementById('loginPanel');const registerPanel=document.getElementById('registerPanel');const forgotPanel=document.getElementById('forgotPanel');const titleEl=document.getElementById('authModalTitle');if(!modal||!loginPanel||!titleEl)return;const showPanel=(panel)=>{const isRegister=panel==='register'&&registerPanel;const isForgot=panel==='forgot'&&forgotPanel;loginPanel.classList.toggle('active',!isRegister&&!isForgot);if(registerPanel)registerPanel.classList.toggle('active',Boolean(isRegister));if(forgotPanel)forgotPanel.classList.toggle('active',Boolean(isForgot));const card=modal.querySelector('.auth-modal');if(card)card.classList.toggle('register-mode',Boolean(isRegister));titleEl.textContent=isRegister?'Create Account':(isForgot?'Reset Password':'Welcome Back');modal.classList.add('active');modal.setAttribute('aria-hidden','false');const activePanel=isRegister?registerPanel:(isForgot?forgotPanel:loginPanel);const firstInput=activePanel?activePanel.querySelector('input'):null;if(firstInput)firstInput.focus()};const hideModal=()=>{modal.classList.remove('active');modal.setAttribute('aria-hidden','true')};document.querySelectorAll('[data-open-auth]').forEach((button)=>{button.addEventListener('click',(event)=>{event.preventDefault();showPanel(button.getAttribute('data-open-auth')||'login')})});document.querySelectorAll('[data-switch-auth]').forEach((button)=>{button.addEventListener('click',(event)=>{event.preventDefault();showPanel(button.getAttribute('data-switch-auth')||'login')})});document.querySelectorAll('[data-close-auth]').forEach((button)=>button.addEventListener('click',hideModal));document.addEventListener('keydown',(event)=>{if(event.key==='Escape'&&modal.classList.contains('active'))hideModal()});const forcedAuth=@json($authPanel ?? request('auth'));const flashAuthForm=@json(session('auth_form'));const oldAuthForm=@json(old('auth_form'));const hasRegisterErrors=Boolean(@json($errors->any()))&&oldAuthForm==='register';const hasLoginErrors=Boolean(@json($errors->any()))&&oldAuthForm==='login';const hasForgotErrors=Boolean(@json($errors->any()))&&oldAuthForm==='forgot';if(hasRegisterErrors){showPanel('register')}else if(hasForgotErrors){showPanel('forgot')}else if(hasLoginErrors||Boolean(@json(session('status')))){showPanel(flashAuthForm==='forgot'?'forgot':'login')}else if(forcedAuth==='register'||forcedAuth==='login'||forcedAuth==='forgot'){showPanel(forcedAuth)}})();
</script>
</body>
</html>
