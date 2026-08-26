<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Waitlist Submission</title>
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
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
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
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .message-box strong {
            color: #856404;
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
        .metadata {
            background-color: #f1f3f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 13px;
            color: #6c757d;
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
            <h1>NEW WAITLIST SUBMISSION</h1>
            <div class="badge">ACTION REQUIRED</div>
        </div-->
        <h2>Hi Admin! 👋</h2>
        <div class="content">
            <p style="font-size: 16px; color: #495057; margin-bottom: 25px;">
                A new person has joined the waitlist. Here are the details:
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
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value">
                        <a href="tel:{{ $submission->phone }}" style="color: #007bff;">{{ $submission->phone }}</a>
                    </div>
                </div>
                @if($submission->company_name)
                <div class="info-row">
                    <div class="info-label">Company:</div>
                    <div class="info-value"><strong>{{ $submission->company_name }}</strong></div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span style="background-color: #ffc107; color: #000; padding: 3px 10px; border-radius: 3px; font-size: 12px; font-weight: bold;">
                            {{ strtoupper($submission->status) }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Submitted:</div>
                    <div class="info-value">{{ $submission->created_at->format('F d, Y \a\t h:i A') }}</div>
                </div>
                @if($plan_name)
                <div class="info-row">
                    <div class="info-label">Plan name:</div>
                    <div class="info-value">{{ $plan_name }}</div>
                </div>
                @endif
            </div>
            @if($submission->message)
            <div class="message-box">
                <p><strong>Message from {{ $submission->name }}:</strong></p>
                <p style="margin-top: 10px; white-space: pre-wrap;">{{ $submission->message }}</p>
            </div>
            @endif

            <!--div class="action-buttons">
                <a href="{{ config('app.url') }}/dashboard/waitlist" class="btn btn-primary">
                    View in Dashboard
                </a>
                <a href="mailto:{{ $submission->email }}?subject=Welcome to {{env('APP_NAME')}} Waitlist" class="btn btn-success">
                    Send Email
                </a>
            </div-->

            <!-- <div class="metadata">
                <strong>Additional Information:</strong><br>
                <strong>ID:</strong> #{{ $submission->id }}<br>
                <strong>IP Address:</strong> {{ $submission->ip_address }}<br>
                <strong>User Agent:</strong> {{ Str::limit($submission->user_agent, 60) }}<br>
                <strong>Submission Time:</strong> {{ $submission->created_at->format('Y-m-d H:i:s T') }}
            </div> -->

            <!--div style="margin-top: 30px; padding: 15px; background-color: #e7f3ff; border-radius: 5px;">
                <p style="margin: 0; font-size: 14px; color: #004085;">
                    <strong>💡 Quick Actions:</strong>
                </p>
                <ul style="margin: 10px 0; padding-left: 20px; color: #004085;">
                    <li>Review their message and company details</li>
                    <li>Add them to your CRM system</li>
                    <li>Schedule a follow-up call or demo</li>
                    <li>Send personalized welcome email</li>
                    <li>Mark as "Contacted" in dashboard once done</li>
                </ul>
            </div-->
        </div>

        <div class="footer">
            <!--p>This is an automated notification from {{env('APP_NAME')}}</p>
            <p>© {{ date('Y') }} {{env('APP_NAME')}}. All rights reserved.</p-->
        </div>
    </div>
</body>
</html>
