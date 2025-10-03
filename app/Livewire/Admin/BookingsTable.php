<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class BookingsTable extends Component
{
    use WithPagination;

    public $sortBy = 'check_in';
    public $sortDirection = 'desc';
    public $perPage = 15;
    public $search = '';
    public $statusFilter = '';
    public $activeView = 'table'; // 'table' or 'calendar'
    public $showEditModal = false;
    public ?Booking $selectedBooking = null;

    // Form fields for editing
    public $editStatus = '';
    public $editNotes = '';
    public $editPayment = "0";
    public $editCheckIn = '';
    public $editCheckOut = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'check_in'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        //
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortByField($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function editBooking($bookingId)
    {
        $this->selectedBooking = Booking::find($bookingId);
        $this->editStatus = $this->selectedBooking->status;
        $this->editNotes = $this->selectedBooking->notes ?? '';
        $this->editPayment = $this->selectedBooking->is_paid ? "1" : "0";

        // Format dates for HTML date inputs (Y-m-d format)
        $this->editCheckIn = \Carbon\Carbon::parse($this->selectedBooking->check_in)->format('Y-m-d');
        $this->editCheckOut = \Carbon\Carbon::parse($this->selectedBooking->check_out)->format('Y-m-d');

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        try {
            $this->showEditModal = false;
            $this->selectedBooking = null;
            $this->editStatus = '';
            $this->editNotes = '';
            $this->editPayment = "0";
            $this->editCheckIn = '';
            $this->editCheckOut = '';

            // Force a component refresh to clear any stale references
            $this->dispatch('modal-closed');
        } catch (\Exception $e) {
            // Log any errors but don't prevent modal closing
            Log::warning('Error closing edit modal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Still close the modal even if there's an error
            $this->showEditModal = false;
            $this->selectedBooking = null;
        }
    }

    public function deleteBooking($bookingId)
    {
        $booking = Booking::find($bookingId);
        if ($booking) {
            $booking->delete();
            session()->flash('success', 'Booking deleted successfully.');
        }
    }

    public function saveBooking()
    {
        if (!$this->selectedBooking) {
            return;
        }

        $validated = $this->validate([
            'editStatus' => 'required|in:pending,confirmed,cancelled,payment_expired,abandoned,refunded,partial_refund',
            'editNotes' => 'nullable|string|max:2000',
            'editPayment' => 'required|in:0,1',
            'editCheckIn' => 'required|date',
            'editCheckOut' => 'required|date|after:editCheckIn',
        ]);

        // Get fresh model from database and update it
        $booking = Booking::find($this->selectedBooking->id);

        // Calculate nights based on new dates
        $checkIn = \Carbon\Carbon::parse($validated['editCheckIn']);
        $checkOut = \Carbon\Carbon::parse($validated['editCheckOut']);
        $nights = $checkIn->diffInDays($checkOut);

        $booking->update([
            'status' => $validated['editStatus'],
            'notes' => $validated['editNotes'],
            'is_paid' => $validated['editPayment'] === "1",
            'check_in' => $validated['editCheckIn'],
            'check_out' => $validated['editCheckOut'],
            'nights' => $nights
        ]);

        // Close modal and reset form
        $this->showEditModal = false;
        $this->selectedBooking = null;
        $this->editStatus = '';
        $this->editNotes = '';
        $this->editPayment = "0";
        $this->editCheckIn = '';
        $this->editCheckOut = '';

        // Clear status filter to ensure updated booking is visible
        $this->statusFilter = '';

        // Force component refresh by resetting pagination and dispatching events
        $this->resetPage();
        $this->dispatch('bookingUpdated'); // Custom event for any listeners

        session()->flash('success', 'Booking updated successfully.');
    }



    public function getNetAmount($booking)
    {
        $totalPrice = (float) $booking->total_price;
        $refundAmount = (float) ($booking->refund_amount ?? 0);
        return $totalPrice - $refundAmount;
    }

    public function render()
    {
        // Get database bookings only (for the table)
        $paginatedBookings = Booking::with('venue')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('venue', function($query) {
                          $query->where('venue_name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhere('booking_id', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.bookings-table', [
            'bookings' => $paginatedBookings
        ]);
    }
}
