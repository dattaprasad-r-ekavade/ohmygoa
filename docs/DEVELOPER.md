# Developer Guide - Ohmygoa Platform

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Setup & Installation](#setup--installation)
3. [Project Structure](#project-structure)
4. [Database Schema](#database-schema)
5. [Services & Helpers](#services--helpers)
6. [API Development](#api-development)
7. [Testing](#testing)
8. [Deployment](#deployment)

---

## Architecture Overview

### Technology Stack

**Backend:**
- Laravel 12.40.2 (PHP 8.2.12)
- SQLite (development) / MySQL (production)
- Laravel Sanctum (API authentication)
- Queue system (database driver)
- Cache system (database/Redis)

**Frontend:**
- Blade templating engine
- Bootstrap 5
- Alpine.js for reactivity
- jQuery for interactions

### Design Patterns

**MVC Pattern:**
- Models: Eloquent ORM
- Views: Blade templates
- Controllers: HTTP request handling

**Service Layer:**
- Business logic separation
- Reusable service classes
- Dependency injection

**Repository Pattern (Optional):**
- Data access abstraction
- Flexible for future changes

### Key Architectural Decisions

1. **SQLite for MVP** - Easy setup, zero configuration
2. **Service-Oriented** - Business logic in service classes
3. **Event-Driven** - Laravel events for decoupling
4. **API-First** - REST API for mobile apps
5. **Caching Strategy** - Multi-layer caching
6. **Queue Jobs** - Async processing for emails

---

## Setup & Installation

### Prerequisites

```bash
php >= 8.2
composer >= 2.0
node >= 16.0
npm >= 8.0
```

### Development Environment

**Option 1: Laravel Valet (macOS)**
```bash
composer global require laravel/valet
valet install
cd ohmygoa
valet link
```

**Option 2: Laravel Homestead (VM)**
```bash
composer require laravel/homestead --dev
php vendor/bin/homestead make
vagrant up
```

**Option 3: Docker**
```bash
docker-compose up -d
```

### Installation Steps

```bash
# Clone repository
git clone https://github.com/dattaprasad-r-ekavade/ohmygoa.git
cd ohmygoa

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Compile assets
npm run dev

# Start development server
php artisan serve
```

### Configuration

**.env File:**
```env
APP_NAME=Ohmygoa
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

CACHE_DRIVER=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
```

---

## Project Structure

```
ohmygoa/
├── app/
│   ├── Console/
│   │   └── Commands/           # Artisan commands
│   ├── Events/                 # Event classes
│   ├── Exceptions/             # Custom exceptions
│   ├── Helpers/                # Helper classes
│   ├── Http/
│   │   ├── Controllers/        # Controllers
│   │   ├── Middleware/         # Middleware
│   │   ├── Requests/           # Form requests
│   │   └── Resources/          # API resources
│   ├── Listeners/              # Event listeners
│   ├── Models/                 # Eloquent models
│   ├── Policies/               # Authorization policies
│   ├── Providers/              # Service providers
│   ├── Services/               # Business logic services
│   └── Traits/                 # Reusable traits
├── bootstrap/                  # App bootstrap
├── config/                     # Configuration files
├── database/
│   ├── factories/              # Model factories
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── docs/                       # Documentation
├── public/                     # Public assets
├── resources/
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript
│   └── views/                  # Blade templates
├── routes/
│   ├── api.php                 # API routes
│   ├── web.php                 # Web routes
│   └── console.php             # Console routes
├── storage/                    # Storage
├── tests/
│   ├── Feature/                # Feature tests
│   └── Unit/                   # Unit tests
└── vendor/                     # Composer dependencies
```

---

## Database Schema

### Core Tables

**users**
- id, name, email, password
- role (free, business, admin)
- subscription_status, subscription_ends_at
- wallet_balance, points_balance
- email_verified_at

**business_listings**
- id, user_id, category_id, location_id
- title, slug, description
- address, latitude, longitude
- phone, email, website
- price, rating, reviews_count, views
- is_featured, status (pending, approved, rejected)

**events**
- id, user_id, category_id, location_id
- title, slug, description
- start_date, end_date
- venue_name, venue_address
- price, available_seats
- is_online, status

**job_listings**
- id, user_id, category_id, location_id
- title, slug, description
- company_name, salary_min, salary_max
- job_type, experience_required
- deadline, status

**products**
- id, user_id, category_id
- title, slug, description
- price, stock, sku
- is_featured, status

**coupons**
- id, business_id
- title, description, code
- discount_type, discount_value
- original_price, final_price
- quantity, validity_start, validity_end
- status

**categories**
- id, parent_id
- name, slug, type
- icon, description
- display_order, is_active

**locations**
- id, parent_id
- name, slug, type
- latitude, longitude
- is_popular, display_order

**reviews**
- id, user_id
- reviewable_type, reviewable_id (polymorphic)
- rating, comment
- helpful_count, status

**payments**
- id, user_id
- type (subscription, coupon, payout)
- amount, commission, net_amount
- status, payment_method
- transaction_id

### Relationships

**User Relationships:**
```php
hasMany: listings, events, jobs, products, reviews
hasMany: bookmarks, enquiries, notifications
```

**Business Listing Relationships:**
```php
belongsTo: user, category, location
hasMany: images, reviews, enquiries
morphMany: bookmarks
```

**Polymorphic Relationships:**
```php
reviews: reviewable (listings, events, products)
bookmarks: bookmarkable (listings, events, jobs, products)
images: imageable (listings, events, products)
```

### Migrations

**Create a new migration:**
```bash
php artisan make:migration create_table_name_table
```

**Run migrations:**
```bash
php artisan migrate
php artisan migrate:fresh --seed  # Fresh install
php artisan migrate:rollback      # Rollback
php artisan migrate:status        # Check status
```

---

## Services & Helpers

### Services

**PaymentService** (`app/Services/PaymentService.php`)
```php
// Handle subscription payments
$paymentService->createSubscription($user, $plan);

// Process coupon purchase
$paymentService->processCouponPurchase($coupon, $user);

// Request payout
$paymentService->requestPayout($user, $amount);
```

**CommissionService** (`app/Services/CommissionService.php`)
```php
// Calculate commission
$commission = $commissionService->calculate($amount);

// Deduct commission
$commissionService->deduct($payment);
```

**CacheService** (`app/Services/CacheService.php`)
```php
// Get cached categories
$categories = $cacheService->getCategories();

// Get featured listings
$featured = $cacheService->getFeaturedListings(12);

// Clear cache
$cacheService->clearListingsCache();
```

**SeoService** (`app/Services/SeoService.php`)
```php
// Generate meta tags
$meta = $seoService->generateMetaTags($title, $description);

// Generate structured data
$schema = $seoService->generateBusinessStructuredData($listing);
```

**EmailService** (`app/Services/EmailService.php`)
```php
// Send welcome email
$emailService->sendWelcomeEmail($user);

// Send verification email
$emailService->sendVerificationEmail($user);
```

### Helpers

**SlugHelper** (`app/Helpers/SlugHelper.php`)
```php
SlugHelper::generate('My Business Name');
// Output: my-business-name
```

**CurrencyHelper** (`app/Helpers/CurrencyHelper.php`)
```php
CurrencyHelper::format(499);
// Output: ₹499

CurrencyHelper::formatWithSymbol(499, 'INR');
// Output: ₹499
```

**DateHelper** (`app/Helpers/DateHelper.php`)
```php
DateHelper::humanReadable('2025-11-30');
// Output: November 30, 2025

DateHelper::timeAgo('2025-11-29 10:00:00');
// Output: 1 day ago
```

### Traits

**HasSlug** (`app/Traits/HasSlug.php`)
- Automatically generates slugs from title

**Bookmarkable** (`app/Traits/Bookmarkable.php`)
- Adds bookmark functionality to models

**Reviewable** (`app/Traits/Reviewable.php`)
- Adds review functionality to models

**Searchable** (`app/Traits/Searchable.php`)
- Adds search scopes to models

**HasViewCount** (`app/Traits/HasViewCount.php`)
- Tracks and increments view counts

---

## API Development

### API Structure

**Base URL:** `/api/v1`

**Authentication:** Laravel Sanctum

**Response Format:**
```json
{
  "success": true,
  "data": {},
  "message": "Success message",
  "meta": {
    "current_page": 1,
    "total_pages": 10
  }
}
```

### Creating API Endpoints

**1. Create API Resource:**
```bash
php artisan make:resource ListingResource
```

**2. Define Resource:**
```php
// app/Http/Resources/ListingResource.php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'slug' => $this->slug,
        'description' => $this->description,
        'price' => $this->price,
        'rating' => $this->rating,
        'category' => new CategoryResource($this->whenLoaded('category')),
        'location' => new LocationResource($this->whenLoaded('location')),
        'images' => ImageResource::collection($this->whenLoaded('images')),
    ];
}
```

**3. Create API Controller:**
```bash
php artisan make:controller Api/ListingController --api
```

**4. Define Routes:**
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('listings', ListingController::class);
});
```

### API Authentication

**Register/Login:**
```php
// Generate token
$token = $user->createToken('mobile-app')->plainTextToken;

// Return token
return response()->json([
    'token' => $token,
    'user' => new UserResource($user)
]);
```

**Authenticated Requests:**
```
Authorization: Bearer {token}
```

### Rate Limiting

**Custom rate limit:**
```php
// app/Http/Kernel.php
'api' => [
    'throttle:60,1', // 60 requests per minute
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

---

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/ListingTest.php
```

### Writing Tests

**Feature Test Example:**
```php
// tests/Feature/ListingTest.php
public function test_user_can_create_listing()
{
    $user = User::factory()->create(['role' => 'business']);
    
    $response = $this->actingAs($user)->post('/listings', [
        'title' => 'Test Listing',
        'description' => 'Test description',
        'category_id' => 1,
        'location_id' => 1,
    ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('business_listings', [
        'title' => 'Test Listing'
    ]);
}
```

**Unit Test Example:**
```php
// tests/Unit/CommissionServiceTest.php
public function test_calculates_commission_correctly()
{
    $service = new CommissionService();
    $amount = 1000;
    
    $commission = $service->calculate($amount);
    
    $this->assertEquals(100, $commission); // 10%
}
```

### Test Database

**Use separate test database:**
```php
// phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

---

## Deployment

### Production Checklist

**1. Environment Configuration:**
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...  # Generate new key

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=ohmygoa_prod

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

**2. Optimization Commands:**
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**3. Set Permissions:**
```bash
chmod -R 755 storage bootstrap/cache
```

**4. Queue Workers:**
```ini
# /etc/supervisor/conf.d/ohmygoa-worker.conf
[program:ohmygoa-worker]
command=php /var/www/ohmygoa/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ohmygoa/storage/logs/worker.log
```

**5. Cron Jobs:**
```bash
* * * * * cd /var/www/ohmygoa && php artisan schedule:run >> /dev/null 2>&1
```

**6. SSL Certificate:**
```bash
# Using Let's Encrypt
sudo certbot --nginx -d ohmygoa.com -d www.ohmygoa.com
```

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name ohmygoa.com;
    root /var/www/ohmygoa/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Deployment Script

```bash
#!/bin/bash

cd /var/www/ohmygoa

# Pull latest code
git pull origin master

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan optimize:clear
php artisan optimize
php artisan cache:warmup
php artisan sitemaps:generate

# Restart services
php artisan queue:restart
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx

echo "Deployment complete!"
```

---

## Best Practices

### Code Standards

1. **Follow PSR-12** - Coding style standards
2. **Type Hints** - Use return types and parameter types
3. **Documentation** - PHPDoc blocks for complex methods
4. **DRY Principle** - Don't repeat yourself
5. **SOLID Principles** - Clean, maintainable code

### Database

1. **Use Migrations** - Never modify database directly
2. **Seeders** - For sample data
3. **Factories** - For testing
4. **Indexes** - On frequently queried columns
5. **Eager Loading** - Prevent N+1 queries

### Security

1. **Validate Input** - Always validate user input
2. **Sanitize Output** - Prevent XSS
3. **CSRF Protection** - On all forms
4. **Rate Limiting** - Prevent abuse
5. **SQL Injection** - Use Eloquent/Query Builder

### Performance

1. **Cache Frequently** - Use caching layers
2. **Queue Heavy Tasks** - Email, reports
3. **Optimize Images** - Compress before upload
4. **Lazy Loading** - Load images on demand
5. **Database Indexes** - On foreign keys and search columns

---

## Troubleshooting

### Common Issues

**Issue: 500 Server Error**
```bash
# Check logs
tail -f storage/logs/laravel.log

# Check permissions
chmod -R 755 storage bootstrap/cache
```

**Issue: Queue Not Processing**
```bash
# Check queue worker
php artisan queue:work --once

# Restart queue
php artisan queue:restart
```

**Issue: Cache Not Clearing**
```bash
# Clear all caches
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Laravel API:** https://laravel.com/api/master
- **Laracasts:** https://laracasts.com
- **Laravel News:** https://laravel-news.com

---

*Last Updated: November 30, 2025*
