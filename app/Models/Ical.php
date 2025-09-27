<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ical extends Model
{
    protected $table = 'ical'; // Specify the table name

    protected $fillable = [
        'venue_id',
        'url',
        'source',
        'name',
        'active',
        'last_synced_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    /**
     * Get the venue that owns the iCal feed
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Update sync statistics
     */
    public function updateSyncStats($bookingsCount)
    {
        $this->update([
            'last_synced_at' => now(),
        ]);
    }
}
