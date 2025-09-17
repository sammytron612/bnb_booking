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
        'depart',
        'leave',
        'venue',
        'nights',
        'total_price',
        'status',
        'notes',
        'pay_method',
        'is_paid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'depart' => 'date',
        'leave' => 'date',
        'total_price' => 'decimal:2',
    ];

    /**
     * Calculate the number of nights automatically
     */
    public function calculateNights(): int
    {
        if ($this->depart && $this->leave) {
            return Carbon::parse($this->depart)->diffInDays(Carbon::parse($this->leave));
        }
        return 0;
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
        if ($this->depart && $this->leave) {
            return Carbon::parse($this->depart)->format('M j, Y') . ' - ' . Carbon::parse($this->leave)->format('M j, Y');
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
