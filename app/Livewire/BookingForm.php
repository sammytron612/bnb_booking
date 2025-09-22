<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use Carbon\Carbon;

class BookingForm extends Component
{
    // Public properties for form data
    public $venueId;
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

    public function mount($venueId, $pricePerNight)
    {
        $this->venueId = $venueId;
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
                'check_in' => $this->checkIn,
                'check_out' => $this->checkOut,
                'venue_id' => $this->venueId,
                'nights' => $this->nights,
                'total_price' => $this->totalPrice,
                'status' => 'pending',
            ]);

            // Generate URL for payment checkout
            $checkoutUrl = route('payment.checkout', ['booking' => $booking->id]);

            // Redirect to Stripe checkout
            return redirect($checkoutUrl);

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
