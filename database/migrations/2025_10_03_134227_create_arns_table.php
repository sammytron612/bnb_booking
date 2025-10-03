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
        Schema::create('arns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('refund_id'); // Stripe refund ID
            $table->string('arn_number')->nullable(); // The actual ARN from Stripe
            $table->decimal('refund_amount', 10, 2); // Amount of this specific refund
            $table->string('currency', 3)->default('gbp');
            $table->string('status')->default('pending'); // pending, succeeded, failed
            $table->timestamp('refund_processed_at')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->unique('refund_id'); // Each Stripe refund ID should be unique
            $table->index(['booking_id', 'refund_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arns');
    }
};
