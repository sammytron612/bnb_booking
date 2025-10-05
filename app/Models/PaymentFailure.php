<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentFailure extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'stripe_payment_intent_id',
        'stripe_session_id',
        'decline_code',
        'failure_reason',
        'attempted_amount',
        'currency',
        'payment_method',
        'stripe_error_data',
        'failed_at'
    ];

    protected $casts = [
        'stripe_error_data' => 'array',
        'failed_at' => 'datetime',
        'attempted_amount' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the attempted amount in human readable format (pounds)
     */
    public function getAttemptedAmountInPoundsAttribute(): string
    {
        return number_format($this->attempted_amount / 100, 2);
    }

    /**
     * Get a user-friendly decline reason
     */
    public function getFriendlyDeclineReasonAttribute(): string
    {
        return match($this->decline_code) {
            'generic_decline' => 'Card was declined',
            'insufficient_funds' => 'Insufficient funds',
            'expired_card' => 'Card has expired',
            'incorrect_cvc' => 'Incorrect security code',
            'processing_error' => 'Processing error - please try again',
            'fraudulent' => 'Transaction blocked for security',
            default => 'Payment failed'
        };
    }
}
