<?php

namespace App\Listeners;

use App\Events\ListingApproved;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class SendListingApprovedNotification
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
    public function handle(ListingApproved $event): void
    {
        $listing = $event->listing;
        $user = $listing->user;

        // Send notification
        $this->notificationService->notifyListingApproved($user, $listing);

        // Update user stats
        $user->increment('total_listings_approved');

        // Log activity
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'activity_type' => 'listing_approved',
            'description' => "Your listing '{$listing->title}' has been approved",
            'reference_type' => 'App\Models\BusinessListing',
            'reference_id' => $listing->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
