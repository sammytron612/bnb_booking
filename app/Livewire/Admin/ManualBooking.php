<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\BookingServices\BookingValidationService;
use Illuminate\Validation\ValidationException;

class ManualBooking extends Component
{
    // Modal state
    public $showModal = false;

    // Form properties
    public $venueId = '';
    public $checkIn = '';
    public $checkOut = '';
    public $guestName = '';
    public $guestEmail = '';
    public $guestPhone = '';
    public $notes = '';
    public $status = 'confirmed';
    public $nights = 0;
    public $totalPrice = 0;
    public $pricePerNight = 0;

    // Available venues and statuses
    public $venues = [];
    public $statuses = [
        'confirmed' => 'Confirmed',
        'pending' => 'Pending',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
        'partial_refund' => 'Partial Refund'
    ];

    // Validation rules
    protected $rules = [
        'venueId' => 'required|exists:venues,id',
        'guestName' => 'required|string|min:2|max:100|regex:/^[a-zA-Z\s\-\'\.]+$/',
        'guestEmail' => 'required|email|max:255',
        'guestPhone' => 'required|string|min:8|max:20|regex:/^[\+]?[0-9\s\-\(\)\.]+$/',
        'checkIn' => 'required|date|after:yesterday',
        'checkOut' => 'required|date|after:checkIn',
        'status' => 'required|in:confirmed,pending,cancelled,refunded,partial_refund',
        'notes' => 'nullable|string|max:1000',
        'totalPrice' => 'required|numeric|min:0|max:50000',
    ];

    protected $messages = [
        // Venue validation
        'venueId.required' => 'Please select a venue.',
        'venueId.exists' => 'Selected venue does not exist.',

        // Guest information validation
        'guestName.required' => 'Please enter the guest name.',
        'guestName.min' => 'Guest name must be at least 2 characters.',
        'guestName.max' => 'Guest name cannot exceed 100 characters.',
        'guestName.regex' => 'Guest name can only contain letters, spaces, hyphens, apostrophes, and periods.',

        'guestEmail.required' => 'Please enter the guest email address.',
        'guestEmail.email' => 'Please enter a valid email address.',
        'guestEmail.max' => 'Email address is too long.',

        'guestPhone.required' => 'Please enter the guest phone number.',
        'guestPhone.min' => 'Phone number must be at least 8 digits.',
        'guestPhone.max' => 'Phone number cannot exceed 20 characters.',
        'guestPhone.regex' => 'Please enter a valid phone number.',

        // Date validation
        'checkIn.required' => 'Please select a check-in date.',
        'checkIn.after' => 'Check-in date must be today or later.',
        'checkOut.required' => 'Please select a check-out date.',
        'checkOut.after' => 'Check-out date must be after check-in date.',

        // Status and price validation
        'status.required' => 'Please select a booking status.',
        'status.in' => 'Invalid booking status selected.',
        'totalPrice.required' => 'Please enter the total price.',
        'totalPrice.numeric' => 'Total price must be a valid number.',
        'totalPrice.min' => 'Total price cannot be negative.',
        'totalPrice.max' => 'Total price cannot exceed £50,000.',

        // Notes validation
        'notes.max' => 'Notes cannot exceed 1000 characters.',
    ];

    public function mount()
    {
        $this->venues = Venue::orderBy('venue_name')->get();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->venueId = '';
        $this->checkIn = '';
        $this->checkOut = '';
        $this->guestName = '';
        $this->guestEmail = '';
        $this->guestPhone = '';
        $this->notes = '';
        $this->status = 'confirmed';
        $this->nights = 0;
        $this->totalPrice = 0;
        $this->pricePerNight = 0;
        $this->resetErrorBag();
    }

    public function updated($propertyName)
    {
        // Real-time calculation when dates or venue change
        if (in_array($propertyName, ['checkIn', 'checkOut', 'venueId'])) {
            $this->calculateBooking();
        }

        // Validate the specific field that was updated
        $this->validateOnly($propertyName);
    }

    public function calculateBooking()
    {
        if ($this->checkIn && $this->checkOut && $this->venueId) {
            try {
                $checkInDate = Carbon::parse($this->checkIn);
                $checkOutDate = Carbon::parse($this->checkOut);

                if ($checkOutDate->gt($checkInDate)) {
                    $this->nights = $checkInDate->diffInDays($checkOutDate);

                    $venue = Venue::find($this->venueId);
                    if ($venue) {
                        $this->pricePerNight = $venue->price;
                        $this->totalPrice = $this->nights * $venue->price;
                    }
                } else {
                    $this->nights = 0;
                    $this->totalPrice = 0;
                }
            } catch (\Exception $e) {
                $this->nights = 0;
                $this->totalPrice = 0;
            }
        } else {
            $this->nights = 0;
            $this->totalPrice = 0;
        }
    }

    public function createBooking()
    {
        // Validate all fields
        $this->validate();

        try {
            // Additional validation for admin bookings
            $checkInDate = Carbon::parse($this->checkIn);
            $checkOutDate = Carbon::parse($this->checkOut);

            // Check minimum stay (allow admins to override this if needed)
            if ($this->nights < 1) {
                $this->addError('checkOut', 'Booking must be at least 1 night.');
                return;
            }

            // Check for date conflicts (unless booking is cancelled/refunded)
            if (in_array($this->status, ['confirmed', 'pending'])) {
                $validationService = app(BookingValidationService::class);
                $validationErrors = $validationService->validateBookingDates(
                    $this->checkIn,
                    $this->checkOut,
                    $this->venueId
                );

                if (!empty($validationErrors)) {
                    session()->flash('booking_error', 'Date conflicts found: ' . implode(' ', $validationErrors));
                    return;
                }
            }

            // Create the booking
            $booking = Booking::create([
                'name' => $this->guestName,
                'email' => $this->guestEmail,
                'phone' => $this->guestPhone,
                'check_in' => $this->checkIn,
                'check_out' => $this->checkOut,
                'nights' => $this->nights,
                'total_price' => $this->totalPrice,
                'venue_id' => $this->venueId,
                'status' => $this->status,
                'notes' => '[MANUAL BOOKING - Created by Admin] ' . ($this->notes ? $this->notes : 'No additional notes.'),
            ]);

            Log::info('Manual booking created by admin', [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_id,
                'venue_id' => $this->venueId,
                'guest_email' => $this->guestEmail,
                'check_in' => $this->checkIn,
                'check_out' => $this->checkOut,
                'status' => $this->status,
                'total_price' => $this->totalPrice
            ]);

            // Store guest name before resetting form
            $guestName = $this->guestName;

            // Close modal and show success message
            $this->closeModal();
            session()->flash('booking_success', 'Manual booking created successfully for ' . $guestName . '.');

            // Emit event to refresh bookings list
            $this->dispatch('bookingCreated');

        } catch (ValidationException $e) {
            // Re-throw validation exceptions to show field errors
            throw $e;
        } catch (\Exception $e) {
            Log::error('Manual booking creation failed: ' . $e->getMessage(), [
                'venue_id' => $this->venueId,
                'guest_email' => $this->guestEmail,
                'check_in' => $this->checkIn,
                'check_out' => $this->checkOut
            ]);

            session()->flash('booking_error', 'Failed to create booking. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.admin.manual-booking');
    }
}
