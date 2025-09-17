// --- Booking calendar state ---

const minNights = 2;
let today = new Date();
let viewYear = today.getFullYear();
let viewMonth = today.getMonth(); // 0-11
let bookedDates = new Set(); // 'YYYY-MM-DD'
let checkIn = null; // Date
let checkOut = null; // Date

// Detect current venue from URL or page content
function getCurrentVenue() {
    const url = window.location.pathname;
    if (url.includes('light-house')) {
        return 'The Light House';
    } else if (url.includes('saras')) {
        return 'Saras';
    }
    // Default fallback - could also check page title or meta tag
    return 'The Light House';
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

// Get elements with null checks
const titleEl = document.getElementById('calendarTitle');
const gridEl = document.getElementById('daysGrid');
const bookingModal = document.getElementById('bookingModal');
const openBookingBtn = document.getElementById('openBookingModal');
const closeBookingBtn = document.getElementById('closeBookingModal');
// Note: proceedBooking and clearSelection elements don't exist - they're Livewire methods
const proceedBookingBtn = null; // This element doesn't exist in current implementation
const prevMonthBtn = document.getElementById('prevMonth');
const nextMonthBtn = document.getElementById('nextMonth');
const clearSelectionBtn = null; // This is handled by Livewire, not a DOM element

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
            const isBooked = bookedDates.has(key);
            const inRange = checkIn && checkOut && date >= checkIn && date <= addDays(checkOut,-1);
            const isStart = checkIn && fmt(date)===fmt(checkIn);
            const isEnd = checkOut && fmt(date)===fmt(addDays(checkOut,-1));
            const btn = document.createElement('button');
            btn.setAttribute('type','button');
            btn.className = [
                'bg-white h-14 w-full text-sm flex items-center justify-center',
                'focus:outline-none',
                'hover:bg-gray-50',
                (isBooked?'line-through text-gray-400 bg-gray-100 cursor-not-allowed':''),
                (isPast?'opacity-40 cursor-not-allowed':''),
                (inRange?'bg-pink-50 text-pink-700 font-semibold':''),
                (isStart||isEnd?'ring-2 ring-pink-500 font-bold':'')
            ].join(' ');
            btn.textContent = d;
            if (!isPast && !isBooked) {
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
        if (!checkIn || (checkIn && checkOut)) { // start new selection
            checkIn = date; checkOut = null; renderCalendar();
            return;
        }
        // enforce forward selection and availability
        if (date <= checkIn) { checkIn = date; renderCalendar(); return; }
        // ensure dates between are not booked
        let cursor = new Date(checkIn);
        let blocked = false;
        while (cursor < date) {
            if (bookedDates.has(fmt(cursor))) { blocked = true; break; }
            cursor = addDays(cursor, 1);
        }
        if (blocked) return; // ignore invalid
        // min nights
        if (diffDays(checkIn, date) < minNights) {
            checkOut = addDays(checkIn, minNights);
        } else {
            checkOut = date;
        }
        renderCalendar();
    }

    // Navigation (with null checks)
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

    // clearSelectionBtn is handled by Livewire, not a DOM element
    // No need to add event listener here

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

    // --- iCal parsing ---
    // Parse a subset of iCal to get date ranges from VEVENT DTSTART/DTEND (date-only formats)
    function parseIcs(icsText) {
        const lines = icsText.replace(/\r/g,'').split('\n');
        const events = [];
        let cur = null;
        for (let raw of lines) {
            const line = raw.trim();
            if (line === 'BEGIN:VEVENT') { cur = {}; continue; }
            if (line === 'END:VEVENT') { if (cur && cur.DTSTART && cur.DTEND) events.push(cur); cur = null; continue; }
            if (!cur) continue;
            if (line.startsWith('DTSTART')) { cur.DTSTART = line.split(':').pop(); }
            if (line.startsWith('DTEND')) { cur.DTEND = line.split(':').pop(); }
        }
        const added = [];
        for (const ev of events) {
            const start = ev.DTSTART.substring(0,8); // YYYYMMDD
            const end = ev.DTEND.substring(0,8); // exclusive
            if (!/^\d{8}$/.test(start) || !/^\d{8}$/.test(end)) continue;
            const s = `${start.slice(0,4)}-${start.slice(4,6)}-${start.slice(6,8)}`;
            const e = `${end.slice(0,4)}-${end.slice(4,6)}-${end.slice(6,8)}`;
            let d = dFrom(s);
            const eDate = dFrom(e);
            while (d < eDate) {
                bookedDates.add(fmt(d));
                added.push(fmt(d));
                d = addDays(d,1);
            }
        }
        return added.length;
    }

    // Load booked dates from Laravel database
    async function loadBookedDatesFromDatabase(venue = null) {
        try {
            const url = venue ? `/api/booked-dates?venue=${encodeURIComponent(venue)}` : '/api/booked-dates';
            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                // Add dates from database to bookedDates Set
                data.bookedDates.forEach(dateStr => {
                    bookedDates.add(dateStr);
                });

                console.log(`Loaded ${data.bookedDates.length} booked dates from database`);
                return data.bookedDates.length;
            } else {
                console.error('Failed to load booked dates:', data);
                return 0;
            }
        } catch (error) {
            console.error('Error loading booked dates from database:', error);
            return 0;
        }
    }

    // Modified iCal loading function
    async function loadIcalFromUrl(url) {
        const status = document.getElementById('icalStatus');
        if (status) status.textContent = 'Loading iCal from URL…';
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('HTTP '+res.status);
            const text = await res.text();
            const count = parseIcs(text);
            if (status) status.textContent = `Imported ${count} booked days from iCal.`;
            console.log(`Loaded ${count} booked dates from iCal`);
            return count;
        } catch (err) {
            console.error('iCal load error:', err);
            if (status) status.textContent = 'Failed to load iCal calendar.';
            return 0;
        }
    }

    // Load all booked dates (database + iCal)
    async function loadAllBookedDates(venue = null) {
        // Clear existing booked dates
        bookedDates.clear();

        // Load from database first
        await loadBookedDatesFromDatabase(venue);

        // Optionally load from iCal (if you have a valid URL)
        // Uncomment the lines below if you want to use iCal integration
        // const icalUrl = 'https://your-actual-ical-url.ics';
        // await loadIcalFromUrl(icalUrl);

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

    // Auto-sync iCal once on load (disabled by default, uncomment if needed)
    // const AIRBNB_ICAL_URL = 'https://example.com/your-airbnb-calendar.ics';
    // loadIcalFromUrl(AIRBNB_ICAL_URL);

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
