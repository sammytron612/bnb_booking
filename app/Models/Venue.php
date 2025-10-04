<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    /**
     * Disable timestamps
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'venue_name',
        'description1',
        'description2',
        'description3',
        'instructions',
        'guest_capacity',
        'price',
        'address1',
        'address2',
        'postcode',
        'theme_color',
        'route',
        'badge_text',
        'booking_enabled',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the property images for the venue.
     */
    public function propertyImages()
    {
        return $this->hasMany(PropertyImage::class, 'property_id');
    }

    /**
     * Get the featured property image for the venue.
     */
    public function featuredImage()
    {
        return $this->hasOne(PropertyImage::class, 'property_id')->where('featured', true);
    }

    /**
     * Get the amenities for the venue.
     */
    public function amenities()
    {
        return $this->hasMany(Amenity::class);
    }

    /**
     * Get only the active amenities for the venue (for frontend display).
     */
    public function activeAmenities()
    {
        return $this->hasMany(Amenity::class)->where('active', true);
    }

    /**
     * Get the bookings for the venue.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
