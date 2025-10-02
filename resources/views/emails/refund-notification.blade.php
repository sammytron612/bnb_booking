<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Confirmation</title>
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
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #27ae60;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #27ae60;
            margin: 0;
            font-size: 28px;
        }
        .refund-amount {
            background-color: #f8f9fa;
            border: 2px solid #27ae60;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .refund-amount .amount {
            font-size: 32px;
            font-weight: bold;
            color: #27ae60;
            margin: 0;
        }
        .refund-amount .label {
            font-size: 14px;
            color: #666;
            margin: 5px 0 0 0;
        }
        .booking-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .booking-details h3 {
            color: #2c3e50;
            margin-top: 0;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
            text-align: right;
        }
        .reason-section {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .reason-section h4 {
            color: #856404;
            margin-top: 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            background-color: #e8f5e8;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .contact-info h4 {
            color: #27ae60;
            margin-top: 0;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Refund Confirmation</h1>
            <p>Your refund has been processed successfully</p>
        </div>

        <div class="refund-amount">
            <p class="amount">£{{ number_format($refundAmount, 2) }}</p>
            <p class="label">Refund Amount</p>
        </div>

        <p>Dear {{ $booking->name }},</p>

        <p>We're emailing to confirm that your refund request has been processed successfully. The refund amount will be returned to your original payment method within 5-10 business days.</p>

        <div class="booking-details">
            <h3>Booking Details</h3>
            <div class="detail-row">
                <span class="detail-label">Booking ID:</span>
                <span class="detail-value">{{ $booking->getDisplayBookingId() }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Property:</span>
                <span class="detail-value">{{ $booking->venue->venue_name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Guest Name:</span>
                <span class="detail-value">{{ $booking->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $booking->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Check-in Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Check-out Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Original Amount:</span>
                <span class="detail-value">£{{ number_format((float)$booking->total_price, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Refund Amount:</span>
                <span class="detail-value"><strong>£{{ number_format($refundAmount, 2) }}</strong></span>
            </div>
        </div>

        @if($refundReason)
            <div class="reason-section">
                <h4>Refund Reason</h4>
                <p>{{ $refundReason }}</p>
            </div>
        @endif

        <div class="contact-info">
            <h4>Need Help?</h4>
            <p>If you have any questions about your refund, please don't hesitate to contact us:</p>
            <p><strong>Email:</strong> {{ env('OWNER_EMAIL', 'support@eileenbnb.com') }}</p>
            <p><strong>Phone:</strong> {{ env('OWNER_PHONE_NO', '+44 (0) 123 456 7890') }}</p>
        </div>

        <p><strong>Refund Timeline:</strong></p>
        <ul>
            <li>Your refund has been processed immediately with Stripe</li>
            <li>It may take 5-10 business days to appear in your account</li>
            <li>The refund will appear on the same payment method used for the original booking</li>
        </ul>

        <p>Thank you for choosing our accommodation services. We apologize for any inconvenience and hope to welcome you again in the future.</p>

        <div class="footer">
            <p>This is an automated email confirmation. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Eileen BnB. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
