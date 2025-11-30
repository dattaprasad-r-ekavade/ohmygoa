<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackViews
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track GET requests
        if ($request->isMethod('GET')) {
            $this->trackView($request);
        }

        return $response;
    }

    /**
     * Track view count.
     */
    protected function trackView(Request $request): void
    {
        // Get model from route
        $route = $request->route();
        
        if (!$route) {
            return;
        }

        // Map route parameters to models
        $trackableModels = [
            'businessListing' => \App\Models\BusinessListing::class,
            'event' => \App\Models\Event::class,
            'jobListing' => \App\Models\JobListing::class,
            'product' => \App\Models\Product::class,
            'coupon' => \App\Models\Coupon::class,
            'classified' => \App\Models\Classified::class,
            'serviceExpert' => \App\Models\ServiceExpert::class,
        ];

        foreach ($trackableModels as $param => $model) {
            $instance = $route->parameter($param);
            
            if ($instance && $instance instanceof $model) {
                // Check if view was already tracked in this session
                $sessionKey = "viewed_{$param}_{$instance->id}";
                
                if (!session()->has($sessionKey)) {
                    $instance->increment('view_count');
                    session()->put($sessionKey, true);
                }
                
                break;
            }
        }
    }
}
