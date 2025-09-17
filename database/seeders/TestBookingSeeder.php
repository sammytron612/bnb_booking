<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use Carbon\Carbon;

class TestBookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some test bookings
        Booking::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123-456-7890',
            'depart' => Carbon::today()->addDays(5)->format('Y-m-d'),
            'leave' => Carbon::today()->addDays(8)->format('Y-m-d'),
            'venue' => 'The Light House',
            'nights' => 3,
            'total_price' => 360.00,
            'status' => 'confirmed'
        ]);

        Booking::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'phone' => '098-765-4321',
            'depart' => Carbon::today()->addDays(15)->format('Y-m-d'),
            'leave' => Carbon::today()->addDays(18)->format('Y-m-d'),
            'venue' => 'The Light House',
            'nights' => 3,
            'total_price' => 360.00,
            'status' => 'pending'
        ]);

        Booking::create([
            'name' => 'Bob Wilson',
            'email' => 'bob@example.com',
            'phone' => '555-123-4567',
            'depart' => Carbon::today()->addDays(25)->format('Y-m-d'),
            'leave' => Carbon::today()->addDays(27)->format('Y-m-d'),
            'venue' => 'Saras',
            'nights' => 2,
            'total_price' => 240.00,
            'status' => 'confirmed'
        ]);

        echo "Created test bookings:\n";
        echo "1. " . Carbon::today()->addDays(5)->format('Y-m-d') . " to " . Carbon::today()->addDays(8)->format('Y-m-d') . " (The Light House)\n";
        echo "2. " . Carbon::today()->addDays(15)->format('Y-m-d') . " to " . Carbon::today()->addDays(18)->format('Y-m-d') . " (The Light House)\n";
        echo "3. " . Carbon::today()->addDays(25)->format('Y-m-d') . " to " . Carbon::today()->addDays(27)->format('Y-m-d') . " (Saras)\n";
    }
}
