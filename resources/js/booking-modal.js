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

        // Can start a booking if:
        // 1. Date is completely free, OR
        // 2. Date is a check-out date (same-day turnover possible), OR
        // 3. Date is a check-in date (same-day turnover possible - guests arrive later)
        const isAvailableForCheckIn = !fullyBookedDates.has(checkKey) ||
                                     checkOutDates.has(checkKey) ||
                                     checkInDates.has(checkKey);

        if (!isAvailableForCheckIn) {
            canStartBooking = false;
            break;
        }
    }

    // Check if we can make a 2-night booking ending on this date
    let canEndBooking = true;
    for (let i = 1; i <= minNights; i++) {
        const checkDate = addDays(date, -i);
        const checkKey = fmt(checkDate);

        // Can end a booking if:
        // 1. Date is completely free, OR
        // 2. Date is a check-out date (same-day turnover possible), OR
        // 3. Date is a check-in date (same-day turnover possible - guests arrive later)
        const isAvailableForCheckIn = !fullyBookedDates.has(checkKey) ||
                                     checkOutDates.has(checkKey) ||
                                     checkInDates.has(checkKey);

        if (!isAvailableForCheckIn) {
            canEndBooking = false;
            break;
        }
    }

    // Date is orphaned if you can neither start nor end a booking there
    return !canStartBooking && !canEndBooking;
}// Wait for DOM to be ready
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
            const isPast = date <= todayStart; // Block today and past dates

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
            // Allow clicking on dates that are:
            // 1. Not in the past
            // 2. Not orphaned (can accommodate minimum stay)
            // 3. Either completely free OR only checkout days (for same-day turnover)
            // Note: Check-in days are non-clickable since someone is already checking in
            const canSameDayTurnover = isCheckOutDay; // Only checkout days allow same-day turnover
            const isClickable = !isPast && !isOrphaned && (!isFullyBooked || canSameDayTurnover) && !isCheckInDay;

            // Style based on availability, with fully booked taking precedence
            if (isOrphaned) {
                classNames.push('line-through text-gray-400 bg-red-50 cursor-not-allowed border border-red-200');
            } else if (isCheckInDay && isFullyBooked) {
                // Check-in days that are also fully booked - non-clickable, informational only
                classNames.push('line-through bg-orange-50 text-orange-700 border border-orange-200 cursor-not-allowed');
            } else if (isCheckOutDay && isFullyBooked) {
                // Check-out days that are also fully booked - available for same-day turnover
                classNames.push('bg-green-50 text-green-700 border border-green-200');
            } else if (isFullyBooked) {
                // Fully booked dates with no same-day turnover option
                classNames.push('line-through text-gray-400 bg-gray-100 cursor-not-allowed');
            } else if (isCheckInDay) {
                // Check-in days (not fully booked) - non-clickable, informational only
                classNames.push('line-through bg-orange-50 text-orange-700 border border-orange-200 cursor-not-allowed');
            } else if (isCheckOutDay) {
                // Check-out days - available for same-day turnover
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
                classNames.push('bg-green-50 text-green-800 font-semibold');
            }

            if (isStart || isEnd) {
                classNames.push('ring-2 ring-green-600 font-bold');
            }

            btn.className = classNames.join(' ');
            btn.textContent = d;

            // Add tooltip for special dates
            if (isOrphaned) {
                btn.title = 'Unavailable - Cannot accommodate minimum 2-night stay';
            } else if (isCheckInDay && isFullyBooked) {
                btn.title = 'Check-in day - Available for checkout via same-day turnover (depart 11am, new guests arrive 3pm)';
            } else if (isCheckInDay) {
                btn.title = 'Check-in day - Available for booking';
            } else if (isCheckOutDay) {
                btn.title = 'Check-out day - Available for check-in (11am departure, 3pm arrival)';
            } else if (isFullyBooked && !isCheckInDay && !isCheckOutDay) {
                btn.title = 'Fully booked - No same-day turnover available';
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
            isCheckOutDay: checkOutDates.has(dateKey),
            fullyBookedDates: fullyBookedDates.has(dateKey),
            currentCheckIn: checkIn ? fmt(checkIn) : null,
            currentCheckOut: checkOut ? fmt(checkOut) : null,
            fullyBookedSet: Array.from(fullyBookedDates),
            checkInSet: Array.from(checkInDates),
            checkOutSet: Array.from(checkOutDates),
            dateKeyType: typeof dateKey
        });

        // Allow clearing orphaned dates if they're currently selected
        const isCurrentlySelectedStart = checkIn && fmt(date) === fmt(checkIn);
        const isCurrentlySelectedEnd = checkOut && fmt(date) === fmt(addDays(checkOut, -1));

        // If clicking on an orphaned date that's currently selected, clear the selection
        if (isOrphaned && (isCurrentlySelectedStart || isCurrentlySelectedEnd)) {
            clearSelection();
            return;
        }

        // When selecting nights to stay:
        // - Can't select nights that are fully booked UNLESS it's a same-day turnover
        // - CAN select on check-out days (previous guest leaves, you can start staying)
        // - CAN select nights ending on check-in days (same-day turnover - you checkout when they checkin)
        const isCheckOutDay = checkOutDates.has(dateKey);
        const canEndOnCheckInDay = checkInDates.has(dateKey); // Same-day turnover

        // Block selection if fully booked AND not a same-day turnover opportunity
        if (fullyBookedDates.has(dateKey) && !isCheckOutDay && !canEndOnCheckInDay) {
            console.log('Blocked selection:', dateKey, 'fullyBooked:', fullyBookedDates.has(dateKey), 'notSameDayTurnover');
            return; // Invalid night selection
        }

        // Also block if orphaned (can't accommodate minimum stay)
        if (isOrphaned) {
            console.log('Blocked selection:', dateKey, 'isOrphaned:', isOrphaned);
            return;
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
            console.log('Extending backwards from', fmt(checkIn), 'to', dateKey);
            const originalCheckOut = checkOut;
            checkIn = date;

            // Validate that extending backwards doesn't create conflicts
            let conflictFound = false;
            let cursor = new Date(checkIn);
            while (cursor < originalCheckOut) {
                const cursorKey = fmt(cursor);
                const isCheckInDay = checkInDates.has(cursorKey);
                const isCheckOutDay = checkOutDates.has(cursorKey);

                console.log(`Checking night ${cursorKey}: fullyBooked=${fullyBookedDates.has(cursorKey)}, isCheckInDay=${isCheckInDay}, isCheckOutDay=${isCheckOutDay}`);

                // Key insight: if it's a check-in day, you can CHECKOUT on that day but not STAY that night
                // Same-day turnover means: you leave morning, they arrive afternoon
                if (fullyBookedDates.has(cursorKey)) {
                    if (isCheckOutDay) {
                        // You can stay this night and they checkout next morning = OK
                        console.log(`Night ${cursorKey} is OK: they checkout, you can stay`);
                    } else if (isCheckInDay) {
                        // They're checking in this day = they stay this night = conflict with you staying
                        console.log(`Night ${cursorKey} CONFLICT: they check-in and stay this night`);
                        conflictFound = true;
                        break;
                    } else {
                        // Fully booked with no turnover opportunity
                        console.log(`Night ${cursorKey} CONFLICT: fully booked, no turnover`);
                        conflictFound = true;
                        break;
                    }
                }
                cursor = addDays(cursor, 1);
            }

            if (conflictFound) {
                // Reset to just this single night if extension creates conflicts
                console.log('Resetting to single night due to backward extension conflict');
                checkIn = date;

                // For minimum nights, find the earliest safe checkout date
                let safeCheckOut = addDays(date, minNights);
                let searchCursor = addDays(date, 1); // Start checking from the day after check-in

                while (searchCursor <= safeCheckOut) {
                    const searchKey = fmt(searchCursor);
                    const isCheckInDay = checkInDates.has(searchKey);
                    const isCheckOutDay = checkOutDates.has(searchKey);
                    const canSameDayTurnover = isCheckInDay || isCheckOutDay;

                    // If this night is fully booked and no same-day turnover, we can't stay past this point
                    if (fullyBookedDates.has(searchKey) && !canSameDayTurnover) {
                        console.log('Cannot extend to minimum nights due to conflict on:', searchKey);
                        safeCheckOut = searchCursor; // Checkout before the conflict
                        break;
                    }

                    // If we found a check-in day (same-day turnover), we can checkout here
                    if (isCheckInDay && fullyBookedDates.has(searchKey)) {
                        console.log('Found same-day turnover opportunity on:', searchKey);
                        safeCheckOut = searchCursor; // Checkout on this day (same-day turnover)
                        break;
                    }

                    searchCursor = addDays(searchCursor, 1);
                }

                // Check if we can meet minimum nights requirement
                if (diffDays(date, safeCheckOut) < minNights) {
                    console.log('Cannot meet minimum nights requirement, clearing selection');
                    checkIn = null;
                    checkOut = null;
                    renderCalendar();
                    return;
                }

                checkOut = safeCheckOut;
            }

        } else if (date >= checkOut) {
            // Check if this is a same-day turnover scenario
            const isCheckInDay = checkInDates.has(dateKey);
            const isCheckOutDay = checkOutDates.has(dateKey);

            if (isCheckInDay && fullyBookedDates.has(dateKey)) {
                // Same-day turnover: clicked date is checkout date (not last night staying)
                console.log('Same-day turnover detected: setting checkout to', dateKey);
                checkOut = date; // Checkout ON this date (same-day turnover)
            } else {
                // Normal extension: date becomes last night, so checkout is day after
                checkOut = addDays(date, 1);
            }
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

            // Check for same-day turnover opportunities
            const isCheckInDay = checkInDates.has(cursorKey);
            const isCheckOutDay = checkOutDates.has(cursorKey);

            console.log(`Main validation checking night ${cursorKey}: fullyBooked=${fullyBookedDates.has(cursorKey)}, isCheckInDay=${isCheckInDay}, isCheckOutDay=${isCheckOutDay}`);

            if (fullyBookedDates.has(cursorKey)) {
                if (isCheckOutDay) {
                    // You can stay this night and they checkout next morning = OK
                    console.log(`Main validation: Night ${cursorKey} is OK: they checkout, you can stay`);
                } else if (isCheckInDay) {
                    // They're checking in this day = they stay this night = conflict with you staying
                    console.log(`Main validation: Night ${cursorKey} CONFLICT: they check-in and stay this night`);
                    blocked = true;
                    break;
                } else {
                    // Fully booked with no turnover opportunity
                    console.log(`Main validation: Night ${cursorKey} CONFLICT: fully booked, no turnover`);
                    blocked = true;
                    break;
                }
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

                // Check for same-day turnover opportunities
                const isCheckInDay = checkInDates.has(minKey);
                const isCheckOutDay = checkOutDates.has(minKey);

                console.log(`Min nights checking night ${minKey}: fullyBooked=${fullyBookedDates.has(minKey)}, isCheckInDay=${isCheckInDay}, isCheckOutDay=${isCheckOutDay}`);

                if (fullyBookedDates.has(minKey)) {
                    if (isCheckOutDay) {
                        // You can stay this night and they checkout next morning = OK
                        console.log(`Minimum nights: Night ${minKey} is OK: they checkout, you can stay`);
                    } else if (isCheckInDay) {
                        // They're checking in this day = they stay this night = conflict with you staying
                        console.log(`Minimum nights: Night ${minKey} CONFLICT: they check-in and stay this night`);
                        minBlocked = true;
                        break;
                    } else {
                        // Fully booked with no turnover opportunity
                        console.log(`Minimum nights: Night ${minKey} CONFLICT: fully booked, no turnover`);
                        minBlocked = true;
                        break;
                    }
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
    }    // Navigation (with null checks)
    if (prevMonthBtn) {
        prevMonthBtn.addEventListener('click', async () => {
            viewMonth--;
            if (viewMonth < 0) {
                viewMonth = 11;
                viewYear--;
            }
            await loadAllBookedDates(currentVenue);
        });
    }

    if (nextMonthBtn) {
        nextMonthBtn.addEventListener('click', async () => {
            viewMonth++;
            if (viewMonth > 11) {
                viewMonth = 0;
                viewYear++;
            }
            await loadAllBookedDates(currentVenue);
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



    // Load booked dates from  database
    async function loadBookedDatesFromDatabase(venueId = null) {
        try {
            const url = venueId ? `/api/booked-dates?venue_id=${venueId}` : '/api/booked-dates';
            console.log('Fetching booked dates from:', url);

            const response = await fetch(url, {
                method: 'GET',
                credentials: 'omit', // Don't send cookies/session data to avoid CSRF issues
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
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



    // Load all booked dates (database + iCal)
    async function loadAllBookedDates(venueId = null) {
        // Clear existing booked dates
        bookedDates.clear();

        // Load from database first
        await loadBookedDatesFromDatabase(venueId);



        // Re-render calendar with updated booked dates
        renderCalendar();
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

    // Initial render and data loading
    loadAllBookedDates(currentVenue); // Load booked dates from database



    // Booking Modal functionality (only if elements exist)
    if (bookingModal && openBookingBtn) {
        // Open booking modal
        openBookingBtn.addEventListener('click', async () => {
            bookingModal.classList.remove('hidden');
            bookingModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            // Refresh booked dates from database when modal opens
            await loadAllBookedDates(currentVenue);
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
