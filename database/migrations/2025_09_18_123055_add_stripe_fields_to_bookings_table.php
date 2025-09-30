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
            $table->string('stripe_payment_intent_id')->nullable()->after('is_paid');
            $table->string('stripe_session_id')->nullable()->after('stripe_payment_intent_id');
            $table->decimal('stripe_amount', 10, 2)->nullable()->after('stripe_session_id');
            $table->string('stripe_currency', 3)->default('gbp')->after('stripe_amount');
            $table->timestamp('payment_completed_at')->nullable()->after('stripe_currency');
            $table->json('stripe_metadata')->nullable()->after('payment_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_payment_intent_id',
                'stripe_session_id',
                'stripe_amount',
                'stripe_currency',
                'payment_completed_at',
                'stripe_metadata'
            ]);
        });
    }
};
