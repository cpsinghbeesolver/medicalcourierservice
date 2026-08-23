<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Inquiry</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            margin: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            background-color: rgba(255,255,255,0.3);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            margin-top: 10px;
        }
        .priority-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .priority-medium {
            background-color: #ffc107;
            color: #000;
        }
        .priority-high {
            background-color: #ff9800;
            color: #fff;
        }
        .priority-urgent {
            background-color: #f44336;
            color: #fff;
        }
        .priority-low {
            background-color: #9e9e9e;
            color: #fff;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            width: 140px;
            flex-shrink: 0;
        }
        .info-value {
            color: #212529;
            flex: 1;
        }
        .message-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .message-box strong {
            color: #0d47a1;
        }
        .message-content {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
            white-space: pre-wrap;
            color: #333;
            line-height: 1.8;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-info {
            background-color: #17a2b8;
        }
        .metadata {
            background-color: #f1f3f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 13px;
            color: #6c757d;
        }
        .urgent-notice {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!--div class="header">
            <h1>📞 NEW CONTACT INQUIRY</h1>
            <div class="badge">RESPOND WITHIN 24 HOURS</div>
        </div-->

        <div class="content">
            <!--div class="urgent-notice">
                <p style="margin: 0; font-weight: bold; color: #856404;">
                    ⏱️ Expected Response Time: <strong>24 hours</strong>
                </p>
            </div-->

            <p style="font-size: 16px; color: #495057; margin-bottom: 25px;">
                A new contact inquiry has been submitted. Please review and respond promptly:
            </p>

            <div class="info-box">
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value"><strong>{{ $submission->name }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">
                        <a href="mailto:{{ $submission->email }}" style="color: #007bff;">{{ $submission->email }}</a>
                    </div>
                </div>
                @if($submission->phone)
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">
                        <a href="tel:{{ $submission->phone }}" style="color: #007bff;">{{ $submission->phone }}</a>
                    </div>
                </div>
                @endif
                @if($submission->company_name)
                <div class="info-row">
                    <div class="info-label">Company:</div>
                    <div class="info-value"><strong>{{ $submission->company_name }}</strong></div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Priority:</div>
                    <div class="info-value">
                        <span class="priority-badge priority-{{ $submission->priority }}">
                            {{ strtoupper($submission->priority) }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span style="background-color: #28a745; color: #fff; padding: 3px 10px; border-radius: 3px; font-size: 12px; font-weight: bold;">
                            {{ strtoupper($submission->status) }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Submitted:</div>
                    <div class="info-value">{{ $submission->created_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
            </div>

            <div class="message-box">
                <p><strong>📝 Message from {{ $submission->name }}:</strong></p>
                <div class="message-content">{{ $submission->message }}</div>
            </div>

            <div class="action-buttons">
                <a href="mailto:{{ $submission->email }}?subject=Re: Your Inquiry to {{env('APP_NAME')}}&body=Hi {{ $submission->name }},%0D%0A%0D%0AThank you for contacting {{env('APP_NAME')}}." class="btn btn-success">
                    Reply via Email
                </a>
                <a href="{{ config('app.url') }}/dashboard/contacts" class="btn btn-primary">
                    View in Dashboard
                </a>
                @if($submission->phone)
                <a href="tel:{{ $submission->phone }}" class="btn btn-info">
                    Call Now
                </a>
                @endif
            </div>

            <!--div style="margin-top: 30px; padding: 15px; background-color: #f0f7ff; border-radius: 5px;">
                <p style="margin: 0; font-size: 14px; color: #004085;">
                    <strong>📋 Recommended Actions:</strong>
                </p>
                <ol style="margin: 10px 0; padding-left: 20px; color: #004085;">
                    <li>Review the inquiry details and company information</li>
                    <li>Research the company if mentioned</li>
                    <li>Prepare answers to their questions</li>
                    <li>Reply within 24 hours with personalized response</li>
                    <li>Update status to "In Progress" in dashboard</li>
                    <li>Schedule follow-up if needed</li>
                    <li>Mark as "Resolved" once fully addressed</li>
                </ol>
            </div-->

            <div class="metadata">
                <strong>Additional Information:</strong><br>
                <strong>Submission ID:</strong> #{{ $submission->id }}<br>
                <strong>IP Address:</strong> {{ $submission->ip_address }}<br>
                <strong>User Agent:</strong> {{ Str::limit($submission->user_agent, 60) }}<br>
                <strong>Submission Time:</strong> {{ $submission->created_at->format('Y-m-d H:i:s T') }}<br>
                <strong>Reference:</strong> #CT{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}
            </div>

            <!--div style="margin-top: 20px; padding: 15px; background-color: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
                <p style="margin: 0; font-size: 13px; color: #856404;">
                    <strong>⚡ Quick Tip:</strong> Respond faster by using email templates in your dashboard. Personal touches matter!
                </p>
            </div-->
        </div>

        <!--div class="footer">
            <p>This is an automated notification from {{env('APP_NAME')}}</p>
            <p>Do not reply to this email - respond directly to {{ $submission->email }}</p>
            <p>© {{ date('Y') }} {{env('APP_NAME')}}. All rights reserved.</p>
        </div-->
    </div>
</body>
</html>
