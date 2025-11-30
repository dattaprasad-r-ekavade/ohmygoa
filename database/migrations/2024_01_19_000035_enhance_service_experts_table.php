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
        Schema::table('service_experts', function (Blueprint $table) {
            $table->json('working_hours')->nullable()->after('is_available');
            $table->string('languages_spoken')->nullable()->after('working_hours');
            $table->string('insurance_details')->nullable()->after('languages_spoken');
            $table->decimal('hourly_rate', 8, 2)->nullable()->after('insurance_details');
            $table->decimal('minimum_charge', 8, 2)->nullable()->after('hourly_rate');
            $table->boolean('offers_emergency_service')->default(false)->after('minimum_charge');
            $table->integer('response_time_hours')->nullable()->after('offers_emergency_service'); // Average response time
            $table->integer('completion_rate')->default(0)->after('response_time_hours'); // Percentage
            $table->integer('total_bookings')->default(0)->after('completion_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_experts', function (Blueprint $table) {
            $table->dropColumn([
                'working_hours',
                'languages_spoken',
                'insurance_details',
                'hourly_rate',
                'minimum_charge',
                'offers_emergency_service',
                'response_time_hours',
                'completion_rate',
                'total_bookings',
            ]);
        });
    }
};
