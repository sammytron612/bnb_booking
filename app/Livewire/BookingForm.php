<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use App\Services\BookingServices\BookingValidationService;

class BookingForm extends Component
{
    // Public properties for form data
    public $venueId;
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

    public function mount($venueId, $pricePerNight)
    {
        $this->venueId = $venueId;
        $this->venue = Venue::find($venueId);
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
        // Log that the method is being called
        \Log::info('submitBooking method called');

        // Check if booking is enabled for this venue
        if (!$this->venue || !$this->venue->booking_enabled) {
            \Log::warning('Booking disabled for venue: ' . ($this->venue ? $this->venue->id : 'null'));
            session()->flash('booking_error', 'Sorry, booking is currently disabled for this property.');
            return;
        }

        // Log validation attempt
        \Log::info('Attempting validation for booking');

        try {
            $this->validate();
        } catch (\Exception $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            session()->flash('booking_error', 'Please check your booking details and try again.');
            return;
        }

        // SECURITY: Additional server-side validation
        try {
            // 1. Validate venue exists and price matches
            $venue = Venue::findOrFail($this->venueId);
            if ($this->pricePerNight != $venue->price_per_night) {
                \Log::warning('Price manipulation detected', [
                    'venue_id' => $this->venueId,
                    'submitted_price' => $this->pricePerNight,
                    'actual_price' => $venue->price_per_night
                ]);
                session()->flash('booking_error', 'Invalid pricing. Please refresh and try again.');
                return;
            }

            // 2. Calculate server-side values first
            $calculatedNights = Carbon::parse($this->checkIn)->diffInDays(Carbon::parse($this->checkOut));
            $calculatedTotal = $calculatedNights * $venue->price_per_night;

            // 3. Use comprehensive validation service (includes external iCal checking)
            $validationService = app(BookingValidationService::class);
            $validationErrors = $validationService->validateBookingDates(
                $this->checkIn,
                $this->checkOut,
                $this->venueId
            );

            if (!empty($validationErrors)) {
                \Log::warning('Booking validation failed', [
                    'venue_id' => $this->venueId,
                    'check_in' => $this->checkIn,
                    'check_out' => $this->checkOut,
                    'errors' => $validationErrors
                ]);
                session()->flash('booking_error', implode(' ', $validationErrors));
                return;
            }

            // 4. Validate calculated totals match server-side calculation
            if ($this->nights != $calculatedNights || $this->totalPrice != $calculatedTotal) {
                \Log::warning('Price calculation manipulation detected', [
                    'venue_id' => $this->venueId,
                    'submitted_nights' => $this->nights,
                    'calculated_nights' => $calculatedNights,
                    'submitted_total' => $this->totalPrice,
                    'calculated_total' => $calculatedTotal
                ]);
                session()->flash('booking_error', 'Invalid calculation. Please refresh and try again.');
                return;
            }

            // 5. Validate minimum stay requirement
            if ($calculatedNights < 2) {
                session()->flash('booking_error', 'Minimum stay is 2 nights.');
                return;
            }

            // All validations passed - create booking with server-calculated values
            $booking = Booking::create([
                'name' => $this->guestName,
                'email' => $this->guestEmail,
                'phone' => $this->guestPhone,
                'check_in' => $this->checkIn,
                'check_out' => $this->checkOut,
                'venue_id' => $this->venueId,
                'nights' => $calculatedNights, // Use server-calculated value
                'total_price' => $calculatedTotal, // Use server-calculated value
                'status' => 'pending',
            ]);

            // Generate signed URL for payment checkout (valid for 24 hours)
            $checkoutUrl = URL::temporarySignedRoute('payment.checkout', now()->addHours(24), ['booking' => $booking->id]);

            // Log for debugging
            \Log::info('Booking created with ID: ' . $booking->id);
            \Log::info('Checkout URL generated: ' . $checkoutUrl);

            // Try using redirectRoute with the URL directly
            return $this->js('window.location.href = "' . $checkoutUrl . '"');

        } catch (\Exception $e) {
            \Log::error('Booking submission error: ' . $e->getMessage());
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
