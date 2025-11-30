<?php

namespace App\Listeners;

use App\Events\PayoutProcessed;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class SendPayoutNotification
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
    public function handle(PayoutProcessed $event): void
    {
        $user = $event->user;
        $amount = $event->amount;
        $status = $event->status;

        // Send notification
        if ($status === 'completed') {
            $this->notificationService->notifyPayoutProcessed($user, $amount);
        }

        // Log activity
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'activity_type' => 'payout_' . $status,
            'description' => "Payout of ₹{$amount} has been {$status}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
