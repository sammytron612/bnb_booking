<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Review extends Model
{
    protected $fillable = [
        'name',
        'review',
        'rating',
        'booking_id',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * Get the booking that this review belongs to.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    /**
     * Get the reply for this review.
     */
    public function reply(): HasOne
    {
        return $this->hasOne(ReviewReply::class);
    }
}
