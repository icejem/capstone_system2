@section('hide_navigation', '1')

<x-app-layout>
    @php
        $userType = auth()->user()?->user_type ?? 'student';
        $dashboardRoute = match ($userType) {
            'admin' => route('admin.dashboard'),
            'instructor' => route('instructor.dashboard'),
            default => route('student.dashboard'),
        };
    @endphp

    <style>
        .min-h-screen.bg-gray-100 {
            background: #07122b !important;
        }

        .profile-page {
            --line: rgba(120, 206, 255, 0.34);
            --panel-bg: rgba(6, 23, 52, 0.82);
            --field-bg: rgba(7, 24, 51, 0.78);
            --label: #b5d7ea;
            --text: #eaf8ff;
            --muted: #99c1d8;
            --accent: #0fd1ff;
            --accent-2: #2a7fff;
            min-height: 100dvh;
            background:
                radial-gradient(900px 420px at -8% -10%, rgba(15, 209, 255, 0.24) 0%, transparent 62%),
                radial-gradient(780px 480px at 115% 110%, rgba(42, 127, 255, 0.22) 0%, transparent 65%),
                linear-gradient(130deg, #07122b, #0b1e40);
            padding: 12px 16px 16px;
        }

        .profile-shell {
            max-width: 1220px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            gap: 12px;
            align-items: start;
        }

        .profile-rail {
            position: sticky;
            top: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: rgba(4, 18, 44, 0.86);
            padding: 10px 8px;
            box-shadow: 0 10px 30px rgba(2, 8, 21, 0.45);
        }

        .profile-rail-link,
        .profile-rail-btn {
            width: 100%;
            height: 40px;
            border-radius: 10px;
            border: 1px solid rgba(121, 211, 255, 0.35);
            background: rgba(10, 39, 79, 0.55);
            color: #d5eeff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 14px;
            transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
        }

        .profile-rail-link:hover,
        .profile-rail-btn:hover {
            transform: translateY(-1px);
            background: rgba(12, 53, 106, 0.72);
            box-shadow: 0 8px 18px rgba(2, 18, 40, 0.35);
        }

        .profile-rail-btn {
            cursor: pointer;
            margin-top: 2px;
        }

        .profile-content {
            min-width: 0;
        }

        .profile-grid {
            display: grid;
            gap: 12px;
        }

        .profile-card {
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: var(--panel-bg);
            box-shadow: 0 14px 34px rgba(1, 8, 21, 0.5);
            padding: 14px 16px;
        }

        .profile-card::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 95% 5%, rgba(15, 209, 255, 0.12), transparent 36%);
        }

        .profile-section > * {
            position: relative;
            z-index: 1;
        }

        .profile-section header {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(135, 208, 255, 0.2);
        }

        .profile-page h2 { color: var(--text) !important; font-weight: 800; }
        .profile-page p { color: var(--muted) !important; }
        .profile-page label { color: var(--label) !important; font-weight: 700; }
        .profile-page .mt-6 { margin-top: 10px !important; }
        .profile-page .space-y-6 > :not([hidden]) ~ :not([hidden]) { margin-top: 10px !important; }

        .profile-page input[type="text"],
        .profile-page input[type="email"],
        .profile-page input[type="password"],
        .profile-page input[type="file"],
        .profile-page select,
        .profile-page textarea {
            border: 1px solid rgba(117, 203, 255, 0.35) !important;
            border-radius: 11px !important;
            background: var(--field-bg) !important;
            color: var(--text) !important;
        }

        .profile-page input[type="file"] {
            padding: 7px 10px !important;
        }

        .profile-page input:focus,
        .profile-page select:focus,
        .profile-page textarea:focus {
            border-color: #33cfff !important;
            box-shadow: 0 0 0 4px rgba(51, 207, 255, 0.2) !important;
            outline: none !important;
        }

        .profile-page .profile-primary-btn {
            background: linear-gradient(135deg, var(--accent), var(--accent-2)) !important;
            border: 0 !important;
            color: #f4fdff !important;
            border-radius: 11px !important;
            font-weight: 800 !important;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 9px 14px !important;
        }

        .profile-page .profile-secondary-btn {
            border-radius: 11px !important;
            border: 1px solid rgba(121, 211, 255, 0.35) !important;
            background: rgba(10, 39, 79, 0.55) !important;
            color: #d8f2ff !important;
        }

        .profile-page .profile-danger-btn {
            border-radius: 11px !important;
            background: rgba(239, 68, 68, 0.16) !important;
            color: #fecaca !important;
            border: 1px solid rgba(248, 113, 113, 0.42) !important;
        }

        .profile-page .text-green-600,
        .profile-page .text-gray-600 {
            color: #9ec6db !important;
        }

        @media (min-width: 980px) {
            .profile-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .profile-card.full {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 860px) {
            .profile-page {
                padding: 10px 10px 14px;
            }
            .profile-shell {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            .profile-rail {
                position: static;
                flex-direction: row;
                justify-content: center;
            }
        }
    </style>

    <div class="profile-page">
        <div class="profile-shell">
            <aside class="profile-rail" aria-label="Profile quick actions">
                <a href="{{ $dashboardRoute }}" class="profile-rail-link" title="Dashboard" aria-label="Dashboard">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a href="#profileInfoCard" class="profile-rail-link" title="Profile Info" aria-label="Profile Info">
                    <i class="fa-solid fa-user"></i>
                </a>
                <a href="#passwordCard" class="profile-rail-link" title="Password" aria-label="Password">
                    <i class="fa-solid fa-key"></i>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="profile-rail-btn" title="Logout" aria-label="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </aside>

            <div class="profile-content">
                <div class="profile-grid">
                    <div class="profile-card profile-section" id="profileInfoCard">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="profile-card profile-section" id="passwordCard">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
