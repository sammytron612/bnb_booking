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
        Schema::create('payment_failures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_intent_id');
            $table->string('stripe_session_id')->nullable();
            $table->string('decline_code')->nullable();
            $table->text('failure_reason')->nullable();
            $table->bigInteger('attempted_amount');
            $table->string('currency', 3)->default('gbp');
            $table->string('payment_method')->default('stripe_checkout');
            $table->json('stripe_error_data')->nullable();
            $table->timestamp('failed_at');
            $table->timestamps();

            $table->index(['booking_id', 'failed_at']);
            $table->index('decline_code');
            $table->index('stripe_payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_failures');
    }
};
