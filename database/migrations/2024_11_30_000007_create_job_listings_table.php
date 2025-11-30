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
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('location_id')->constrained()->onDelete('restrict');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->string('company_name');
            $table->string('company_website')->nullable();
            $table->string('company_logo')->nullable();
            $table->enum('job_type', ['full-time', 'part-time', 'contract', 'freelance', 'internship'])->default('full-time');
            $table->enum('experience_level', ['entry', 'mid', 'senior', 'lead'])->default('mid');
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency')->default('INR');
            $table->enum('salary_period', ['hourly', 'monthly', 'yearly'])->default('monthly');
            $table->json('skills')->nullable();
            $table->json('benefits')->nullable();
            $table->string('application_email')->nullable();
            $table->string('application_url')->nullable();
            $table->date('application_deadline')->nullable();
            $table->integer('vacancies')->default(1);
            $table->integer('applications_count')->default(0);
            $table->enum('status', ['draft', 'active', 'closed', 'filled'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_remote')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_active']);
            $table->index(['category_id', 'location_id', 'status']);
            $table->index('slug');
            $table->index('job_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
