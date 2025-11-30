<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Define Gates for user roles
        Gate::define('access-admin', function (User $user) {
            return $user->hasRole(UserRole::ADMIN);
        });

        Gate::define('access-business', function (User $user) {
            return $user->hasRole(UserRole::BUSINESS) || $user->hasRole(UserRole::ADMIN);
        });

        Gate::define('manage-listings', function (User $user) {
            return $user->hasPermission('create_listing') || $user->hasPermission('manage_listings');
        });

        Gate::define('create-listing', function (User $user) {
            return $user->hasPermission('create_listing');
        });

        Gate::define('create-event', function (User $user) {
            return $user->hasPermission('create_event');
        });

        Gate::define('create-job', function (User $user) {
            return $user->hasPermission('create_job');
        });

        Gate::define('create-product', function (User $user) {
            return $user->hasPermission('create_product');
        });

        Gate::define('create-coupon', function (User $user) {
            return $user->hasPermission('create_coupon');
        });

        Gate::define('manage-users', function (User $user) {
            return $user->hasPermission('manage_users');
        });

        Gate::define('approve-content', function (User $user) {
            return $user->hasPermission('approve_content');
        });

        Gate::define('view-analytics', function (User $user) {
            return $user->hasPermission('view_analytics') || $user->hasPermission('view_all_analytics');
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->hasPermission('manage_settings');
        });
    }
}
