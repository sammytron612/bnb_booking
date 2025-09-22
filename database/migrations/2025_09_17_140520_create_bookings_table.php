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
        // Only create table if it doesn't exist
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone');
                $table->date('check_in'); // Check-in date
                $table->date('check_out');  // Check-out date
                $table->string('venue'); // Original venue string column (will be updated by later migration)
                $table->integer('nights')->default(0);
                $table->decimal('total_price', 10, 2)->default(0.00);
                $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
                $table->text('notes')->nullable();
                $table->string('pay_method')->nullable();
                $table->boolean('is_paid')->default(false);
                $table->date('review_link')->nullable();
                $table->date('check_in_reminder')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
