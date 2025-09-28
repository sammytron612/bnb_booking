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
     * Get the secure URL for this image
     */
    public function getSecureUrlAttribute()
    {
        return secure_image_url($this->location);
    }

    /**
     * Get the secure admin URL for this image
     */
    public function getSecureAdminUrlAttribute()
    {
        return secure_image_url($this->location, true);
    }

    /**
     * Get the venue that owns the property image.
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'property_id');
    }
}
