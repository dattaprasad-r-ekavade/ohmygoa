# Ohmygoa - Goa's Premier Directory & Community Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.40.2-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2.12-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 🌴 About Ohmygoa

Ohmygoa is a comprehensive directory and community platform designed specifically for Goa, India. It connects tourists and residents with local businesses, events, jobs, products, and services across the beautiful state of Goa.

### Key Features

- 🏢 **Business Listings** - Discover hotels, restaurants, water sports, tours, and more
- 🎉 **Events** - Find music festivals, beach parties, cultural events, and exhibitions
- 💼 **Job Board** - Browse job opportunities in hospitality, IT, healthcare, and other sectors
- 🛍️ **Marketplace** - Shop for handicrafts, Goan specialties, and local products
- 👨‍💼 **Service Experts** - Connect with photographers, event planners, and professionals
- 📰 **Classifieds** - Post and browse classified ads
- ✍️ **Blog & News** - Stay updated with the latest from Goa
- 💰 **Coupons** - Access exclusive deals and discounts
- ⭐ **Reviews & Ratings** - Make informed decisions with verified reviews
- 🔍 **Advanced Search** - Find exactly what you're looking for with filters
- 📱 **Mobile Responsive** - Works seamlessly on all devices

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- SQLite (default) or MySQL/PostgreSQL
- Node.js & NPM (for asset compilation)

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/dattaprasad-r-ekavade/ohmygoa.git
cd ohmygoa
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Set up database**
```bash
# SQLite (default)
touch database/database.sqlite
php artisan migrate --seed
```

5. **Compile assets**
```bash
npm run dev
```

6. **Start development server**
```bash
php artisan serve
```

Visit http://localhost:8000 to access the application.

### Default Credentials

**Admin User:**
- Email: admin@ohmygoa.com
- Password: password

**Business User:**
- Email: business@ohmygoa.com
- Password: password

**Free User:**
- Email: user@ohmygoa.com
- Password: password

## 📚 Documentation

### Complete Guides

- **[User Guide](docs/USER_GUIDE.md)** - For end users and businesses
- **[Admin Guide](docs/ADMIN_GUIDE.md)** - For administrators
- **[API Documentation](docs/API.md)** - RESTful API reference
- **[Developer Guide](docs/DEVELOPER.md)** - Architecture and development
- **[Database Schema](docs/DATABASE.md)** - Complete database documentation
- **[Email System](docs/EMAIL_SYSTEM.md)** - Email configuration and templates
- **[Performance](docs/PERFORMANCE.md)** - Optimization and caching
- **[SEO Guide](docs/SEO.md)** - SEO implementation and best practices

## 🏗️ Tech Stack

### Backend
- **Framework:** Laravel 12.40.2
- **Language:** PHP 8.2.12
- **Database:** SQLite (MySQL/PostgreSQL compatible)
- **Authentication:** Laravel Sanctum
- **Queue:** Database driver (Redis-ready)
- **Cache:** Database driver (Redis/Memcached-ready)

### Frontend
- **Template Engine:** Blade
- **CSS Framework:** Bootstrap 5
- **JavaScript:** Alpine.js, jQuery
- **Icons:** FontAwesome

### Services
- **Payment Gateway:** Razorpay (mocked for MVP)
- **Email:** SMTP (Mailtrap for development)
- **File Storage:** Local (S3-ready)
- **Search:** Full-text search with filters

## 🎯 Features Overview

### For Users (Free)
- Browse all listings, events, jobs, and products
- Search with advanced filters
- View business profiles and reviews
- Purchase coupons and deals
- Bookmark favorite listings
- Submit enquiries to businesses
- Earn points for activities
- Save searches and get notifications

### For Business Users (₹499/month)
- Create and manage business listings
- Post events and job openings
- Sell products and services
- Create and manage coupons (10% commission)
- Manage customer enquiries
- View analytics and insights
- Receive payment to wallet
- Request payouts (₹1000 minimum)
- Premium placement and features

### For Administrators
- Manage all users and content
- Moderate listings, events, jobs, products
- Approve/reject submissions
- Manage categories and locations
- Configure site settings
- View financial reports
- Send notifications
- Manage advertisements
- SEO management tools

## 💳 Pricing & Monetization

### Free Plan
- Browse all content
- Purchase coupons
- Submit reviews
- Earn points

### Premium Plan (₹499/month)
- Create business listings
- Post unlimited content
- Access analytics
- Premium support
- Featured placement

### Revenue Streams
1. **Subscriptions** - ₹499/month for business users
2. **Commission** - 10% on coupon sales (auto-deducted)
3. **Advertisements** - Featured placements and banners
4. **Premium Features** - Highlighted listings, top placement

### Commission System
- Automatic 10% commission on all coupon sales
- ₹1000 minimum payout threshold
- Weekly/monthly payout processing
- Transparent wallet system

## 🗺️ Goa Coverage

### 33 Locations Covered

**North Goa:**
Panaji, Mapusa, Calangute, Baga, Candolim, Anjuna, Vagator, Morjim, Arambol, Sinquerim, Nerul, Siolim, Assagao, Porvorim, Reis Magos

**South Goa:**
Margao, Vasco da Gama, Colva, Benaulim, Varca, Cavelossim, Mobor, Palolem, Agonda, Betalbatim, Majorda, Utorda, Bogmalo, Canacona, Patnem

### 80+ Categories

**Business:** Hotels & Resorts, Restaurants & Cafes, Water Sports, Tours & Travel, Spa & Wellness, Nightlife, Shopping, Real Estate

**Jobs:** Hospitality & Tourism, IT, Healthcare, Education, Sales & Marketing

**Events:** Music Festivals, Beach Parties, Cultural Events, Sports Events, Food Festivals, Art Exhibitions

**Products:** Handicrafts & Souvenirs, Goan Specialties, Fashion & Accessories

**Services:** Photography, Event Planning, Home Services, Legal Services, Financial Services

## 🔐 Security Features

- Laravel authentication with role-based access control
- CSRF protection on all forms
- XSS prevention with Blade escaping
- SQL injection protection via Eloquent ORM
- Password hashing with bcrypt
- Rate limiting on APIs and auth routes
- File upload validation and sanitization
- Secure file storage with validation
- API authentication via Sanctum tokens

## 🚀 Performance

- **Caching:** Multi-layer caching for categories, locations, listings
- **Database:** 15 optimized indexes on frequently queried tables
- **CDN Ready:** Asset URL configuration for CDN integration
- **Image Optimization:** Automatic compression and resizing
- **Lazy Loading:** Images load on-demand
- **Query Optimization:** Eager loading to prevent N+1 queries
- **Response Caching:** HTTP response caching for public pages

## 📊 SEO Optimization

- Dynamic meta tags for all pages
- Open Graph tags for social sharing
- Twitter Card tags
- JSON-LD structured data (LocalBusiness, Event, JobPosting, Product)
- XML sitemaps (auto-generated)
- Robots.txt configuration
- Canonical URLs
- SEO-friendly URLs with slugs
- Breadcrumbs with microdata

## 📱 API

RESTful API available for mobile app development:

- **Base URL:** `/api/v1`
- **Authentication:** Laravel Sanctum tokens
- **Format:** JSON
- **Rate Limiting:** 60 requests/minute
- **Documentation:** See [API Documentation](docs/API.md)

### Key Endpoints
- `/api/v1/listings` - Business listings CRUD
- `/api/v1/events` - Events CRUD
- `/api/v1/jobs` - Job listings CRUD
- `/api/v1/products` - Products CRUD
- `/api/v1/search` - Global search
- `/api/v1/auth` - Authentication endpoints

## 🛠️ Development

### Project Structure
```
ohmygoa/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Http/Controllers/     # Controllers
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic services
│   └── Helpers/              # Helper classes
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── docs/                     # Documentation
├── resources/
│   ├── views/                # Blade templates
│   └── js/                   # JavaScript
├── routes/
│   ├── web.php               # Web routes
│   └── api.php               # API routes
└── tests/                    # PHPUnit tests
```

### Development Commands

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear

# Warm up cache
php artisan cache:warmup

# Generate sitemaps
php artisan sitemaps:generate

# Run tests
php artisan test

# Run queue worker
php artisan queue:work

# Watch assets
npm run dev
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

## 🚢 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate production `APP_KEY`
- [ ] Configure production database
- [ ] Set up mail configuration
- [ ] Configure Redis for cache/sessions
- [ ] Run `php artisan optimize`
- [ ] Set up queue workers with Supervisor
- [ ] Configure cron for scheduled tasks
- [ ] Set up SSL certificate
- [ ] Configure file permissions
- [ ] Set up backups
- [ ] Configure monitoring

## 📅 Scheduled Tasks

Add to cron (`crontab -e`):
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks include:
- Cache warmup (hourly)
- Sitemap generation (daily)
- Database cleanup (weekly)
- Report generation (daily)

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed

## 📝 License

This project is licensed under the MIT License.

## 👥 Team

- **Project Lead:** Dattaprasad R Ekavade
- **Repository:** [github.com/dattaprasad-r-ekavade/ohmygoa](https://github.com/dattaprasad-r-ekavade/ohmygoa)

## 📞 Support

- **Email:** support@ohmygoa.com
- **Documentation:** [docs/](docs/)
- **Issues:** [GitHub Issues](https://github.com/dattaprasad-r-ekavade/ohmygoa/issues)

## 🗺️ Roadmap

### Phase 1 - MVP (Current)
- ✅ Core platform features
- ✅ Business listings and management
- ✅ Events, jobs, and products
- ✅ Payment integration (mocked)
- ✅ Commission system
- ✅ Admin panel
- ✅ RESTful API

### Phase 2 - Enhancement (Q1 2026)
- Real Razorpay integration
- SMS notifications
- Advanced analytics
- Mobile apps (iOS/Android)
- Real-time chat
- Advanced booking system

### Phase 3 - Scale (Q2 2026)
- Multi-language support
- Loyalty program
- Social media integration
- Advanced SEO features
- Performance optimization
- CDN integration

## 📊 Stats

- **Models:** 31+
- **Migrations:** 46+
- **Controllers:** 20+
- **Routes:** 100+
- **API Endpoints:** 60+
- **Blade Templates:** 60+
- **Documentation:** 5000+ lines

---

**Built with ❤️ for Goa**

*Discover. Connect. Experience Goa.*
