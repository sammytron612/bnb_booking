<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use Carbon\Carbon;

class BookingForm extends Component
{
    // Public properties for form data
    public $venue;
    public $checkIn = null;
    public $checkOut = null;
    public $guestName = '';
    public $guestEmail = '';
    public $guestPhone = '';
    public $nights = 0;
    public $totalPrice = 0;
    public $pricePerNight;

    // Validation rules
    protected $rules = [
        'guestName' => 'required|string|min:2',
        'guestEmail' => 'required|email',
        'guestPhone' => 'required|string|min:10',
        'checkIn' => 'required|date|after_or_equal:today',
        'checkOut' => 'required|date|after:checkIn',
    ];

    protected $messages = [
        'guestName.required' => 'Please enter your full name.',
        'guestEmail.required' => 'Please enter your email address.',
        'guestEmail.email' => 'Please enter a valid email address.',
        'guestPhone.required' => 'Please enter your phone number.',
        'checkIn.required' => 'Please select a check-in date.',
        'checkOut.required' => 'Please select a check-out date.',
        'checkOut.after' => 'Check-out date must be after check-in date.',
    ];

    // Listen for events from JavaScript
    protected $listeners = [
        'datesSelected' => 'updateDates',
        'datesCleared' => 'clearDates'
    ];

    public function mount($venue, $pricePerNight)
    {
        $this->venue = $venue;
        $this->pricePerNight = $pricePerNight;
    }

    // Method to receive dates from JavaScript calendar
    public function updateDates($checkIn, $checkOut)
    {
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->calculateBooking();
    }

    public function clearDates()
    {
        $this->checkIn = null;
        $this->checkOut = null;
        $this->nights = 0;
        $this->totalPrice = 0;
        $this->resetForm();
    }

    public function clearSelection()
    {
        $this->clearDates();
        // Emit event to clear JavaScript calendar
        $this->dispatch('clearCalendarSelection');
    }

    private function calculateBooking()
    {
        if ($this->checkIn && $this->checkOut) {
            $checkInDate = Carbon::parse($this->checkIn);
            $checkOutDate = Carbon::parse($this->checkOut);

            $this->nights = $checkInDate->diffInDays($checkOutDate);
            $this->totalPrice = $this->nights * $this->pricePerNight;
        }
    }

    private function resetForm()
    {
        $this->guestName = '';
        $this->guestEmail = '';
        $this->guestPhone = '';
        $this->resetValidation();
    }

    public function submitBooking()
    {
        $this->validate();

        try {
            $booking = Booking::create([
                'name' => $this->guestName,
                'email' => $this->guestEmail,
                'phone' => $this->guestPhone,
                'depart' => $this->checkIn,
                'leave' => $this->checkOut,
                'venue' => $this->venue,
                'nights' => $this->nights,
                'total_price' => $this->totalPrice,
                'status' => 'pending',
            ]);

            // Success message
            session()->flash('booking_success', 'Booking request submitted successfully! We will contact you shortly to confirm your reservation.');

            // Clear form
            $this->resetForm();
            $this->clearDates();

            // Emit success event to close modal
            $this->dispatch('bookingSubmitted', bookingId: $booking->id);

        } catch (\Exception $e) {
            session()->flash('booking_error', 'Sorry, there was an error processing your booking. Please try again.');
        }
    }

    // Computed properties for the view
    public function getFormattedCheckInProperty()
    {
        return $this->checkIn ? Carbon::parse($this->checkIn)->format('M j, Y') : '—';
    }

    public function getFormattedCheckOutProperty()
    {
        return $this->checkOut ? Carbon::parse($this->checkOut)->format('M j, Y') : '—';
    }

    public function getFormattedTotalProperty()
    {
        return '£' . number_format($this->totalPrice, 0);
    }

    public function getHasValidDatesProperty()
    {
        return $this->checkIn && $this->checkOut && $this->nights >= 2;
    }

    public function render()
    {
        return view('livewire.booking-form');
    }
}
