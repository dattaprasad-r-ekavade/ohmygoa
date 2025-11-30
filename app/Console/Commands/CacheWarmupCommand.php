<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;

class CacheWarmupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warmup {--clear : Clear cache before warming up}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up application cache with frequently accessed data';

    protected CacheService $cacheService;

    /**
     * Create a new command instance.
     */
    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting cache warmup...');

        // Clear cache if requested
        if ($this->option('clear')) {
            $this->warn('Clearing existing cache...');
            $this->cacheService->clearAllCache();
            $this->info('Cache cleared successfully.');
        }

        $this->info('Warming up cache with frequently accessed data...');

        $progressBar = $this->output->createProgressBar(10);
        $progressBar->start();

        // Warm up categories
        $this->cacheService->getCategories();
        $progressBar->advance();

        foreach (['business', 'event', 'job', 'product'] as $type) {
            $this->cacheService->getCategories($type);
        }
        $progressBar->advance();

        // Warm up locations
        $this->cacheService->getLocations();
        $this->cacheService->getPopularLocations();
        $progressBar->advance();

        // Warm up featured listings
        $this->cacheService->getFeaturedListings();
        $progressBar->advance();

        // Warm up popular listings
        $this->cacheService->getPopularListings();
        $progressBar->advance();

        // Warm up recent listings
        $this->cacheService->getRecentListings();
        $progressBar->advance();

        // Warm up top-rated listings
        $this->cacheService->getTopRatedListings();
        $progressBar->advance();

        // Warm up settings
        $this->cacheService->getSettings();
        $progressBar->advance();

        // Warm up dashboard stats
        $this->cacheService->getDashboardStats();
        $progressBar->advance();

        $progressBar->finish();

        $this->newLine(2);
        $this->info('✓ Cache warmup completed successfully!');
        $this->info('All frequently accessed data has been cached.');

        return Command::SUCCESS;
    }
}
