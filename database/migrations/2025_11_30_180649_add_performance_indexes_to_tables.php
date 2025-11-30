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
        // Business Listings indexes
        Schema::table('business_listings', function (Blueprint $table) {
            $table->index(['status', 'is_featured', 'created_at'], 'idx_listings_featured');
            $table->index(['status', 'views', 'rating'], 'idx_listings_popular');
            $table->index(['status', 'rating', 'reviews_count'], 'idx_listings_rated');
            $table->index(['category_id', 'status'], 'idx_listings_category');
            $table->index(['location_id', 'status'], 'idx_listings_location');
        });

        // Reviews indexes
        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['reviewable_type', 'reviewable_id', 'status'], 'idx_reviews_polymorphic');
            $table->index(['user_id', 'status'], 'idx_reviews_user');
        });

        // Events indexes
        Schema::table('events', function (Blueprint $table) {
            $table->index(['status', 'start_date'], 'idx_events_upcoming');
            $table->index(['category_id', 'status'], 'idx_events_category');
        });

        // Job Listings indexes
        Schema::table('job_listings', function (Blueprint $table) {
            $table->index(['status', 'deadline'], 'idx_jobs_active');
            $table->index(['category_id', 'status'], 'idx_jobs_category');
        });

        // Products indexes
        Schema::table('products', function (Blueprint $table) {
            $table->index(['status', 'is_featured'], 'idx_products_featured');
            $table->index(['category_id', 'status'], 'idx_products_category');
        });

        // Payments indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_payments_user');
            $table->index(['type', 'status'], 'idx_payments_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_listings', function (Blueprint $table) {
            $table->dropIndex('idx_listings_featured');
            $table->dropIndex('idx_listings_popular');
            $table->dropIndex('idx_listings_rated');
            $table->dropIndex('idx_listings_category');
            $table->dropIndex('idx_listings_location');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('idx_reviews_polymorphic');
            $table->dropIndex('idx_reviews_user');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_upcoming');
            $table->dropIndex('idx_events_category');
        });

        Schema::table('job_listings', function (Blueprint $table) {
            $table->dropIndex('idx_jobs_active');
            $table->dropIndex('idx_jobs_category');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_featured');
            $table->dropIndex('idx_products_category');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_user');
            $table->dropIndex('idx_payments_type');
        });
    }
};
