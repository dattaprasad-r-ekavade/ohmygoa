<?php

namespace App\Console\Commands;

use App\Models\BusinessListing;
use App\Models\Classified;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap for SEO';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $sitemap .= $this->addUrl(url('/'), now(), 'daily', '1.0');

        // Static pages
        $staticPages = ['about', 'contact', 'how-to', 'faq', 'pricing', 'privacy', 'terms'];
        foreach ($staticPages as $page) {
            $sitemap .= $this->addUrl(route($page), now()->subDays(30), 'monthly', '0.8');
        }

        // Business Listings
        $listings = BusinessListing::where('status', 'approved')
            ->select('slug', 'updated_at')
            ->get();
        
        foreach ($listings as $listing) {
            $sitemap .= $this->addUrl(
                route('listings.show', $listing->slug),
                $listing->updated_at,
                'weekly',
                '0.9'
            );
        }
        
        $this->line("Added {$listings->count()} business listings");

        // Events
        $events = Event::where('status', 'approved')
            ->select('slug', 'updated_at')
            ->get();
        
        foreach ($events as $event) {
            $sitemap .= $this->addUrl(
                route('events.show', $event->slug),
                $event->updated_at,
                'weekly',
                '0.8'
            );
        }
        
        $this->line("Added {$events->count()} events");

        // Jobs
        $jobs = JobListing::where('status', 'active')
            ->select('slug', 'updated_at')
            ->get();
        
        foreach ($jobs as $job) {
            $sitemap .= $this->addUrl(
                route('jobs.show', $job->slug),
                $job->updated_at,
                'weekly',
                '0.7'
            );
        }
        
        $this->line("Added {$jobs->count()} jobs");

        // Products
        $products = Product::where('status', 'approved')
            ->select('slug', 'updated_at')
            ->get();
        
        foreach ($products as $product) {
            $sitemap .= $this->addUrl(
                route('products.show', $product->slug),
                $product->updated_at,
                'weekly',
                '0.8'
            );
        }
        
        $this->line("Added {$products->count()} products");

        // Coupons
        $coupons = Coupon::where('status', 'approved')
            ->where('valid_until', '>', now())
            ->select('slug', 'updated_at')
            ->get();
        
        foreach ($coupons as $coupon) {
            $sitemap .= $this->addUrl(
                route('coupons.show', $coupon->slug),
                $coupon->updated_at,
                'daily',
                '0.9'
            );
        }
        
        $this->line("Added {$coupons->count()} coupons");

        // Classifieds
        $classifieds = Classified::where('status', 'approved')
            ->where('expires_at', '>', now())
            ->select('slug', 'updated_at')
            ->get();
        
        foreach ($classifieds as $classified) {
            $sitemap .= $this->addUrl(
                route('classifieds.show', $classified->slug),
                $classified->updated_at,
                'daily',
                '0.7'
            );
        }
        
        $this->line("Added {$classifieds->count()} classifieds");

        $sitemap .= '</urlset>';

        // Save sitemap
        $path = public_path('sitemap.xml');
        File::put($path, $sitemap);

        $this->info("Sitemap successfully generated at: {$path}");
        return 0;
    }

    /**
     * Add URL to sitemap.
     */
    protected function addUrl(string $url, $lastmod, string $changefreq, string $priority): string
    {
        return sprintf(
            '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%s</priority></url>',
            htmlspecialchars($url),
            $lastmod->toW3cString(),
            $changefreq,
            $priority
        );
    }
}
