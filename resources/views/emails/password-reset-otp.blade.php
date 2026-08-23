<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Password Reset Code</title>
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
        .otp-container {
            text-align: center;
            margin: 35px 0;
            padding: 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            border: 2px dashed #a8b456;
        }
        .otp-label {
            font-size: 14px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 700;
            color: #2c3e50;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .otp-expiry {
            font-size: 13px;
            color: #856404;
            margin-top: 15px;
            padding: 10px;
            background-color: #fff3cd;
            border-radius: 6px;
            font-weight: 500;
        }
        .instructions {
            margin-top: 30px;
            padding: 20px;
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            border-radius: 6px;
        }
        .instructions h3 {
            margin: 0 0 15px 0;
            color: #0d47a1;
            font-size: 16px;
        }
        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #1565c0;
        }
        .instructions li {
            margin-bottom: 10px;
            font-size: 14px;
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
            background-color: #ffe7e7;
            border-left: 4px solid #f44336;
            border-radius: 4px;
        }
        .security-notice p {
            margin: 0;
            color: #c62828;
            font-size: 13px;
            font-weight: 500;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 30px 20px;
            }
            .otp-code {
                font-size: 36px;
                letter-spacing: 6px;
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
                <p>Please use the verification code below to reset your password in the mobile app:</p>
            </div>

            <div class="otp-container">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expiry">
                    ⏱ This code expires in {{ $expiresAt }} minutes
                </div>
            </div>

            <div class="instructions">
                <h3>📱 How to Reset Your Password:</h3>
                <ol>
                    <li>Open the {{ config('app.name') }} mobile app</li>
                    <li>Go to the "Forgot Password" screen</li>
                    <li>Enter this 6-digit verification code</li>
                    <li>Create your new secure password</li>
                </ol>
            </div>

            <div class="warning">
                <p><strong>⚠️ Security Notice:</strong> If you didn't request a password reset, please ignore this email and contact support immediately. Your password will remain unchanged.</p>
            </div>

            <div class="security-notice">
                <p><strong>🔒 IMPORTANT - HIPAA Compliance:</strong> Never share this code with anyone. Our support team will NEVER ask for this code. This email contains sensitive account information - please delete it after use.</p>
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
