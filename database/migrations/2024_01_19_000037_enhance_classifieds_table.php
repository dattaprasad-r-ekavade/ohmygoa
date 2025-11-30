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
        Schema::table('classifieds', function (Blueprint $table) {
            $table->enum('listing_type', ['free', 'featured', 'premium'])->default('free')->after('ad_type');
            $table->string('brand')->nullable()->after('condition');
            $table->string('model')->nullable()->after('brand');
            $table->integer('year')->nullable()->after('model');
            $table->json('specifications')->nullable()->after('year'); // For detailed specs
            $table->integer('quantity')->default(1)->after('specifications');
            $table->boolean('accepts_exchange')->default(false)->after('is_negotiable');
            $table->text('exchange_preferences')->nullable()->after('accepts_exchange');
            $table->integer('total_inquiries')->default(0)->after('views_count');
            $table->timestamp('featured_until')->nullable()->after('expires_at');
            $table->timestamp('bumped_at')->nullable()->after('featured_until'); // Last bumped to top
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classifieds', function (Blueprint $table) {
            $table->dropColumn([
                'listing_type',
                'brand',
                'model',
                'year',
                'specifications',
                'quantity',
                'accepts_exchange',
                'exchange_preferences',
                'total_inquiries',
                'featured_until',
                'bumped_at',
            ]);
        });
    }
};
