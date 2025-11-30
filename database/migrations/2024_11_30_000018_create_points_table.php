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
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('amount');
            $table->integer('balance_after');
            $table->enum('type', ['credit', 'debit'])->default('credit');
            $table->enum('reason', [
                'purchase',
                'referral',
                'promotion',
                'listing_boost',
                'featured_listing',
                'admin_adjustment',
                'refund'
            ]);
            $table->text('description')->nullable();
            $table->morphs('transactionable'); // nullable, for tracking what triggered this
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'reason']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
