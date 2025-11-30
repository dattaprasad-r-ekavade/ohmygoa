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
use App\Http\Controllers\SearchController;
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

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/results', [SearchController::class, 'search'])->name('search.results');
Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');

// Q&A / Community Forum
Route::prefix('qa')->name('qa.')->group(function () {
    Route::get('/', [\App\Http\Controllers\QaController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\QaController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/', [\App\Http\Controllers\QaController::class, 'store'])->name('store')->middleware('auth');
    Route::get('/{question}', [\App\Http\Controllers\QaController::class, 'show'])->name('show');
    Route::post('/{question}/answer', [\App\Http\Controllers\QaController::class, 'answer'])->name('answer')->middleware('auth');
    Route::post('/{question}/answers/{answer}/accept', [\App\Http\Controllers\QaController::class, 'acceptAnswer'])->name('answer.accept')->middleware('auth');
    Route::post('/{type}/{id}/vote', [\App\Http\Controllers\QaController::class, 'vote'])->name('vote')->middleware('auth');
});

// Enquiries
Route::post('/enquiries', [\App\Http\Controllers\EnquiryController::class, 'store'])->name('enquiries.store');
Route::get('/my-enquiries', [\App\Http\Controllers\EnquiryController::class, 'myEnquiries'])->name('enquiries.my')->middleware('auth');

// Subscriptions
Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('index');
    Route::post('/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscribe')->middleware('auth');
    Route::post('/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('cancel')->middleware('auth');
    Route::post('/{subscription}/renew', [\App\Http\Controllers\SubscriptionController::class, 'renew'])->name('renew')->middleware('auth');
    Route::post('/upgrade', [\App\Http\Controllers\SubscriptionController::class, 'upgrade'])->name('upgrade')->middleware('auth');
});

// Reviews
Route::prefix('reviews')->name('reviews.')->middleware('auth')->group(function () {
    Route::post('/', [\App\Http\Controllers\ReviewController::class, 'store'])->name('store');
    Route::patch('/{review}', [\App\Http\Controllers\ReviewController::class, 'update'])->name('update');
    Route::delete('/{review}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('destroy');
    Route::post('/{review}/helpful', [\App\Http\Controllers\ReviewController::class, 'markHelpful'])->name('helpful');
    Route::post('/{review}/not-helpful', [\App\Http\Controllers\ReviewController::class, 'markNotHelpful'])->name('not-helpful');
    Route::post('/{review}/reply', [\App\Http\Controllers\ReviewController::class, 'reply'])->name('reply');
});

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

// News - Public
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [\App\Http\Controllers\NewsController::class, 'index'])->name('index');
    Route::get('/category/{category}', [\App\Http\Controllers\NewsController::class, 'category'])->name('category');
    Route::get('/{slug}', [\App\Http\Controllers\NewsController::class, 'show'])->name('show');
});

// Places - Public
Route::prefix('places')->name('places.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PlaceController::class, 'index'])->name('index');
    Route::get('/category/{category}', [\App\Http\Controllers\PlaceController::class, 'category'])->name('category');
    Route::get('/location/{location}', [\App\Http\Controllers\PlaceController::class, 'location'])->name('location');
    Route::get('/{slug}', [\App\Http\Controllers\PlaceController::class, 'show'])->name('show');
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

    // News Management
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Business\NewsController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Business\NewsController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Business\NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [\App\Http\Controllers\Business\NewsController::class, 'edit'])->name('edit');
        Route::patch('/{news}', [\App\Http\Controllers\Business\NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [\App\Http\Controllers\Business\NewsController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [\App\Http\Controllers\Admin\DashboardController::class, 'analytics'])->name('analytics');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\UserController::class, 'statistics'])->name('statistics');
        Route::get('/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::patch('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/restore', [\App\Http\Controllers\Admin\UserController::class, 'restore'])->name('restore');
        Route::delete('/{user}/force-delete', [\App\Http\Controllers\Admin\UserController::class, 'forceDelete'])->name('force-delete');
        Route::post('/{user}/verify-email', [\App\Http\Controllers\Admin\UserController::class, 'verifyEmail'])->name('verify-email');
        Route::post('/{user}/change-role', [\App\Http\Controllers\Admin\UserController::class, 'changeRole'])->name('change-role');
        Route::post('/{user}/suspend', [\App\Http\Controllers\Admin\UserController::class, 'suspendUser'])->name('suspend');
        Route::post('/{user}/unsuspend', [\App\Http\Controllers\Admin\UserController::class, 'unsuspendUser'])->name('unsuspend');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('bulk-action');
    });

    // Enquiry Management
    Route::prefix('enquiries')->name('enquiries.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EnquiryController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\EnquiryController::class, 'statistics'])->name('statistics');
        Route::get('/{enquiry}', [\App\Http\Controllers\Admin\EnquiryController::class, 'show'])->name('show');
        Route::patch('/{enquiry}', [\App\Http\Controllers\Admin\EnquiryController::class, 'update'])->name('update');
        Route::post('/{enquiry}/mark-replied', [\App\Http\Controllers\Admin\EnquiryController::class, 'markAsReplied'])->name('mark-replied');
        Route::post('/{enquiry}/close', [\App\Http\Controllers\Admin\EnquiryController::class, 'close'])->name('close');
        Route::delete('/{enquiry}', [\App\Http\Controllers\Admin\EnquiryController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\EnquiryController::class, 'bulkAction'])->name('bulk-action');
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

    // News Management
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\NewsController::class, 'index'])->name('index');
        Route::get('/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'show'])->name('show');
        Route::get('/{news}/edit', [\App\Http\Controllers\Admin\NewsController::class, 'edit'])->name('edit');
        Route::patch('/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'destroy'])->name('destroy');
        Route::post('/{news}/approve', [\App\Http\Controllers\Admin\NewsController::class, 'approve'])->name('approve');
        Route::post('/{news}/reject', [\App\Http\Controllers\Admin\NewsController::class, 'reject'])->name('reject');
        Route::post('/{news}/toggle-breaking', [\App\Http\Controllers\Admin\NewsController::class, 'toggleBreaking'])->name('toggle-breaking');
        Route::post('/{news}/toggle-featured', [\App\Http\Controllers\Admin\NewsController::class, 'toggleFeatured'])->name('toggle-featured');
    });

    // Places Management
    Route::prefix('places')->name('places.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlaceController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PlaceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PlaceController::class, 'store'])->name('store');
        Route::get('/{place}', [\App\Http\Controllers\Admin\PlaceController::class, 'show'])->name('show');
        Route::get('/{place}/edit', [\App\Http\Controllers\Admin\PlaceController::class, 'edit'])->name('edit');
        Route::patch('/{place}', [\App\Http\Controllers\Admin\PlaceController::class, 'update'])->name('update');
        Route::delete('/{place}', [\App\Http\Controllers\Admin\PlaceController::class, 'destroy'])->name('destroy');
        Route::post('/{place}/toggle-featured', [\App\Http\Controllers\Admin\PlaceController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{place}/toggle-popular', [\App\Http\Controllers\Admin\PlaceController::class, 'togglePopular'])->name('toggle-popular');
        Route::delete('/{place}/remove-image', [\App\Http\Controllers\Admin\PlaceController::class, 'removeImage'])->name('remove-image');
    });

    // Classifieds Management
    Route::prefix('classifieds')->name('classifieds.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ClassifiedController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\ClassifiedController::class, 'statistics'])->name('statistics');
        Route::get('/{classified}', [\App\Http\Controllers\Admin\ClassifiedController::class, 'show'])->name('show');
        Route::patch('/{classified}', [\App\Http\Controllers\Admin\ClassifiedController::class, 'update'])->name('update');
        Route::delete('/{classified}', [\App\Http\Controllers\Admin\ClassifiedController::class, 'destroy'])->name('destroy');
        Route::post('/{classified}/toggle-featured', [\App\Http\Controllers\Admin\ClassifiedController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{classified}/toggle-urgent', [\App\Http\Controllers\Admin\ClassifiedController::class, 'toggleUrgent'])->name('toggle-urgent');
        Route::post('/{classified}/extend', [\App\Http\Controllers\Admin\ClassifiedController::class, 'extend'])->name('extend');
        Route::post('/{classified}/upgrade', [\App\Http\Controllers\Admin\ClassifiedController::class, 'upgrade'])->name('upgrade');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\ClassifiedController::class, 'bulkAction'])->name('bulk-action');
    });

    // Content Management
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/listings', [\App\Http\Controllers\Admin\ContentController::class, 'listings'])->name('listings');
        Route::patch('/listings/{listing}', [\App\Http\Controllers\Admin\ContentController::class, 'updateListing'])->name('listings.update');
        Route::get('/jobs', [\App\Http\Controllers\Admin\ContentController::class, 'jobs'])->name('jobs');
        Route::patch('/jobs/{job}', [\App\Http\Controllers\Admin\ContentController::class, 'updateJob'])->name('jobs.update');
        Route::get('/products', [\App\Http\Controllers\Admin\ContentController::class, 'products'])->name('products');
        Route::patch('/products/{product}', [\App\Http\Controllers\Admin\ContentController::class, 'updateProduct'])->name('products.update');
        Route::get('/events', [\App\Http\Controllers\Admin\ContentController::class, 'events'])->name('events');
        Route::patch('/events/{event}', [\App\Http\Controllers\Admin\ContentController::class, 'updateEvent'])->name('events.update');
        Route::get('/coupons', [\App\Http\Controllers\Admin\ContentController::class, 'coupons'])->name('coupons');
        Route::patch('/coupons/{coupon}', [\App\Http\Controllers\Admin\ContentController::class, 'updateCoupon'])->name('coupons.update');
        Route::get('/blogs', [\App\Http\Controllers\Admin\ContentController::class, 'blogs'])->name('blogs');
        Route::patch('/blogs/{blog}', [\App\Http\Controllers\Admin\ContentController::class, 'updateBlog'])->name('blogs.update');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\ContentController::class, 'bulkAction'])->name('bulk-action');
    });

    // Financial Management
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/payments', [\App\Http\Controllers\Admin\FinancialController::class, 'payments'])->name('payments');
        Route::get('/payments/{payment}', [\App\Http\Controllers\Admin\FinancialController::class, 'paymentDetails'])->name('payments.show');
        Route::post('/payments/{payment}/refund', [\App\Http\Controllers\Admin\FinancialController::class, 'refundPayment'])->name('payments.refund');
        Route::get('/commissions', [\App\Http\Controllers\Admin\FinancialController::class, 'commissions'])->name('commissions');
        Route::get('/payouts', [\App\Http\Controllers\Admin\FinancialController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/{payout}/approve', [\App\Http\Controllers\Admin\FinancialController::class, 'approvePayout'])->name('payouts.approve');
        Route::post('/payouts/{payout}/mark-paid', [\App\Http\Controllers\Admin\FinancialController::class, 'markPayoutAsPaid'])->name('payouts.mark-paid');
        Route::post('/payouts/{payout}/reject', [\App\Http\Controllers\Admin\FinancialController::class, 'rejectPayout'])->name('payouts.reject');
        Route::get('/subscriptions', [\App\Http\Controllers\Admin\FinancialController::class, 'subscriptions'])->name('subscriptions');
        Route::post('/subscriptions/{subscription}/extend', [\App\Http\Controllers\Admin\FinancialController::class, 'extendSubscription'])->name('subscriptions.extend');
        Route::post('/subscriptions/{subscription}/cancel', [\App\Http\Controllers\Admin\FinancialController::class, 'cancelSubscription'])->name('subscriptions.cancel');
        Route::get('/invoices', [\App\Http\Controllers\Admin\FinancialController::class, 'invoices'])->name('invoices');
        Route::get('/revenue-analytics', [\App\Http\Controllers\Admin\FinancialController::class, 'revenueAnalytics'])->name('revenue-analytics');
    });

    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/user-activity', [\App\Http\Controllers\Admin\ReportController::class, 'userActivity'])->name('user-activity');
        Route::get('/content-performance', [\App\Http\Controllers\Admin\ReportController::class, 'contentPerformance'])->name('content-performance');
        Route::get('/financial', [\App\Http\Controllers\Admin\ReportController::class, 'financial'])->name('financial');
        Route::get('/search-analytics', [\App\Http\Controllers\Admin\ReportController::class, 'searchAnalytics'])->name('search-analytics');
        Route::get('/location-performance', [\App\Http\Controllers\Admin\ReportController::class, 'locationPerformance'])->name('location-performance');
        Route::get('/category-performance', [\App\Http\Controllers\Admin\ReportController::class, 'categoryPerformance'])->name('category-performance');
        Route::get('/enquiries', [\App\Http\Controllers\Admin\ReportController::class, 'enquiries'])->name('enquiries');
        Route::post('/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('export');
    });

    // Reviews Management
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('index');
        Route::get('/statistics', [\App\Http\Controllers\Admin\ReviewController::class, 'statistics'])->name('statistics');
        Route::get('/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('show');
        Route::post('/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('approve');
        Route::post('/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reject');
        Route::delete('/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [\App\Http\Controllers\Admin\ReviewController::class, 'bulkAction'])->name('bulk-action');
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
