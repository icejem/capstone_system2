<x-guest-layout>
    <style>
        .approval-wrap {
            max-width: 400px;
            margin: 0 auto;
        }

        .approval-card {
            border: 1px solid rgba(125, 211, 252, 0.22);
            border-radius: 18px;
            padding: 20px;
            background: linear-gradient(160deg, rgba(6, 26, 56, 0.74), rgba(6, 20, 44, 0.82));
            box-shadow: inset 0 0 0 1px rgba(125, 211, 252, 0.06);
            display: grid;
            gap: 12px;
        }

        .approval-badge {
            width: max-content;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .approval-badge.is-approved {
            background: rgba(8, 145, 178, 0.18);
            border: 1px solid rgba(34, 211, 238, 0.3);
            color: #d8fbff;
        }

        .approval-badge.is-denied,
        .approval-badge.is-invalid {
            background: rgba(127, 29, 29, 0.18);
            border: 1px solid rgba(248, 113, 113, 0.3);
            color: #fee2e2;
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

    </style>

    <div class="approval-wrap">
        <div class="approval-card">
            <div class="approval-badge {{ $state === 'approved' ? 'is-approved' : 'is-'.$state }}">
                {{ $state === 'approved' ? 'Approved' : ($state === 'denied' ? 'Denied' : 'Unavailable') }}
            </div>
            <h1 class="approval-title">{{ $title }}</h1>
            <p class="approval-copy">{{ $message }}</p>
        </div>
    </div>
</x-guest-layout>
