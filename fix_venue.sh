#!/bin/bash
# Simple replacement script for venue references
sed -i 's/{{ $booking->venue }}/{{ $booking->venue->venue_name }}/g' "/c/Laravel_new/bnb/resources/views/livewire/bookings-table.blade.php"
