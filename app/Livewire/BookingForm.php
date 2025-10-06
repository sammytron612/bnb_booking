<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Services\BookingServices\BookingValidationService;
use Illuminate\Validation\ValidationException;

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
    'guestName' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s\-\'\.]+$/',
    'guestEmail' => 'required|email|max:255',
    'guestPhone' => 'required|string|min:8|max:20|regex:/^[\+]?[0-9\s\-\(\)\.]+$/',
    'checkIn' => 'required|date|after_or_equal:today|before:+2 years',
    'checkOut' => 'required|date|after:checkIn|after_or_equal:checkIn,+2 days|before:+2 years',
    ];

    protected $messages = [
        // Name validation messages
        'guestName.required' => 'Please enter your full name.',
        'guestName.min' => 'Name must be at least 2 characters.',
        'guestName.max' => 'Name cannot exceed 100 characters.',
        'guestName.regex' => 'Name can only contain letters, spaces, hyphens, apostrophes, and periods.',

        // Email validation messages
        'guestEmail.required' => 'Please enter your email address.',
        'guestEmail.email' => 'Please enter a valid email address.',
        'guestEmail.max' => 'Email address is too long.',

        // Phone validation messages
        'guestPhone.required' => 'Please enter your phone number.',
        'guestPhone.min' => 'Phone number must be at least 8 digits.',
        'guestPhone.max' => 'Phone number cannot exceed 20 characters.',
        'guestPhone.regex' => 'Please enter a valid phone number (numbers, spaces, dashes, and parentheses only).',

        // Date validation messages
        'checkIn.required' => 'Please select a check-in date.',
        'checkIn.after_or_equal' => 'Check-in date cannot be in the past.',
        'checkIn.before' => 'Check-in date cannot be more than 2 years in advance.',
        'checkOut.required' => 'Please select a check-out date.',
        'checkOut.after' => 'Check-out date must be after check-in date.',
        'checkOut.after_or_equal' => 'Minimum stay is 2 nights. Please select a check-out date at least 2 days after check-in.',
        'checkOut.before' => 'Check-out date cannot be more than 2 years in advance.',
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

    // Add sanitization method
    private function sanitizeInputs()
    {
        $this->guestName = trim($this->guestName);
        $this->guestEmail = trim(strtolower($this->guestEmail));
        $this->guestPhone = trim($this->guestPhone);
    }

    public function submitBooking()
    {
        // Rate limiting check
        $cacheKey = 'booking_attempt_' . request()->ip();
        $attempts = cache()->get($cacheKey, 0);

        if ($attempts >= 5) {
            session()->flash('booking_error', 'Too many booking attempts. Please wait 10 minutes before trying again.');
            return;
        }

        cache()->put($cacheKey, $attempts + 1, now()->addMinutes(10));

        // Sanitize inputs before validation
        $this->sanitizeInputs();

        // Log that the method is being called
        \Log::info('submitBooking method called');

        // Check if booking is enabled for this venue
        if (!$this->venue || !$this->venue->booking_enabled) {
            \Log::warning('Booking disabled for venue: ' . ($this->venue ? $this->venue->id : 'null'));
            session()->flash('booking_error', 'Sorry, booking is currently disabled for this property.');
            return;
        }

        // Add honeypot check (add hidden field in blade)
        if (request()->filled('website')) { // Honeypot field
            \Log::warning('Bot detected via honeypot field', ['ip' => request()->ip()]);
            session()->flash('booking_error', 'Please check your booking details and try again.');
            return;
        }

        // Log validation attempt
        \Log::info('Attempting validation for booking');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            // Let validation exceptions bubble up to show specific field errors
            \Log::info('Validation failed with specific errors', ['errors' => $e->validator->errors()->toArray()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Unexpected validation error: ' . $e->getMessage());
            session()->flash('booking_error', 'Please check your booking details and try again.');
            return;
        }

        // Enhanced security validation
        try {
            // 1. Validate dates are not too far in future
            $checkInDate = Carbon::parse($this->checkIn);
            $checkOutDate = Carbon::parse($this->checkOut);

            if ($checkInDate->gt(now()->addYears(2)) || $checkOutDate->gt(now()->addYears(2))) {
                session()->flash('booking_error', 'Booking dates cannot be more than 2 years in advance.');
                return;
            }

            // 2. Validate maximum stay (prevent extremely long bookings)
            $calculatedNights = $checkInDate->diffInDays($checkOutDate);
            if ($calculatedNights > 365) {
                session()->flash('booking_error', 'Maximum stay is 365 nights.');
                return;
            }

            // 3. Check for duplicate booking attempt
            $recentBooking = Booking::where('email', $this->guestEmail)
                ->where('venue_id', $this->venueId)
                ->where('check_in', $this->checkIn)
                ->where('check_out', $this->checkOut)
                ->where('created_at', '>', now()->subMinutes(5))
                ->first();

            if ($recentBooking) {
                session()->flash('booking_error', 'A similar booking was recently submitted. Please check your email or wait a few minutes before trying again.');
                return;
            }

            // 4. Validate venue exists and price matches
            $venue = Venue::findOrFail($this->venueId);

            // Debug price comparison
            \Log::info('Price comparison debug', [
                'venue_id' => $this->venueId,
                'frontend_price' => $this->pricePerNight,
                'frontend_price_type' => gettype($this->pricePerNight),
                'database_price' => $venue->price,
                'database_price_type' => gettype($venue->price),
                'are_equal' => ($this->pricePerNight == $venue->price),
                'strict_equal' => ($this->pricePerNight === $venue->price)
            ]);

            // Use loose comparison to handle string/float differences
            if ((float)$this->pricePerNight != (float)$venue->price) {
                \Log::warning('Price manipulation detected', [
                    'venue_id' => $this->venueId,
                    'submitted_price' => $this->pricePerNight,
                    'actual_price' => $venue->price
                ]);
                session()->flash('booking_error', 'Invalid pricing. Please refresh and try again.');
                return;
            }

            // 2. Calculate server-side values first
            $calculatedNights = Carbon::parse($this->checkIn)->diffInDays(Carbon::parse($this->checkOut));
            $calculatedTotal = $calculatedNights * $venue->price;

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
            \Log::info('About to redirect to checkout', [
                'booking_id' => $booking->id,
                'checkout_url' => $checkoutUrl
            ]);

            // Clear rate limiting on successful booking
            cache()->forget($cacheKey);

            redirect()->to($checkoutUrl);

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
