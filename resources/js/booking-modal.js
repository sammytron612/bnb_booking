// --- Booking calendar module ---
(function() {
    'use strict';

    // Prevent multiple initializations
    if (window.bookingCalendarInitialized) {
        return;
    }
    window.bookingCalendarInitialized = true;

const minNights = 2;
let today = new Date();
let viewYear = today.getFullYear();
let viewMonth = today.getMonth(); // 0-11
let bookedDates = new Set(); // 'YYYY-MM-DD' - Backward compatibility
let checkInDates = new Set(); // Dates when properties are checking in (can end booking here but not start)
let checkOutDates = new Set(); // Dates when properties are checking out (can start booking here but not end)
let fullyBookedDates = new Set(); // Dates completely unavailable
let icalBookedDates = new Set(); // iCal dates cached for month navigation
let checkIn = null; // Date
let checkOut = null; // Date

// Detect current venue ID from URL or page content
function getCurrentVenue() {
    const url = window.location.pathname;
    if (url.includes('light-house')) {
        return 1; // The Light House venue ID
    } else if (url.includes('saras')) {
        return 2; // Saras venue ID
    }
    // Default fallback
    return 1;
}

const currentVenue = getCurrentVenue();

// Helpers - Fixed for timezone issues
const fmt = (d) => {
    if (!d) return '';
    // Use local date without timezone conversion
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const dFrom = (s) => {
    if (!s) return null;
    // Parse date string directly without timezone conversion
    const [year, month, day] = s.split('-').map(Number);
    return new Date(year, month - 1, day); // month is 0-indexed in JS
};

const addDays = (d, n) => {
    const x = new Date(d.getFullYear(), d.getMonth(), d.getDate() + n);
    return x;
};

const diffDays = (a,b) => Math.round((b - a) / (1000*60*60*24));

// Function to check if a date is orphaned (can't accommodate minimum stay)
function isOrphanedDate(date) {
    const dateKey = fmt(date);

    // If already fully booked, not orphaned (it's just unavailable)
    if (fullyBookedDates.has(dateKey)) {
        return false;
    }

    // Check if we can make a 2-night booking starting from this date
    let canStartBooking = true;
    for (let i = 0; i < minNights; i++) {
        const checkDate = addDays(date, i);
        const checkKey = fmt(checkDate);

        // Can't start a booking if any day in the minimum stay period is fully booked
        if (fullyBookedDates.has(checkKey)) {
            canStartBooking = false;
            break;
        }
    }

    // Check if we can make a 2-night booking ending on this date
    let canEndBooking = true;
    for (let i = 1; i <= minNights; i++) {
        const checkDate = addDays(date, -i);
        const checkKey = fmt(checkDate);

        // Can't end a booking if any day in the minimum stay period is fully booked
        if (fullyBookedDates.has(checkKey)) {
            canEndBooking = false;
            break;
        }
    }

    // Date is orphaned if you can neither start nor end a booking there
    return !canStartBooking && !canEndBooking;
}

// Wait for DOM to be ready
function initializeBookingCalendar() {
    // Get elements with null checks
    const titleEl = document.getElementById('calendarTitle');
    const gridEl = document.getElementById('daysGrid');
    const bookingModal = document.getElementById('bookingModal');
    const openBookingBtn = document.getElementById('openBookingModal');
    const closeBookingBtn = document.getElementById('closeBookingModal');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    // Note: proceedBooking element doesn't exist - it's a Livewire method
    const proceedBookingBtn = null; // This element doesn't exist in current implementation
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    // Debug logging
    console.log('Booking modal elements:', {
        titleEl: !!titleEl,
        gridEl: !!gridEl,
        bookingModal: !!bookingModal,
        openBookingBtn: !!openBookingBtn,
        closeBookingBtn: !!closeBookingBtn,
        clearSelectionBtn: !!clearSelectionBtn
    });

    // Only proceed if essential elements exist
    if (titleEl && gridEl && bookingModal) {

    // Render calendar
    function renderCalendar() {
        const first = new Date(viewYear, viewMonth, 1);
        const startDay = first.getDay();
        const last = new Date(viewYear, viewMonth + 1, 0);
        const daysInMonth = last.getDate();
        titleEl.textContent = first.toLocaleString(undefined, { month: 'long', year: 'numeric' });
        gridEl.innerHTML = '';
        // leading blanks
        for (let i=0; i<startDay; i++) gridEl.appendChild(document.createElement('div'));
        for (let d=1; d<=daysInMonth; d++) {
            const date = new Date(viewYear, viewMonth, d);
            const key = fmt(date);
            // Fix past date comparison to avoid timezone issues
            const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const isPast = date < todayStart;

            // Determine availability based on new date categories
            const isCheckInDay = checkInDates.has(key);
            const isCheckOutDay = checkOutDates.has(key);
            const isFullyBooked = fullyBookedDates.has(key);
            const isBooked = bookedDates.has(key); // For backward compatibility
            const isOrphaned = isOrphanedDate(date);



            // Calculate selection ranges for visual display
            // inRange represents nights you're staying (check-in date to day before check-out)
            const inRange = checkIn && checkOut && date >= checkIn && date < checkOut;
            const isStart = checkIn && fmt(date)===fmt(checkIn); // First night
            const isEnd = checkOut && fmt(date)===fmt(addDays(checkOut,-1)); // Last night

            const btn = document.createElement('button');
            btn.setAttribute('type','button');

            // Determine styling based on availability
            let classNames = [
                'bg-white h-14 w-full text-sm flex items-center justify-center',
                'focus:outline-none',
                'hover:bg-gray-50'
            ];

            // Determine if the date is clickable
            // Only fully booked dates and past dates are not clickable
            const isClickable = !isPast && !isFullyBooked;

            // Style based on availability, with fully booked taking precedence
            // Note: Removed orphaned date blocking - let users try to book and handle minimum nights in selection logic
            if (isFullyBooked) {
                // Fully booked dates are always gray and blocked
                classNames.push('line-through text-gray-400 bg-gray-100 cursor-not-allowed');
            } else if (isCheckInDay) {
                // Check-in days (only if not fully booked)
                classNames.push('bg-orange-50 text-orange-700 border border-orange-200');
            } else if (isCheckOutDay) {
                classNames.push('bg-green-50 text-green-700 border border-green-200');
            }

            if (isPast) {
                classNames.push('opacity-40 cursor-not-allowed');
            }

            // Add cursor-pointer for clickable dates
            if (isClickable) {
                classNames.push('cursor-pointer');
            }

            if (inRange) {
                classNames.push('bg-pink-50 text-pink-700 font-semibold');
            }

            if (isStart || isEnd) {
                classNames.push('ring-2 ring-pink-500 font-bold');
            }

            btn.className = classNames.join(' ');
            btn.textContent = d;

            // Add tooltip for special dates
            if (isCheckInDay) {
                btn.title = 'Check-in day - Available for checkout (11am departure, 3pm arrival)';
            } else if (isCheckOutDay) {
                btn.title = 'Check-out day - Available for check-in (11am departure, 3pm arrival)';
            } else if (isFullyBooked && !isCheckInDay && !isCheckOutDay) {
                btn.title = 'Fully booked';
            } else if (isOrphaned) {
                btn.title = 'Limited availability - May require flexible dates for minimum stay';
            }

            // Add click handler based on clickability
            if (isClickable) {
                btn.addEventListener('click', () => onPick(date));
            }
            const cell = document.createElement('div');
            cell.className = 'bg-white';
            cell.appendChild(btn);
            gridEl.appendChild(cell);
        }
        updateSelectionUI();
    }

    function clearSelection() {
        checkIn = null; checkOut = null;
        // Reset the booking form
        const form = document.getElementById('guestDetailsForm');
        if (form) {
            form.reset();
        }
        renderCalendar();
    }

    function onPick(date) {
        const dateKey = fmt(date);
        const isOrphaned = isOrphanedDate(date);
        const isCheckInDay = checkInDates.has(dateKey);

        console.log('Clicked date:', dateKey, {
            isOrphaned,
            isCheckInDay,
            fullyBookedDates: fullyBookedDates.has(dateKey),
            currentCheckIn: checkIn ? fmt(checkIn) : null,
            currentCheckOut: checkOut ? fmt(checkOut) : null,
            fullyBookedSet: Array.from(fullyBookedDates),
            dateKeyType: typeof dateKey
        });

        // When selecting nights to stay:
        // - Can't select nights that are fully booked (someone staying that night)
        // - CAN select on check-out days (previous guest leaves, you can start staying)
        // - CANNOT select on check-in days (someone else is starting to stay that night)
        // Note: Removed orphaned date blocking - let users select any available date
        if (fullyBookedDates.has(dateKey)) {
            console.log('Blocked selection:', dateKey, 'fullyBooked:', fullyBookedDates.has(dateKey));
            return; // Invalid night selection
        }

        if (!checkIn) {
            // Start new selection - this date becomes the first night
            console.log('Starting new selection from:', dateKey);
            checkIn = date; // Check-in is this date
            checkOut = addDays(date, 1); // Check-out is next day
            renderCalendar();
            return;
        }

        console.log('Extending selection to:', dateKey);
        // Extend or modify selection
        if (date < checkIn) {
            // Extend backwards - new start date
            checkIn = date;
        } else if (date >= checkOut) {
            // Extend forwards - date becomes last night, so checkout is day after
            checkOut = addDays(date, 1);
        } else {
            // Clicking within current range - restart from this date
            checkIn = date;
            checkOut = addDays(date, 1);
        }

        // Validate the selection doesn't span any problematic dates
        let cursor = new Date(checkIn);
        let blocked = false;
        while (cursor < checkOut) { // Check all nights we're staying
            const cursorKey = fmt(cursor);

            // Block if any night is fully booked - we can't stay on nights others are staying
            if (fullyBookedDates.has(cursorKey)) {
                console.log('Selection blocked by fully booked night:', cursorKey);
                blocked = true;
                break;
            }
            cursor = addDays(cursor, 1);
        }

        if (blocked) {
            // Reset to just this single night if selection spans problematic dates
            console.log('Resetting to single night due to conflict');
            checkIn = date;
            checkOut = addDays(date, 1);
        }

        // Ensure minimum nights requirement
        if (diffDays(checkIn, checkOut) < minNights) {
            console.log('Enforcing minimum nights:', minNights);
            checkOut = addDays(checkIn, minNights);

            // Validate minimum nights doesn't conflict with any fully booked nights
            let minCursor = new Date(checkIn);
            let minBlocked = false;
            while (minCursor < checkOut) {
                const minKey = fmt(minCursor);
                if (fullyBookedDates.has(minKey)) {
                    console.log('Cannot meet minimum nights requirement due to fully booked night:', minKey);
                    minBlocked = true;
                    break;
                }
                minCursor = addDays(minCursor, 1);
            }

            if (minBlocked) {
                // Can't meet minimum nights - reset
                console.log('Cannot meet minimum nights requirement, clearing selection');
                checkIn = null;
                checkOut = null;
                renderCalendar();
                return;
            }
        }

        console.log('Final selection:', fmt(checkIn), 'to', fmt(checkOut));
        renderCalendar();
    }    // Navigation (with null checks) - only loads database data for performance
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', async () => {
            viewMonth--;
            if (viewMonth < 0) {
                viewMonth = 11;
                viewYear--;
            }
            await loadDatabaseBookedDates(currentVenue);
        });
    }

    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', async () => {
            viewMonth++;
            if (viewMonth > 11) {
                viewMonth = 0;
                viewYear++;
            }
            await loadDatabaseBookedDates(currentVenue);
        });
    }

    // Clear selection button
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', () => {
            clearSelection();
        });
    }

    // Update sidebar and emit Livewire events
    function updateSelectionUI() {
        // Check if Livewire is available before using it
        if (typeof Livewire !== 'undefined') {
            // Emit Livewire events when dates change
            if (checkIn && checkOut) {
                // Format dates for Livewire
                const checkInDate = formatDateForLivewire(checkIn);
                const checkOutDate = formatDateForLivewire(checkOut);

                // Emit to Livewire component
                Livewire.dispatch('datesSelected', {
                    checkIn: checkInDate,
                    checkOut: checkOutDate
                });
            } else {
                // Emit clear event to Livewire
                Livewire.dispatch('datesCleared');
            }
        }

        // Update proceed button if it exists
        updateProceedButton();
    }

    // Helper function to format dates for Livewire
    function formatDateForLivewire(date) {
        if (!date) return null;

        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    // Listen for clear events from Livewire (only if Livewire is available)
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:init', () => {
            Livewire.on('clearCalendarSelection', () => {
                // Clear the JavaScript calendar
                checkIn = null;
                checkOut = null;
                renderCalendar();
                updateSelectionUI();
            });

            Livewire.on('bookingSubmitted', (event) => {
                // Close modal on successful booking
                if (bookingModal) {
                    bookingModal.classList.add('hidden');
                    bookingModal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                // Show success notification
                alert('Booking submitted successfully! We will contact you shortly.');
            });
        });
    }



    // Load booked dates from Laravel database
    async function loadBookedDatesFromDatabase(venueId = null) {
        try {
            const url = venueId ? `/api/booked-dates?venue_id=${venueId}` : '/api/booked-dates';
            console.log('Fetching booked dates from:', url);

            const response = await fetch(url);
            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('API Response:', data);

            if (data.success) {
                // Clear existing data
                checkInDates.clear();
                checkOutDates.clear();
                fullyBookedDates.clear();
                bookedDates.clear();

                // Add new date categories
                if (data.checkInDates) {
                    console.log('Check-in dates from API:', data.checkInDates);
                    data.checkInDates.forEach(dateStr => {
                        checkInDates.add(dateStr);
                        bookedDates.add(dateStr); // For backward compatibility
                    });
                }

                if (data.checkOutDates) {
                    console.log('Check-out dates from API:', data.checkOutDates);
                    data.checkOutDates.forEach(dateStr => {
                        checkOutDates.add(dateStr);
                    });
                }

                if (data.fullyBookedDates) {
                    console.log('Fully booked dates from API:', data.fullyBookedDates);
                    data.fullyBookedDates.forEach(dateStr => {
                        fullyBookedDates.add(dateStr);
                        bookedDates.add(dateStr); // For backward compatibility
                    });
                }

                // Fallback for backward compatibility with old API response
                if (data.bookedDates && !data.checkInDates) {
                    data.bookedDates.forEach(dateStr => {
                        bookedDates.add(dateStr);
                    });
                }

                console.log(`Loaded booking data - Check-ins: ${checkInDates.size}, Check-outs: ${checkOutDates.size}, Fully booked: ${fullyBookedDates.size}`);

                // Reapply cached iCal dates during month navigation
                if (icalBookedDates.size > 0) {
                    let icalReappliedCount = 0;
                    icalBookedDates.forEach(dateStr => {
                        if (!fullyBookedDates.has(dateStr) && !bookedDates.has(dateStr)) {
                            fullyBookedDates.add(dateStr);
                            bookedDates.add(dateStr);
                            icalReappliedCount++;
                        }
                    });
                    console.log(`Reapplied ${icalReappliedCount} cached iCal dates during month navigation`);
                }

                return data.count || data.bookedDates?.length || 0;
            } else {
                console.error('Failed to load booked dates:', data);
                return 0;
            }
        } catch (error) {
            console.error('Error loading booked dates from database:', error);
            return 0;
        }
    }



    // Load iCal data from our API endpoints
    async function loadIcalDataFromAPI(venueId = null) {
        if (!venueId) return 0;

        // Update loading status
        updateLoadingStatus('Fetching external calendar data...');

        const status = document.getElementById('icalStatus');
        if (status) status.textContent = 'Loading iCal calendars…';

        try {
            const url = `/api/ical/fetch?venue_id=${venueId}`;
            console.log('Fetching iCal data from API:', url);

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('iCal API Response:', data);

            if (data.success && data.booked_dates) {
                // Clear and store iCal dates for persistence during navigation
                icalBookedDates.clear();

                // Add iCal booked dates as fully booked dates (blocked)
                let addedCount = 0;
                data.booked_dates.forEach(dateStr => {
                    // Store in iCal cache for month navigation
                    icalBookedDates.add(dateStr);

                    if (!fullyBookedDates.has(dateStr) && !bookedDates.has(dateStr)) {
                        fullyBookedDates.add(dateStr);
                        bookedDates.add(dateStr);
                        addedCount++;
                    }
                });

                if (status) {
                    const message = `Loaded ${data.booked_dates.length} dates from ${data.calendars_synced} iCal calendar(s)`;
                    status.textContent = message;
                }

                console.log(`Added ${addedCount} new booked dates from iCal as fully booked`);

                // Update loading status with success
                updateLoadingStatus(`Synced ${data.calendars_synced} calendar(s) successfully`);

                return addedCount;
            } else {
                if (status) status.textContent = data.message || 'No iCal data available';
                return 0;
            }

        } catch (err) {
            console.error('iCal API load error:', err);
            if (status) status.textContent = 'Failed to load iCal calendars.';
            return 0;
        }
    }

    // Get venue calendars (for admin/debug purposes)
    async function getVenueCalendars(venueId) {
        try {
            const response = await fetch(`/api/ical/venue/${venueId}/calendars`);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('Venue calendars:', data);
            return data;

        } catch (err) {
            console.error('Error fetching venue calendars:', err);
            return null;
        }
    }

    // Load database booking dates only (for regular calendar navigation)
    async function loadDatabaseBookedDates(venueId = null) {
        // Clear existing booked dates
        bookedDates.clear();
        checkInDates.clear();
        checkOutDates.clear();
        fullyBookedDates.clear();

        // Load detailed database booking data only
        await loadBookedDatesFromDatabase(venueId);

        // Re-render calendar with updated booked dates
        renderCalendar();
    }

    // Load all booked dates (database + iCal via API) - for modal opening only
    async function loadAllBookedDates(venueId = null) {
        // Clear existing booked dates
        bookedDates.clear();
        checkInDates.clear();
        checkOutDates.clear();
        fullyBookedDates.clear();

        // Update loading status
        setLoadingState(true, 'Loading database bookings...');

        // Load detailed database booking data first (for check-in/out categorization)
        await loadBookedDatesFromDatabase(venueId);

        // Update loading status for iCal sync
        setLoadingState(true, 'Syncing external calendars...');

        // Load additional iCal dates and add them as fully booked
        await loadIcalDataFromAPI(venueId);

        // Re-render calendar with updated booked dates
        renderCalendar();
    }

    // Alternative: Load combined booking data in one API call
    async function loadCombinedBookingData(venueId = null) {
        if (!venueId) return;

        try {
            const url = `/api/ical/combined?venue_id=${venueId}`;
            console.log('Fetching combined booking data from:', url);

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('Combined API Response:', data);

            if (data.success && data.booked_dates) {
                // Clear existing data
                bookedDates.clear();
                checkInDates.clear();
                checkOutDates.clear();
                fullyBookedDates.clear();

                // Add all booked dates
                data.booked_dates.forEach(dateStr => {
                    bookedDates.add(dateStr);
                });

                console.log(`Loaded ${data.booked_dates.length} total booked dates (Database: ${data.sources.database_count}, iCal: ${data.sources.ical_count})`);

                // Re-render calendar
                renderCalendar();
                return data.booked_dates.length;
            } else {
                console.error('Failed to load combined booking data:', data.message);
                return 0;
            }

        } catch (err) {
            console.error('Combined booking data load error:', err);
            return 0;
        }
    }

    // Enable/disable proceed button based on selection
    function updateProceedButton() {
        if (!proceedBookingBtn) return; // Exit if button doesn't exist

        const nights = (checkIn && checkOut) ? diffDays(checkIn, checkOut) : 0;
        if (nights >= 2) {
            proceedBookingBtn.disabled = false;
            proceedBookingBtn.textContent = `Book ${nights} Nights`;
        } else {
            proceedBookingBtn.disabled = true;
            proceedBookingBtn.textContent = 'Select Dates (Min 2 nights)';
        }
    }

    // Loading state management
    function setLoadingState(isLoading, statusText = 'Syncing calendar data...') {
        const calendarContainer = document.querySelector('#daysGrid');
        const modalContent = bookingModal?.querySelector('.relative');

        if (isLoading) {
            // Show loading spinner
            if (calendarContainer && !document.querySelector('#calendarLoadingSpinner')) {
                const spinner = document.createElement('div');
                spinner.id = 'calendarLoadingSpinner';
                spinner.className = 'absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10';
                spinner.innerHTML = `
                    <div class="flex flex-col items-center space-y-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-pink-600"></div>
                        <p id="loadingStatusText" class="text-sm text-gray-600">${statusText}</p>
                    </div>
                `;

                // Make calendar container relative for positioning
                calendarContainer.style.position = 'relative';
                calendarContainer.appendChild(spinner);
            } else {
                // Update existing status text
                const statusElement = document.querySelector('#loadingStatusText');
                if (statusElement) {
                    statusElement.textContent = statusText;
                }
            }

            // Disable calendar interactions
            if (calendarContainer) {
                calendarContainer.style.pointerEvents = 'none';
                calendarContainer.style.opacity = '0.7';
            }

            // Disable navigation buttons
            if (prevMonthBtn) prevMonthBtn.disabled = true;
            if (nextMonthBtn) nextMonthBtn.disabled = true;

        } else {
            // Hide loading spinner
            const spinner = document.querySelector('#calendarLoadingSpinner');
            if (spinner) {
                spinner.remove();
            }

            // Re-enable calendar interactions
            if (calendarContainer) {
                calendarContainer.style.pointerEvents = 'auto';
                calendarContainer.style.opacity = '1';
            }

            // Re-enable navigation buttons
            if (prevMonthBtn) prevMonthBtn.disabled = false;
            if (nextMonthBtn) nextMonthBtn.disabled = false;
        }
    }

    // Update loading status text
    function updateLoadingStatus(statusText) {
        const statusElement = document.querySelector('#loadingStatusText');
        if (statusElement) {
            statusElement.textContent = statusText;
        }
    }

    // Initial render and data loading (database only for performance)
    loadDatabaseBookedDates(currentVenue); // Load booked dates from database only



    // Booking Modal functionality (only if elements exist)
    if (bookingModal && openBookingBtn) {
        // Open booking modal
        openBookingBtn.addEventListener('click', async () => {
            bookingModal.classList.remove('hidden');
            bookingModal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            // Show loading state while syncing iCal data
            setLoadingState(true);

            try {
                // Sync all booking data (database + iCal) when modal opens
                await loadAllBookedDates(currentVenue);
            } catch (error) {
                console.error('Error loading booking data:', error);
                // Still allow modal to function with database data only
            } finally {
                // Hide loading state regardless of success/failure
                setLoadingState(false);
            }
        });

        // Close booking modal
        function closeBookingModal() {
            bookingModal.classList.add('hidden');
            bookingModal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        if (closeBookingBtn) {
            closeBookingBtn.addEventListener('click', closeBookingModal);
        }

        // Close modal when clicking outside
        bookingModal.addEventListener('click', (e) => {
            if (e.target === bookingModal) {
                closeBookingModal();
            }
        });

        // Note: proceedBooking button doesn't exist in current implementation
        // Booking is handled through Livewire component instead
        console.log('Booking functionality is handled through Livewire component');
    }

    } else {
        // Console log when essential elements are missing
        console.log('Booking calendar elements not found - skipping initialization');
    }

} // End of initializeBookingCalendar function

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeBookingCalendar);
} else {
    // DOM is already loaded
    initializeBookingCalendar();
}

})(); // End of IIFE
