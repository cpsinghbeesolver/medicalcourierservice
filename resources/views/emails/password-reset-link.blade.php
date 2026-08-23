<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #a8b456 0%, #8a9644 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .message {
            color: #555;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .reset-button {
            display: inline-block;
            padding: 15px 40px;
            background-color: #a8b456;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .reset-button:hover {
            background-color: #96a048;
        }
        .alternative-link {
            margin-top: 25px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #a8b456;
        }
        .alternative-link p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        .link-text {
            word-break: break-all;
            color: #3498db;
            font-size: 13px;
            font-family: 'Courier New', monospace;
        }
        .warning {
            margin-top: 25px;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 4px;
        }
        .warning p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 13px;
        }
        .security-notice {
            margin-top: 20px;
            padding: 15px;
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            border-radius: 4px;
        }
        .security-notice p {
            margin: 0;
            color: #0d47a1;
            font-size: 13px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 30px 20px;
            }
            .reset-button {
                display: block;
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            <div class="greeting">Hello {{ $userName }},</div>

            <div class="message">
                <p>We received a request to reset your password for your {{ config('app.name') }} account.</p>
                <p>Click the button below to reset your password. This link will expire in {{ $expiresAt }} minutes.</p>
            </div>

            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">Reset Password</a>
            </div>

            <div class="alternative-link">
                <p><strong>If the button doesn't work, copy and paste this link into your browser:</strong></p>
                <p class="link-text">{{ $resetUrl }}</p>
            </div>

            <div class="warning">
                <p><strong>⚠️ Security Notice:</strong> If you didn't request a password reset, please ignore this email. Your password will remain unchanged.</p>
            </div>

            <div class="security-notice">
                <p><strong>🔒 HIPAA Compliance:</strong> This email contains sensitive account information. Please ensure you're viewing this on a secure device. Do not share this link with anyone.</p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated email. Please do not reply to this message.</p>
            <p style="margin-top: 15px; font-size: 12px;">
                Secure medical courier services compliant with HIPAA regulations.
            </p>
        </div>
    </div>
</body>
</html>
