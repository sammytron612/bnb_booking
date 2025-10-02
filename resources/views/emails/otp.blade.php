<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .otp-code {
            background-color: #3498db;
            color: #ffffff;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            letter-spacing: 5px;
            margin: 20px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        .expiry {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
            <h2>OTP Verification Code</h2>
        </div>

        <p>Hello {{ $userName ?: 'Admin' }},</p>

        <p>You have requested to access the admin panel. For security purposes, please enter the following One-Time Password (OTP) to complete your login:</p>

        <div class="otp-code">
            {{ $otpCode }}
        </div>

        <div class="warning">
            <strong>Security Notice:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>This code is valid for <span class="expiry">10 minutes only</span></li>
                <li>Never share this code with anyone</li>
                <li>If you didn't request this code, please contact support immediately</li>
                <li>This code can only be used once</li>
            </ul>
        </div>

        <p>If you're having trouble accessing your account, please contact our support team.</p>

        <div class="footer">
            <p>This is an automated message from Eileen BnB Admin System.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}.</p>
        </div>
    </div>
</body>
</html>
