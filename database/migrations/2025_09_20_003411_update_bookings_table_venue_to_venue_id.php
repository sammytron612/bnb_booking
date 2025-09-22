<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Booking;
use App\Models\Venue;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the new venue_id column if it doesn't exist
        if (!Schema::hasColumn('bookings', 'venue_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('venue_id')->nullable()->after('check_out')->constrained('venues')->onDelete('cascade');
            });
        }

        // Update existing bookings to use venue_id based on venue name
        if (Schema::hasColumn('bookings', 'venue')) {
            $bookings = Booking::all();
            foreach ($bookings as $booking) {
                if ($booking->venue) {
                    $venue = Venue::where('venue_name', $booking->venue)->first();
                    if ($venue) {
                        $booking->venue_id = $venue->id;
                        $booking->save();
                    }
                }
            }

            // Remove the old venue column
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('venue');
            });
        }

        // Make venue_id not nullable if it exists and is nullable
        if (Schema::hasColumn('bookings', 'venue_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->foreignId('venue_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the venue string column
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('venue')->after('check_out');
        });

        // Update bookings to use venue name based on venue_id
        $bookings = Booking::with('venue')->get();
        foreach ($bookings as $booking) {
            if ($booking->venue) {
                $booking->venue = $booking->venue->venue_name;
                $booking->save();
            }
        }

        // Drop the venue_id foreign key
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
            $table->dropColumn('venue_id');
        });
    }
};
