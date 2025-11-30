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
        Schema::create('qa_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('qa_questions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->integer('votes_count')->default(0);
            $table->boolean('is_accepted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['question_id', 'is_accepted', 'votes_count']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qa_answers');
    }
};
