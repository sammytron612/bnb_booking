<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Ical;
use App\Http\Controllers\IcalController;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;

class BookingsTable extends Component
{
    use WithPagination;

    public $sortBy = 'check_in';
    public $sortDirection = 'desc';
    public $perPage = 15;
    public $search = '';
    public $statusFilter = '';
    public $showEditModal = false;
    public ?Booking $selectedBooking = null;
    public $calendarOffset = 0; // Track calendar navigation offset
    public $syncing = false; // Track sync status
    public $lastSyncTime = null;

    // Form fields for editing
    public $editStatus = '';
    public $editNotes = '';
    public $editPayment = "0";

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'check_in'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        // Sync external bookings on page load
        $this->syncExternalBookings();
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
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedBooking = null;
        $this->editStatus = '';
        $this->editNotes = '';
        $this->editPayment = "0";
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
            'editStatus' => 'required|in:pending,confirmed,cancelled',
            'editNotes' => 'nullable|string|max:2000',
            'editPayment' => 'required|in:0,1',
        ]);

        // Get fresh model from database and update it
        $booking = Booking::find($this->selectedBooking->id);
        $booking->update([
            'status' => $validated['editStatus'],
            'notes' => $validated['editNotes'],
            'is_paid' => $validated['editPayment'] === "1"
        ]);

        $this->showEditModal = false;
        $this->selectedBooking = null;
        $this->editStatus = '';
        $this->editNotes = '';
        $this->editPayment = "0";
        session()->flash('success', 'Booking updated successfully.');
    }

    public function navigateCalendar($direction)
    {
        if ($direction === 'next') {
            $this->calendarOffset += 14;
        } elseif ($direction === 'prev') {
            $this->calendarOffset -= 14;
        }
    }

    public function getCalendarData()
    {
        $days = collect();
        $startDate = now()->addDays($this->calendarOffset);

        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i);

            // Get bookings for this date
            $dayBookings = Booking::with('venue')->where(function ($query) use ($date) {
                $query->where('check_in', '<=', $date->format('Y-m-d'))
                      ->where('check_out', '>', $date->format('Y-m-d'));
            })
            ->where('status', '!=', 'cancelled')
            ->get();

            // Get check-ins for this specific date
            $checkIns = Booking::with('venue')
                ->whereDate('check_in', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->get();

            // Get check-outs for this specific date
            $checkOuts = Booking::with('venue')
                ->whereDate('check_out', $date->format('Y-m-d'))
                ->where('status', '!=', 'cancelled')
                ->get();

            $days->push([
                'date' => $date,
                'bookings' => $dayBookings,
                'booking_count' => $dayBookings->count(),
                'check_ins' => $checkIns,
                'check_in_count' => $checkIns->count(),
                'check_outs' => $checkOuts,
                'check_out_count' => $checkOuts->count(),
                'is_today' => $date->isToday(),
                'is_weekend' => $date->isWeekend()
            ]);
        }

        return $days;
    }

    /**
     * Sync external bookings from iCal feeds
     */
    public function syncExternalBookings()
    {
        $this->syncing = true;

        try {
            // Get all active iCal feeds
            $activeFeeds = Ical::where('active', true)->with('venue')->get();

            if ($activeFeeds->count() > 0) {
                $icalController = new IcalController();
                $request = new Request();

                // Trigger sync for all feeds
                $response = $icalController->fetchIcalData($request);

                $this->lastSyncTime = now()->format('H:i');

                // Flash success message
                session()->flash('message', 'External bookings synced successfully at ' . $this->lastSyncTime);
            }
        } catch (\Exception $e) {
            // Flash error message
            session()->flash('error', 'Failed to sync external bookings: ' . $e->getMessage());
        } finally {
            $this->syncing = false;
        }
    }

    /**
     * Manual sync trigger
     */
    public function refreshExternalBookings()
    {
        $this->syncExternalBookings();
        $this->resetPage(); // Refresh the booking list
    }

    /**
     * Get sync status for display
     */
    public function getSyncStatus()
    {
        $icalFeeds = Ical::where('active', true)->get();

        return [
            'total_feeds' => $icalFeeds->count(),
            'last_sync' => $icalFeeds->max('last_synced_at'),
            'has_errors' => $icalFeeds->whereNotNull('last_error')->count() > 0
        ];
    }

    public function render()
    {
        $bookings = Booking::with('venue')
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

        $calendarData = $this->getCalendarData();
        $syncStatus = $this->getSyncStatus();

        return view('livewire.bookings-table', [
            'bookings' => $bookings,
            'calendarData' => $calendarData,
            'syncStatus' => $syncStatus,
            'syncing' => $this->syncing,
            'lastSyncTime' => $this->lastSyncTime
        ]);
    }
}
