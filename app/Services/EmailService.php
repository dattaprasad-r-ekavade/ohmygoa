<?php

namespace App\Services;

use App\Mail\NotificationEmail;
use App\Mail\PasswordResetEmail;
use App\Mail\PaymentReceiptEmail;
use App\Mail\VerificationEmail;
use App\Mail\WelcomeEmail;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send welcome email to new user
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
            Log::info("Welcome email sent to user: {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send welcome email to {$user->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email verification link
     */
    public function sendVerificationEmail(User $user, string $verificationUrl): bool
    {
        try {
            Mail::to($user->email)->send(new VerificationEmail($user, $verificationUrl));
            Log::info("Verification email sent to user: {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send verification email to {$user->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(User $user, string $resetUrl, string $token): bool
    {
        try {
            Mail::to($user->email)->send(new PasswordResetEmail($user, $resetUrl, $token));
            Log::info("Password reset email sent to user: {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send password reset email to {$user->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment receipt email
     */
    public function sendPaymentReceiptEmail(Payment $payment): bool
    {
        try {
            Mail::to($payment->user->email)->send(new PaymentReceiptEmail($payment));
            Log::info("Payment receipt sent to user: {$payment->user->email} for transaction: {$payment->transaction_id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send payment receipt to {$payment->user->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send generic notification email
     */
    public function sendNotificationEmail(
        User $user, 
        string $title, 
        string $message, 
        ?string $actionUrl = null, 
        ?string $actionText = null
    ): bool {
        try {
            Mail::to($user->email)->send(
                new NotificationEmail($user, $title, $message, $actionUrl, $actionText)
            );
            Log::info("Notification email sent to user: {$user->email} - {$title}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send notification email to {$user->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send listing approved notification
     */
    public function sendListingApprovedEmail(User $user, $listing): bool
    {
        return $this->sendNotificationEmail(
            $user,
            'Your Listing Has Been Approved!',
            "Great news! Your listing '{$listing->title}' has been approved and is now live on Ohmygoa.",
            route('listings.show', $listing->slug),
            'View Your Listing'
        );
    }

    /**
     * Send listing rejected notification
     */
    public function sendListingRejectedEmail(User $user, $listing, string $reason = ''): bool
    {
        $message = "Your listing '{$listing->title}' was not approved.";
        if ($reason) {
            $message .= "\n\nReason: {$reason}";
        }
        $message .= "\n\nPlease review our guidelines and make necessary changes before resubmitting.";

        return $this->sendNotificationEmail(
            $user,
            'Listing Review Update',
            $message,
            route('listings.edit', $listing->id),
            'Edit Listing'
        );
    }

    /**
     * Send new review notification to business owner
     */
    public function sendNewReviewEmail(User $businessOwner, $review, $listing): bool
    {
        $rating = str_repeat('⭐', $review->rating);
        $message = "You received a new {$review->rating}-star review on your listing '{$listing->title}'.\n\n";
        $message .= "Review: \"{$review->comment}\"";

        return $this->sendNotificationEmail(
            $businessOwner,
            "New Review: {$rating}",
            $message,
            route('listings.show', $listing->slug) . '#reviews',
            'View Review'
        );
    }

    /**
     * Send enquiry notification to business owner
     */
    public function sendEnquiryNotificationEmail(User $businessOwner, $enquiry, $listing): bool
    {
        $message = "You have received a new enquiry for '{$listing->title}'.\n\n";
        $message .= "From: {$enquiry->name} ({$enquiry->email})\n";
        $message .= "Phone: {$enquiry->phone}\n\n";
        $message .= "Message: {$enquiry->message}";

        return $this->sendNotificationEmail(
            $businessOwner,
            'New Enquiry Received',
            $message,
            route('dashboard.enquiries'),
            'View Enquiries'
        );
    }

    /**
     * Send subscription activated email
     */
    public function sendSubscriptionActivatedEmail(User $user): bool
    {
        $message = "Your premium subscription has been activated successfully!\n\n";
        $message .= "You now have access to:\n";
        $message .= "- Create unlimited business listings\n";
        $message .= "- Post events and job opportunities\n";
        $message .= "- Sell products and offer coupons\n";
        $message .= "- Access detailed analytics\n";
        $message .= "- Priority customer support";

        return $this->sendNotificationEmail(
            $user,
            'Welcome to Premium! 🎉',
            $message,
            route('dashboard'),
            'Go to Dashboard'
        );
    }

    /**
     * Send subscription expiring soon reminder
     */
    public function sendSubscriptionExpiringEmail(User $user, int $daysLeft): bool
    {
        $message = "Your premium subscription will expire in {$daysLeft} days.\n\n";
        $message .= "Don't lose access to your premium features! Renew your subscription today to continue enjoying unlimited listings, analytics, and priority support.";

        return $this->sendNotificationEmail(
            $user,
            'Subscription Expiring Soon',
            $message,
            route('subscription.renew'),
            'Renew Subscription'
        );
    }

    /**
     * Send payout processed email
     */
    public function sendPayoutProcessedEmail(User $user, $payout): bool
    {
        $message = "Your payout request has been processed successfully!\n\n";
        $message .= "Amount: ₹" . number_format($payout->amount, 2) . "\n";
        $message .= "Transaction ID: {$payout->transaction_id}\n";
        $message .= "Payment Method: {$payout->payment_method}\n\n";
        $message .= "The amount should reflect in your account within 3-5 business days.";

        return $this->sendNotificationEmail(
            $user,
            'Payout Processed',
            $message,
            route('dashboard.wallet'),
            'View Wallet'
        );
    }
}
