<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Mock Razorpay payment initialization.
     */
    public function initializePayment(User $user, float $amount, string $purpose, array $metadata = []): array
    {
        $orderId = 'order_' . Str::random(14);
        $paymentId = 'pay_' . Str::random(14);

        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'payment_id' => $paymentId,
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => 'INR',
            'payment_method' => 'razorpay',
            'payment_status' => 'pending',
            'purpose' => $purpose,
            'metadata' => $metadata,
        ]);

        return [
            'order_id' => $orderId,
            'payment_id' => $paymentId,
            'amount' => $amount * 100, // Razorpay expects amount in paise
            'currency' => 'INR',
            'key' => config('services.razorpay.key_id', 'rzp_test_mock_key'),
            'name' => config('app.name'),
            'description' => $purpose,
            'prefill' => [
                'name' => $user->name,
                'email' => $user->email,
                'contact' => $user->phone ?? '',
            ],
        ];
    }

    /**
     * Verify and complete mock payment.
     */
    public function verifyPayment(string $paymentId, string $orderId, string $signature = null): bool
    {
        $payment = Payment::where('payment_id', $paymentId)
            ->where('order_id', $orderId)
            ->first();

        if (!$payment) {
            return false;
        }

        // Mock verification - in production, verify signature with Razorpay
        $payment->update([
            'payment_status' => 'completed',
            'payment_signature' => $signature ?? Str::random(40),
            'paid_at' => now(),
        ]);

        return true;
    }

    /**
     * Process payment failure.
     */
    public function handlePaymentFailure(string $paymentId, string $reason = null): void
    {
        $payment = Payment::where('payment_id', $paymentId)->first();

        if ($payment) {
            $payment->update([
                'payment_status' => 'failed',
                'failure_reason' => $reason,
            ]);
        }
    }

    /**
     * Get payment details.
     */
    public function getPayment(string $paymentId): ?Payment
    {
        return Payment::where('payment_id', $paymentId)->first();
    }

    /**
     * Create refund (mock).
     */
    public function createRefund(Payment $payment, float $amount = null): array
    {
        $refundAmount = $amount ?? $payment->amount;
        $refundId = 'rfnd_' . Str::random(14);

        $payment->update([
            'refund_status' => 'refunded',
            'refund_amount' => $refundAmount,
            'refund_id' => $refundId,
            'refunded_at' => now(),
        ]);

        return [
            'refund_id' => $refundId,
            'amount' => $refundAmount,
            'status' => 'processed',
        ];
    }

    /**
     * Get user's payment history.
     */
    public function getUserPayments(User $user, int $limit = 10)
    {
        return Payment::where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
