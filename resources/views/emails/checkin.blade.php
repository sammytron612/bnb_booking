<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Reminder</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .content {
            padding: 40px 30px;
        }
        .booking-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            border-left: 4px solid #667eea;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            flex: 1;
        }
        .detail-value {
            flex: 2;
            text-align: right;
            color: #212529;
        }
        .highlight {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #bbdefb;
            margin: 25px 0;
        }
        .checkin-info {
            background: #e8f5e8;
            border: 1px solid #c8e6c9;
            color: #2e7d32;
        }
        .footer {
            background: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        .price {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .booking-id {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .container {
                margin: 0 10px;
            }
            .content, .header {
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
            <h1>🏠 Check-in Reminder</h1>
            <p>Your stay begins soon!</p>
        </div>

        <div class="content">
            <p>Dear {{ $booking->name }},</p>

            <p>We're excited to welcome you to <strong>{{ $booking->venue->venue_name }}</strong>! Your check-in date is approaching, and we wanted to send you this friendly reminder with all the important details for your stay.</p>

            <div class="highlight checkin-info">
                <h3 style="margin-top: 0; color: #2e7d32;">✅ Check-in!</h3>
                <p style="margin-bottom: 0;">Don't forget - your check-in is scheduled for <strong>{{ \Carbon\Carbon::parse($booking->check_in)->format('l, F j, Y') }} after 3:00 PM</strong></p>
            </div>

            <div class="booking-details">
                <h3 style="margin-top: 0; color: #495057;">📋 Booking Details</h3>

                <div class="detail-row">
                    <span class="detail-label">Booking Reference:</span>
                    <span class="detail-value booking-id">{{ $booking->getDisplayBookingId() }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Guest Name:</span>
                    <span class="detail-value">{{ $booking->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Property:</span>
                    <span class="detail-value">{{ $booking->venue->venue_name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value">
                        {{ $booking->venue->address1 }}
                        @if($booking->venue->address2), {{ $booking->venue->address2 }}@endif
                        {{ $booking->venue->postcode }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Check-in:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->check_in)->format('l, F j, Y') }} after 3:00 PM</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Check-out:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->check_out)->format('l, F j, Y') }} before 11:00 AM</span>
                </div>

                @if($booking->venue->instructions)
                    <div class="detail-row">
                        <span class="detail-label">Check In Instructions:</span>
                        <span class="detail-value">{{ $booking->venue->instructions }}</span>
                    </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">{{ $booking->nights }} {{ $booking->nights === 1 ? 'night' : 'nights' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value price">£{{ number_format($booking->total_price, 2) }}</span>
                </div>
            </div>

            <p>We're looking forward to hosting you and hope you have a wonderful stay at {{ $booking->venue->venue_name }}!</p>

            <p>If you have any questions or need to make any changes to your booking, please contact us as soon as possible at {{ config('app.owner_phone_no') }} or <a href="mailto:{{ config('app.owner_email') }}">{{ config('app.owner_email') }}</a>.</p>

            <p>Safe travels,<br>
            <strong>{{ config('app.name') }} Team</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated reminder email for booking {{ $booking->getDisplayBookingId() }}.</p>
            <p>If you have any questions, please don't hesitate to contact us at {{ config('app.owner_phone_no') }} or <a href="mailto:{{ config('app.owner_email') }}">{{ config('app.owner_email') }}</a>.</p>
        </div>
    </div>
</body>
</html>
