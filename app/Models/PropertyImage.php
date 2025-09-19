<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'title',
        'location',
        'featured'
    ];

    protected $casts = [
        'featured' => 'boolean'
    ];

    /**
     * Get the venue that owns the property image.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'property_id');
    }
}
