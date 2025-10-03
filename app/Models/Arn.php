<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arn extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'refund_id',
        'arn_number',
        'refund_amount',
        'currency',
        'status',
        'refund_processed_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'refund_processed_at' => 'datetime',
    ];

    /**
     * Get the booking that owns this ARN record
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
