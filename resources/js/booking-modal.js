// --- Booking calendar state ---
    const nightlyRate = 140; // GBP
    const minNights = 2;
    let today = new Date();
    let viewYear = today.getFullYear();
    let viewMonth = today.getMonth(); // 0-11
    let bookedDates = new Set(); // 'YYYY-MM-DD'
    let checkIn = null; // Date
    let checkOut = null; // Date

    // Helpers
    const fmt = (d) => d ? d.toISOString().slice(0,10) : '';
    const dFrom = (s) => new Date(s + 'T00:00:00');
    const addDays = (d, n) => { const x = new Date(d); x.setDate(x.getDate()+n); return x; };
    const diffDays = (a,b) => Math.round((b - a) / (1000*60*60*24));

    // Render calendar
    const titleEl = document.getElementById('calendarTitle');
    const gridEl = document.getElementById('daysGrid');
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
            const isPast = date < dFrom(fmt(today));
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
        checkIn = null; checkOut = null; renderCalendar();
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

    // Navigation
    document.getElementById('prevMonth').addEventListener('click', () => {
        viewMonth--; if (viewMonth<0) { viewMonth=11; viewYear--; } renderCalendar();
    });
    document.getElementById('nextMonth').addEventListener('click', () => {
        viewMonth++; if (viewMonth>11) { viewMonth=0; viewYear++; } renderCalendar();
    });
    document.getElementById('clearSelection').addEventListener('click', clearSelection);

    // Update sidebar
    function updateSelectionUI() {
        const inEl = document.getElementById('selCheckIn');
        const outEl = document.getElementById('selCheckOut');
        const nightsEl = document.getElementById('selNights');
        const totalEl = document.getElementById('selTotal');
        const nf = new Intl.NumberFormat(undefined, { style: 'currency', currency: 'GBP' });
        if (checkIn) inEl.textContent = checkIn.toDateString(); else inEl.textContent = '—';
        if (checkIn && checkOut) outEl.textContent = checkOut.toDateString(); else outEl.textContent = '—';
        const nights = (checkIn && checkOut) ? diffDays(checkIn, checkOut) : 0;
        nightsEl.textContent = nights;
        totalEl.textContent = nf.format(nightlyRate * nights);
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

    // URL load (note: may require CORS; Airbnb often allows direct fetch when hosted server-side. If blocked in browser, load via your backend.)
    async function loadIcalFromUrl(url) {
        const status = document.getElementById('icalStatus');
        if (status) status.textContent = 'Loading iCal from URL…';
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error('HTTP '+res.status);
            const text = await res.text();
            const count = parseIcs(text);
            if (status) status.textContent = `Imported ${count} booked days from URL.`;
            renderCalendar();
        } catch (err) {
            if (status) status.textContent = 'Failed to load iCal (likely CORS). Configure a backend proxy or host this page on your server.';
        }
    }

    // Hard-coded Airbnb iCal URL — replace with your listing's iCal link
    const AIRBNB_ICAL_URL = 'https://example.com/your-airbnb-calendar.ics';

    // Initial render
    renderCalendar();
    // Auto-sync iCal once on load
    loadIcalFromUrl(AIRBNB_ICAL_URL);

    // Booking Modal functionality
    const bookingModal = document.getElementById('bookingModal');
    const openBookingBtn = document.getElementById('openBookingModal');
    const closeBookingBtn = document.getElementById('closeBookingModal');
    const proceedBookingBtn = document.getElementById('proceedBooking');

    // Open booking modal
    openBookingBtn.addEventListener('click', () => {
        bookingModal.classList.remove('hidden');
        bookingModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        renderCalendar(); // Refresh calendar when modal opens
    });

    // Close booking modal
    function closeBookingModal() {
        bookingModal.classList.add('hidden');
        bookingModal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    closeBookingBtn.addEventListener('click', closeBookingModal);

    // Close modal when clicking outside
    bookingModal.addEventListener('click', (e) => {
        if (e.target === bookingModal) {
            closeBookingModal();
        }
    });

    // Enable/disable proceed button based on selection
    function updateProceedButton() {
        const nights = (checkIn && checkOut) ? diffDays(checkIn, checkOut) : 0;
        if (nights >= 2) {
            proceedBookingBtn.disabled = false;
            proceedBookingBtn.textContent = `Book ${nights} Nights`;
        } else {
            proceedBookingBtn.disabled = true;
            proceedBookingBtn.textContent = 'Select Dates (Min 2 nights)';
        }
    }

    // Update the existing updateSelectionUI function to also update the proceed button
    const originalUpdateSelectionUI = updateSelectionUI;
    updateSelectionUI = function() {
        originalUpdateSelectionUI();
        updateProceedButton();
    };

    // Handle proceed booking
    proceedBookingBtn.addEventListener('click', () => {
        if (checkIn && checkOut) {
            const nights = diffDays(checkIn, checkOut);
            const total = nightlyRate * nights;
            const checkInStr = checkIn.toLocaleDateString();
            const checkOutStr = checkOut.toLocaleDateString();

            // Here you would normally integrate with a booking system
            // For now, we'll show an alert
            alert(`Booking Request:\n\nProperty: The Light House\nCheck-in: ${checkInStr}\nCheck-out: ${checkOutStr}\nNights: ${nights}\nTotal: £${total}\n\nThis would normally redirect to a booking form or payment system.`);
        }
    });
