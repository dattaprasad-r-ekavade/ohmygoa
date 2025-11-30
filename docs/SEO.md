# SEO Implementation Guide

## Overview
This guide covers the complete SEO implementation for Ohmygoa, including meta tags, structured data, XML sitemaps, and best practices for search engine optimization.

## 1. SeoService

The `SeoService` class (`app/Services/SeoService.php`) provides centralized SEO functionality:

### Meta Tags Generation

```php
use App\Services\SeoService;

$seoService = new SeoService();

// Generate meta tags
$meta = $seoService->generateMetaTags(
    title: 'Best Hotels in Goa - Ohmygoa',
    description: 'Discover the best hotels, resorts, and accommodations in Goa. Book your stay with verified reviews and best prices.',
    image: 'https://ohmygoa.com/images/hotels-goa.jpg',
    url: 'https://ohmygoa.com/listings/hotels',
    type: 'website'
);

// Returns array with:
// - title (sanitized, max 60 chars)
// - description (sanitized, max 160 chars)
// - canonical URL
// - Open Graph tags (og:title, og:description, og:image, og:url, og:type)
// - Twitter Card tags (twitter:card, twitter:title, twitter:description, twitter:image)
```

### Structured Data (JSON-LD)

#### Business Listing Schema
```php
$listing = BusinessListing::find(1);
$structuredData = $seoService->generateBusinessStructuredData($listing);

// Generates LocalBusiness schema with:
// - Business name, description, URL
// - Contact information (phone, email)
// - Address with PostalAddress schema
// - Geo coordinates
// - Aggregate rating
// - Opening hours
// - Price range indicator
```

#### Event Schema
```php
$event = Event::find(1);
$structuredData = $seoService->generateEventStructuredData($event);

// Generates Event schema with:
// - Event details (name, description, dates)
// - Event status and attendance mode
// - Location with Place schema
// - Ticket offers with price and availability
// - Organizer information
```

#### Job Posting Schema
```php
$job = JobListing::find(1);
$structuredData = $seoService->generateJobStructuredData($job);

// Generates JobPosting schema with:
// - Job title, description, posting date
// - Employment type (full-time, part-time, etc.)
// - Hiring organization details
// - Job location
// - Salary information
// - Experience requirements
```

#### Product Schema
```php
$product = Product::find(1);
$structuredData = $seoService->generateProductStructuredData($product);

// Generates Product schema with:
// - Product name, description, image
// - SKU and brand information
// - Aggregate rating
// - Offer with price, currency, availability
// - Seller information
```

#### Breadcrumb Schema
```php
$breadcrumbs = [
    ['name' => 'Listings', 'url' => route('listings.index')],
    ['name' => 'Hotels & Resorts', 'url' => route('listings.index', ['category' => 'hotels'])],
    ['name' => 'Luxury Beach Resort', 'url' => null], // Current page
];

$structuredData = $seoService->generateBreadcrumbStructuredData($breadcrumbs);
```

## 2. Blade Components

### SEO Meta Component

Use the `<x-seo-meta>` component in your layout's `<head>` section:

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO Meta Tags --}}
    <x-seo-meta :meta="$meta ?? []" :structuredData="$structuredData ?? null" />
    
    {{-- Other head elements --}}
</head>
<body>
    @yield('content')
</body>
</html>
```

### Usage in Controllers

```php
// Business Listing Detail Page
public function show(string $slug)
{
    $listing = BusinessListing::where('slug', $slug)
        ->with(['user', 'category', 'location', 'images'])
        ->firstOrFail();
    
    $seoService = new SeoService();
    
    // Generate meta tags
    $meta = $seoService->generateMetaTags(
        title: $listing->title . ' - ' . config('app.name'),
        description: strip_tags(Str::limit($listing->description, 150)),
        image: $listing->featured_image_url,
        url: route('listings.show', $listing->slug),
        type: 'article'
    );
    
    // Generate structured data
    $structuredData = $seoService->generateBusinessStructuredData($listing);
    
    return view('listings.show', compact('listing', 'meta', 'structuredData'));
}
```

### Breadcrumbs Component

Add breadcrumbs to pages for better navigation and SEO:

```blade
{{-- In your page template --}}
<x-breadcrumbs :items="[
    ['name' => 'Listings', 'url' => route('listings.index')],
    ['name' => $category->name, 'url' => route('listings.index', ['category' => $category->slug])],
    ['name' => $listing->title, 'url' => null]
]" />
```

The component includes microdata markup for structured breadcrumbs.

## 3. XML Sitemaps

### Sitemap Structure

The platform generates multiple sitemaps:

- **sitemap.xml** - Sitemap index (links to all other sitemaps)
- **sitemap-listings.xml** - Business listings (priority: 0.8)
- **sitemap-events.xml** - Events (priority: 0.7)
- **sitemap-jobs.xml** - Job listings (priority: 0.7)
- **sitemap-products.xml** - Products (priority: 0.6)
- **sitemap-categories.xml** - Category pages (priority: 0.7)
- **sitemap-locations.xml** - Location pages (priority: 0.6)
- **sitemap-static.xml** - Static pages (priority varies)

### Generate Sitemaps

#### Manual Generation
```bash
# Generate all sitemaps
php artisan sitemaps:generate

# Clear cached sitemaps and regenerate
php artisan sitemaps:generate --clear
```

#### Scheduled Generation
Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Generate sitemaps daily at 2 AM
    $schedule->command('sitemaps:generate')->dailyAt('02:00');
    
    // Or generate every 6 hours
    $schedule->command('sitemaps:generate')->everySixHours();
}
```

### Sitemap URLs

All sitemaps are publicly accessible:
- https://ohmygoa.com/sitemap.xml
- https://ohmygoa.com/sitemap-listings.xml
- https://ohmygoa.com/sitemap-events.xml
- https://ohmygoa.com/sitemap-jobs.xml
- https://ohmygoa.com/sitemap-products.xml
- https://ohmygoa.com/sitemap-categories.xml
- https://ohmygoa.com/sitemap-locations.xml
- https://ohmygoa.com/sitemap-static.xml

### Caching

Sitemaps are cached to improve performance:
- Sitemap index: 24 hours
- Static sitemaps: 24 hours
- Dynamic sitemaps: 1 hour

Cache is automatically cleared when generating sitemaps with the `--clear` flag.

## 4. Robots.txt

The `robots.txt` file is dynamically generated and accessible at:
https://ohmygoa.com/robots.txt

### Configuration

The robots.txt includes:
```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /dashboard/
Disallow: /api/
Disallow: /login
Disallow: /register
Disallow: /password/

Sitemap: https://ohmygoa.com/sitemap.xml
```

All authenticated and API routes are disallowed to prevent indexing of private content.

## 5. SEO Best Practices

### Page Titles
- Keep under 60 characters
- Include primary keyword
- Add brand name at the end
- Make unique for each page

**Examples:**
- Homepage: "Ohmygoa - Discover the Best of Goa"
- Listing: "Luxury Beach Resort | Calangute, Goa - Ohmygoa"
- Category: "Hotels & Resorts in Goa - Ohmygoa"

### Meta Descriptions
- Keep under 160 characters
- Include call-to-action
- Use primary and secondary keywords naturally
- Make compelling and unique

**Examples:**
- "Discover luxury hotels, water sports, restaurants, and more in Goa. Browse verified listings with reviews and book your perfect Goa experience."

### URL Structure
Use clean, descriptive URLs:
- ✅ `/listings/hotels-resorts/luxury-beach-resort-calangute`
- ❌ `/listing.php?id=123&cat=5`

### Canonical URLs
Always set canonical URLs to prevent duplicate content:
```blade
<link rel="canonical" href="{{ route('listings.show', $listing->slug) }}">
```

### Image Optimization
- Use descriptive file names: `luxury-beach-resort-goa.jpg` not `IMG_1234.jpg`
- Add alt text to all images: `<img alt="Luxury Beach Resort in Calangute, Goa">`
- Optimize file size (max 200KB for web)
- Use responsive images with srcset
- Implement lazy loading

### Internal Linking
- Link from homepage to top categories
- Link related listings within content
- Use descriptive anchor text
- Create hub pages for important topics

### Mobile Optimization
- Responsive design (already implemented)
- Fast page load times
- Touch-friendly interface
- Mobile-first indexing ready

### Page Speed
- Enable caching (see PERFORMANCE.md)
- Optimize images
- Minify CSS/JS
- Use CDN for static assets
- Enable gzip compression

## 6. Implementation Checklist

### For All Pages
- [ ] Unique title tag (max 60 chars)
- [ ] Unique meta description (max 160 chars)
- [ ] Canonical URL set
- [ ] Open Graph tags
- [ ] Twitter Card tags
- [ ] Breadcrumbs (where applicable)
- [ ] Responsive design
- [ ] Fast load time (< 3 seconds)

### For Listing Pages
- [ ] LocalBusiness structured data
- [ ] High-quality images with alt text
- [ ] Contact information visible
- [ ] Location map
- [ ] Customer reviews
- [ ] Clear call-to-action

### For Event Pages
- [ ] Event structured data
- [ ] Event dates clearly displayed
- [ ] Ticket/RSVP information
- [ ] Venue location and map
- [ ] Share buttons

### For Job Pages
- [ ] JobPosting structured data
- [ ] Salary information (if available)
- [ ] Application deadline
- [ ] Company information
- [ ] Clear application process

### For Product Pages
- [ ] Product structured data
- [ ] Multiple product images
- [ ] Price and availability
- [ ] Product specifications
- [ ] Customer reviews

## 7. Testing SEO Implementation

### Google Tools
1. **Google Search Console**
   - Submit sitemap.xml
   - Monitor indexing status
   - Check for crawl errors
   - View search performance

2. **Google Rich Results Test**
   - Test structured data: https://search.google.com/test/rich-results
   - Verify LocalBusiness schema
   - Check Event schema
   - Validate JobPosting schema

3. **PageSpeed Insights**
   - Test performance: https://pagespeed.web.dev/
   - Aim for 90+ score
   - Fix Core Web Vitals issues

### Other Tools
1. **Bing Webmaster Tools**
   - Submit sitemap
   - Monitor Bing indexing

2. **Schema.org Validator**
   - Validate structured data: https://validator.schema.org/

3. **Screaming Frog**
   - Crawl site for issues
   - Check title/description lengths
   - Find broken links
   - Audit redirects

### Manual Testing
```bash
# Test robots.txt
curl https://ohmygoa.com/robots.txt

# Test sitemap index
curl https://ohmygoa.com/sitemap.xml

# Test specific sitemap
curl https://ohmygoa.com/sitemap-listings.xml

# Validate XML
xmllint --noout sitemap.xml
```

## 8. Monitoring and Maintenance

### Regular Tasks

**Daily:**
- Monitor Google Search Console for errors
- Check crawl stats

**Weekly:**
- Review top performing pages
- Check for broken links
- Monitor keyword rankings

**Monthly:**
- Analyze search traffic trends
- Update content for top pages
- Review and improve low-performing pages
- Update meta descriptions for better CTR

**Quarterly:**
- Full SEO audit
- Competitor analysis
- Update SEO strategy

### Key Metrics to Track
- Organic traffic (Google Analytics)
- Keyword rankings (Google Search Console)
- Click-through rate (CTR)
- Bounce rate
- Page load speed
- Index coverage
- Core Web Vitals

## 9. Advanced SEO Features

### Local SEO
- Claim Google Business Profile
- Add location schema to all listings
- Encourage customer reviews
- Use local keywords (e.g., "restaurants in Calangute")
- Create location-specific landing pages

### Content Strategy
- Regular blog posts about Goa
- User-generated content (reviews)
- FAQ pages for common questions
- Guides and how-to articles
- Event coverage and updates

### Social Signals
- Share buttons on all content
- Open Graph optimization
- Twitter Card optimization
- Encourage social sharing

### Backlink Strategy
- Partner with tourism websites
- Guest posts on travel blogs
- Local business directories
- Tourism board listings
- Press releases for events

## 10. Common Issues and Solutions

### Issue: Pages Not Being Indexed
**Solutions:**
- Submit sitemap to Google Search Console
- Check robots.txt isn't blocking
- Ensure no noindex meta tags
- Improve page quality and content
- Build internal links to the page

### Issue: Duplicate Content
**Solutions:**
- Set canonical URLs
- Use 301 redirects
- Avoid parameter-based URLs
- Use rel="nofollow" for filters

### Issue: Slow Page Load
**Solutions:**
- Enable caching (see PERFORMANCE.md)
- Optimize images
- Minify CSS/JS
- Use CDN
- Enable gzip compression

### Issue: Low Click-Through Rate
**Solutions:**
- Improve title tags (add numbers, questions)
- Write compelling meta descriptions
- Use schema markup for rich snippets
- Test different titles/descriptions

### Issue: Poor Mobile Performance
**Solutions:**
- Optimize images for mobile
- Reduce page weight
- Prioritize above-the-fold content
- Enable AMP (optional)

## Best Practices Summary

1. **Content is King** - Create high-quality, relevant content
2. **User Experience** - Fast, mobile-friendly, easy to navigate
3. **Technical SEO** - Proper markup, sitemaps, structured data
4. **Regular Updates** - Keep content fresh and relevant
5. **Monitor Performance** - Track metrics and adjust strategy
6. **Build Authority** - Earn quality backlinks
7. **Local Focus** - Optimize for Goa-specific searches
8. **Structured Data** - Implement rich snippets
9. **Mobile-First** - Optimize for mobile devices
10. **Page Speed** - Keep load times under 3 seconds

## Resources

- **Google Search Central**: https://developers.google.com/search
- **Schema.org**: https://schema.org/
- **Google Search Console**: https://search.google.com/search-console
- **PageSpeed Insights**: https://pagespeed.web.dev/
- **Rich Results Test**: https://search.google.com/test/rich-results
- **Structured Data Testing Tool**: https://validator.schema.org/
