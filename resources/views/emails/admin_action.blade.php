<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Alert - Consultation Action</title>
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
            max-width: 700px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 30px 20px;
        }

        .alert-banner {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
        }

        .action-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
            margin: 5px 5px 5px 0;
            color: #ffffff;
        }

        .action-submitted {
            background-color: #17a2b8;
        }

        .action-cancelled {
            background-color: #ff6b6b;
        }

        .action-approved {
            background-color: #28a745;
        }

        .action-declined {
            background-color: #ffc107;
            color: #000;
        }

        .action-call {
            background-color: #007bff;
        }

        .summary-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }

        .summary-title {
            color: #2c3e50;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: bold;
            color: #2c3e50;
            min-width: 150px;
        }

        .detail-value {
            flex: 1;
            text-align: left;
            color: #555;
            padding-left: 10px;
        }

        .consultation-details {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
        }

        .consultation-details strong {
            color: #1565c0;
        }

        .action-details {
            background-color: #f0f0f0;
            border-left: 4px solid #999;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }

        .timestamp {
            font-size: 12px;
            color: #999;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
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
            color: #2c3e50;
            text-decoration: none;
        }

        .section-divider {
            height: 1px;
            background-color: #dee2e6;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🔔 Admin Alert</h1>
            <p>Consultation Platform - Action Notification</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Alert Banner -->
            <div class="alert-banner">
                <strong>⚠️ This is an automated admin notification.</strong> A consultation action has been recorded and
                requires your awareness.
            </div>

            <!-- Action Summary -->
            <div class="summary-section">
                <div class="summary-title">Action Summary</div>

                <div class="detail-row">
                    <span class="detail-label">Action Type:</span>
                    <span class="detail-value">
                        @if ($actionType === 'submitted')
                            <span class="action-badge action-submitted">New Request Submitted</span>
                        @elseif ($actionType === 'cancelled')
                            <span class="action-badge action-cancelled">Request Cancelled</span>
                        @elseif ($actionType === 'approved')
                            <span class="action-badge action-approved">Request Approved</span>
                        @elseif ($actionType === 'declined')
                            <span class="action-badge action-declined">Request Declined</span>
                        @elseif ($actionType === 'call_started')
                            <span class="action-badge action-call">Call Started</span>
                        @else
                            <span class="action-badge">{{ ucfirst($actionType) }}</span>
                        @endif
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Performed By:</span>
                    <span class="detail-value">
                        <strong>{{ $actionPerformedBy }}</strong> ({{ ucfirst($actionUserType) }})
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Related To:</span>
                    <span class="detail-value">
                        <strong>{{ $relatedUserName }}</strong> ({{ ucfirst($relatedUserType) }})
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Timestamp:</span>
                    <span class="detail-value">{{ $timestamp }}</span>
                </div>
            </div>

            <!-- Consultation Details Section -->
            @if ($consultationDetails)
                <div class="consultation-details">
                    <strong>📋 Consultation Details</strong>
                    <div style="margin-top: 10px; font-size: 14px;">
                        @if (isset($consultationDetails['date']))
                            <div>
                                <strong>Date:</strong> {{ $consultationDetails['date'] }}
                            </div>
                        @endif
                        @if (isset($consultationDetails['time']))
                            <div>
                                <strong>Time:</strong> {{ $consultationDetails['time'] }}
                            </div>
                        @endif
                        @if (isset($consultationDetails['type']))
                            <div>
                                <strong>Type:</strong> {{ $consultationDetails['type'] }}
                            </div>
                        @endif
                        @if (isset($consultationDetails['mode']))
                            <div>
                                <strong>Mode:</strong> {{ $consultationDetails['mode'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Action Description -->
            @if ($actionDescription)
                <div class="action-details">
                    <strong>📝 Action Description:</strong>
                    <div style="margin-top: 10px;">
                        {{ $actionDescription }}
                    </div>
                </div>
            @endif

            <!-- Alert Box -->
            <div style="background-color: #f8f9fa; border-left: 4px solid #2c3e50; padding: 15px; border-radius: 4px; margin: 20px 0;">
                <strong>💡 Admin Tip:</strong><br>
                Review this action in your admin dashboard to monitor platform activity. Take action if this
                consultation needs escalation or if there are any concerns.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                This is an automated notification. Visit your admin dashboard for more details.<br>
                Please do not reply to this email.
            </p>
            <p style="margin: 0;">
                &copy; {{ date('Y') }} Consultation Platform. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
