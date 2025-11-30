<?php

namespace App\Console\Commands;

use App\Events\SubscriptionExpiring;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscriptions expiring in 3, 7, and 14 days and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expiring subscriptions...');

        $warningDays = [3, 7, 14];
        $totalNotified = 0;

        foreach ($warningDays as $days) {
            $targetDate = Carbon::now()->addDays($days)->startOfDay();
            
            $expiringUsers = User::whereNotNull('subscription_ends_at')
                ->whereDate('subscription_ends_at', $targetDate)
                ->whereNull('subscription_cancelled_at')
                ->get();

            foreach ($expiringUsers as $user) {
                // Dispatch event to send notification
                event(new SubscriptionExpiring($user, $days));
                $totalNotified++;
                
                $this->line("Notified {$user->name} - subscription expires in {$days} days");
            }
        }

        if ($totalNotified === 0) {
            $this->info('No expiring subscriptions found for notification.');
        } else {
            $this->info("Successfully notified {$totalNotified} users about expiring subscriptions.");
        }

        return 0;
    }
}
