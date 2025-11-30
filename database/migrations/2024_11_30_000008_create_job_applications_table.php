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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('applicant_phone')->nullable();
            $table->string('resume_path');
            $table->text('cover_letter')->nullable();
            $table->json('additional_info')->nullable();
            $table->enum('status', ['pending', 'shortlisted', 'rejected', 'interviewed', 'hired'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['job_listing_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->unique(['job_listing_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
