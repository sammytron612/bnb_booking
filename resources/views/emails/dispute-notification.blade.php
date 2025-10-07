<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Dispute Alert</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: white; padding: 30px; border: 1px solid #ddd; }
        .dispute-details { background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107; }
        .booking-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .urgent { background: #f8d7da; border-left: 4px solid #dc3545; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
        .button { background: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
        .tips { background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 Payment Dispute Alert</h1>
            <p>A payment dispute has been filed against one of your bookings</p>
        </div>

        <div class="content">
            <div class="dispute-details @if($dispute->is_urgent) urgent @endif">
                <h3>⚖️ Dispute Details</h3>
                <p><strong>Amount:</strong> {{ $dispute->amount_in_pounds }}</p>
                <p><strong>Status:</strong> {{ $dispute->friendly_status }}</p>
                <p><strong>Reason:</strong> {{ $dispute->friendly_reason }}</p>
                <p><strong>Stripe Dispute ID:</strong> <span class="code">{{ $dispute->stripe_dispute_id }}</span></p>

                @if($dispute->evidence_due_by)
                <p><strong>⏰ Evidence Due:</strong> {{ $dispute->evidence_due_by->format('l, F j, Y \a\t H:i') }}</p>
                <p><strong>Days Remaining:</strong> {{ $dispute->days_until_due }} days</p>

                @if($dispute->is_urgent)
                <p style="color: #dc3545; font-weight: bold;">🚨 URGENT: Evidence due in {{ $dispute->days_until_due }} days or less!</p>
                @endif
                @else
                <p><strong>⏰ Evidence Due:</strong> Not specified</p>
                @endif
            </div>

            <div class="booking-details">
                <h3>📋 Booking Information</h3>
                <p><strong>Guest:</strong> {{ $guest }}</p>
                <p><strong>Booking ID:</strong> {{ $booking->booking_id ?? $booking->id }}</p>
                <p><strong>Check-in:</strong> {{ $booking->check_in ? \Carbon\Carbon::parse($booking->check_in)->format('l, F j, Y') : 'Not set' }}</p>
                <p><strong>Check-out:</strong> {{ $booking->check_out ? \Carbon\Carbon::parse($booking->check_out)->format('l, F j, Y') : 'Not set' }}</p>
                <p><strong>Total Amount:</strong> £{{ number_format(($booking->total_price ?? 0), 2) }}</p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $stripeDisputeUrl }}" class="button">View in Stripe Dashboard</a>
            </div>
        </div>

        <div class="footer">
            <p><em>This dispute was automatically detected and logged in your system. Please respond promptly to protect your revenue.</em></p>
            <p><strong>{{ config('app.name') }}</strong></p>
        </div>
    </div>
</body>
</html>
