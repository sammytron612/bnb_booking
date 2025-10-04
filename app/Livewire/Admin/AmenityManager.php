<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Amenity;
use App\Models\Venue;

class AmenityManager extends Component
{
    // Venue property
    public $venue = null;
    public $venueId = null;

    // Amenities management
    public $venueAmenities = [];
    public $newAmenityTitle = '';
    public $newAmenitySvg = '';
    public $newAmenityActive = true;
    public $editingAmenityId = null;
    public $editingAmenityTitle = '';
    public $editingAmenitySvg = '';
    public $editingAmenityActive = true;

    protected $listeners = ['venueSelected' => 'handleVenueSelection'];

    public function mount($venueId = null)
    {
        if ($venueId) {
            $this->venueId = $venueId;
            $this->loadVenueAmenities();
        }
    }

    public function handleVenueSelection($venueId)
    {
        $this->venueId = $venueId;
        $this->loadVenueAmenities();
        $this->resetForm();
    }

    public function loadVenueAmenities()
    {
        if ($this->venueId) {
            $this->venue = Venue::with('amenities')->find($this->venueId);
            $this->venueAmenities = $this->venue ? $this->venue->amenities : collect();
        } else {
            $this->venueAmenities = collect();
        }
    }

    public function addAmenity()
    {
        $this->validate([
            'newAmenityTitle' => 'required|string|max:255',
            'newAmenitySvg' => 'nullable|string',
        ]);

        if ($this->venueId) {
            Amenity::create([
                'venue_id' => $this->venueId,
                'title' => $this->newAmenityTitle,
                'svg' => $this->newAmenitySvg,
                'active' => $this->newAmenityActive,
            ]);

            $this->newAmenityTitle = '';
            $this->newAmenitySvg = '';
            $this->newAmenityActive = true;
            $this->loadVenueAmenities();

            // Dispatch event to notify parent component
            $this->dispatch('amenityUpdated');
            session()->flash('amenity_message', 'Amenity added successfully!');
        }
    }

    public function startEditingAmenity($amenityId, $title, $svg, $active = true)
    {
        $this->editingAmenityId = $amenityId;
        $this->editingAmenityTitle = $title;
        $this->editingAmenitySvg = $svg;
        $this->editingAmenityActive = $active;
    }

    public function updateAmenity()
    {
        $this->validate([
            'editingAmenityTitle' => 'required|string|max:255',
            'editingAmenitySvg' => 'nullable|string',
        ]);

        if ($this->editingAmenityId) {
            Amenity::where('id', $this->editingAmenityId)->update([
                'title' => $this->editingAmenityTitle,
                'svg' => $this->editingAmenitySvg,
                'active' => $this->editingAmenityActive,
            ]);

            $this->editingAmenityId = null;
            $this->editingAmenityTitle = '';
            $this->editingAmenitySvg = '';
            $this->editingAmenityActive = true;
            $this->loadVenueAmenities();

            // Dispatch event to notify parent component
            $this->dispatch('amenityUpdated');
            session()->flash('amenity_message', 'Amenity updated successfully!');
        }
    }

    public function cancelEditingAmenity()
    {
        $this->editingAmenityId = null;
        $this->editingAmenityTitle = '';
        $this->editingAmenitySvg = '';
    }

    public function deleteAmenity($amenityId)
    {
        $amenity = Amenity::find($amenityId);
        if ($amenity) {
            $amenity->delete();
            $this->loadVenueAmenities();

            // Dispatch event to notify parent component
            $this->dispatch('amenityUpdated');
            session()->flash('amenity_message', 'Amenity deleted successfully!');
        }
    }

    private function resetForm()
    {
        $this->newAmenityTitle = '';
        $this->newAmenitySvg = '';
        $this->newAmenityActive = true;
        $this->editingAmenityId = null;
        $this->editingAmenityTitle = '';
        $this->editingAmenitySvg = '';
        $this->editingAmenityActive = true;
    }

    public function render()
    {
        return view('livewire.admin.amenity-manager');
    }
}
