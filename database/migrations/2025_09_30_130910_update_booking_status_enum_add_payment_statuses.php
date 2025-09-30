<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum to include new payment-related statuses
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'payment_expired', 'abandoned') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if any bookings use the new statuses before removing them
        $hasPaymentExpired = DB::table('bookings')->where('status', 'payment_expired')->exists();
        $hasAbandoned = DB::table('bookings')->where('status', 'abandoned')->exists();

        if ($hasPaymentExpired || $hasAbandoned) {
            throw new Exception('Cannot rollback: There are bookings with payment_expired or abandoned status. Please update these records first.');
        }

        // Revert to original enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending'");
    }
};
