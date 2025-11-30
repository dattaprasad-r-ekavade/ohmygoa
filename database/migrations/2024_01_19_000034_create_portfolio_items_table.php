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
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_expert_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('images')->nullable(); // Array of image paths
            $table->string('project_type')->nullable(); // e.g., residential, commercial, personal
            $table->date('completion_date')->nullable();
            $table->string('client_name')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('order_number')->default(0);
            $table->timestamps();

            $table->index('service_expert_id');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_items');
    }
};
