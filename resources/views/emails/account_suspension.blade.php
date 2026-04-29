<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspension Notice</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#111827;">
    <div style="max-width:640px;margin:20px auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
        <div style="background:#991b1b;color:#ffffff;padding:16px 20px;">
            <h1 style="margin:0;font-size:20px;">Account Suspension Notice</h1>
        </div>
        <div style="padding:20px;">
            <p style="margin:0 0 12px;">Hello {{ $user->name }},</p>
            <p style="margin:0 0 12px;">
                Your account has been temporarily suspended by the system administrator.
            </p>
            @if(!empty($remainingLabel))
                <p style="margin:0 0 12px;">
                    Suspension period: <strong>{{ $remainingLabel }}</strong>
                </p>
            @endif
            @if($expiresAt)
                <p style="margin:0 0 12px;">
                    Reactivation date: <strong>{{ $expiresAt->copy()->setTimezone('Asia/Manila')->format('F d, Y h:i A') }} (Asia/Manila)</strong>
                </p>
            @endif
            <p style="margin:0 0 8px;">Reason provided by admin:</p>
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-left:4px solid #991b1b;padding:12px 14px;margin:0 0 14px;">
                {{ $reason }}
            </div>
            <p style="margin:0;">
                If you believe this was applied in error, please contact the administrator.
            </p>
        </div>
    </div>
</body>
</html>

