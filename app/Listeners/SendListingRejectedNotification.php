<?php

namespace App\Listeners;

use App\Events\ListingRejected;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class SendListingRejectedNotification
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
    public function handle(ListingRejected $event): void
    {
        $listing = $event->listing;
        $user = $listing->user;
        $reason = $event->reason;

        // Send notification
        $this->notificationService->notifyListingRejected($user, $listing, $reason);

        // Log activity
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'activity_type' => 'listing_rejected',
            'description' => "Your listing '{$listing->title}' has been rejected: {$reason}",
            'reference_type' => 'App\Models\BusinessListing',
            'reference_id' => $listing->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
