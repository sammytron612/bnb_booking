<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment</title>
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
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .booking-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .payment-button {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 20px 0;
            font-weight: bold;
        }
        .payment-button:hover {
            background-color: #0056b3;
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
            font-size: 14px;
            color: #666;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Session Expired</h1>
        <p>Don't worry - your booking is still reserved!</p>
    </div>

    <p>Hi {{ $booking->name }},</p>

    <p>Your payment session for booking <strong>{{ $booking->getDisplayBookingId() }}</strong> has expired, but we've kept your reservation secure.</p>

    <div class="warning">
        <strong>⏰ Act quickly!</strong> Your booking will be held until <strong>{{ $expiryDate }}</strong>. After this time, the dates may become available to other guests.
    </div>

    <div class="booking-details">
        <h3>📍 Booking Details</h3>
        <p><strong>Property:</strong> {{ $booking->venue->venue_name }}</p>
        <p><strong>Dates:</strong> {{ $booking->date_range }}</p>
        <p><strong>Guests:</strong> {{ $booking->nights }} {{ $booking->nights === 1 ? 'night' : 'nights' }}</p>
        <p><strong>Total Amount:</strong> {{ $booking->formatted_total }}</p>
    </div>

    <div style="text-align: center;">
        <a href="{{ $resumePaymentUrl }}" class="payment-button">
            💳 Complete Payment Now
        </a>
    </div>

    <h3>Why did this happen?</h3>
    <p>Payment sessions automatically expire after 24 hours for security reasons. This is normal and helps protect your payment information.</p>

    <h3>What happens next?</h3>
    <ul>
        <li>✅ Click the button above to complete your payment securely</li>
        <li>✅ You'll be taken to a new, secure payment page</li>
        <li>✅ Your booking details are already saved - no need to re-enter them</li>
        <li>✅ You'll receive confirmation once payment is complete</li>
    </ul>

    <div class="footer">
        <p>This secure payment link expires on {{ $expiryDate }}.</p>
        <p>If you have any questions, please contact us and reference booking {{ $booking->getDisplayBookingId() }}.</p>
        <p>Thank you for choosing {{ $booking->venue->venue_name }}!</p>
    </div>
</body>
</html>
