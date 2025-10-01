<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: white; padding: 30px; border: 1px solid #ddd; }
        .enquiry { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
        .button { background: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Enquiry</h1>

        </div>

        <div class="content">
            <div class="enquiry">
                <h3>📋 Enquiry Details</h3>
                <p><strong>Name:</strong> {{ $enquiry['name'] }}</p>
                <p><strong>Email:</strong> {{ $enquiry['email'] }}</p>
                <p><strong>Subject:</strong> {{ $enquiry['subject'] }}</p>
                <p><strong>Message:</strong> {{ $enquiry['message'] }}</p>
            </div>

        <div class="footer">
            <p>{{ config('app.name') }}</p>
            <p>📞 {{ config('app.owner_phone_no') }} | 📧 {{ config('app.owner_email') }}</p>
        </div>
    </div>
</body>
</html>
