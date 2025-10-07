<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('booking_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->string('stripe_dispute_id')->unique();
            $table->string('stripe_charge_id');
            $table->integer('amount'); // Amount in pence
            $table->string('currency', 3)->default('gbp');
            $table->string('reason'); // fraudulent, unrecognized, duplicate, etc.
            $table->string('status'); // warning_needs_response, warning_under_review, warning_closed, needs_response, under_review, charge_refunded, won, lost
            $table->text('evidence_details')->nullable(); // JSON of evidence submitted
            $table->timestamp('evidence_due_by')->nullable();
            $table->timestamp('created_at_stripe'); // When dispute was created at Stripe
            $table->boolean('admin_notified')->default(false);
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index('created_at_stripe');
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_disputes');
    }
};
