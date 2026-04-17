<x-guest-layout>
    <style>
        .verify-stack {
            max-width: 560px;
            margin: 0 auto;
            display: grid;
            gap: 18px;
        }

        .verify-heading {
            margin: 0;
            font-size: 30px;
            font-weight: 800;
            line-height: 1.1;
        }

        .verify-copy {
            margin: 0;
            color: #9cc2d8;
            line-height: 1.7;
        }

        .verify-panel {
            border: 1px solid rgba(125, 211, 252, 0.22);
            border-radius: 18px;
            padding: 18px 20px;
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
            line-height: 1.8;
        }

        .verify-status,
        .verify-error {
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 14px;
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
            font-size: 14px;
            padding: 12px 18px;
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
    </style>

    <div class="verify-stack">
        <div>
            <p class="brand-pill">Step 2 of 2</p>
            <h1 class="verify-heading">Confirm this login from your email</h1>
            <p class="verify-copy">
                We found valid credentials for <strong>{{ $email }}</strong>, but dashboard access will stay blocked until you confirm the one-time verification link we just sent.
            </p>
        </div>

        @if (session('status'))
            <div class="verify-status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="verify-error">{{ $errors->first() }}</div>
        @endif

        <div class="verify-panel">
            <dl class="verify-list">
                <li>Email destination: <strong>{{ $email }}</strong></li>
                <li>Requested device: <strong>{{ $deviceLabel ?: 'Unknown device' }}</strong></li>
                <li>Link expires: <strong>{{ $expiresAt->format('M d, Y h:i A') }}</strong></li>
            </dl>
        </div>

        <div class="verify-panel">
            <p class="verify-copy">After you click the email button, the system will complete the login and send you to your dashboard.</p>
            <div class="verify-actions">
                <form method="POST" action="{{ route('login.verification.resend') }}">
                    @csrf
                    <button
                        type="submit"
                        class="verify-button"
                        id="resendButton"
                        @disabled(! $canResend)
                    >
                        Resend verification email
                    </button>
                </form>
                <a href="{{ route('login') }}" class="verify-link">Back to login</a>
                <span class="verify-timer" id="resendTimer" data-resend-at="{{ $resendAvailableAt->toIso8601String() }}">
                    @if (! $canResend)
                        You can resend in a few seconds.
                    @else
                        You can request another email now.
                    @endif
                </span>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const timer = document.getElementById('resendTimer');
            const button = document.getElementById('resendButton');

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
        })();
    </script>
</x-guest-layout>
