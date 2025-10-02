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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('code', 6); // 6-digit OTP code
            $table->timestamp('expires_at'); // When the OTP expires
            $table->timestamp('verified_at')->nullable(); // When OTP was verified
            $table->boolean('used')->default(false); // Whether OTP has been used
            $table->string('ip_address')->nullable(); // Track IP for security
            $table->string('user_agent')->nullable(); // Track user agent
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index for performance
            $table->index(['user_id', 'code']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
