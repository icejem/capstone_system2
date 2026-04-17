<x-guest-layout>
    <style>
        .verify-stack {
            max-width: 420px;
            margin: 0 auto;
            display: grid;
            gap: 12px;
        }

        .verify-card {
            border: 1px solid rgba(125, 211, 252, 0.22);
            border-radius: 18px;
            padding: 18px;
            background: linear-gradient(160deg, rgba(6, 26, 56, 0.74), rgba(6, 20, 44, 0.82));
            box-shadow: inset 0 0 0 1px rgba(125, 211, 252, 0.06);
        }

        .verify-heading {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.12;
        }

        .verify-copy {
            margin: 0;
            color: #9cc2d8;
            font-size: 13px;
            line-height: 1.6;
        }

        .verify-panel {
            border: 1px solid rgba(125, 211, 252, 0.22);
            border-radius: 14px;
            padding: 14px 15px;
            background: rgba(6, 26, 56, 0.66);
            box-shadow: inset 0 0 0 1px rgba(125, 211, 252, 0.08);
        }

        .verify-panel strong {
            color: #ecfeff;
        }

        .verify-list {
            margin: 0;
            padding-left: 18px;
            color: #c9e7f7;
            font-size: 12px;
            line-height: 1.7;
        }

        .verify-status,
        .verify-error {
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 13px;
        }

        .verify-status {
            background: rgba(14, 116, 144, 0.2);
            border: 1px solid rgba(34, 211, 238, 0.34);
            color: #d8fbff;
        }

        .verify-error {
            background: rgba(127, 29, 29, 0.22);
            border: 1px solid rgba(248, 113, 113, 0.38);
            color: #fee2e2;
        }

        .verify-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .verify-button,
        .verify-link {
            border-radius: 12px;
            font-weight: 800;
            font-size: 13px;
            padding: 10px 14px;
            text-decoration: none;
        }

        .verify-button {
            border: 1px solid rgba(56, 189, 248, 0.45);
            background: linear-gradient(120deg, rgba(8, 145, 178, 0.7), rgba(37, 99, 235, 0.72));
            color: #effbff;
            cursor: pointer;
        }

        .verify-button[disabled] {
            cursor: not-allowed;
            opacity: 0.58;
        }

        .verify-link {
            color: #7dd3fc;
            border: 1px solid rgba(125, 211, 252, 0.22);
            background: rgba(5, 16, 36, 0.58);
        }

        .verify-timer {
            color: #9cc2d8;
            font-size: 13px;
        }

        .verify-live {
            color: #67e8f9;
            font-size: 12px;
            font-weight: 700;
        }

        .verify-kicker {
            width: max-content;
            margin: 0 0 8px;
            padding: 5px 9px;
            border-radius: 999px;
            border: 1px solid rgba(125, 211, 252, 0.26);
            background: rgba(8, 36, 73, 0.56);
            color: #d6f5ff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
    </style>

    <div class="verify-stack">
        <div class="verify-card">
            <p class="verify-kicker">Step 2 of 2</p>
            <h1 class="verify-heading">Confirm this login from your email</h1>
            <p class="verify-copy">
                We found valid credentials for <strong>{{ $email }}</strong>, but dashboard access will stay blocked until you confirm the one-time verification link we just sent.
            </p>

            @if (session('status'))
                <div class="verify-status" style="margin-top: 12px;">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="verify-error" style="margin-top: 12px;">{{ $errors->first() }}</div>
            @endif

            <div class="verify-panel" style="margin-top: 12px;">
                <ul class="verify-list">
                    <li>Email: <strong>{{ $email }}</strong></li>
                    <li>Device: <strong>{{ $deviceLabel ?: 'Unknown device' }}</strong></li>
                    <li>Expires: <strong>{{ $expiresAt->format('M d, Y h:i A') }}</strong></li>
                </ul>
            </div>

            <div class="verify-panel" style="margin-top: 12px;">
                <p class="verify-copy">Tap YES in Gmail, then this original browser will continue automatically.</p>
                <div class="verify-actions" style="margin-top: 10px;">
                    <form method="POST" action="{{ route('login.verification.resend') }}">
                        @csrf
                        <button
                            type="submit"
                            class="verify-button"
                            id="resendButton"
                            @disabled(! $canResend)
                        >
                            Resend Email
                        </button>
                    </form>
                    <a href="{{ route('login') }}" class="verify-link">Back</a>
                </div>
                <p class="verify-timer" id="resendTimer" data-resend-at="{{ $resendAvailableAt->toIso8601String() }}" style="margin: 10px 0 0;">
                    @if (! $canResend)
                        You can resend in a few seconds.
                    @else
                        You can request another email now.
                    @endif
                </p>
                <p class="verify-live" id="verifyLiveStatus" style="margin: 10px 0 0;">Listening for approval from your email...</p>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const timer = document.getElementById('resendTimer');
            const button = document.getElementById('resendButton');
            const liveStatus = document.getElementById('verifyLiveStatus');

            if (!timer || !button) {
                return;
            }

            const targetAt = Date.parse(timer.dataset.resendAt || '');

            if (Number.isNaN(targetAt)) {
                return;
            }

            const tick = () => {
                const remaining = Math.max(0, Math.ceil((targetAt - Date.now()) / 1000));

                if (remaining <= 0) {
                    button.disabled = false;
                    timer.textContent = 'You can request another email now.';
                    return;
                }

                button.disabled = true;
                timer.textContent = `You can resend in ${remaining}s.`;
                window.setTimeout(tick, 1000);
            };

            tick();

            let polling = true;

            const pollStatus = async () => {
                if (!polling) {
                    return;
                }

                try {
                    const response = await window.fetch(@json(route('login.verification.status')), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error('Status check failed.');
                    }

                    const data = await response.json();

                    if (data.status === 'approved' && data.complete_url) {
                        polling = false;
                        if (liveStatus) {
                            liveStatus.textContent = 'Approval detected. Opening your dashboard...';
                        }
                        window.location.assign(data.complete_url);
                        return;
                    }

                    if ((data.status === 'expired' || data.status === 'missing') && data.redirect) {
                        polling = false;
                        if (liveStatus) {
                            liveStatus.textContent = 'This login request is no longer active. Redirecting...';
                        }
                        window.location.assign(data.redirect);
                        return;
                    }

                    if (data.status === 'denied' && data.redirect) {
                        polling = false;
                        if (liveStatus) {
                            liveStatus.textContent = 'This login request was denied. Redirecting...';
                        }
                        window.location.assign(data.redirect);
                        return;
                    }

                    if (data.status === 'completed' && data.redirect) {
                        polling = false;
                        window.location.assign(data.redirect);
                        return;
                    }

                    window.setTimeout(pollStatus, 2500);
                } catch (error) {
                    if (liveStatus) {
                        liveStatus.textContent = 'Still waiting for approval...';
                    }
                    window.setTimeout(pollStatus, 4000);
                }
            };

            pollStatus();
        })();
    </script>
</x-guest-layout>
