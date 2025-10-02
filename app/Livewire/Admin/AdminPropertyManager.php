<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Venue;
use App\Models\PropertyImage;
use App\Models\Amenity;
use Illuminate\Support\Facades\Storage;

class AdminPropertyManager extends Component
{
    use WithFileUploads;

    // Property selection
    public $selectedVenue = null;
    public $venues = [];

    // Property editing
    public $venueName = '';
    public $venueDescription1 = '';
    public $venueDescription2 = '';
    public $venueDescription3 = '';
    public $venuePrice = '';
    public $venueAddress1 = '';
    public $venueAddress2 = '';
    public $venuePostcode = '';
    public $venueGuestCapacity = '';
    public $venueInstructions = '';
    public $venueBookingEnabled = true;

    // Image management
    public $newImages = [];
    public $existingImages = [];
    public $editingImageId = null;
    public $editingImageTitle = '';

    // Amenities management
    public $venueAmenities = [];
    public $newAmenityTitle = '';
    public $newAmenitySvg = '';
    public $editingAmenityId = null;
    public $editingAmenityTitle = '';
    public $editingAmenitySvg = '';

    public function mount()
    {
        $this->venues = Venue::with(['propertyImages', 'amenities'])->get();
    }

    public function selectVenue($venueId)
    {
        $this->selectedVenue = Venue::with(['propertyImages', 'amenities'])->find($venueId);

        if ($this->selectedVenue) {
            $this->venueName = $this->selectedVenue->venue_name;
            $this->venueDescription1 = $this->selectedVenue->description1;
            $this->venueDescription2 = $this->selectedVenue->description2;
            $this->venueDescription3 = $this->selectedVenue->description3;
            $this->venuePrice = $this->selectedVenue->price;
            $this->venueAddress1 = $this->selectedVenue->address1;
            $this->venueAddress2 = $this->selectedVenue->address2;
            $this->venuePostcode = $this->selectedVenue->postcode;
            $this->venueGuestCapacity = $this->selectedVenue->guest_capacity;
            $this->venueInstructions = $this->selectedVenue->instructions;
            $this->venueBookingEnabled = $this->selectedVenue->booking_enabled;
            $this->existingImages = $this->selectedVenue->propertyImages;
            $this->venueAmenities = $this->selectedVenue->amenities;
        }
    }

    public function updateVenue()
    {
        $this->validate([
            'venueName' => 'required|string|max:255',
            'venueDescription1' => 'nullable|string',
            'venueDescription2' => 'nullable|string',
            'venueDescription3' => 'nullable|string',
            'venuePrice' => 'required|numeric|min:0',
            'venueAddress1' => 'required|string|max:255',
            'venueAddress2' => 'nullable|string|max:255',
            'venuePostcode' => 'required|string|max:20',
            'venueGuestCapacity' => 'nullable|integer|min:1|max:20',
            'venueInstructions' => 'nullable|string',
            'venueBookingEnabled' => 'boolean',
        ]);

        if ($this->selectedVenue) {
            $this->selectedVenue->update([
                'venue_name' => $this->venueName,
                'description1' => $this->venueDescription1,
                'description2' => $this->venueDescription2,
                'description3' => $this->venueDescription3,
                'price' => $this->venuePrice,
                'address1' => $this->venueAddress1,
                'address2' => $this->venueAddress2,
                'postcode' => $this->venuePostcode,
                'guest_capacity' => $this->venueGuestCapacity,
                'instructions' => $this->venueInstructions,
                'booking_enabled' => $this->venueBookingEnabled,
            ]);

            session()->flash('message', 'Venue updated successfully!');
            $this->venues = Venue::with('propertyImages')->get();
        }
    }

    public function toggleBookingEnabled()
    {
        if ($this->selectedVenue) {
            $this->venueBookingEnabled = !$this->venueBookingEnabled;

            $this->selectedVenue->update([
                'booking_enabled' => $this->venueBookingEnabled,
            ]);

            $status = $this->venueBookingEnabled ? 'enabled' : 'disabled';
            session()->flash('message', "Booking {$status} for {$this->selectedVenue->venue_name}!");

            // Refresh venues list
            $this->venues = Venue::with('propertyImages')->get();
        }
    }

    public function uploadImages()
    {
        $this->validate([
            'newImages.*' => 'image|max:2048', // 2MB Max
        ]);

        if ($this->selectedVenue && $this->newImages) {
            foreach ($this->newImages as $image) {
                $path = $image->store('property-images', 'public');

                PropertyImage::create([
                    'property_id' => $this->selectedVenue->id,
                    'location' => '/storage/' . $path,
                    'title' => 'Property Image',
                    'featured' => false,
                ]);
            }

            $this->newImages = [];
            $this->selectedVenue = Venue::with('propertyImages')->find($this->selectedVenue->id);
            $this->existingImages = $this->selectedVenue->propertyImages;

            session()->flash('message', 'Images uploaded successfully!');
        }
    }

    public function deleteImage($imageId)
    {
        $image = PropertyImage::find($imageId);

        if ($image) {
            // Delete file from storage
            $path = str_replace('/storage/', '', $image->location);
            Storage::disk('public')->delete($path);

            // Delete from database
            $image->delete();

            // Refresh images
            if ($this->selectedVenue) {
                $this->selectedVenue = Venue::with('propertyImages')->find($this->selectedVenue->id);
                $this->existingImages = $this->selectedVenue->propertyImages;
            }

            session()->flash('message', 'Image deleted successfully!');
        }
    }

    public function toggleFeatured($imageId)
    {
        if ($this->selectedVenue) {
            // Remove featured from all images of this venue
            PropertyImage::where('property_id', $this->selectedVenue->id)
                ->update(['featured' => false]);

            // Set the selected image as featured
            PropertyImage::where('id', $imageId)->update(['featured' => true]);

            // Refresh images
            $this->selectedVenue = Venue::with('propertyImages')->find($this->selectedVenue->id);
            $this->existingImages = $this->selectedVenue->propertyImages;

            session()->flash('message', 'Featured image updated!');
        }
    }

    public function startEditingImageTitle($imageId, $currentTitle)
    {
        $this->editingImageId = $imageId;
        $this->editingImageTitle = $currentTitle;
    }

    public function updateImageTitle()
    {
        $this->validate([
            'editingImageTitle' => 'required|string|max:255',
        ]);

        if ($this->editingImageId) {
            PropertyImage::where('id', $this->editingImageId)
                ->update(['title' => $this->editingImageTitle]);

            // Refresh images
            if ($this->selectedVenue) {
                $this->selectedVenue = Venue::with('propertyImages')->find($this->selectedVenue->id);
                $this->existingImages = $this->selectedVenue->propertyImages;
            }

            $this->editingImageId = null;
            $this->editingImageTitle = '';

            session()->flash('message', 'Image title updated successfully!');
        }
    }

    public function cancelEditingImageTitle()
    {
        $this->editingImageId = null;
        $this->editingImageTitle = '';
    }

    // Amenities Management Methods
    public function addAmenity()
    {
        $this->validate([
            'newAmenityTitle' => 'required|string|max:255',
            'newAmenitySvg' => 'nullable|string',
        ]);

        if ($this->selectedVenue) {
            Amenity::create([
                'venue_id' => $this->selectedVenue->id,
                'title' => $this->newAmenityTitle,
                'svg' => $this->newAmenitySvg,
            ]);

            $this->newAmenityTitle = '';
            $this->newAmenitySvg = '';
            $this->refreshVenueData();
            session()->flash('message', 'Amenity added successfully!');
        }
    }

    public function startEditingAmenity($amenityId, $title, $svg)
    {
        $this->editingAmenityId = $amenityId;
        $this->editingAmenityTitle = $title;
        $this->editingAmenitySvg = $svg;
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
            ]);

            $this->editingAmenityId = null;
            $this->editingAmenityTitle = '';
            $this->editingAmenitySvg = '';
            $this->refreshVenueData();
            session()->flash('message', 'Amenity updated successfully!');
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
            $this->refreshVenueData();
            session()->flash('message', 'Amenity deleted successfully!');
        }
    }

    private function refreshVenueData()
    {
        if ($this->selectedVenue) {
            $this->selectedVenue = Venue::with(['propertyImages', 'amenities'])->find($this->selectedVenue->id);
            $this->existingImages = $this->selectedVenue->propertyImages;
            $this->venueAmenities = $this->selectedVenue->amenities;
            $this->venues = Venue::with(['propertyImages', 'amenities'])->get();
        }
    }

    public function render()
    {
        return view('livewire.admin.admin-property-manager');
    }
}
