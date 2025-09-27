<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ical', function (Blueprint $table) {
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Airbnb", "Booking.com"
            $table->text('url'); // iCal URL
            $table->boolean('active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->integer('bookings_count')->default(0);
            $table->text('last_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ical', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
            $table->dropColumn([
                'venue_id',
                'name',
                'url',
                'active',
                'last_synced_at',
                'bookings_count',
                'last_error'
            ]);
        });
    }
};
