<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ical extends Model
{
    protected $table = 'ical';

    protected $fillable = [
        'venue_id',
        'url',
        'source',
        'name',
        'active',
        'last_synced_at'
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_synced_at' => 'datetime'
    ];

    /**
     * Get the venue that this iCal feed belongs to
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Check if this feed is active and ready for sync
     */
    public function isActive(): bool
    {
        return $this->active && !empty($this->url);
    }

    /**
     * Update sync timestamp
     */
    public function updateSyncStats(int $bookingsCount = 0): void
    {
        $this->update([
            'last_synced_at' => now()
        ]);
    }
}
