<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Please Leave a Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .btn:hover {
            background: #764ba2;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .stars {
            font-size: 20px;
            color: #ffc107;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌟 How was your stay? 🌟</h1>
        <p>We'd love to hear about your experience!</p>
    </div>

    <div class="content">
        <p>Hello! {{ $data['name'] }}</p>

    <p>Thank you for choosing {{ config('app.name') }} for your recent stay at {{ $data['venue'] }}. We hope you had a wonderful time at our coastal accommodation!</p>

        <p>Your feedback is incredibly valuable to us and helps future guests make informed decisions about their stay. If you enjoyed your experience, we would be delighted if you could take a few minutes to share a review.</p>

        <p><strong>This link is valid for 72 hours.</strong></p>

        <div style="text-align: center;">
            <a href="{{ $data['reviewLink'] }}" class="btn">Leave a Review</a>
        </div>

        <p>Your review helps us:</p>
        <ul>
            <li>Continue providing excellent service</li>
            <li>Assist future guests in choosing their perfect coastal getaway</li>
            <li>Maintain our high standards of hospitality</li>
        </ul>

        <p>Thank you once again for staying with us. We hope to welcome you back to the beautiful Seaham coast soon!</p>

    <p>Warm regards,<br>
    <strong>The {{ config('app.name') }} Team</strong></p>
    </div>

    <div class="footer">
        <p>{{ config('app.name') }} | Discovering luxury coastal living in County Durham</p>
        <p>If you have any questions, please contact us at {{ config('app.owner_phone_no') }} or <a href="mailto:{{ config('app.owner_email') }}">{{ config('app.owner_email') }}</a>.</p>
    </div>
</body>
</html>
