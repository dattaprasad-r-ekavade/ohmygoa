<?php

namespace App\Listeners;

use App\Events\SubscriptionExpiring;
use App\Services\NotificationService;

class SendSubscriptionExpiringNotification
{
    protected NotificationService $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(SubscriptionExpiring $event): void
    {
        $user = $event->user;
        $daysRemaining = $event->daysRemaining;

        // Send notification
        $this->notificationService->notifySubscriptionExpiring($user, $daysRemaining);
    }
}
