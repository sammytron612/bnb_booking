<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Failed - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .booking-details {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .booking-details h3 {
            margin-top: 0;
            color: #495057;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .retry-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .retry-button:hover {
            transform: translateY(-1px);
            text-decoration: none;
            color: white;
        }
        .error-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .contact-info {
            margin-top: 20px;
            padding: 15px;
            background: #e7f3ff;
            border-radius: 6px;
            border-left: 4px solid #0066cc;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Payment Failed</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">We couldn't process your payment</p>
        </div>

        <div class="content">
            <p>Dear {{ $booking->name }},</p>

            <p>Unfortunately, we were unable to process the payment for your booking. Don't worry - your booking is still reserved for a short time while you resolve this issue.</p>

            @if($booking->payment_failure_reason)
            <div class="error-info">
                <strong>Payment Error:</strong> {{ $booking->payment_failure_reason }}
            </div>
            @endif

            <div class="booking-details">
                <h3>📅 Booking Details</h3>
                <div class="detail-row">
                    <span><strong>Booking ID:</strong></span>
                    <span>{{ $booking->booking_id }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Property:</strong></span>
                    <span>{{ $booking->venue ? $booking->venue->venue_name : 'Property Details' }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Check-in:</strong></span>
                    <span>{{ $booking->check_in->format('D, M j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Check-out:</strong></span>
                    <span>{{ $booking->check_out->format('D, M j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Total Amount:</strong></span>
                    <span><strong>£{{ number_format($booking->total_price, 2) }}</strong></span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $retryUrl }}" class="retry-button">
                    💳 Retry Payment Now
                </a>
            </div>

            <h4>🔧 What you can do:</h4>
            <ul>
                <li><strong>Check your card details</strong> - Ensure your card number, expiry date, and CVC are correct</li>
                <li><strong>Verify your billing address</strong> - Make sure it matches your card's billing address</li>
                <li><strong>Check your account balance</strong> - Ensure you have sufficient funds</li>
                <li><strong>Contact your bank</strong> - Your card might be blocked for online purchases</li>
                <li><strong>Try a different card</strong> - Use an alternative payment method</li>
            </ul>

            <div class="contact-info">
                <h4 style="margin-top: 0;">💬 Need Help?</h4>
                <p style="margin-bottom: 0;">If you continue to experience issues, please don't hesitate to contact us. We're here to help ensure your booking goes smoothly.</p>
                <p style="margin: 10px 0 0 0;"><strong>Email:</strong> {{ config('contact.email', 'info@example.com') }}</p>
            </div>

            <p><strong>Important:</strong> This booking will be automatically cancelled if payment is not completed within 24 hours. Please retry your payment as soon as possible to secure your reservation.</p>

            <p>Thank you for choosing {{ config('app.name') }}!</p>

            <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
