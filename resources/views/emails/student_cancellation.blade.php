<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultation Cancelled</title>
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
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
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
            background-color: #ffe6e6;
            border-left: 4px solid #ff6b6b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .alert-box strong {
            color: #cc0000;
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
            color: #ff6b6b;
            min-width: 120px;
        }

        .detail-value {
            text-align: right;
            color: #555;
        }

        .status-badge {
            display: inline-block;
            background-color: #ff6b6b;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .instructions {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }

        .instructions strong {
            color: #856404;
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
            color: #ff6b6b;
            text-decoration: none;
        }

        .next-steps {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }

        .next-steps strong {
            color: #1565c0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>❌ Consultation Cancelled</h1>
            <p style="margin: 10px 0 0 0;">Your consultation request has been cancelled</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hi <strong>{{ $relatedUserName }}</strong>,
            </div>

            <p style="margin: 15px 0;">
                <strong>{{ $studentName }}</strong> has cancelled a consultation request that was scheduled for your review.
                The consultation is no longer available for acceptance.
            </p>

            <!-- Alert Box -->
            <div class="alert-box">
                <strong>Status Update:</strong> This consultation request is now <span class="status-badge">CANCELLED</span>
            </div>

            <!-- Consultation Details -->
            <div class="details-section">
                <div style="text-align: center; margin-bottom: 15px;">
                    <strong style="color: #ff6b6b; font-size: 16px;">Cancelled Consultation Details</strong>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Student:</span>
                    <span class="detail-value">{{ $studentName }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ $consultationDate }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Time:</span>
                    <span class="detail-value">
                        {{ \Illuminate\Support\Str::limit($consultationTime, 5) }}
                        @if ($consultationEndTime)
                            to {{ \Illuminate\Support\Str::limit($consultationEndTime, 5) }}
                        @endif
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value">{{ ucfirst($consultationType) }}</span>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <strong>What Happens Next:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This request is removed from your pending consultations</li>
                    <li>No further action is required on your part</li>
                    <li>The student may submit a new request if needed</li>
                </ul>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <strong>Questions?</strong><br>
                If you have any concerns about this cancellation or need to follow up with the student, please
                contact them directly or reach out to the platform administrator.
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
