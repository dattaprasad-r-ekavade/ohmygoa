<?php

namespace App\Http\Controllers;

use App\Services\SeoService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    public function __construct(private SeoService $seoService)
    {
    }

    /**
     * Display the sitemap index
     */
    public function sitemapIndex(): Response
    {
        $xml = Cache::remember('sitemap.index', 86400, function () {
            return $this->seoService->generateSitemapIndex();
        });

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the business listings sitemap
     */
    public function sitemapListings(): Response
    {
        $xml = Cache::remember('sitemap.listings', 3600, function () {
            return $this->seoService->generateBusinessListingsSitemap();
        });

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the events sitemap
     */
    public function sitemapEvents(): Response
    {
        $xml = Cache::remember('sitemap.events', 3600, function () {
            return $this->seoService->generateEventsSitemap();
        });

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the jobs sitemap
     */
    public function sitemapJobs(): Response
    {
        $xml = Cache::remember('sitemap.jobs', 3600, function () {
            return $this->seoService->generateJobsSitemap();
        });

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the products sitemap
     */
    public function sitemapProducts(): Response
    {
        $xml = Cache::remember('sitemap.products', 3600, function () {
            return $this->seoService->generateProductsSitemap();
        });

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the categories sitemap
     */
    public function sitemapCategories(): Response
    {
        $xml = Cache::remember('sitemap.categories', 86400, function () {
            return $this->seoService->generateCategoriesSitemap();
        });

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the locations sitemap
     */
    public function sitemapLocations(): Response
    {
        $xml = Cache::remember('sitemap.locations', 86400, function () {
            return $this->seoService->generateLocationsSitemap();
        });

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the static pages sitemap
     */
    public function sitemapStatic(): Response
    {
        $xml = Cache::remember('sitemap.static', 86400, function () {
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
            return $xml;
        });

        return response($xml, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Display the robots.txt file
     */
    public function robotsTxt(): Response
    {
        $content = $this->seoService->generateRobotsTxt();

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
}
