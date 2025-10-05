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
        Schema::table('bookings', function (Blueprint $table) {
            // Change stripe_amount from DECIMAL(10,2) to BIGINT
            // This allows storing Stripe amounts exactly as sent (in cents/pence)
            $table->bigInteger('stripe_amount')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Revert back to DECIMAL(10,2)
            $table->decimal('stripe_amount', 10, 2)->nullable()->change();
        });
    }
};
