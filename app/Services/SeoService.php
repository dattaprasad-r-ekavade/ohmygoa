<?php

namespace App\Services;

use App\Models\BusinessListing;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;

class SeoService
{
    /**
     * Generate meta tags for a page
     */
    public function generateMetaTags(
        string $title,
        string $description,
        ?string $image = null,
        ?string $url = null,
        string $type = 'website'
    ): array {
        $url = $url ?? URL::current();
        $image = $image ?? asset('images/og-default.jpg');
        
        return [
            'title' => $this->sanitizeTitle($title),
            'description' => $this->sanitizeDescription($description),
            'canonical' => $url,
            'og_title' => $this->sanitizeTitle($title),
            'og_description' => $this->sanitizeDescription($description),
            'og_image' => $image,
            'og_url' => $url,
            'og_type' => $type,
            'og_site_name' => config('app.name'),
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $this->sanitizeTitle($title),
            'twitter_description' => $this->sanitizeDescription($description),
            'twitter_image' => $image,
        ];
    }

    /**
     * Generate structured data (JSON-LD) for a business listing
     */
    public function generateBusinessStructuredData(BusinessListing $listing): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $listing->title,
            'description' => strip_tags($listing->description),
            'url' => route('listings.show', $listing->slug),
            'image' => $listing->featured_image_url,
            'telephone' => $listing->phone,
            'email' => $listing->email,
            'priceRange' => $this->getPriceRange($listing->price),
        ];

        if ($listing->address) {
            $data['address'] = [
                '@type' => 'PostalAddress',
                'streetAddress' => $listing->address,
                'addressLocality' => $listing->location?->name,
                'addressRegion' => 'Goa',
                'addressCountry' => 'IN',
                'postalCode' => $listing->zip_code,
            ];
        }

        if ($listing->latitude && $listing->longitude) {
            $data['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $listing->latitude,
                'longitude' => $listing->longitude,
            ];
        }

        if ($listing->rating > 0) {
            $data['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $listing->rating,
                'reviewCount' => $listing->reviews_count,
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }

        if ($listing->opening_hours) {
            $data['openingHours'] = $this->formatOpeningHours($listing->opening_hours);
        }

        return $data;
    }

    /**
     * Generate structured data for an event
     */
    public function generateEventStructuredData(Event $event): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->title,
            'description' => strip_tags($event->description),
            'startDate' => $event->start_date->toIso8601String(),
            'endDate' => $event->end_date->toIso8601String(),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => $event->is_online 
                ? 'https://schema.org/OnlineEventAttendanceMode'
                : 'https://schema.org/OfflineEventAttendanceMode',
            'image' => $event->featured_image_url,
            'url' => route('events.show', $event->slug),
        ];

        if ($event->venue_name) {
            $data['location'] = [
                '@type' => 'Place',
                'name' => $event->venue_name,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $event->venue_address,
                    'addressLocality' => $event->location?->name,
                    'addressRegion' => 'Goa',
                    'addressCountry' => 'IN',
                ],
            ];

            if ($event->latitude && $event->longitude) {
                $data['location']['geo'] = [
                    '@type' => 'GeoCoordinates',
                    'latitude' => $event->latitude,
                    'longitude' => $event->longitude,
                ];
            }
        }

        if ($event->price > 0) {
            $data['offers'] = [
                '@type' => 'Offer',
                'price' => $event->price,
                'priceCurrency' => 'INR',
                'availability' => $event->available_seats > 0 
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/SoldOut',
                'url' => route('events.show', $event->slug),
                'validFrom' => now()->toIso8601String(),
            ];
        }

        if ($event->organizer_name) {
            $data['organizer'] = [
                '@type' => 'Organization',
                'name' => $event->organizer_name,
                'email' => $event->organizer_email,
                'telephone' => $event->organizer_phone,
            ];
        }

        return $data;
    }

    /**
     * Generate structured data for a job posting
     */
    public function generateJobStructuredData(JobListing $job): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->title,
            'description' => strip_tags($job->description),
            'datePosted' => $job->created_at->toIso8601String(),
            'validThrough' => $job->deadline->toIso8601String(),
            'employmentType' => strtoupper($job->job_type),
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $job->company_name,
                'sameAs' => $job->company_website,
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job->location?->name,
                    'addressRegion' => 'Goa',
                    'addressCountry' => 'IN',
                ],
            ],
            'url' => route('jobs.show', $job->slug),
        ];

        if ($job->salary_min || $job->salary_max) {
            $data['baseSalary'] = [
                '@type' => 'MonetaryAmount',
                'currency' => 'INR',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $job->salary_min,
                    'maxValue' => $job->salary_max,
                    'unitText' => strtoupper($job->salary_period ?? 'MONTH'),
                ],
            ];
        }

        if ($job->experience_min || $job->experience_max) {
            $data['experienceRequirements'] = [
                '@type' => 'OccupationalExperienceRequirements',
                'monthsOfExperience' => ($job->experience_min ?? 0) * 12,
            ];
        }

        return $data;
    }

    /**
     * Generate structured data for a product
     */
    public function generateProductStructuredData(Product $product): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => strip_tags($product->description),
            'image' => $product->featured_image_url,
            'url' => route('products.show', $product->slug),
            'sku' => $product->sku ?? $product->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand ?? config('app.name'),
            ],
        ];

        if ($product->rating > 0) {
            $data['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->rating,
                'reviewCount' => $product->reviews_count,
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }

        if ($product->price > 0) {
            $data['offers'] = [
                '@type' => 'Offer',
                'price' => $product->price,
                'priceCurrency' => 'INR',
                'availability' => $product->stock > 0 
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'url' => route('products.show', $product->slug),
                'seller' => [
                    '@type' => 'Organization',
                    'name' => $product->user?->name ?? config('app.name'),
                ],
            ];
        }

        return $data;
    }

    /**
     * Generate breadcrumb structured data
     */
    public function generateBreadcrumbStructuredData(array $items): array
    {
        $itemList = [];
        
        foreach ($items as $position => $item) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemList,
        ];
    }

    /**
     * Generate XML sitemap content for business listings
     */
    public function generateBusinessListingsSitemap(): string
    {
        $listings = BusinessListing::where('status', 'approved')
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        return $this->generateSitemapXml($listings, 'listings.show', 'weekly', '0.8');
    }

    /**
     * Generate XML sitemap content for events
     */
    public function generateEventsSitemap(): string
    {
        $events = Event::where('status', 'approved')
            ->where('start_date', '>=', now())
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        return $this->generateSitemapXml($events, 'events.show', 'weekly', '0.7');
    }

    /**
     * Generate XML sitemap content for job listings
     */
    public function generateJobsSitemap(): string
    {
        $jobs = JobListing::where('status', 'approved')
            ->where('deadline', '>=', now())
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        return $this->generateSitemapXml($jobs, 'jobs.show', 'weekly', '0.7');
    }

    /**
     * Generate XML sitemap content for products
     */
    public function generateProductsSitemap(): string
    {
        $products = Product::where('status', 'approved')
            ->where('stock', '>', 0)
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        return $this->generateSitemapXml($products, 'products.show', 'weekly', '0.6');
    }

    /**
     * Generate XML sitemap content for categories
     */
    public function generateCategoriesSitemap(): string
    {
        $categories = Category::where('is_active', true)
            ->select('slug', 'type', 'updated_at')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($categories as $category) {
            $route = match($category->type) {
                'business' => 'listings.index',
                'event' => 'events.index',
                'job' => 'jobs.index',
                'product' => 'products.index',
                default => null,
            };

            if ($route) {
                $xml .= $this->generateUrlElement(
                    route($route, ['category' => $category->slug]),
                    $category->updated_at,
                    'weekly',
                    '0.7'
                );
            }
        }

        $xml .= '</urlset>';
        return $xml;
    }

    /**
     * Generate XML sitemap content for locations
     */
    public function generateLocationsSitemap(): string
    {
        $locations = Location::where('is_active', true)
            ->select('slug', 'type', 'updated_at')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($locations as $location) {
            $xml .= $this->generateUrlElement(
                route('listings.index', ['location' => $location->slug]),
                $location->updated_at,
                'weekly',
                '0.6'
            );
        }

        $xml .= '</urlset>';
        return $xml;
    }

    /**
     * Generate sitemap index
     */
    public function generateSitemapIndex(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $sitemaps = [
            'sitemap-listings.xml',
            'sitemap-events.xml',
            'sitemap-jobs.xml',
            'sitemap-products.xml',
            'sitemap-categories.xml',
            'sitemap-locations.xml',
            'sitemap-static.xml',
        ];

        foreach ($sitemaps as $sitemap) {
            $xml .= "  <sitemap>\n";
            $xml .= "    <loc>" . url($sitemap) . "</loc>\n";
            $xml .= "    <lastmod>" . now()->toIso8601String() . "</lastmod>\n";
            $xml .= "  </sitemap>\n";
        }

        $xml .= '</sitemapindex>';
        return $xml;
    }

    /**
     * Generate robots.txt content
     */
    public function generateRobotsTxt(): string
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /dashboard/\n";
        $content .= "Disallow: /api/\n";
        $content .= "Disallow: /login\n";
        $content .= "Disallow: /register\n";
        $content .= "Disallow: /password/\n";
        $content .= "\n";
        $content .= "Sitemap: " . url('sitemap.xml') . "\n";

        return $content;
    }

    /**
     * Sanitize title for meta tags (max 60 characters)
     */
    private function sanitizeTitle(string $title): string
    {
        $title = strip_tags($title);
        return mb_strlen($title) > 60 ? mb_substr($title, 0, 57) . '...' : $title;
    }

    /**
     * Sanitize description for meta tags (max 160 characters)
     */
    private function sanitizeDescription(string $description): string
    {
        $description = strip_tags($description);
        $description = preg_replace('/\s+/', ' ', $description);
        return mb_strlen($description) > 160 ? mb_substr($description, 0, 157) . '...' : $description;
    }

    /**
     * Get price range indicator
     */
    private function getPriceRange(?float $price): string
    {
        if (!$price) return '$';
        if ($price < 500) return '$';
        if ($price < 2000) return '$$';
        if ($price < 5000) return '$$$';
        return '$$$$';
    }

    /**
     * Format opening hours for structured data
     */
    private function formatOpeningHours(?string $hours): array
    {
        if (!$hours) return [];
        
        // Assuming format like: "Mon-Fri: 9:00-18:00, Sat: 10:00-16:00"
        // This is a simple implementation - adjust based on your data format
        return explode(', ', $hours);
    }

    /**
     * Generate sitemap XML for a collection
     */
    private function generateSitemapXml($items, string $route, string $changefreq, string $priority): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($items as $item) {
            $xml .= $this->generateUrlElement(
                route($route, $item->slug),
                $item->updated_at,
                $changefreq,
                $priority
            );
        }

        $xml .= '</urlset>';
        return $xml;
    }

    /**
     * Generate URL element for sitemap
     */
    private function generateUrlElement(string $url, $lastmod, string $changefreq, string $priority): string
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
        $xml .= "    <lastmod>" . $lastmod->toIso8601String() . "</lastmod>\n";
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";
        return $xml;
    }
}
