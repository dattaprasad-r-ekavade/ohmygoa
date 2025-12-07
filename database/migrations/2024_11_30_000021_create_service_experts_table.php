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
        Schema::create('service_experts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('location_id')->constrained()->onDelete('restrict');
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->json('services_offered')->nullable();
            $table->json('service_areas')->nullable();
            $table->string('contact_phone', 20);
            $table->string('contact_email')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->json('certifications')->nullable();
            $table->json('skills')->nullable();
            $table->json('languages_spoken')->nullable();
            $table->json('working_hours')->nullable();
            $table->text('insurance_details')->nullable();
            $table->string('availability')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('minimum_charge', 10, 2)->nullable();
            $table->boolean('offers_emergency_service')->default(false);
            $table->unsignedInteger('response_time_hours')->nullable();
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->unsignedInteger('total_bookings')->default(0);
            $table->unsignedInteger('jobs_completed')->default(0);
            $table->json('portfolio_images')->nullable();
            $table->string('profile_image')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_active']);
            $table->index(['category_id', 'location_id', 'status']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_experts');
    }
};
