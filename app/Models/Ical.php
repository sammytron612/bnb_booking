<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ical extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'ical';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'venue_id',
        'url',
        'source',
        'name',
        'active',
        'last_synced',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
        'last_synced' => 'datetime',
    ];

    /**
     * Get the venue that owns the ical calendar.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
