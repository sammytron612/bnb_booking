<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'venue_id',
        'title',
        'svg',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Get the venue that owns the amenity.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
