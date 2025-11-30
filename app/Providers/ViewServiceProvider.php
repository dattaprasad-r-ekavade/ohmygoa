<?php

namespace App\Providers;

use App\View\Composers\CategoryComposer;
use App\View\Composers\LocationComposer;
use App\View\Composers\SettingsComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        // Register view composers
        View::composer('*', CategoryComposer::class);
        View::composer('*', LocationComposer::class);
        View::composer('*', SettingsComposer::class);

        // Custom Blade directives for roles
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->user_role === $role;
        });

        Blade::if('hasrole', function (...$roles) {
            return auth()->check() && in_array(auth()->user()->user_role, $roles);
        });

        Blade::if('business', function () {
            return auth()->check() && auth()->user()->user_role === 'Business';
        });

        Blade::if('admin', function () {
            return auth()->check() && auth()->user()->user_role === 'Admin';
        });

        Blade::if('premium', function () {
            return auth()->check() && auth()->user()->is_premium;
        });

        Blade::if('subscribed', function () {
            return auth()->check() && 
                   auth()->user()->subscription_ends_at && 
                   auth()->user()->subscription_ends_at->isFuture();
        });

        // Custom Blade directive for currency formatting
        Blade::directive('currency', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::format($expression); ?>";
        });

        // Custom Blade directive for date formatting
        Blade::directive('datetime', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::formatWithTime($expression); ?>";
        });

        Blade::directive('dateonly', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::format($expression); ?>";
        });

        Blade::directive('timeago', function ($expression) {
            return "<?php echo \App\Helpers\DateHelper::diffForHumans($expression); ?>";
        });

        // Custom Blade directive for string truncation
        Blade::directive('excerpt', function ($expression) {
            return "<?php echo \App\Helpers\StringHelper::excerpt($expression); ?>";
        });
    }
}
