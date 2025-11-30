<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContentApprovalController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\BusinessListingController;
use App\Http\Controllers\ClassifiedController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceExpertController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return view('index');
})->name('home');

// Static Pages
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact-us')->name('contact');
Route::view('/how-to', 'how-to')->name('how-to');
Route::view('/faq', 'faq')->name('faq');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy');
Route::view('/terms-of-use', 'terms-of-use')->name('terms');

// Business Listings - Public
Route::prefix('listings')->name('listings.')->group(function () {
    Route::get('/', [BusinessListingController::class, 'index'])->name('index');
    Route::get('/{slug}', [BusinessListingController::class, 'show'])->name('show');
});

// Events - Public
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
});

// Jobs - Public
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', [JobListingController::class, 'index'])->name('index');
    Route::get('/{slug}', [JobListingController::class, 'show'])->name('show');
});

// Products - Public
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

// Coupons - Public
Route::prefix('coupons')->name('coupons.')->group(function () {
    Route::get('/', [CouponController::class, 'index'])->name('index');
    Route::get('/{code}', [CouponController::class, 'show'])->name('show');
});

// Classifieds - Public
Route::prefix('classifieds')->name('classifieds.')->group(function () {
    Route::get('/', [ClassifiedController::class, 'index'])->name('index');
    Route::get('/{slug}', [ClassifiedController::class, 'show'])->name('show');
});

// Service Experts - Public
Route::prefix('service-experts')->name('service-experts.')->group(function () {
    Route::get('/', [ServiceExpertController::class, 'index'])->name('index');
    Route::get('/{slug}', [ServiceExpertController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Free User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Bookmarks
    Route::post('/listings/{listing}/bookmark', [BusinessListingController::class, 'toggleBookmark'])->name('listings.bookmark');
    Route::post('/events/{event}/bookmark', [EventController::class, 'toggleBookmark'])->name('events.bookmark');
    Route::post('/jobs/{job}/bookmark', [JobListingController::class, 'toggleBookmark'])->name('jobs.bookmark');
    Route::post('/products/{product}/bookmark', [ProductController::class, 'toggleBookmark'])->name('products.bookmark');
    Route::post('/coupons/{coupon}/bookmark', [CouponController::class, 'toggleBookmark'])->name('coupons.bookmark');
    Route::post('/classifieds/{classified}/bookmark', [ClassifiedController::class, 'toggleBookmark'])->name('classifieds.bookmark');
    Route::post('/service-experts/{serviceExpert}/bookmark', [ServiceExpertController::class, 'toggleBookmark'])->name('service-experts.bookmark');

    // Follow Business
    Route::post('/listings/{listing}/follow', [BusinessListingController::class, 'toggleFollow'])->name('listings.follow');

    // Job Applications
    Route::get('/jobs/{job}/apply', [JobListingController::class, 'apply'])->name('jobs.apply');
    Route::post('/jobs/{job}/apply', [JobListingController::class, 'storeApplication'])->name('jobs.apply.store');

    // Coupon Redemption
    Route::post('/coupons/{coupon}/redeem', [CouponController::class, 'redeem'])->name('coupons.redeem');
    Route::post('/coupons/validate', [CouponController::class, 'validate'])->name('coupons.validate');

    // Service Expert Booking
    Route::get('/service-experts/{serviceExpert}/book', [ServiceExpertController::class, 'book'])->name('service-experts.book');
    Route::post('/service-experts/{serviceExpert}/book', [ServiceExpertController::class, 'storeBooking'])->name('service-experts.book.store');

    // Classified Ad Management (Any authenticated user)
    Route::prefix('classifieds')->name('classifieds.')->group(function () {
        Route::get('/create', [ClassifiedController::class, 'create'])->name('create');
        Route::post('/', [ClassifiedController::class, 'store'])->name('store');
        Route::get('/{classified}/edit', [ClassifiedController::class, 'edit'])->name('edit');
        Route::patch('/{classified}', [ClassifiedController::class, 'update'])->name('update');
        Route::delete('/{classified}', [ClassifiedController::class, 'destroy'])->name('destroy');
        Route::post('/{classified}/mark-sold', [ClassifiedController::class, 'markAsSold'])->name('mark-sold');
        Route::post('/{classified}/renew', [ClassifiedController::class, 'renew'])->name('renew');
    });
});

/*
|--------------------------------------------------------------------------
| Business User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:business'])->prefix('business')->name('business.')->group(function () {
    
    // Business Dashboard
    Route::get('/dashboard', function () {
        return view('business.dashboard');
    })->name('dashboard');

    // Business Listings Management
    Route::prefix('listings')->name('listings.')->group(function () {
        Route::get('/', function () {
            return view('business.listings.index');
        })->name('index');
        Route::get('/create', [BusinessListingController::class, 'create'])->name('create');
        Route::post('/', [BusinessListingController::class, 'store'])->name('store');
        Route::get('/{listing}/edit', [BusinessListingController::class, 'edit'])->name('edit');
        Route::patch('/{listing}', [BusinessListingController::class, 'update'])->name('update');
        Route::delete('/{listing}', [BusinessListingController::class, 'destroy'])->name('destroy');
    });

    // Events Management
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', function () {
            return view('business.events.index');
        })->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');
        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
        Route::patch('/{event}', [EventController::class, 'update'])->name('update');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
    });

    // Jobs Management
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/', function () {
            return view('business.jobs.index');
        })->name('index');
        Route::get('/create', [JobListingController::class, 'create'])->name('create');
        Route::post('/', [JobListingController::class, 'store'])->name('store');
        Route::get('/{job}/edit', [JobListingController::class, 'edit'])->name('edit');
        Route::patch('/{job}', [JobListingController::class, 'update'])->name('update');
        Route::delete('/{job}', [JobListingController::class, 'destroy'])->name('destroy');
    });

    // Products Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', function () {
            return view('business.products.index');
        })->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::patch('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // Coupons Management
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::get('/', function () {
            return view('business.coupons.index');
        })->name('index');
        Route::get('/create', [CouponController::class, 'create'])->name('create');
        Route::post('/', [CouponController::class, 'store'])->name('store');
        Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
        Route::patch('/{coupon}', [CouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');
    });

    // Service Experts Management
    Route::prefix('service-experts')->name('service-experts.')->group(function () {
        Route::get('/', function () {
            return view('business.service-experts.index');
        })->name('index');
        Route::get('/create', [ServiceExpertController::class, 'create'])->name('create');
        Route::post('/', [ServiceExpertController::class, 'store'])->name('store');
        Route::get('/{serviceExpert}/edit', [ServiceExpertController::class, 'edit'])->name('edit');
        Route::patch('/{serviceExpert}', [ServiceExpertController::class, 'update'])->name('update');
        Route::delete('/{serviceExpert}', [ServiceExpertController::class, 'destroy'])->name('destroy');
        Route::post('/{serviceExpert}/toggle-availability', [ServiceExpertController::class, 'toggleAvailability'])->name('toggle-availability');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/toggle-verification', [UserManagementController::class, 'toggleVerification'])->name('toggle-verification');
        Route::post('/bulk-action', [UserManagementController::class, 'bulkAction'])->name('bulk-action');
    });

    // Content Approval
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ContentApprovalController::class, 'index'])->name('index');
        
        Route::post('/listings/{listing}/approve', [ContentApprovalController::class, 'approveListing'])->name('listings.approve');
        Route::post('/listings/{listing}/reject', [ContentApprovalController::class, 'rejectListing'])->name('listings.reject');
        
        Route::post('/products/{product}/approve', [ContentApprovalController::class, 'approveProduct'])->name('products.approve');
        Route::post('/products/{product}/reject', [ContentApprovalController::class, 'rejectProduct'])->name('products.reject');
        
        Route::post('/classifieds/{classified}/approve', [ContentApprovalController::class, 'approveClassified'])->name('classifieds.approve');
        Route::post('/classifieds/{classified}/reject', [ContentApprovalController::class, 'rejectClassified'])->name('classifieds.reject');
        
        Route::post('/service-experts/{serviceExpert}/approve', [ContentApprovalController::class, 'approveServiceExpert'])->name('service-experts.approve');
        Route::post('/service-experts/{serviceExpert}/reject', [ContentApprovalController::class, 'rejectServiceExpert'])->name('service-experts.reject');
        
        Route::post('/bulk-approve', [ContentApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ContentApprovalController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Categories Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [AdminCategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::patch('/{category}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
        Route::post('/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{category}/toggle-featured', [AdminCategoryController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/reorder', [AdminCategoryController::class, 'reorder'])->name('reorder');
    });

    // Locations Management
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/', [AdminLocationController::class, 'index'])->name('index');
        Route::get('/create', [AdminLocationController::class, 'create'])->name('create');
        Route::post('/', [AdminLocationController::class, 'store'])->name('store');
        Route::get('/{location}', [AdminLocationController::class, 'show'])->name('show');
        Route::get('/{location}/edit', [AdminLocationController::class, 'edit'])->name('edit');
        Route::patch('/{location}', [AdminLocationController::class, 'update'])->name('update');
        Route::delete('/{location}', [AdminLocationController::class, 'destroy'])->name('destroy');
        Route::post('/{location}/toggle-status', [AdminLocationController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{location}/toggle-popular', [AdminLocationController::class, 'togglePopular'])->name('toggle-popular');
        Route::post('/reorder', [AdminLocationController::class, 'reorder'])->name('reorder');
        Route::post('/import', [AdminLocationController::class, 'import'])->name('import');
    });

    // Settings Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/general', [SettingsController::class, 'updateGeneral'])->name('general.update');
        Route::post('/seo', [SettingsController::class, 'updateSeo'])->name('seo.update');
        Route::post('/email', [SettingsController::class, 'updateEmail'])->name('email.update');
        Route::post('/payment', [SettingsController::class, 'updatePayment'])->name('payment.update');
        Route::post('/social', [SettingsController::class, 'updateSocial'])->name('social.update');
        Route::post('/business', [SettingsController::class, 'updateBusiness'])->name('business.update');
        Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear-cache');
        Route::post('/clear-specific-cache', [SettingsController::class, 'clearSpecificCache'])->name('clear-specific-cache');
        Route::post('/backup-database', [SettingsController::class, 'backupDatabase'])->name('backup-database');
    });
});

require __DIR__.'/auth.php';
