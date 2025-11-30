<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;

class CommissionService
{
    /**
     * Commission rate (10%).
     */
    const COMMISSION_RATE = 0.10;

    /**
     * Calculate commission from amount.
     */
    public function calculateCommission(float $amount): float
    {
        return round($amount * self::COMMISSION_RATE, 2);
    }

    /**
     * Calculate net amount after commission deduction.
     */
    public function calculateNetAmount(float $amount): float
    {
        return round($amount - $this->calculateCommission($amount), 2);
    }

    /**
     * Process commission on coupon sale.
     */
    public function processCommission(Payment $payment, User $business): void
    {
        if ($payment->payment_status !== 'completed') {
            return;
        }

        $commission = $this->calculateCommission($payment->amount);
        $netAmount = $this->calculateNetAmount($payment->amount);

        // Update payment with commission details
        $payment->update([
            'commission_amount' => $commission,
            'net_amount' => $netAmount,
        ]);

        // Add net amount to business wallet
        $business->increment('wallet_balance', $netAmount);

        // Log the transaction
        $this->logCommission($payment, $business, $commission, $netAmount);
    }

    /**
     * Log commission transaction.
     */
    private function logCommission(Payment $payment, User $business, float $commission, float $netAmount): void
    {
        // Create activity log
        \DB::table('activity_logs')->insert([
            'user_id' => $business->id,
            'action' => 'commission_deducted',
            'description' => "Commission deducted from payment {$payment->payment_id}",
            'metadata' => json_encode([
                'payment_id' => $payment->id,
                'gross_amount' => $payment->amount,
                'commission' => $commission,
                'net_amount' => $netAmount,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Get commission statistics for a business.
     */
    public function getCommissionStats(User $business): array
    {
        $payments = Payment::where('user_id', $business->id)
            ->where('payment_status', 'completed')
            ->get();

        $totalGross = $payments->sum('amount');
        $totalCommission = $payments->sum('commission_amount');
        $totalNet = $payments->sum('net_amount');

        return [
            'total_sales' => $totalGross,
            'total_commission' => $totalCommission,
            'total_earned' => $totalNet,
            'commission_rate' => self::COMMISSION_RATE * 100 . '%',
        ];
    }
}
