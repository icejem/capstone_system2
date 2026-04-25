<x-guest-layout>
    <style>
        .approval-wrap {
            max-width: 460px;
            margin: 0 auto;
        }

        .approval-card {
            border: 1px solid rgba(125, 211, 252, 0.22);
            border-radius: 18px;
            padding: 20px;
            background: linear-gradient(160deg, rgba(6, 26, 56, 0.74), rgba(6, 20, 44, 0.82));
            box-shadow: inset 0 0 0 1px rgba(125, 211, 252, 0.06);
            display: grid;
            gap: 14px;
        }

        .approval-badge {
            width: max-content;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            background: rgba(8, 145, 178, 0.18);
            border: 1px solid rgba(34, 211, 238, 0.3);
            color: #d8fbff;
        }

        .approval-title {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.12;
            color: #f3fbff;
        }

        .approval-copy {
            margin: 0;
            color: #9cc2d8;
            font-size: 13px;
            line-height: 1.6;
        }

        .approval-details {
            display: grid;
            gap: 8px;
            border: 1px solid rgba(125, 211, 252, 0.18);
            border-radius: 14px;
            padding: 14px;
            background: rgba(5, 16, 36, 0.42);
        }

        .approval-detail {
            color: #d7efff;
            font-size: 13px;
            line-height: 1.5;
        }

        .approval-detail strong {
            color: #f3fbff;
        }

        .approval-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .approval-btn {
            border: 0;
            border-radius: 12px;
            padding: 11px 15px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .approval-btn.confirm {
            background: linear-gradient(120deg, rgba(8, 145, 178, 0.78), rgba(37, 99, 235, 0.82));
            color: #effbff;
        }

        .approval-btn.cancel {
            background: rgba(127, 29, 29, 0.16);
            border: 1px solid rgba(248, 113, 113, 0.3);
            color: #fee2e2;
        }
    </style>

    <div class="approval-wrap">
        <div class="approval-card">
            <div class="approval-badge">Confirm</div>
            <h1 class="approval-title">Are you sure you want to approve this login?</h1>
            <p class="approval-copy">Please review the details below before allowing access to the original browser session.</p>

            <div class="approval-details">
                <div class="approval-detail"><strong>Device:</strong> {{ $deviceLabel ?: 'Unknown device' }}</div>
                <div class="approval-detail"><strong>Network/IP:</strong> {{ $ipAddress ?: 'Unavailable' }}</div>
                <div class="approval-detail"><strong>Attempted at:</strong> {{ $attemptedAt?->format('M d, Y h:i A') ?: 'Unavailable' }}</div>
            </div>

            <div class="approval-actions">
                <form method="POST" action="{{ route('login.verification.confirm') }}">
                    @csrf
                    <input type="hidden" name="verification" value="{{ $verification->id }}">
                    <input type="hidden" name="payload" value="{{ $payload }}">
                    <button type="submit" class="approval-btn confirm">Yes, approve login</button>
                </form>
                <a href="{{ route('login') }}" class="approval-btn cancel">Cancel</a>
            </div>
        </div>
    </div>
</x-guest-layout>
