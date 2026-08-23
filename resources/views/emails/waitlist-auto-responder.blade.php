<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{env('APP_NAME')}} Waitlist</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #667eea;
            font-size: 22px;
            margin-top: 0;
        }
        .highlight-box {
            background-color: #f8f9ff;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .highlight-box p {
            margin: 0;
            color: #555;
        }
        .message-box {
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .message-box strong {
            color: #667eea;
        }
        .benefits {
            margin: 25px 0;
        }
        .benefits ul {
            list-style: none;
            padding: 0;
        }
        .benefits li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
        }
        .benefits li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4CAF50;
            font-weight: bold;
            font-size: 18px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9ff;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎉 Welcome to {{env('APP_NAME')}}!</h1>
            <p>Thank you for joining our waitlist</p>
        </div>

        <div class="content">
            <h2>Hi {{ $name }}! 👋</h2>

            <p>Thank you for your interest in <strong>{{env('APP_NAME')}}</strong> - the HIPAA-compliant chain of custody platform designed specifically for medical and courier services.</p>

            <!--div class="highlight-box">
                <p><strong>🎁 Exclusive Waitlist Benefit:</strong></p>
                <p>As an early member of our waitlist, you'll receive <strong>20% off your first year</strong> when we launch!</p>
            </div>

            <div class="message-box">
                <p><strong>Your Message:</strong></p>
                <p style="margin-top: 10px;">"{{ $messageText }}"</p>
            </div>
            <p>We've received your message and our team will review it carefully. We'll reach out to discuss how {{env('APP_NAME')}} can meet your specific needs.</p>

            @if($company_name)
            <p>We're excited to help <strong>{{ $company_name }}</strong> streamline your medical delivery operations with our secure, compliant platform.</p>
            @endif

            <div class="benefits">
                <h3 style="color: #667eea;">What to Expect Next:</h3>
                <ul>
                    <li>Early access to our platform before public launch</li>
                    <li>Personalized onboarding and setup assistance</li>
                    <li>Direct line to our product team for feedback</li>
                    <li>Exclusive launch pricing and discounts</li>
                    <li>Priority customer support</li>
                </ul>
            </div>

            <div class="benefits">
                <h3 style="color: #667eea;">Key Features You'll Love:</h3>
                <ul>
                    <li><strong>100% HIPAA Compliant</strong> - Secure handling of medical specimens</li>
                    <li><strong>Real-Time Tracking</strong> - Live GPS location of all drivers</li>
                    <li><strong>Chain of Custody</strong> - Complete audit trail for compliance</li>
                    <li><strong>Digital Signatures</strong> - Proof of pickup and delivery</li>
                    <li><strong>Instant Reports</strong> - Generate compliance reports on demand</li>
                </ul>
            </div-->

            <p style="margin-top: 30px;">We'll keep you updated on our launch progress and notify you as soon as we're ready to onboard you.</p>

            <p><strong>Questions?</strong> Feel free to reply to this email - we're here to help!</p>
            <p style="margin-top: 20px;"><strong>Best regards,</strong><br>The {{env('APP_NAME')}} Team</p>
        </div>

        <!-- <div class="footer">
            <p><strong>Regards</strong></p>
            <p><strong>{{env('APP_NAME')}}</strong></p>
            <p>HIPAA-Compliant Chain of Custody Platform</p>
            <p style="margin-top: 15px;">This email was sent because you joined our waitlist at reliatrack.com</p>
            <p style="font-size: 12px; color: #999; margin-top: 15px;">
                © {{ date('Y') }} {{env('APP_NAME')}} Track. All rights reserved.
            </p>
        </div> -->
    </div>
</body>
</html>
