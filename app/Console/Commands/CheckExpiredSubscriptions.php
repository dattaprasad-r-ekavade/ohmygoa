<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and expire subscriptions that have ended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        $expiredCount = User::whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<=', now())
            ->whereNull('subscription_cancelled_at')
            ->count();

        if ($expiredCount === 0) {
            $this->info('No expired subscriptions found.');
            return 0;
        }

        // Update expired subscriptions
        $updated = User::whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<=', now())
            ->whereNull('subscription_cancelled_at')
            ->update([
                'subscription_ends_at' => null,
                'is_premium' => false,
            ]);

        // Log activity for each expired user
        $expiredUsers = User::whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<=', now())
            ->get();

        foreach ($expiredUsers as $user) {
            DB::table('activity_logs')->insert([
                'user_id' => $user->id,
                'activity_type' => 'subscription_expired',
                'description' => 'Your premium subscription has expired',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info("Successfully expired {$updated} subscriptions.");
        return 0;
    }
}
