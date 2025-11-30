# Ohmygoa MVP Implementation Plan

## Overview
**Project:** Ohmygoa - Directory & Community Platform for Goa, India
**Tech Stack:** Laravel 12.40.2, PHP 8.2.12, SQLite (MySQL-compatible), Razorpay (Mocked), Blade Templates
**Timeline:** 12-16 weeks
**Total Tasks:** 52
**Completed:** 32/52 (62%)
**Estimated Effort:** ~600-800 hours

## Key MVP Features
- ✅ All functionalities fully implemented (not just mockups)
- ✅ Mock Razorpay with complete payment flow structure
- ✅ 10% auto-commission deduction on coupon sales
- ✅ ₹1000 minimum payout threshold for businesses
- ✅ ₹499/month premium subscription with feature gating
- ✅ Multi-role authentication (Free, Business, Admin)
- ✅ 60+ HTML templates converted to Blade
- ✅ Database designed for easy SQLite ↔ MySQL migration
- ✅ Complete admin control panel
- ✅ RESTful APIs for future mobile app

## Development Phases

### Phase 1: Foundation & Setup (Week 1-2) ✅ [COMPLETED]
**Tasks:** 1-15 | **Status:** ✅ Complete
- ✅ Initialize Laravel project with proper structure
- ✅ Configure SQLite with MySQL compatibility
- ✅ Implement multi-role authentication (Free, Business, Admin)
- ✅ Set up Git repository and development environment
- ✅ Configure environment variables and dependencies
- ✅ Create 31 database migrations (users, listings, events, jobs, products, coupons, etc.)
- ✅ Build 21 Eloquent models with relationships
- ✅ Implement 5 reusable traits (HasSlug, Bookmarkable, Reviewable, Searchable, HasViewCount)
- ✅ Create 13 controllers (7 business + 6 admin)
- ✅ Build 9 FormRequest validation classes
- ✅ Create 14 API resources for data transformation
- ✅ Implement comprehensive routing with middleware protection
- ✅ Build 6 service classes (Payment, Commission, Payout, Subscription, Notification, FileUpload)
- ✅ Create 6 helper classes (Slug, Date, Currency, Seo, String, Validation)
- ✅ Implement 3 view composers and custom Blade directives
- ✅ Build 5 custom middleware (Subscription, Points, RateLimiting, TrackViews, Maintenance)
- ✅ Create event-driven architecture with 6 events and listeners
- ✅ Implement global search across all content types
- ✅ Build complete REST API with Laravel Sanctum authentication

**Deliverables:**
- ✅ Running Laravel application with authentication
- ✅ Database schema foundation (31 migrations)
- ✅ Role-based middleware with Gate policies
- ✅ Complete backend architecture (Models, Controllers, Services)
- ✅ API endpoints for mobile app
- ✅ Event-driven notification system
- ✅ Search functionality with filters
- ✅ Helper utilities and custom middleware

### Phase 2: Core Business Modules (Week 3-6)
**Tasks:** 4-11 | **Effort:** 140-180 hours
- User management & profiles
- Business listing CRUD with multi-step forms
- Business verification workflow
- Review & rating system
- Coupon management (creation, listing, purchase)
- Mock Razorpay payment integration
- Subscription system (₹499/month)
- Business wallet with auto-commission (10%)
- Payout system with ₹1000 threshold

**Deliverables:**
- Complete user dashboard
- Business listing management
- Payment flow structure
- Commission tracking system

### Phase 3: Extended Features (Week 7-10)
**Tasks:** 12-21 | **Effort:** 150-200 hours
- Advertising system (post, manage, analytics)
- Events management (CRUD, RSVP, calendar)
- Jobs board (postings, applications, profiles)
- Blog & content management
- News section with categories
- Service expert directory
- Product marketplace with shopping cart
- Places/attractions directory
- Classifieds module
- Community forums & Q&A

**Deliverables:**
- All content modules functional
- User-generated content workflows
- Booking and inquiry systems

### Phase 4: Admin Panel & Tools (Week 11-13)
**Tasks:** 22-33 | **Effort:** 120-150 hours
- Global search & advanced filters
- Enquiry & communication system
- Complete admin dashboard
- User management (all roles)
- Business & listing moderation
- Content moderation tools
- Financial tracking & reports
- Location & category management
- Ad management system
- SEO management tools
- Communication tools (email, notifications)
- Analytics & reporting dashboards
- Point/credit system

**Deliverables:**
- Fully functional admin panel
- Moderation workflows
- Analytics dashboards
- Revenue tracking

### Phase 5: Frontend & Integration (Week 14-15)
**Tasks:** 34-44 | **Effort:** 80-100 hours
- Notification system (email, in-app)
- File upload & media management
- Convert all 60+ HTML to Blade templates
- Implement public pages
- Integrate user dashboards
- Integrate admin panel UI
- Build RESTful APIs
- Security hardening (CSRF, XSS, SQL injection)
- Email service & templates
- Goa-specific data seeding

**Deliverables:**
- Complete UI integration
- Production-ready APIs
- Security implementation
- Sample data loaded

### Phase 6: Testing, Optimization & Deployment (Week 16)
**Tasks:** 45-52 | **Effort:** 70-120 hours
- Unit tests for business logic
- Feature tests for workflows
- Browser/E2E tests
- Performance optimization (caching, queries)
- SEO implementation
- Comprehensive documentation
- Deployment configuration
- Final QA & bug fixes

**Deliverables:**
- Test coverage reports
- Performance benchmarks
- Complete documentation
- Production-ready application

## Detailed Task List

### ✅ 1. Project Setup & Environment Configuration [COMPLETED]
- Initialize Laravel project, configure SQLite database with MySQL compatibility layer, set up Git, configure environment variables, install required dependencies (Laravel 10+, PHP 8.1+)

### ✅ 2. Database Architecture & Migrations [COMPLETED]
- Design complete database schema for all modules (users, businesses, listings, coupons, events, jobs, products, reviews, payments, etc.). Create migrations with proper indexing, foreign keys, and ensure SQLite/MySQL compatibility

### ✅ 3. Authentication & Authorization System [COMPLETED]
- Implement multi-role auth (Free Users, Business Users, Admin). Use Laravel Breeze/Sanctum. Create middleware for role-based access control, email verification, password reset

### ✅ 4. Core Models & Relationships [COMPLETED]
- Build user registration, profile management (db-my-profile.html), settings (db-setting.html), notifications (db-notifications.html), following/bookmarks system. Convert HTML templates to Blade views

### ✅ 5. Business Logic Controllers [COMPLETED]
- Implement business listing CRUD (add-listing-start.html, db-all-listing.html, listing-details.html), category management, multi-step listing creation, photo uploads, business hours, location mapping

### ✅ 6. Admin Panel Controllers [COMPLETED]
- Create business profile pages (company-profile.html), ownership verification workflow, business claim system, profile editing, analytics dashboard for businesses

### ✅ 7. Form Request Validation Classes [COMPLETED]
- Build review submission, star ratings, review moderation (db-review.html), review responses by businesses, helpful votes, review reporting, average rating calculations

### ✅ 8. API Resources & Collections [COMPLETED]
- Implement coupon creation (db-coupons.html), coupon listing (coupons.html), coupon purchase flow, redemption tracking, free vs paid coupons, expiry management

### ✅ 9. Routing & Route Groups [COMPLETED]
- Create mock Razorpay payment gateway with dummy pages for subscriptions, coupon purchases, ad payments. Store payment records, generate invoices (db-invoice-all.html), implement 10% commission logic

### ✅ 10. Service Classes & Business Logic [COMPLETED]
- Implement ₹499/month premium plan (pricing-details.html), subscription management, feature gating, auto-renewal logic, upgrade/downgrade flows, payment history (db-payment.html)

### ✅ 11. Helper Classes & Utilities [COMPLETED]
- Create wallet system for business earnings, ₹1000 minimum payout threshold, payout request workflow, commission auto-deduction (10%), payout history, pending balance tracking

### ✅ 12. Middleware Extensions [COMPLETED]
- Build ad posting (db-post-ads.html, post-your-ads.html), ad management, ad placement on various pages, ad pricing, promote business feature (db-promote.html), ad analytics

### ✅ 13. Event Listeners [COMPLETED]
- Implement events CRUD (events.html, event-details.html, db-events.html), event categories, RSVP system, event calendar, featured events, event search/filter

### ✅ 14. Search Implementation [COMPLETED]
- Create job posting (create-job.html, db-jobs.html), job seeker profiles (create-job-seeker-profile.html), job applications (db-user-applied-jobs.html), job categories, job search, application management

### ✅ 15. API Controllers [COMPLETED]
- Build blog system (blog-posts.html, blog-details.html, db-blog-posts.html), article creation, categories, tags, comments, SEO meta tags, featured posts, content moderation

### ✅ 16. Console Commands & Scheduled Tasks [COMPLETED]
- Created 5 console commands: CheckExpiredSubscriptions (daily), CheckExpiringSubscriptions (daily), CleanupOldNotifications (weekly), GenerateSitemap (daily), SyncAnalytics (daily at 23:55)
- Configured Laravel Task Scheduler with proper cron scheduling

### ✅ 17. Database Seeders - Goa Data [COMPLETED]
- LocationSeeder: India > Goa > North/South Goa > 40 cities (Panaji, Margao, Mapusa, Vasco, etc.) > 16 beaches
- CategorySeeder: 180+ categories across 5 types (business, event, job, product, service) with parent-child hierarchy
- SettingSeeder: 40 system settings (payment config, features, email, SEO, limits, points system)
- Updated DatabaseSeeder to orchestrate all seeders

### ✅ 18. Email Templates & Mail System [COMPLETED]
- Created 7 Mailable classes: WelcomeEmail, PaymentReceiptEmail, ListingApprovedEmail, ListingRejectedEmail, SubscriptionExpiringEmail, PayoutProcessedEmail, JobApplicationReceivedEmail
- Designed professional responsive email templates with gradient styling
- All emails include relevant data, action buttons, and footer information

### ✅ 19. News Section Module [COMPLETED]
- Created news migration with comprehensive fields (category, breaking, featured, status, SEO meta)
- Built News model with relationships, scopes, and search functionality
- Implemented public NewsController for news listing, details, and category filtering
- Created Business\NewsController for news submission and management
- Built Admin\NewsController with moderation, approval/rejection, breaking/featured toggles
- Support for 5 categories: Tourism, Business, Culture, Events, General
- Full moderation workflow with status tracking (draft, pending, published, rejected)

### ✅ 20. Service Expert Directory Enhancement [COMPLETED]
- Created portfolio_items table for expert work showcase with images and project details
- Enhanced service_experts table with working_hours, languages_spoken, insurance_details
- Added pricing fields: hourly_rate, minimum_charge
- Added performance metrics: offers_emergency_service, response_time_hours, completion_rate, total_bookings
- Built PortfolioItem model with featured items and ordering
- Updated ServiceExpert model with portfolioItems relationship and all new fields

### ✅ 21. Places/Attractions Directory [COMPLETED]
- Created places table with 10 categories (beach, church, temple, fort, museum, waterfall, viewpoint, market, wildlife, other)
- Location mapping with GPS coordinates, multiple images with featured image
- Comprehensive visitor information: timings, entry fee, best time to visit, how to reach
- 8 facility types tracked (parking, restroom, food, wheelchair, wifi, guide, photography, water sports)
- Built Place model with relationships, scopes, and search functionality
- Implemented PlaceController for public access with category/location filtering
- Created Admin\PlaceController with full CRUD and image management
- Support for nearby and similar places recommendations

### ✅ 22. Classifieds Module Enhancement [COMPLETED]
- Enhanced classifieds table with 11 new fields for pricing tiers, product details, exchange options
- Added listing_type (free, featured, premium) with timed promotions
- Product fields: brand, model, year, specifications JSON
- Exchange system: accepts_exchange, exchange_preferences
- Performance tracking: total_inquiries, bumped_at for bump-to-top
- Updated Classified model with new scopes and helper methods
- Built comprehensive Admin\ClassifiedController with full CRUD, bulk actions, statistics
- Featured/urgent toggles, expiry extension, listing upgrades

### ✅ 23. Community Forums & Q&A [COMPLETED]
- Created qa_questions table with categories, tags, votes, accepted answers, status tracking
- Created qa_answers table with votes, accepted answer flag
- Created votes table for upvote/downvote tracking (polymorphic)
- Built QaQuestion model with relationships (user, category, answers, votes), scopes, and answer acceptance
- Built QaAnswer model with relationships and voting functionality
- Built Vote model with static upvote/downvote/removeVote methods
- Implemented QaController with question posting, answering, voting, and accepted answers
- Added complete Q&A routes with authentication middleware

### ✅ 24. Global Search System [COMPLETED]
- Created searches table to track all search queries, results count, filters, IP addresses
- Built Search model with search logging and analytics methods
- Enhanced SearchController to search across 8 content types simultaneously:
  * Listings, Jobs, Products, Events (existing)
  * Service Experts, Classifieds, Places, News (newly integrated)
- Implemented search autocomplete with recent and popular suggestions
- Added search filtering by location, category, and content type
- Search logging for analytics and trending queries

### ✅ 25. Enquiry System Enhancement [COMPLETED]
- Enhanced enquiries table with read_at and replied_at timestamps for workflow tracking
- Updated Enquiry model with new scopes (replied, closed, byStatus)
- Added methods: markAsReplied(), close(), getStatuses()
- Created EnquiryController for public enquiry submission (polymorphic to any content)
- Built Admin\EnquiryController with comprehensive management:
  * Filtering by status, type, and search
  * Mark as read/replied/closed workflows
  * Admin notes for internal tracking
  * Bulk actions (mark_read, mark_replied, close, delete)
  * Statistics dashboard with daily enquiry tracking

### ✅ 26. Admin Dashboard Core [COMPLETED]
- Created comprehensive Admin\DashboardController with 40+ metrics:
  * User statistics (total, by role, new today/month, verified, suspended)
  * Subscription analytics (active, expiring soon)
  * Content statistics across all 9 modules (listings, jobs, products, events, classifieds, service experts, news, places, and more)
  * Financial metrics (total revenue, monthly revenue, today revenue)
  * Enquiry tracking (total, new, today)
- Recent activity feeds for users, listings, payments, enquiries
- Chart data for user registrations (last 30 days) and revenue (last 12 months)
- Analytics page with top performing content, category/location performance
- Ready for dashboard Blade template integration

### ✅ 27. Admin User Management [COMPLETED]
- Built comprehensive Admin\UserController with full CRUD operations
- User listing with filters (role, status, search) and relationship counts
- User detail view with all associated content and payment history
- Create/edit users with role assignment and email verification
- Advanced user actions:
  * Soft delete/restore/force delete
  * Email verification toggle
  * Role changes (free/business/admin)
  * Suspend/unsuspend accounts
- Bulk actions supporting multiple users at once (delete, verify, suspend, unsuspend, change_role)
- User statistics page with growth charts and subscription tracking
- Replaced old UserManagementController routes with new comprehensive controller

### ✅ 28. Admin Content Management [COMPLETED]
- Created comprehensive Admin\ContentController for unified content moderation
- Listings management: filtering by status/category/location, search, status updates, featured toggles
- Jobs management: full CRUD with approval workflow, rejection reasons
- Products management: status control, featured toggles, stock management
- Events management: status updates, cancellation, completion tracking
- Coupons management: approval workflow, featured toggles, expiry management
- Blog posts management: draft/pending/published/rejected workflow
- Bulk actions: approve, reject, delete, feature, unfeature across all content types
- Added 14 admin content routes with filtering and search

### ✅ 29. Admin Financial Management [COMPLETED]
- Built Admin\FinancialController with complete financial operations suite
- Payment tracking: listing with filters (status, type, date range, search), payment details view
- Refund system: process refunds with reasons and timestamps
- Commission tracking: 10% auto-deduction reporting, monthly/total commission views
- Payout management:
  * Approve payouts (pending → processing)
  * Mark as paid with transaction reference and payment method
  * Reject payouts with balance return to user
  * Pending and paid amount tracking
- Subscription admin: view all subscriptions, extend by days, cancel with reasons
- Invoice management: listing with search and status filters
- Revenue analytics:
  * Monthly revenue trends (last 12 months)
  * Revenue by payment type breakdown
  * Top 10 paying users
  * Total revenue and commission calculations
- Added 13 financial management routes

### ✅ 30. Admin Reports & Analytics [COMPLETED]
- Created comprehensive Admin\ReportController with 8 report types:
  * User Activity: new users, active users, daily registrations, users by role
  * Content Performance: top listings by views, top jobs by applications, content creation trends, status breakdown
  * Financial: total revenue, commission, transactions, average transaction, daily revenue, revenue by type
  * Search Analytics: popular searches, zero-result searches, daily trends, searches by type
  * Location Performance: listings/jobs/events by location with counts
  * Category Performance: listings/jobs/products by category with avg views/applications/sales
  * Enquiry Report: total, responded, response rate, by status/type, daily trends
- Date range filtering for all reports
- Export functionality placeholder (CSV/Excel/PDF)
- Added 9 report routes with comprehensive analytics

### ✅ 31. Subscription System [COMPLETED]
- Built SubscriptionController with 3-tier subscription plans:
  * Basic: ₹499/month or ₹4999/year (5 listings, 1 featured/month, basic analytics, email support)
  * Premium: ₹999/month or ₹9999/year (unlimited listings, 5 featured/month, advanced analytics, verified badge, SEO optimization)
  * Enterprise: ₹2499/month or ₹24999/year (everything + API access, white-label, dedicated support, unlimited featured)
- Subscribe workflow: plan selection, billing cycle (monthly/yearly), payment integration
- Feature gating system with JSON-based feature storage per plan
- Subscription management:
  * Cancel subscription with reason tracking
  * Renew expired subscriptions with payment logging
  * Upgrade to higher tier with prorated pricing
- Auto role update: Free → Business on subscription
- Payment logging for all subscription transactions
- Added 5 subscription routes with authentication

### ✅ 32. Review & Rating System [COMPLETED]
- Created ReviewController for public review functionality:
  * 5-star rating with title and detailed comment
  * Image uploads (max 5 images, validated)
  * Duplicate review prevention per user per item
  * Update and delete own reviews
  * Helpful/Not helpful voting (AJAX endpoints)
  * Business owner reply capability with timestamp
- Built Admin\ReviewController for review moderation:
  * Listing with filters (status, rating, type, search)
  * Approve/reject individual reviews
  * Delete reviews with soft delete
  * Bulk actions (approve, reject, delete multiple)
  * Review statistics dashboard:
    - Total, approved, pending, deleted counts
    - Reviews by rating (1-5 stars breakdown)
    - Reviews by type (listings, products, etc.) with avg rating
    - Daily review trends (last 30 days)
- Polymorphic relationships: works with any content type
- Added 6 public review routes and 7 admin review routes

### 33. Location & Category Setup
- Implement Goa-specific location management (admin-all-city.html, admin-add-city.html), category/subcategory CRUD for all modules, location hierarchy, category customization

### 34. Points/Credit System
- Implement points purchase (buy-points.html), point history (db-point-history.html), point redemption, point-based features, admin point settings

### 35. Notification System
- Build comprehensive notification system: email notifications, in-app notifications, SMS (optional), push notifications structure, notification preferences, notification templates

### 36. File Upload & Media Management
- Implement secure file uploads (images, documents), image optimization, thumbnail generation, gallery management, file validation, storage management, AWS S3 compatibility

### 37. Frontend - Convert HTML to Blade Templates
- Convert all 60+ HTML templates to Laravel Blade views with proper layouts, components, includes. Implement master layouts, partials for header/footer/sidebar, blade directives

### 38. Frontend - Public Pages
- Implement public pages: homepage (index.html), about (about.html), contact (contact-us.html), pricing (pricing-details.html), FAQ (faq.html), how-to (how-to.html), terms, privacy policy

### 39. Frontend - Dashboard Integration
- Integrate user dashboard (dashboard.html) with all sub-sections: listings, ads, events, jobs, products, coupons, reviews, bookings, payments, analytics. Make data dynamic

### 40. Frontend - Admin Panel Integration
- Convert admin HTML templates to Blade, integrate with backend APIs, implement AJAX for quick actions, create reusable admin components, admin navigation, permission-based UI

### 41. API Development - RESTful APIs
- Build RESTful APIs for all modules using Laravel API resources. Implement proper HTTP methods, status codes, error handling, pagination, filtering, sorting for future mobile app

### 42. Security Implementation
- Implement CSRF protection, XSS prevention, SQL injection protection, rate limiting, input validation, sanitization, secure file uploads, password hashing, API authentication

### 43. Email Integration & Templates
- Set up email service (Mailtrap for dev, SMTP for prod), create email templates: welcome, verification, password reset, payment receipts, notifications, newsletters using Laravel Mail

### 44. Goa-Specific Data Seeding
- Create seeders for Goa locations (cities, areas, beaches), business categories, job categories, event categories, product categories, service types, tourist attractions

### 45. Testing - Unit Tests
- Write PHPUnit tests for models, services, helpers, utilities. Test business logic: commission calculations, subscription handling, coupon validation, payout threshold checks

### 46. Testing - Feature Tests
- Create feature tests for major workflows: user registration, listing creation, coupon purchase, subscription flow, payment processing, admin moderation, review submission

### 47. Testing - Browser/E2E Tests
- Implement Laravel Dusk tests for critical user journeys: complete listing creation, end-to-end payment flow, multi-step forms, admin approval workflows

### 48. Performance Optimization
- Implement query optimization, eager loading, caching (Redis/file cache), image optimization, lazy loading, database indexing, route caching, config caching, view caching

### 49. SEO Implementation
- Implement dynamic meta tags, OpenGraph tags, Twitter cards, JSON-LD structured data, XML sitemap generation, robots.txt, canonical URLs, SEO-friendly URLs, breadcrumbs

### 50. Documentation
- Create comprehensive documentation: installation guide, database schema, API documentation, admin user guide, business user guide, deployment instructions, environment setup

### 51. Deployment Setup
- Configure production environment, set up server (Apache/Nginx), SSL certificate, environment variables, database migration scripts, backup strategy, monitoring tools, error tracking (Sentry)

### 52. Final QA & Bug Fixes
- Comprehensive QA testing across all modules, cross-browser testing, mobile responsiveness verification, fix identified bugs, performance testing, security audit, user acceptance testing

---

## Technical Architecture

### Database Design Principles
- **SQLite for MVP:** Lightweight, zero-config, file-based
- **MySQL Migration Ready:** Use standard SQL, avoid SQLite-specific features
- **Key Considerations:**
  - Foreign keys properly defined
  - Proper indexing on search/filter columns
  - ENUM vs VARCHAR choices documented
  - Timestamp columns with timezone awareness
  - Soft deletes where appropriate

### Laravel Best Practices
- **MVC Pattern:** Strict separation of concerns
- **Service Layer:** Business logic outside controllers
- **Repositories:** Data access abstraction (optional for MVP)
- **Form Requests:** Validation in dedicated classes
- **API Resources:** Consistent API responses
- **Events & Listeners:** For notifications, emails
- **Jobs & Queues:** For background tasks (future)

### Frontend Architecture
- **Blade Templates:** Master layouts, components, includes
- **Asset Management:** Laravel Mix/Vite
- **Reusable Components:** Header, footer, sidebar, modals
- **JavaScript:** Minimal jQuery, Alpine.js for interactivity
- **CSS Framework:** Bootstrap (already in HTML templates)
- **Icons:** FontAwesome/Similar

### Security Measures
- CSRF protection on all forms
- XSS prevention (Blade auto-escaping)
- SQL injection protection (Eloquent ORM)
- Rate limiting on APIs and auth routes
- Password hashing (bcrypt)
- Input validation & sanitization
- File upload restrictions
- API authentication (Sanctum)

### Performance Optimization Strategy
- Eager loading for relationships (N+1 prevention)
- Query optimization (select specific columns)
- Redis/File caching for frequently accessed data
- Image optimization (compression, thumbnails)
- Lazy loading for images
- Route caching in production
- Config caching in production
- View caching in production
- CDN for static assets (future)

---

## Development Guidelines

### Code Standards
- Follow PSR-12 coding style
- Meaningful variable/method names
- Single Responsibility Principle
- DRY (Don't Repeat Yourself)
- Comment complex business logic
- Type hints for parameters and returns

### Git Workflow
- Main/Master branch: Production-ready code
- Develop branch: Integration branch
- Feature branches: feature/task-name
- Commit message format: "[Task #] Brief description"
- Pull requests for code review
- Semantic versioning for releases

### Testing Strategy
- **Unit Tests:** Models, Services, Helpers (70% coverage)
- **Feature Tests:** Controllers, Routes, Workflows (80% coverage)
- **Browser Tests:** Critical user journeys (10-15 key flows)
- **Manual Testing:** UI/UX, cross-browser, mobile responsive

### Documentation Requirements
1. **README.md:** Project overview, setup instructions
2. **INSTALL.md:** Detailed installation guide
3. **DATABASE.md:** Schema documentation with ERD
4. **API.md:** API endpoint documentation
5. **ADMIN_GUIDE.md:** Admin panel user guide
6. **BUSINESS_GUIDE.md:** Business user guide
7. **DEPLOYMENT.md:** Production deployment steps
8. **CHANGELOG.md:** Version history

---

## Risk Management

### Technical Risks
| Risk | Impact | Mitigation |
|------|--------|------------|
| SQLite limitations at scale | Medium | Design with MySQL migration path; document breaking points |
| HTML template integration complexity | Medium | Start with simple pages, create reusable components early |
| Payment flow without real gateway | Low | Mock comprehensive flow, document Razorpay integration steps |
| Performance with large datasets | Medium | Implement pagination, caching, indexing from start |
| Security vulnerabilities | High | Follow Laravel security best practices, regular audits |

### Project Risks
| Risk | Impact | Mitigation |
|------|--------|------------|
| Scope creep | High | Stick to MVP task list, maintain "nice-to-have" backlog |
| Timeline delays | Medium | Weekly progress tracking, adjust scope if needed |
| Quality vs Speed tradeoff | Medium | Prioritize core features, implement comprehensive testing |
| Requirements changes | Medium | Document all changes, assess impact before implementing |

---

## Success Criteria

### MVP Launch Ready When:
- ✅ All 52 tasks completed
- ✅ Core user journeys working end-to-end
- ✅ Admin can manage all content and users
- ✅ Business users can create listings, manage coupons
- ✅ Free users can browse, purchase coupons
- ✅ Payment flow (mock) working completely
- ✅ Commission calculation automated
- ✅ 70%+ test coverage
- ✅ No critical bugs
- ✅ Mobile responsive
- ✅ Documentation complete
- ✅ Deployed to staging environment

### Post-MVP Enhancements (Future)
- Real Razorpay integration
- SMS notifications
- Progressive Web App (PWA)
- Mobile apps (React Native/Flutter)
- Real-time chat
- Advanced booking system
- Loyalty/rewards program
- Multi-language support
- Social media integration
- Advanced analytics & insights

---

## Resource Requirements

### Development Team (Recommended)
- **1 Full-stack Laravel Developer** (Primary)
- **1 Frontend Developer** (Part-time for HTML→Blade conversion)
- **1 QA Engineer** (Week 15-16)

### Tools & Services (MVP)
- **Version Control:** Git + GitHub/GitLab
- **Development:** Local (XAMPP/Laragon/Valet)
- **Email Testing:** Mailtrap
- **Error Tracking:** Sentry (free tier)
- **Project Management:** Trello/Asana/Notion
- **Documentation:** Markdown files

### Hosting (Production)
- **Server:** VPS (DigitalOcean, Linode, AWS Lightsail)
- **Web Server:** Nginx or Apache
- **PHP:** 8.1+
- **Database:** SQLite (migrate to MySQL later)
- **SSL:** Let's Encrypt (free)
- **Domain:** Register .in domain
- **Storage:** Local (S3 for future scaling)

---

## Next Steps

1. **Review & Approve Plan:** Stakeholder sign-off
2. **Set Up Development Environment:** Install tools, configure workspace
3. **Start Task 1:** Initialize Laravel project
4. **Weekly Progress Reviews:** Track against timeline
5. **Adjust as Needed:** Remain flexible within MVP scope

---

**Document Version:** 1.0  
**Last Updated:** November 30, 2025  
**Prepared By:** Development Team
