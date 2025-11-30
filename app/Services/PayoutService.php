<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    /**
     * Minimum payout threshold (₹1000).
     */
    const MINIMUM_THRESHOLD = 1000;

    /**
     * Check if user is eligible for payout.
     */
    public function isEligibleForPayout(User $user): bool
    {
        return $user->wallet_balance >= self::MINIMUM_THRESHOLD;
    }

    /**
     * Create payout request.
     */
    public function createPayoutRequest(User $user, array $bankDetails): array
    {
        if (!$this->isEligibleForPayout($user)) {
            throw new \Exception("Minimum payout threshold is ₹" . self::MINIMUM_THRESHOLD);
        }

        $payoutId = 'payout_' . uniqid();
        $amount = $user->wallet_balance;

        DB::beginTransaction();
        try {
            // Create payout record
            $payout = DB::table('payouts')->insertGetId([
                'user_id' => $user->id,
                'payout_id' => $payoutId,
                'amount' => $amount,
                'bank_details' => json_encode($bankDetails),
                'status' => 'pending',
                'requested_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Deduct from wallet
            $user->decrement('wallet_balance', $amount);

            // Log activity
            DB::table('activity_logs')->insert([
                'user_id' => $user->id,
                'action' => 'payout_requested',
                'description' => "Payout request created for ₹{$amount}",
                'metadata' => json_encode([
                    'payout_id' => $payoutId,
                    'amount' => $amount,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'payout_id' => $payoutId,
                'amount' => $amount,
                'status' => 'pending',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Approve payout (Admin action).
     */
    public function approvePayout(string $payoutId, string $transactionId = null): void
    {
        DB::table('payouts')
            ->where('payout_id', $payoutId)
            ->update([
                'status' => 'completed',
                'transaction_id' => $transactionId,
                'completed_at' => now(),
                'updated_at' => now(),
            ]);

        $payout = DB::table('payouts')->where('payout_id', $payoutId)->first();

        // Log activity
        DB::table('activity_logs')->insert([
            'user_id' => $payout->user_id,
            'action' => 'payout_completed',
            'description' => "Payout completed for ₹{$payout->amount}",
            'metadata' => json_encode([
                'payout_id' => $payoutId,
                'transaction_id' => $transactionId,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reject payout (Admin action).
     */
    public function rejectPayout(string $payoutId, string $reason): void
    {
        $payout = DB::table('payouts')->where('payout_id', $payoutId)->first();

        DB::beginTransaction();
        try {
            // Update payout status
            DB::table('payouts')
                ->where('payout_id', $payoutId)
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                    'rejected_at' => now(),
                    'updated_at' => now(),
                ]);

            // Refund to wallet
            User::find($payout->user_id)->increment('wallet_balance', $payout->amount);

            // Log activity
            DB::table('activity_logs')->insert([
                'user_id' => $payout->user_id,
                'action' => 'payout_rejected',
                'description' => "Payout rejected: {$reason}",
                'metadata' => json_encode([
                    'payout_id' => $payoutId,
                    'amount' => $payout->amount,
                    'reason' => $reason,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get user's payout history.
     */
    public function getPayoutHistory(User $user)
    {
        return DB::table('payouts')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get pending payouts (Admin).
     */
    public function getPendingPayouts()
    {
        return DB::table('payouts')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
