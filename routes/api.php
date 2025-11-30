<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BusinessListingApiController;
use App\Http\Controllers\Api\CouponApiController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\JobListingApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\LocationApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API Routes
Route::prefix('v1')->group(function () {
    
    // Authentication
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);

    // Business Listings
    Route::get('/listings', [BusinessListingApiController::class, 'index']);
    Route::get('/listings/{id}', [BusinessListingApiController::class, 'show']);

    // Events
    Route::get('/events', [EventApiController::class, 'index']);
    Route::get('/events/{id}', [EventApiController::class, 'show']);

    // Job Listings
    Route::get('/jobs', [JobListingApiController::class, 'index']);
    Route::get('/jobs/{id}', [JobListingApiController::class, 'show']);

    // Products
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{id}', [ProductApiController::class, 'show']);

    // Coupons
    Route::get('/coupons', [CouponApiController::class, 'index']);
    Route::get('/coupons/{id}', [CouponApiController::class, 'show']);

    // Search
    Route::get('/search', [SearchApiController::class, 'search']);
    Route::get('/search/autocomplete', [SearchApiController::class, 'autocomplete']);
    Route::get('/search/suggestions', [SearchApiController::class, 'suggestions']);

    // Categories
    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/categories/{id}', [CategoryApiController::class, 'show']);
    Route::get('/categories/{id}/listings', [CategoryApiController::class, 'listings']);

    // Locations
    Route::get('/locations', [LocationApiController::class, 'index']);
    Route::get('/locations/{id}', [LocationApiController::class, 'show']);
    Route::get('/locations/{id}/listings', [LocationApiController::class, 'listings']);
});

// Protected API Routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Authentication
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::put('/profile', [AuthApiController::class, 'updateProfile']);
    Route::put('/change-password', [AuthApiController::class, 'changePassword']);

    // Business Listings
    Route::get('/my-listings', [BusinessListingApiController::class, 'myListings']);
    Route::post('/listings', [BusinessListingApiController::class, 'store']);
    Route::put('/listings/{id}', [BusinessListingApiController::class, 'update']);
    Route::delete('/listings/{id}', [BusinessListingApiController::class, 'destroy']);

    // Events
    Route::get('/my-events', [EventApiController::class, 'myEvents']);
    Route::post('/events', [EventApiController::class, 'store']);
    Route::put('/events/{id}', [EventApiController::class, 'update']);
    Route::delete('/events/{id}', [EventApiController::class, 'destroy']);

    // Job Listings
    Route::get('/my-jobs', [JobListingApiController::class, 'myJobs']);
    Route::post('/jobs', [JobListingApiController::class, 'store']);
    Route::put('/jobs/{id}', [JobListingApiController::class, 'update']);
    Route::delete('/jobs/{id}', [JobListingApiController::class, 'destroy']);
    Route::post('/jobs/{id}/apply', [JobListingApiController::class, 'apply']);

    // Products
    Route::get('/my-products', [ProductApiController::class, 'myProducts']);
    Route::post('/products', [ProductApiController::class, 'store']);
    Route::put('/products/{id}', [ProductApiController::class, 'update']);
    Route::delete('/products/{id}', [ProductApiController::class, 'destroy']);

    // Coupons
    Route::get('/my-coupons', [CouponApiController::class, 'myCoupons']);
    Route::get('/my-purchased-coupons', [CouponApiController::class, 'myPurchasedCoupons']);
    Route::post('/coupons', [CouponApiController::class, 'store']);
    Route::post('/coupons/{id}/purchase', [CouponApiController::class, 'purchase']);

    // Profile & User Features
    Route::get('/bookmarks', [ProfileApiController::class, 'bookmarks']);
    Route::post('/bookmarks/toggle', [ProfileApiController::class, 'toggleBookmark']);
    Route::get('/follows', [ProfileApiController::class, 'follows']);
    Route::post('/follows/toggle', [ProfileApiController::class, 'toggleFollow']);
    
    // Notifications
    Route::get('/notifications', [ProfileApiController::class, 'notifications']);
    Route::put('/notifications/{id}/read', [ProfileApiController::class, 'markNotificationRead']);
    Route::put('/notifications/read-all', [ProfileApiController::class, 'markAllNotificationsRead']);
    
    // Wallet & Points
    Route::get('/wallet', [ProfileApiController::class, 'wallet']);
    Route::get('/points', [ProfileApiController::class, 'points']);
    Route::post('/points/purchase', [ProfileApiController::class, 'purchasePoints']);
    Route::post('/points/redeem', [ProfileApiController::class, 'redeemPoints']);

    // Reviews
    Route::post('/reviews', [ProfileApiController::class, 'addReview']);
    Route::put('/reviews/{id}', [ProfileApiController::class, 'updateReview']);
    Route::delete('/reviews/{id}', [ProfileApiController::class, 'deleteReview']);

    // Enquiries
    Route::post('/enquiries', [ProfileApiController::class, 'sendEnquiry']);
    Route::get('/my-enquiries', [ProfileApiController::class, 'myEnquiries']);

    // Media uploads
    Route::post('/media/upload', [ProfileApiController::class, 'uploadMedia']);
    Route::delete('/media/{id}', [ProfileApiController::class, 'deleteMedia']);
});
