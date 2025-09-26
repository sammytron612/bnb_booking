<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

class BookingForm extends Component
{
    // Public properties for form data
    public $venueId;
    public $venue;
    public $pricePerNight;
    public $checkIn = null;
    public $checkOut = null;
    public $guestName = '';
    public $guestEmail = '';
    public $guestPhone = '';
    public $nights = 0;
    public $totalPrice = 0;

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

    public function mount($venueId)
    {
        $this->venueId = $venueId;
        $this->venue = Venue::find($venueId);
        // SECURITY: Always use database price, never frontend-provided price
        $this->pricePerNight = $this->venue->price;
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
        if ($this->checkIn && $this->checkOut && $this->venue) {
            $checkInDate = Carbon::parse($this->checkIn);
            $checkOutDate = Carbon::parse($this->checkOut);

            $this->nights = $checkInDate->diffInDays($checkOutDate);

            // Use venue price from database for consistency
            // This ensures client and server calculations always match
            $this->totalPrice = $this->nights * $this->venue->price;
        }
    }    private function resetForm()
    {
        $this->guestName = '';
        $this->guestEmail = '';
        $this->guestPhone = '';
        $this->resetValidation();
    }

    public function submitBooking()
    {
        // Check if booking is enabled for this venue
        if (!$this->venue || !$this->venue->booking_enabled) {
            session()->flash('booking_error', 'Sorry, booking is currently disabled for this property.');
            return;
        }

        $this->validate();

        // SECURITY: Recalculate price server-side before creating booking
        $checkInDate = Carbon::parse($this->checkIn);
        $checkOutDate = Carbon::parse($this->checkOut);
        $calculatedNights = $checkInDate->diffInDays($checkOutDate);
        $calculatedPrice = $calculatedNights * $this->venue->price;

        // Validate that client-side calculations match server-side
        // Allow small tolerance for floating point differences
        if (abs($this->totalPrice - $calculatedPrice) > 0.01 || $this->nights !== $calculatedNights) {
            \Log::warning('Price calculation mismatch in Livewire component', [
                'client_total' => $this->totalPrice,
                'server_total' => $calculatedPrice,
                'client_nights' => $this->nights,
                'server_nights' => $calculatedNights,
                'venue_id' => $this->venueId,
                'venue_price' => $this->venue->price,
                'price_difference' => abs($this->totalPrice - $calculatedPrice),
                'session_id' => session()->getId(),
                'timestamp' => now()->toISOString()
            ]);

            // Instead of blocking, use server-calculated values and log the discrepancy
            // This handles cases where the passed pricePerNight differs from venue->price
            $this->totalPrice = $calculatedPrice;
            $this->nights = $calculatedNights;
        }        try {
            // SECURITY: Use database transaction with locking to prevent race conditions
            $booking = \DB::transaction(function () use ($calculatedNights, $calculatedPrice) {
                // Lock venue to prevent concurrent bookings
                $venue = Venue::where('id', $this->venueId)->lockForUpdate()->first();
                
                if (!$venue) {
                    throw new \Exception('Venue not found');
                }
                
                // Check for booking conflicts within the transaction
                $hasConflict = Booking::where('venue_id', $this->venueId)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            // New booking starts during existing booking
                            $q->where('check_in', '<=', $this->checkIn)
                              ->where('check_out', '>', $this->checkIn);
                        })->orWhere(function ($q) {
                            // New booking ends during existing booking  
                            $q->where('check_in', '<', $this->checkOut)
                              ->where('check_out', '>=', $this->checkOut);
                        })->orWhere(function ($q) {
                            // New booking completely contains existing booking
                            $q->where('check_in', '>=', $this->checkIn)
                              ->where('check_out', '<=', $this->checkOut);
                        })->orWhere(function ($q) {
                            // Existing booking completely contains new booking
                            $q->where('check_in', '<=', $this->checkIn)
                              ->where('check_out', '>=', $this->checkOut);
                        });
                    })->exists();
                
                if ($hasConflict) {
                    throw new \Exception('These dates are no longer available. Please select different dates.');
                }
                
                // Create booking only if no conflicts
                return Booking::create([
                    'name' => $this->guestName,
                    'email' => $this->guestEmail,
                    'phone' => $this->guestPhone,
                    'check_in' => $this->checkIn,
                    'check_out' => $this->checkOut,
                    'venue_id' => $this->venueId,
                    'nights' => $calculatedNights, // Use server-calculated value
                    'total_price' => $calculatedPrice, // Use server-calculated value
                    'status' => 'pending',
                ]);
            }, 3); // Retry 3 times on deadlock

            // Generate signed URL for payment checkout (valid for 24 hours)
            $checkoutUrl = URL::temporarySignedRoute('payment.checkout', now()->addHours(24), ['booking' => $booking->id]);

            // Redirect to Stripe checkout
            return redirect($checkoutUrl);

        } catch (\Exception $e) {
            \Log::warning('Booking creation failed', [
                'venue_id' => $this->venueId,
                'check_in' => $this->checkIn,
                'check_out' => $this->checkOut,
                'error' => $e->getMessage(),
                'session_id' => session()->getId(),
            ]);
            
            session()->flash('booking_error', $e->getMessage() ?: 'Sorry, there was an error processing your booking. Please try again.');
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
