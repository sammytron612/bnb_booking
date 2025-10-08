<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use App\Services\BookingServices\ExternalCalendarService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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
        // External bookings don't have pricing data
        if ($this->isExternalBooking($booking)) {
            return 0;
        }

        $totalPrice = (float) $booking->total_price;
        $refundAmount = (float) ($booking->refund_amount ?? 0);
        return $totalPrice - $refundAmount;
    }

    public function isExternalBooking($booking)
    {
        return isset($booking->is_external) && $booking->is_external === true;
    }

    public function canEditBooking($booking)
    {
        return !$this->isExternalBooking($booking);
    }

    public function canDeleteBooking($booking)
    {
        return !$this->isExternalBooking($booking);
    }

    public function render()
    {
        // Get database bookings
        $dbBookingsQuery = Booking::with('venue')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhereHas('venue', function($query) {
                          $query->where('venue_name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhere('booking_id', 'like', '%' . $this->search . '%')
                      ->orWhereRaw("CONCAT('BNB-', COALESCE(booking_id, id)) LIKE ?", ['%' . $this->search . '%']);
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'external') {
                    // When filtering for external only, exclude all database bookings
                    $query->whereRaw('1 = 0'); // This will return no results
                } elseif ($this->statusFilter === 'partial_refund') {
                    $query->where('status', 'confirmed')
                          ->where('refund_amount', '>', 0)
                          ->whereRaw('refund_amount < total_price');
                } elseif ($this->statusFilter === 'refunded') {
                    $query->where(function($q) {
                        $q->where('status', 'refunded')
                          ->orWhereRaw('refund_amount >= total_price');
                    });
                } else {
                    $query->where('status', $this->statusFilter);
                }
            });

        // Get external bookings
        $externalBookings = $this->getExternalBookings();

        // Filter external bookings by search and status if needed
        $filteredExternalBookings = $externalBookings->when($this->search, function ($collection) {
            return $collection->filter(function ($booking) {
                return stripos($booking->name, $this->search) !== false ||
                       stripos($booking->email, $this->search) !== false ||
                       stripos($booking->booking_id, $this->search) !== false ||
                       stripos($booking->venue->venue_name ?? '', $this->search) !== false;
            });
        })->when($this->statusFilter, function ($collection) {
            if ($this->statusFilter === 'external') {
                return $collection; // Return all external bookings when filtering for external
            } elseif ($this->statusFilter === 'confirmed') {
                return $collection; // External bookings are shown as confirmed
            }
            return collect(); // Return empty if filtering for other statuses
        });

        // Get database bookings as collection
        $dbBookings = $dbBookingsQuery->get();

        // Merge database and external bookings
        $allBookings = $dbBookings->concat($filteredExternalBookings);

        // Sort the merged collection
        $sortedBookings = $allBookings->sortBy([
            [$this->sortBy, $this->sortDirection === 'asc' ? 'asc' : 'desc']
        ]);

        // Manual pagination
        $currentPage = Paginator::resolveCurrentPage();
        $itemsForCurrentPage = $sortedBookings->forPage($currentPage, $this->perPage);

        $paginatedBookings = new LengthAwarePaginator(
            $itemsForCurrentPage->values(),
            $sortedBookings->count(),
            $this->perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        return view('livewire.admin.bookings-table', [
            'bookings' => $paginatedBookings
        ]);
    }

    private function getExternalBookings()
    {
        $externalBookings = collect();

        try {
            $externalCalendarService = app(ExternalCalendarService::class);
            $rawExternalBookings = $externalCalendarService->getExternalBookings();

            if ($rawExternalBookings && $rawExternalBookings->count() > 0) {
                $index = 0;
                foreach ($rawExternalBookings as $rawBooking) {
                    $sourceName = $rawBooking->source ?? 'External Booking';
                    $uniqueId = 'ext-' . $rawBooking->source . '-' . $index . '-' . uniqid();

                    $booking = new Booking([
                        'venue_id' => $rawBooking->venue_id,
                        'booking_id' => 'EXT-' . strtoupper(substr(md5($uniqueId), 0, 6)),
                        'name' => $sourceName,
                        'email' => 'external@booking.com',
                        'phone' => '',
                        'check_in' => $rawBooking->check_in,
                        'check_out' => $rawBooking->check_out,
                        'total_price' => 0,
                        'status' => 'confirmed',
                        'notes' => $rawBooking->summary ?? 'External calendar booking',
                        'nights' => $rawBooking->check_in->diffInDays($rawBooking->check_out),
                        'pay_method' => 'external',
                        'is_paid' => true
                    ]);

                    // Add a flag to identify this as external
                    $booking->is_external = true;

                    // Load venue relationship if exists
                    if ($rawBooking->venue_id) {
                        $booking->setRelation('venue', \App\Models\Venue::find($rawBooking->venue_id));
                    }

                    $externalBookings->push($booking);
                    $index++;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch external bookings: ' . $e->getMessage());
        }

        return $externalBookings;
    }
}
