<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'check_in',
        'check_out',
        'venue',
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
        'stripe_amount' => 'decimal:2',
        'payment_completed_at' => 'datetime',
        'stripe_metadata' => 'array',
    ];

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
        return (int) ($this->stripe_amount * 100);
    }

    /**
     * Set Stripe amount from cents
     */
    public function setStripeAmountFromCents(int $amountInCents): void
    {
        $this->setAttribute('stripe_amount', $amountInCents / 100);
    }

    /**
     * Get formatted total price
     */
    public function getFormattedTotalAttribute(): string
    {
        return '£' . number_format((float) $this->total_price, 2);
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
    public function scopeForVenue($query, $venue)
    {
        return $query->where('venue', $venue);
    }

    /**
     * Get the reviews for this booking.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
