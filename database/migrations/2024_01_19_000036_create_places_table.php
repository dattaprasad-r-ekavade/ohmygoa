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
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('highlights')->nullable(); // Key features/highlights
            $table->enum('category', ['beach', 'church', 'temple', 'fort', 'museum', 'waterfall', 'viewpoint', 'market', 'wildlife', 'other'])->default('other');
            $table->foreignId('location_id')->constrained()->onDelete('restrict');
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('images')->nullable(); // Array of image paths
            $table->string('featured_image')->nullable();
            $table->json('timings')->nullable(); // Opening hours
            $table->string('entry_fee')->nullable();
            $table->text('best_time_to_visit')->nullable();
            $table->text('how_to_reach')->nullable();
            $table->json('facilities')->nullable(); // Parking, restroom, food, etc.
            $table->json('contact_info')->nullable(); // Phone, email, website
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('view_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('category');
            $table->index('location_id');
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_popular');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
