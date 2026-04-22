<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor is Calling</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .content {
            padding: 30px 20px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }

        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .alert-box strong {
            color: #856404;
        }

        .details-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 8px;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: bold;
            color: #667eea;
            min-width: 120px;
        }

        .detail-value {
            text-align: right;
            color: #555;
        }

        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 15px;
            text-align: center;
        }

        .action-button:hover {
            opacity: 0.9;
        }

        .instructions {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }

        .instructions strong {
            color: #1565c0;
        }

        .footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .footer a {
            color: #667eea;
            text-decoration: none;
        }

        .attempt-badge {
            display: inline-block;
            background-color: #ffc107;
            color: #000;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .security-note {
            background-color: #f0f0f0;
            border-left: 4px solid #999;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    @php
        $formatTime = function ($time) {
            $value = trim((string) $time);
            if ($value === '') {
                return '--';
            }

            try {
                return \Illuminate\Support\Carbon::createFromFormat('H:i:s', strlen($value) === 5 ? $value . ':00' : $value, 'Asia/Manila')
                    ->setTimezone('Asia/Manila')
                    ->format('g:i A');
            } catch (\Throwable $e) {
                return $value;
            }
        };
    @endphp
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>📞 Session Started</h1>
            <p style="margin: 10px 0 0 0;">Your instructor is calling for your consultation</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hi there,
            </div>

            <p style="margin: 15px 0;">
                <strong>{{ $instructorName }}</strong> is now calling for your scheduled consultation.
                Please join the session to begin your consultation.
            </p>

            <!-- Alert Box -->
            <div class="alert-box">
                <strong>⏰ Important:</strong> Your instructor is waiting. Please respond to the call as soon as possible
                to avoid delays.
            </div>

            <!-- Consultation Details -->
            <div class="details-section">
                <div style="text-align: center; margin-bottom: 15px;">
                    <strong style="color: #667eea; font-size: 16px;">Consultation Details</strong>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ $consultationDate }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Time:</span>
                    <span class="detail-value">
                        {{ $formatTime($consultationTime) }}
                        @if ($consultationEndTime)
                            to {{ $formatTime($consultationEndTime) }}
                        @endif
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value">{{ ucfirst($consultationType) }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Attempt:</span>
                    <span class="detail-value">
                        Attempt #{{ $callAttempt }}
                        @if ($callAttempt > 1)
                            <span class="attempt-badge">Retry</span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <strong>How to Join:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Log in to the consultation platform</li>
                    <li>Go to "My Consultations" or "Dashboard"</li>
                    <li>Click on the active consultation session</li>
                    <li>Join the video call when prompted</li>
                </ul>
            </div>

            <!-- Call to Action -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/student/dashboard" class="action-button">Join Consultation Now</a>
            </div>

            <!-- Security Note -->
            <div class="security-note">
                <strong>Security:</strong> If you did not expect this call or did not schedule a consultation, please
                contact your
                instructor directly or report the issue to the platform administrator.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                This is an automated notification from the Consultation Platform.<br>
                Please do not reply to this email.
            </p>
            <p style="margin: 0;">
                &copy; {{ date('Y') }} Consultation Platform. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
