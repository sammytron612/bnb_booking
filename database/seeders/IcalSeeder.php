<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IcalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample entry based on your data structure
        \App\Models\Ical::create([
            'venue_id' => 1,
            'url' => 'http://eileen_bnb.test/test-calendar.ics',
            'source' => 'airbnb',
            'name' => 'Airbnb Calendar - The Light House',
            'active' => true,
            'last_synced_at' => '2025-09-25 08:43:35'
        ]);
    }
}
