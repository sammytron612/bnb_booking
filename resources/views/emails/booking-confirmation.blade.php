{{-- resources/views/emails/booking-confirmation.blade.php --}}
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
        .booking-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
        .button { background: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏠 Booking Confirmed!</h1>
            <p>Thank you for choosing {{ config('app.name') }}</p>
        </div>

        <div class="content">
            <h2>Hello {{ $booking->name }},</h2>

            <p>Great news! Your booking has been confirmed and payment has been successfully processed.</p>

            <div class="booking-details">
                <h3>📋 Booking Details</h3>
                <p><strong>Booking Reference:</strong> {{ $booking->getDisplayBookingId() }}</p>
                <p><strong>Venue:</strong> {{ $booking->venue->venue_name }}</p>
                @if($booking->venue->address1)
                    <p><strong>Address:</strong> {{ $booking->venue->address1 }}</p>
                @endif
                @if($booking->venue->address2)
                    <p>{{ $booking->venue->address2 }}</p>
                @endif
                @if($booking->venue->postcode)
                    <p><strong>Postcode:</strong> {{ $booking->venue->postcode }}</p>
                @endif
                <p><strong>Guest Name:</strong> {{ $booking->name }}</p>
                <p><strong>Email:</strong> {{ $booking->email }}</p>
                <p><strong>Phone:</strong> {{ $booking->phone }}</p>
                <p><strong>Check-in:</strong> {{ \Carbon\Carbon::parse($booking->check_in)->format('l, F j, Y') }}</p>
                <p><strong>Check-out:</strong> {{ \Carbon\Carbon::parse($booking->check_out)->format('l, F j, Y') }}</p>
                <p><strong>Nights:</strong> {{ $booking->nights }}</p>
                <p><strong>Total Paid:</strong> {{ $booking->formatted_total }}</p>
                <p><strong>Payment Method:</strong> {{ ucfirst($booking->pay_method) }}</p>
            </div>

            <h3>🗝️ What's Next?</h3>
            <ul>
                <li>Check-in time: 3:00 PM</li>
                <li>Check-out time: 11:00 AM</li>
                <li>You'll receive check-in instructions 24 hours before arrival</li>
                <li>Free parking is available on-site</li>
            </ul>

            <p>If you have any questions, please don't hesitate to contact us!</p>

            <a href="mailto:{{ config('app.owner_email') }}" class="button">Contact Us</a>
        </div>

        <div class="footer">
            <p>{{ config('app.name') }}</p>
            <p>📞 {{ config('app.owner_phone_no') }} | 📧 {{ config('app.owner_email') }}</p>
        </div>
    </div>
</body>
</html>
