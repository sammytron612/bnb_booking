<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ical;

class IcalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing ical records to avoid duplicates
        Ical::truncate();

        // Create ical records for The Light House (venue_id: 1)
        Ical::create([
            'venue_id' => 1,
            'url' => 'https://www.airbnb.com/calendar/ical/YOUR_AIRBNB_LISTING_ID_1.ics',
            'source' => 'airbnb',
            'name' => 'Airbnb Calendar - The Light House',
            'active' => false,
            'last_synced' => null,
        ]);

        Ical::create([
            'venue_id' => 1,
            'url' => 'https://admin.booking.com/hotel/hoteladmin/extranet_ng/manage/calendar.html?ses=YOUR_BOOKING_SESSION_1',
            'source' => 'booking',
            'name' => 'Booking.com Calendar - The Light House',
            'active' => true,
            'last_synced' => now()->subHours(2),
        ]);

        // Create ical records for Saras (venue_id: 2)
        Ical::create([
            'venue_id' => 2,
            'url' => 'https://www.airbnb.com/calendar/ical/YOUR_AIRBNB_LISTING_ID_2.ics',
            'source' => 'airbnb',
            'name' => 'Airbnb Calendar - Saras',
            'active' => true,
            'last_synced' => now()->subHour(),
        ]);

        Ical::create([
            'venue_id' => 2,
            'url' => 'https://admin.booking.com/hotel/hoteladmin/extranet_ng/manage/calendar.html?ses=YOUR_BOOKING_SESSION_2',
            'source' => 'booking',
            'name' => 'Booking.com Calendar - Saras',
            'active' => false,
            'last_synced' => null,
        ]);
    }
}
