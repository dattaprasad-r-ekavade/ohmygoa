<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class CleanupOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=90 : Number of days to keep notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old notifications older than specified days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        $this->info("Cleaning up notifications older than {$days} days...");

        $cutoffDate = now()->subDays($days);

        $deletedCount = Notification::where('created_at', '<', $cutoffDate)
            ->delete();

        if ($deletedCount === 0) {
            $this->info('No old notifications found to delete.');
        } else {
            $this->info("Successfully deleted {$deletedCount} old notifications.");
        }

        return 0;
    }
}
