<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'stripe_dispute_id',
        'stripe_charge_id',
        'amount',
        'currency',
        'reason',
        'status',
        'evidence_details',
        'evidence_due_by',
        'created_at_stripe',
        'admin_notified',
        'admin_notes'
    ];

    protected $casts = [
        'evidence_details' => 'array',
        'evidence_due_by' => 'datetime',
        'created_at_stripe' => 'datetime',
        'admin_notified' => 'boolean',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the dispute amount in human readable format (pounds)
     */
    public function getAmountInPoundsAttribute(): string
    {
        return '£' . number_format($this->amount / 100, 2);
    }

    /**
     * Get a user-friendly dispute reason
     */
    public function getFriendlyReasonAttribute(): string
    {
        return match($this->reason) {
            'fraudulent' => 'Fraudulent Transaction',
            'unrecognized' => 'Unrecognized Charge',
            'duplicate' => 'Duplicate Charge',
            'subscription_canceled' => 'Subscription Cancelled',
            'product_unacceptable' => 'Product/Service Unacceptable',
            'product_not_received' => 'Product/Service Not Received',
            'general' => 'General Dispute',
            'credit_not_processed' => 'Credit Not Processed',
            default => ucfirst(str_replace('_', ' ', $this->reason))
        };
    }

    /**
     * Get user-friendly status
     */
    public function getFriendlyStatusAttribute(): string
    {
        return match($this->status) {
            'warning_needs_response' => '⚠️ Warning - Needs Response',
            'warning_under_review' => '⚠️ Warning - Under Review',
            'warning_closed' => '✅ Warning - Closed',
            'needs_response' => '🚨 Needs Response',
            'under_review' => '🔍 Under Review',
            'charge_refunded' => '💰 Charge Refunded',
            'won' => '🎉 Won',
            'lost' => '❌ Lost',
            default => ucfirst(str_replace('_', ' ', $this->status))
        };
    }

    /**
     * Check if dispute is urgent (needs response soon)
     */
    public function getIsUrgentAttribute(): bool
    {
        if (!$this->evidence_due_by) {
            return false;
        }

        return now()->diffInDays($this->evidence_due_by, false) <= 2;
    }

    /**
     * Get days until evidence is due
     */
    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->evidence_due_by) {
            return null;
        }

        return max(0, now()->diffInDays($this->evidence_due_by, false));
    }
}
