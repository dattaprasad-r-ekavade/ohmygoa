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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['free', 'business', 'admin'])->default('free')->after('email');
            $table->boolean('is_verified')->default(false)->after('role');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            $table->string('phone')->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('bio');
            $table->string('city')->nullable()->after('avatar');
            $table->string('state')->nullable()->after('city');
            $table->string('country')->default('India')->after('state');
            $table->boolean('is_active')->default(true)->after('country');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'is_verified',
                'verified_at',
                'phone',
                'bio',
                'avatar',
                'city',
                'state',
                'country',
                'is_active',
                'last_login_at',
                'deleted_at'
            ]);
        });
    }
};
