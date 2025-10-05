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
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_decline_code',
                'payment_failure_reason',
                'payment_failed_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('stripe_decline_code')->nullable();
            $table->text('payment_failure_reason')->nullable();
            $table->timestamp('payment_failed_at')->nullable();
        });
    }
};
