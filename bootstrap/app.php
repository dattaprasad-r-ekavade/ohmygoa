<?php

use App\Http\Middleware\CheckUserActive;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\SubscriptionRequired;
use App\Http\Middleware\PointsRequired;
use App\Http\Middleware\RateLimiting;
use App\Http\Middleware\TrackViews;
use App\Http\Middleware\CheckMaintenanceMode;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'verified' => EnsureEmailIsVerified::class,
            'active' => CheckUserActive::class,
            'subscribed' => SubscriptionRequired::class,
            'points' => PointsRequired::class,
            'throttle.custom' => RateLimiting::class,
            'track.views' => TrackViews::class,
        ]);
        
        $middleware->web(append: [
            CheckUserActive::class,
            CheckMaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
