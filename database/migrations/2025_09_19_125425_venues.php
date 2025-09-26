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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('venue_name');
            $table->text('description1')->nullable();
            $table->text('description2')->nullable();
            $table->text('description3')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('guest_capacity')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('postcode');
            $table->string('theme_color')->default('blue');
            $table->string('route')->nullable();
            $table->string('badge_text')->nullable();
            $table->boolean('booking_enabled')->default(true);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
