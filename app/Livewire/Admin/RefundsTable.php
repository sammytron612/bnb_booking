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

    protected $listeners = ['refreshComponent' => '$refresh'];

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

            // Get the payment service via dependency injection
            $paymentService = app(PaymentService::class);

            // Call Stripe API to process the refund
            $result = $paymentService->processRefund(
                $this->selectedBooking->stripe_payment_intent_id,
                $this->refundAmount,
                $reason
            );

            if ($result['success']) {
                Log::info('Refund initiated successfully with Stripe via Livewire', [
                    'booking_id' => $this->selectedBooking->booking_id,
                    'stripe_refund_id' => $result['refund_id'],
                    'amount' => $this->refundAmount
                ]);

                // Send refund notification email to customer
                try {
                    Mail::to($this->selectedBooking->email)->send(
                        new RefundNotification($this->selectedBooking, $this->refundAmount, $reason)
                    );

                    Log::info('Refund notification email sent via Livewire', [
                        'booking_id' => $this->selectedBooking->booking_id,
                        'email' => $this->selectedBooking->email,
                        'amount' => $this->refundAmount
                    ]);
                } catch (\Exception $emailException) {
                    Log::error('Failed to send refund notification email via Livewire', [
                        'booking_id' => $this->selectedBooking->booking_id,
                        'email' => $this->selectedBooking->email,
                        'error' => $emailException->getMessage()
                    ]);
                    // Don't fail the refund process if email fails
                }

                session()->flash('success',
                    "Refund of £{$this->refundAmount} initiated successfully. Database will be updated automatically when Stripe confirms the refund. A confirmation email has been sent to the customer."
                );

                $this->closeRefundModal();
                $this->dispatch('refreshComponent');

            } else {
                Log::error('Failed to initiate refund with Stripe via Livewire', [
                    'booking_id' => $this->selectedBooking->booking_id,
                    'error' => $result['error']
                ]);

                session()->flash('error',
                    'Failed to process refund: ' . $result['error']
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
        $query = Booking::whereIn('status', ['confirmed', 'partial_refund'])
            ->where('is_paid', true)
            ->whereNotNull('stripe_payment_intent_id')
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
            $query->where('status', $this->statusFilter);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.refunds-table', compact('bookings'));
    }
}
