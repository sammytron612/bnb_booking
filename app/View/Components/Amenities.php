<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Amenity;

class Amenities extends Component
{
    public $venueId;
    public $amenities;
    public $displayAmenities;
    public $theme_color;
    public $hasMore;

    /**
     * Create a new component instance.
     */
    public function __construct($venueId = null, $theme_color = 'blue')
    {
        $this->venueId = $venueId;
        $this->theme_color = $theme_color;

        if ($venueId) {
            $this->amenities = Amenity::where('venue_id', $venueId)->get();
            $this->displayAmenities = $this->amenities->take(6);
            $this->hasMore = $this->amenities->count() > 6;
        } else {
            $this->amenities = collect();
            $this->displayAmenities = collect();
            $this->hasMore = false;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.amenities');
    }
}
