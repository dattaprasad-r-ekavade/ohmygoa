<?php

namespace App\Console\Commands;

use App\Services\SeoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class GenerateSitemapsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemaps:generate {--clear : Clear cached sitemaps before generating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemaps for all content types';

    /**
     * Execute the console command.
     */
    public function handle(SeoService $seoService)
    {
        $this->info('Starting sitemap generation...');

        // Clear cached sitemaps if requested
        if ($this->option('clear')) {
            $this->info('Clearing cached sitemaps...');
            Cache::forget('sitemap.index');
            Cache::forget('sitemap.listings');
            Cache::forget('sitemap.events');
            Cache::forget('sitemap.jobs');
            Cache::forget('sitemap.products');
            Cache::forget('sitemap.categories');
            Cache::forget('sitemap.locations');
            Cache::forget('sitemap.static');
        }

        $bar = $this->output->createProgressBar(8);
        $bar->start();

        // Generate sitemap index
        $this->generateSitemap(
            'sitemap.xml',
            $seoService->generateSitemapIndex(),
            'sitemap.index'
        );
        $bar->advance();

        // Generate listings sitemap
        $this->generateSitemap(
            'sitemap-listings.xml',
            $seoService->generateBusinessListingsSitemap(),
            'sitemap.listings'
        );
        $bar->advance();

        // Generate events sitemap
        $this->generateSitemap(
            'sitemap-events.xml',
            $seoService->generateEventsSitemap(),
            'sitemap.events'
        );
        $bar->advance();

        // Generate jobs sitemap
        $this->generateSitemap(
            'sitemap-jobs.xml',
            $seoService->generateJobsSitemap(),
            'sitemap.jobs'
        );
        $bar->advance();

        // Generate products sitemap
        $this->generateSitemap(
            'sitemap-products.xml',
            $seoService->generateProductsSitemap(),
            'sitemap.products'
        );
        $bar->advance();

        // Generate categories sitemap
        $this->generateSitemap(
            'sitemap-categories.xml',
            $seoService->generateCategoriesSitemap(),
            'sitemap.categories'
        );
        $bar->advance();

        // Generate locations sitemap
        $this->generateSitemap(
            'sitemap-locations.xml',
            $seoService->generateLocationsSitemap(),
            'sitemap.locations'
        );
        $bar->advance();

        // Generate static pages sitemap
        $this->generateStaticSitemap();
        $bar->advance();

        $bar->finish();
        $this->newLine(2);

        $this->info('✓ Sitemap generation completed successfully!');
        $this->info('All sitemaps have been generated and cached.');

        return Command::SUCCESS;
    }

    /**
     * Generate and cache a sitemap file
     */
    private function generateSitemap(string $filename, string $xml, string $cacheKey): void
    {
        // Save to public directory
        $path = public_path($filename);
        File::put($path, $xml);

        // Cache the sitemap
        Cache::put($cacheKey, $xml, 86400); // Cache for 24 hours
    }

    /**
     * Generate static pages sitemap
     */
    private function generateStaticSitemap(): void
    {
        $staticPages = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('about'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => route('contact'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => route('faq'), 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => route('privacy'), 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['url' => route('terms'), 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['url' => route('listings.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('events.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => route('jobs.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => route('products.index'), 'priority' => '0.8', 'changefreq' => 'daily'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($staticPages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($page['url']) . "</loc>\n";
            $xml .= "    <lastmod>" . now()->toIso8601String() . "</lastmod>\n";
            $xml .= "    <changefreq>{$page['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$page['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        $this->generateSitemap('sitemap-static.xml', $xml, 'sitemap.static');
    }
}

