<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Contacting {{env('APP_NAME')}}</title>
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
            color: #4CAF50;
            font-size: 22px;
            margin-top: 0;
        }
        .highlight-box {
            background-color: #f1f8f4;
            border-left: 4px solid #4CAF50;
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
            color: #4CAF50;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 10px 20px 10px 0;
            font-weight: bold;
            color: #4CAF50;
            width: 40%;
        }
        .info-value {
            display: table-cell;
            padding: 10px 0;
            color: #555;
        }
        .response-time {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .footer {
            background-color: #f1f8f4;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #fff;
            border-radius: 5px;
        }
        .contact-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📧 Thank You for Reaching Out!</h1>
            <p>We've received your message</p>
        </div>

        <div class="content">
            <h2>Hi {{ $name }}! 👋</h2>

            <p>Thank you for contacting <strong>{{env('APP_NAME')}}</strong>. We've received your inquiry and our team is reviewing it right now.</p>

            <div class="response-time">
                <p style="margin: 0;"><strong>⏱️ Expected Response Time:</strong></p>
                <p style="margin: 5px 0 0;">We'll get back to you within <strong>24 hours</strong> (usually much sooner!)</p>
            </div>

            <div class="message-box">
                <p><strong>Your Message:</strong>{{ $messageText }}</p>
                <!-- <p style="margin-top: 10px;">"{{ $messageText }}"</p> -->
            </div>

            <!--p>One of our team members will carefully review your inquiry and provide you with a detailed response addressing all your questions.</p-->
            @if($phone)
            <div class="highlight-box">
                <p><strong>Phone:</strong> {{ $phone }}</p>
            </div>
            @endif
            @if($company_name)
            <div class="highlight-box">
                <p><strong>Company:</strong> {{ $company_name }}</p>
                <p style="margin-top: 5px;">We're excited to help your organization streamline medical delivery operations!</p>
            </div>
            @endif
            

            <!--div style="margin: 30px 0; padding: 20px; background-color: #f8f9fa; border-radius: 5px;">
                <h3 style="color: #4CAF50; margin-top: 0;">While You Wait...</h3>
                <p>Here are some resources that might interest you:</p>
                <ul style="color: #555;">
                    <li><strong>HIPAA Compliance:</strong> Learn about our security features</li>
                    <li><strong>Feature Overview:</strong> Explore what {{env('APP_NAME')}} can do</li>
                    <li><strong>Pricing Plans:</strong> Find the perfect plan for your needs</li>
                    <li><strong>Case Studies:</strong> See how others are using our platform</li>
                </ul>
            </div>

            <div class="highlight-box">
                <p><strong>🎯 Reference Number:</strong> #CT{{ str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) }}</p>
                <p style="margin-top: 5px; font-size: 14px;">Please mention this number if you need to follow up on your inquiry.</p>
            </div>

            <p><strong>Need immediate assistance?</strong></p>
            <p>If your inquiry is urgent, you can also reach us at:</p>
            <div class="contact-info">
                <p>📞 <strong>Phone:</strong> +1 (555) 0100</p>
                <p>📧 <strong>Email:</strong> support@reliatrack.com</p>
                <p>⏰ <strong>Hours:</strong> Monday - Friday, 9 AM - 6 PM EST</p>
            </div>

            <p style="margin-top: 30px;">We appreciate your interest in {{env('APP_NAME')}}and look forward to speaking with you soon!</p-->

            <p style="margin-top: 20px;"><strong>Best regards,</strong><br>The {{env('APP_NAME')}} Team</p>
        </div>

        <!--div class="footer">
            <p><strong>{{env('APP_NAME')}}</strong></p>
            <p>HIPAA-Compliant Chain of Custody Platform</p>
            <p style="margin-top: 15px;">This is an automated response confirming we received your message.</p>
            <p style="font-size: 12px; color: #999; margin-top: 15px;">
                © {{ date('Y') }} {{env('APP_NAME')}}. All rights reserved.
            </p>
        </div-->
    </div>
</body>
</html>
