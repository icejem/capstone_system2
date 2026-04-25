<x-guest-layout>
    <style>
        :root {
            --verify-bg: #2f3337;
            --verify-panel: #2f3337;
            --verify-border: rgba(255, 255, 255, 0.18);
            --verify-text: #f5f7fb;
            --verify-muted: #c2c7d0;
            --verify-subtle: #9ea4af;
            --verify-danger: #f28b82;
            --verify-success: #81c995;
            --verify-primary: #8ab4f8;
        }

        body {
            background: var(--verify-bg) !important;
        }

        .verify-shell {
            min-height: 100vh;
            width: 100%;
            background: linear-gradient(180deg, #31353a 0%, #2c3034 100%);
            color: var(--verify-text);
            padding: 28px 18px 44px;
        }

        .verify-prompt {
            width: min(100%, 720px);
            margin: 0 auto;
        }

        .verify-status,
        .verify-error {
            border-radius: 16px;
            padding: 12px 14px;
            margin-bottom: 18px;
            font-size: 14px;
            line-height: 1.5;
        }

        .verify-status {
            background: rgba(138, 180, 248, 0.12);
            border: 1px solid rgba(138, 180, 248, 0.2);
            color: #d8e7ff;
        }

        .verify-error {
            background: rgba(242, 139, 130, 0.12);
            border: 1px solid rgba(242, 139, 130, 0.24);
            color: #ffe5e1;
        }

        .verify-heading {
            margin: 56px 0 26px;
            font-size: clamp(34px, 5vw, 62px);
            line-height: 1.06;
            font-weight: 500;
            letter-spacing: -0.03em;
            color: var(--verify-text);
        }

        .verify-account {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 54px;
        }

        .verify-avatar {
            width: 56px;
            height: 56px;
            border-radius: 999px;
            background: linear-gradient(135deg, #5f6368 0%, #3c4043 100%);
            border: 1px solid rgba(255, 255, 255, 0.14);
            display: grid;
            place-items: center;
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        .verify-email {
            font-size: clamp(24px, 4vw, 34px);
            font-weight: 500;
            color: var(--verify-text);
            line-height: 1.15;
            overflow-wrap: anywhere;
        }

        .verify-details {
            display: grid;
            gap: 38px;
            margin-bottom: 76px;
        }

        .verify-detail-label {
            margin: 0 0 8px;
            font-size: clamp(22px, 3.2vw, 30px);
            font-weight: 700;
            color: var(--verify-text);
        }

        .verify-detail-value {
            margin: 0;
            font-size: clamp(24px, 3.6vw, 32px);
            font-weight: 400;
            line-height: 1.28;
            color: var(--verify-muted);
        }

        .verify-meta {
            margin-top: 10px;
            font-size: 14px;
            color: var(--verify-subtle);
            line-height: 1.6;
        }

        .verify-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .verify-action-form {
            margin: 0;
        }

        .verify-button {
            width: 100%;
            min-height: 74px;
            border-radius: 16px;
            border: 1px solid var(--verify-border);
            background: rgba(47, 51, 55, 0.88);
            color: var(--verify-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            padding: 18px 22px;
            font-size: clamp(20px, 3vw, 24px);
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.18s ease, border-color 0.18s ease, background-color 0.18s ease;
        }

        .verify-button:hover {
            transform: translateY(-1px);
            border-color: rgba(255, 255, 255, 0.28);
        }

        .verify-button--deny .verify-button-icon {
            color: var(--verify-danger);
        }

        .verify-button--approve .verify-button-icon {
            color: var(--verify-success);
        }

        .verify-button-icon {
            font-size: 28px;
            line-height: 1;
        }

        @media (max-width: 640px) {
            .verify-shell {
                padding: 22px 20px 36px;
            }

            .verify-heading {
                margin-top: 34px;
                margin-bottom: 20px;
            }

            .verify-account {
                gap: 14px;
                margin-bottom: 44px;
            }

            .verify-avatar {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .verify-details {
                gap: 30px;
                margin-bottom: 56px;
            }

            .verify-actions {
                gap: 14px;
            }

            .verify-button {
                min-height: 66px;
                padding: 14px 16px;
                font-size: 16px;
                gap: 10px;
            }

            .verify-button-icon {
                font-size: 22px;
            }
        }

        @media (max-width: 460px) {
            .verify-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @php
        $accountInitial = strtoupper(substr((string) $email, 0, 1));
    @endphp

    <div class="verify-shell">
        <div class="verify-prompt">
            @if (session('status'))
                <div class="verify-status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="verify-error">{{ $errors->first() }}</div>
            @endif

            <h1 class="verify-heading">Are you trying to sign in?</h1>

            <div class="verify-account">
                <div class="verify-avatar">{{ $accountInitial }}</div>
                <div class="verify-email">{{ $email }}</div>
            </div>

            <div class="verify-details">
                <div>
                    <p class="verify-detail-label">Device</p>
                    <p class="verify-detail-value">{{ $deviceName ?: ($deviceLabel ?: 'Unknown device') }}</p>
                    @if ($deviceLabel)
                        <p class="verify-meta">{{ $deviceLabel }}</p>
                    @endif
                </div>

                <div>
                    <p class="verify-detail-label">Near</p>
                    <p class="verify-detail-value">{{ $locationLabel ?: 'Unknown location' }}</p>
                </div>

                <div>
                    <p class="verify-detail-label">Time</p>
                    <p class="verify-detail-value">{{ $timeLabel ?: 'Just now' }}</p>
                </div>
            </div>

            <div class="verify-actions">
                <form method="POST" action="{{ route('login.verification.deny.prompt') }}" class="verify-action-form">
                    @csrf
                    <button type="submit" class="verify-button verify-button--deny">
                        <span class="verify-button-icon">&#10005;</span>
                        <span>No, don't allow</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('login.verification.approve') }}" class="verify-action-form">
                    @csrf
                    <button type="submit" class="verify-button verify-button--approve">
                        <span class="verify-button-icon">&#10003;</span>
                        <span>Yes, it's me</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
