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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('listing_approved')->default(true);
            $table->boolean('listing_rejected')->default(true);
            $table->boolean('new_enquiry')->default(true);
            $table->boolean('new_review')->default(true);
            $table->boolean('subscription_expiring')->default(true);
            $table->boolean('payment_received')->default(true);
            $table->boolean('payout_processed')->default(true);
            $table->boolean('new_message')->default(true);
            $table->boolean('job_application')->default(true);
            $table->boolean('product_order')->default(true);
            $table->boolean('marketing_emails')->default(false);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
