<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($booking) {
            $booking->generateBookingId();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'name',
        'email',
        'phone',
        'check_in',
        'check_out',
        'venue_id',
        'nights',
        'total_price',
        'status',
        'notes',
        'pay_method',
        'is_paid',
        'stripe_payment_intent_id',
        'stripe_session_id',
        'stripe_amount',
        'stripe_currency',
        'payment_completed_at',
        'stripe_metadata',
        'review_link',
        'check_in_reminder',
        'confirmation_email_sent',
        'refund_amount',
        'refund_reason',
        'refunded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_price' => 'decimal:2',
        'stripe_amount' => 'integer', // Changed from decimal:2 to integer (stores pence)
        'refund_amount' => 'decimal:2',
        'payment_completed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'stripe_metadata' => 'array',
        'review_link' => 'date',
        'check_in_reminder' => 'date',
        'confirmation_email_sent' => 'datetime',
    ];

    /**
     * Generate a unique 7-digit booking ID
     */
    public function generateBookingId(): void
    {
        if (!$this->booking_id) {
            do {
                // Generate a 7-digit number using the record ID and random padding
                $bookingId = $this->generateUniqueBookingNumber();
            } while (self::where('booking_id', $bookingId)->exists());

            $this->update(['booking_id' => $bookingId]);
        }
    }

    /**
     * Generate a 7-digit booking number based on record ID
     */
    private function generateUniqueBookingNumber(): string
    {
        // Take the record ID and pad it to create a 7-digit number
        $idLength = strlen((string) $this->id);

        if ($idLength >= 7) {
            // If ID is already 7+ digits, just use the ID
            return (string) $this->id;
        }

        // Generate random prefix to make it 7 digits
        $prefixLength = 7 - $idLength;
        $prefix = str_pad(rand(1, pow(10, $prefixLength) - 1), $prefixLength, '0', STR_PAD_LEFT);

        return $prefix . $this->id;
    }

    /**
     * Get the booking ID for display (fallback to record ID if no booking_id)
     */
    public function getDisplayBookingId(): string
    {
        $bookingId = $this->booking_id ?? $this->id;
        return 'BNB-' . $bookingId;
    }

    /**
     * Calculate the number of nights automatically
     */
    public function calculateNights(): int
    {
        if ($this->check_in && $this->check_out) {
            return Carbon::parse($this->check_in)->diffInDays(Carbon::parse($this->check_out));
        }
        return 0;
    }

    /**
     * Check if booking has a Stripe payment
     */
    public function hasStripePayment(): bool
    {
        return !empty($this->stripe_payment_intent_id);
    }

    /**
     * Check if Stripe payment is completed
     */
    public function isStripePaymentCompleted(): bool
    {
        return $this->hasStripePayment() && !empty($this->payment_completed_at);
    }

    /**
     * Get Stripe amount in cents
     */
    public function getStripeAmountInCents(): int
    {
        return (int) ($this->total_price * 100);
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalAttribute(): string
    {
        return '£' . number_format((float) $this->total_price, 2);
    }

    /**
     * Get the booking ID for Stripe and emails
     */
    public function getBookingReference(): string
    {
        return $this->booking_id ?? $this->id;
    }

    /**
     * Get formatted date range
     */
    public function getDateRangeAttribute(): string
    {
        if ($this->check_in && $this->check_out) {
            return Carbon::parse($this->check_in)->format('M j, Y') . ' - ' . Carbon::parse($this->check_out)->format('M j, Y');
        }
        return '';
    }

    /**
     * Scope for pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for specific venue
     */
    /**
     * Get the venue that owns the booking.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Scope a query to only include bookings for a specific venue.
     */
    public function scopeForVenue($query, $venueId)
    {
        return $query->where('venue_id', $venueId);
    }

    /**
     * Get the reviews for this booking.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all payment failures for this booking.
     */
    public function paymentFailures(): HasMany
    {
        return $this->hasMany(PaymentFailure::class);
    }

    /**
     * Get the latest payment failure for this booking.
     */
    public function latestPaymentFailure(): HasOne
    {
        return $this->hasOne(PaymentFailure::class)->latest();
    }

    /**
     * Get all ARN records for this booking.
     */
    public function arns()
    {
        return $this->hasMany(Arn::class);
    }

    /**
     * Get the latest ARN for this booking.
     */
    public function latestArn()
    {
        return $this->hasOne(Arn::class)->latest();
    }

    /**
     * Get all disputes for this booking.
     */
    public function disputes()
    {
        return $this->hasMany(BookingDispute::class);
    }

    /**
     * Get the latest dispute for this booking.
     */
    public function latestDispute()
    {
        return $this->hasOne(BookingDispute::class)->latest();
    }
}
