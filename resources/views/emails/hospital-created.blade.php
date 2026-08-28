<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Created</title>
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
            background: linear-gradient(135deg, #f44336 0%, #e91e63 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px 30px;
        }
        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .alert-box strong {
            color: #856404;
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
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            width: 150px;
        }
        .info-value {
            color: #212529;
            flex: 1;
        }
        .security-notice {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .security-notice strong {
            color: #155724;
        }
        .action-box {
            background-color: #fff;
            border: 2px solid #f44336;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .action-box h3 {
            color: #f44336;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        ul {
            padding-left: 20px;
        }
        ul li {
            margin: 8px 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="email-container">

        <div class="content">
            <h2 style="color: #333;">Welcome to {{ env('APP_NAME') }}. Your Hospital Account Has Been Created 👋</h2>

            <div class="security-notice">
                <p style="margin: 0;"><strong>✅ Hello {{ $name }} Team</strong></p>
            </div>

            <div class="info-box">
                <h3 style="margin-top: 0; color: #495057;">Here are Your Login Credentials</h3>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Password:</div>
                    <div class="info-value">{{ $password }}</div>
                </div>
            </div>

            <div class="alert-box">
                <p style="margin: 0;"><strong>📧 Need Help?</strong></p>
                <p style="margin: 5px 0 0;">If you have any questions or concerns about your account security, please contact our support team at <a href="mailto:support@reliatrack.com" style="color: #007bff;">support@reliatrack.com</a></p>
            </div>

            <p style="margin-top: 30px;">Thank you for using {{ env('APP_NAME') }}!</p>

            <p style="margin-top: 20px;"><strong>Best regards,</strong>
        </div>

        <div class="footer">
            <p><strong>{{ env('APP_NAME') }}</strong></p>
            <p>HIPAA-Compliant Chain of Custody Platform</p>
            <p style="font-size: 12px; color: #999; margin-top: 15px;">
                © {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
