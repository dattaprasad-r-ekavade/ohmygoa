<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Services\CommissionService;
use App\Services\NotificationService;

class ProcessPaymentCommission
{
    protected CommissionService $commissionService;
    protected NotificationService $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(CommissionService $commissionService, NotificationService $notificationService)
    {
        $this->commissionService = $commissionService;
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;
        $user = $payment->user;

        // Process commission if payment is for coupon sale
        if ($payment->payment_for === 'coupon_purchase' && $payment->status === 'completed') {
            $coupon = $payment->payable;
            
            if ($coupon && $coupon->user_id) {
                $this->commissionService->processCommission($payment);
            }
        }

        // Send notification
        $this->notificationService->notifyPaymentReceived($user, $payment);

        // Update user stats
        $user->increment('total_spent', $payment->amount);
    }
}
