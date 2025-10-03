<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Mail\RefundNotification;
use App\Services\PaymentServices\PaymentService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RefundsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $showRefundModal = false;
    public $selectedBooking;
    public $refundAmount;
    public $refundReason;

    protected $rules = [
        'refundAmount' => 'required|numeric|min:0.01',
        'refundReason' => 'nullable|string|max:255'
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function openRefundModal($bookingId)
    {
        $this->selectedBooking = Booking::with('venue')->find($bookingId);

        if ($this->selectedBooking) {
            // Calculate remaining refundable amount
            $alreadyRefunded = $this->selectedBooking->refund_amount ?? 0;
            $remainingAmount = $this->selectedBooking->total_price - $alreadyRefunded;

            // Set default refund amount to remaining amount
            $this->refundAmount = $remainingAmount;
            $this->refundReason = '';
            $this->showRefundModal = true;
        }
    }

    public function closeRefundModal()
    {
        $this->showRefundModal = false;
        $this->selectedBooking = null;
        $this->refundAmount = null;
        $this->refundReason = '';
        $this->resetValidation();
    }

    public function processRefund()
    {
        if (!$this->selectedBooking) {
            session()->flash('error', 'No booking selected for refund.');
            return;
        }

        // Calculate remaining refundable amount
        $alreadyRefunded = $this->selectedBooking->refund_amount ?? 0;
        $remainingAmount = $this->selectedBooking->total_price - $alreadyRefunded;

        // Update validation rules with dynamic max
        $this->rules['refundAmount'] = "required|numeric|min:0.01|max:{$remainingAmount}";

        $this->validate();

        // Additional safety check
        if ($this->refundAmount > $remainingAmount) {
            session()->flash('error',
                "Refund amount (£{$this->refundAmount}) exceeds remaining refundable amount (£{$remainingAmount})."
            );
            return;
        }

        try {
            $reason = $this->refundReason ?: 'Admin refund';

            Log::info('Admin initiating refund via Livewire', [
                'booking_id' => $this->selectedBooking->booking_id,
                'amount' => $this->refundAmount,
                'admin_reason' => $reason
            ]);

            // Add admin's refund reason to notes field and database immediately
            $adminNote = "Admin refund request: £{$this->refundAmount} - {$reason}";
            $currentNotes = $this->selectedBooking->notes;
            $updatedNotes = $currentNotes ? $currentNotes . "\n" . $adminNote : $adminNote;

            $this->selectedBooking->update([
                'notes' => $updatedNotes,
                'refund_reason' => $reason  // Store admin's refund reason
            ]);

            Log::info('Added admin refund note and reason to booking', [
                'booking_id' => $this->selectedBooking->booking_id,
                'admin_note' => $adminNote,
                'admin_reason' => $reason
            ]);

            // Get the payment service via dependency injection
            $paymentService = app(PaymentService::class);

            // Call Stripe API to process the refund (convert amount to cents)
            // Ensure we're working with a proper decimal number, then convert to integer cents
            $refundAmountInPounds = number_format((float)$this->refundAmount, 2, '.', '');
            $refundAmountInCents = (int)(round((float)$refundAmountInPounds * 100));

            Log::info('Refund amount conversion', [
                'input_raw' => $this->refundAmount,
                'input_type' => gettype($this->refundAmount),
                'pounds_formatted' => $refundAmountInPounds,
                'cents_calculated' => $refundAmountInCents,
                'booking_id' => $this->selectedBooking->booking_id
            ]);

            $result = $paymentService->processRefund(
                $this->selectedBooking->stripe_payment_intent_id,
                $refundAmountInCents
            );

            if ($result) {
                Log::info('Refund initiated successfully with Stripe via Livewire', [
                    'booking_id' => $this->selectedBooking->booking_id,
                    'amount' => $this->refundAmount
                ]);

                // Send refund notification email to customer
                try {
                    Log::info('Attempting to send refund notification email', [
                        'booking_id' => $this->selectedBooking->booking_id,
                        'customer_email' => $this->selectedBooking->email,
                        'customer_name' => $this->selectedBooking->name,
                        'refund_amount' => $this->refundAmount,
                        'refund_reason' => $reason,
                        'mail_driver' => config('mail.default'),
                        'booking_has_venue' => !is_null($this->selectedBooking->venue),
                        'venue_name' => $this->selectedBooking->venue->venue_name ?? 'No venue loaded'
                    ]);

                    // Create and send the email
                    $refundNotification = new RefundNotification($this->selectedBooking, $this->refundAmount, $reason);
                    Mail::to($this->selectedBooking->email)->send($refundNotification);

                    Log::info('Refund notification email sent successfully', [
                        'booking_id' => $this->selectedBooking->booking_id,
                        'email' => $this->selectedBooking->email,
                        'amount' => $this->refundAmount
                    ]);

                    session()->flash('success', 'Refund processed successfully and notification email sent to customer.');
                } catch (\Exception $emailException) {
                    Log::error('Failed to send refund notification email', [
                        'booking_id' => $this->selectedBooking->booking_id,
                        'email' => $this->selectedBooking->email,
                        'error_message' => $emailException->getMessage(),
                        'error_file' => $emailException->getFile(),
                        'error_line' => $emailException->getLine(),
                        'error_trace' => $emailException->getTraceAsString()
                    ]);
                    session()->flash('warning', 'Refund processed successfully, but failed to send notification email to customer. Check logs for details.');
                    // Don't fail the refund process if email fails
                }

                session()->flash('success',
                    "Refund of £{$this->refundAmount} initiated successfully. Database will be updated automatically when Stripe confirms the refund. A confirmation email has been sent to the customer."
                );

                $this->closeRefundModal();

                // Refresh the component to show updated data
                $this->resetPage(); // Reset pagination to show latest data
                $this->dispatch('$refresh'); // Force component refresh

            } else {
                Log::error('Failed to initiate refund with Stripe via Livewire', [
                    'booking_id' => $this->selectedBooking->booking_id,
                    'payment_intent_id' => $this->selectedBooking->stripe_payment_intent_id,
                    'amount' => $this->refundAmount
                ]);

                session()->flash('error',
                    'Failed to process refund. Please check the logs for more details.'
                );
            }

        } catch (\Exception $e) {
            Log::error('Exception during refund process via Livewire', [
                'booking_id' => $this->selectedBooking->booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error',
                'An error occurred while processing the refund. Please try again.'
            );
        }
    }

    public function render()
    {
        $query = Booking::where('is_paid', true)
            ->whereNotNull('stripe_payment_intent_id')
            ->whereIn('status', ['confirmed', 'partial_refund', 'refunded'])
            ->with(['venue']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('booking_id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('venue', function ($venueQuery) {
                      $venueQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter) {
            if ($this->statusFilter === 'fully_refunded') {
                // Show only bookings where refund_amount equals total_price
                $query->where('status', 'refunded')
                      ->whereRaw('COALESCE(refund_amount, 0) >= total_price');
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.refunds-table', compact('bookings'));
    }
}
