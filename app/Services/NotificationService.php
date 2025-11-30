<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create notification for user.
     */
    public function create(User $user, string $title, string $message, string $type = 'info', array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Send email notification.
     */
    public function sendEmail(User $user, string $subject, string $view, array $data = []): void
    {
        try {
            Mail::send($view, $data, function ($message) use ($user, $subject) {
                $message->to($user->email, $user->name)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error('Email notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Notify about listing approval.
     */
    public function notifyListingApproved(User $user, $listing): void
    {
        $this->create(
            $user,
            'Listing Approved',
            "Your listing '{$listing->name}' has been approved and is now live!",
            'success',
            ['listing_id' => $listing->id, 'listing_slug' => $listing->slug]
        );

        $this->sendEmail($user, 'Your Listing Has Been Approved', 'emails.listing-approved', [
            'user' => $user,
            'listing' => $listing,
        ]);
    }

    /**
     * Notify about listing rejection.
     */
    public function notifyListingRejected(User $user, $listing, string $reason = null): void
    {
        $message = "Your listing '{$listing->name}' was not approved.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        $this->create(
            $user,
            'Listing Rejected',
            $message,
            'error',
            ['listing_id' => $listing->id, 'reason' => $reason]
        );

        $this->sendEmail($user, 'Listing Review Update', 'emails.listing-rejected', [
            'user' => $user,
            'listing' => $listing,
            'reason' => $reason,
        ]);
    }

    /**
     * Notify about payment received.
     */
    public function notifyPaymentReceived(User $user, $payment): void
    {
        $this->create(
            $user,
            'Payment Received',
            "Payment of ₹{$payment->amount} received successfully.",
            'success',
            ['payment_id' => $payment->id, 'amount' => $payment->amount]
        );

        $this->sendEmail($user, 'Payment Confirmation', 'emails.payment-received', [
            'user' => $user,
            'payment' => $payment,
        ]);
    }

    /**
     * Notify about payout processed.
     */
    public function notifyPayoutProcessed(User $user, $payout): void
    {
        $this->create(
            $user,
            'Payout Processed',
            "Your payout of ₹{$payout->amount} has been processed.",
            'success',
            ['payout_id' => $payout->payout_id, 'amount' => $payout->amount]
        );

        $this->sendEmail($user, 'Payout Completed', 'emails.payout-processed', [
            'user' => $user,
            'payout' => $payout,
        ]);
    }

    /**
     * Notify about subscription expiring soon.
     */
    public function notifySubscriptionExpiring(User $user, int $daysRemaining): void
    {
        $this->create(
            $user,
            'Subscription Expiring Soon',
            "Your premium subscription expires in {$daysRemaining} days. Renew now to continue enjoying premium features.",
            'warning',
            ['days_remaining' => $daysRemaining]
        );

        $this->sendEmail($user, 'Subscription Expiring Soon', 'emails.subscription-expiring', [
            'user' => $user,
            'days_remaining' => $daysRemaining,
        ]);
    }

    /**
     * Notify about new job application.
     */
    public function notifyNewJobApplication(User $user, $job, $application): void
    {
        $this->create(
            $user,
            'New Job Application',
            "You received a new application for '{$job->title}'.",
            'info',
            ['job_id' => $job->id, 'application_id' => $application->id]
        );

        $this->sendEmail($user, 'New Job Application Received', 'emails.new-job-application', [
            'user' => $user,
            'job' => $job,
            'application' => $application,
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->update(['is_read' => true, 'read_at' => now()]);
    }

    /**
     * Mark all user notifications as read.
     */
    public function markAllAsRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    /**
     * Get unread count for user.
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Delete old notifications (run via cron).
     */
    public function deleteOldNotifications(int $daysOld = 30): int
    {
        return Notification::where('created_at', '<=', now()->subDays($daysOld))
            ->delete();
    }
}
