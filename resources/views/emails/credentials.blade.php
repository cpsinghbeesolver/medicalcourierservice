<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credentials Generated</title>
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
        <div class="header">
            <h1>Hi {{ $submission->name }}</h1>
        </div>

        <div class="content">
            <p style="font-size: 16px; color: #495057; margin-bottom: 25px;">
                Welcome to {{env('APP_NAME')}}! We are excited to have you on board. Your account has been successfully created, and we have generated your login credentials.
            </p>
            <!-- <div class="message-box">
                <p><strong>Password:</strong></p>
                <p style="margin-top: 10px;">{{ $password }}</p>
            </div> -->
            <div class="message-box">
                <b style="margin-top: 10px;"><a href="{{ route('set-password', $submission->email) }}" target="_blank">Click here to verify your email address and generate password</a></b>
            </div>
        </div>

        <div class="footer">
            <p>Do not reply to this email</p>
            <p>© {{ date('Y') }} Relia Track. All rights reserved.</p>
        </div>
    </div>
</body>
</html>