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
        Schema::create('qa_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('content');
            $table->json('tags')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('answers_count')->default(0);
            $table->integer('votes_count')->default(0);
            $table->foreignId('accepted_answer_id')->nullable()->constrained('qa_answers')->nullOnDelete();
            $table->boolean('is_answered')->default(false);
            $table->enum('status', ['active', 'closed', 'deleted'])->default('active');
            $table->timestamp('closed_at')->nullable();
            $table->string('closed_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['category_id', 'is_answered']);
            $table->index('votes_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qa_questions');
    }
};
