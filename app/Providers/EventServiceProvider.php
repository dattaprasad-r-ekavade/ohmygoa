<?php

namespace App\Providers;

use App\Events\JobApplicationReceived;
use App\Events\ListingApproved;
use App\Events\ListingRejected;
use App\Events\PaymentReceived;
use App\Events\PayoutProcessed;
use App\Events\SubscriptionExpiring;
use App\Listeners\ProcessPaymentCommission;
use App\Listeners\SendJobApplicationNotification;
use App\Listeners\SendListingApprovedNotification;
use App\Listeners\SendListingRejectedNotification;
use App\Listeners\SendPayoutNotification;
use App\Listeners\SendSubscriptionExpiringNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ListingApproved::class => [
            SendListingApprovedNotification::class,
        ],
        ListingRejected::class => [
            SendListingRejectedNotification::class,
        ],
        PaymentReceived::class => [
            ProcessPaymentCommission::class,
        ],
        PayoutProcessed::class => [
            SendPayoutNotification::class,
        ],
        SubscriptionExpiring::class => [
            SendSubscriptionExpiringNotification::class,
        ],
        JobApplicationReceived::class => [
            SendJobApplicationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
