<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimiting
{
    /**
     * Handle an incoming request.
     *
     * @param string $limiter The rate limiter name
     */
    public function handle(Request $request, Closure $next, string $limiter = 'api'): Response
    {
        $key = $this->resolveKey($request, $limiter);

        // Define rate limits
        $limits = [
            'api' => [60, 1], // 60 requests per minute
            'search' => [30, 1], // 30 requests per minute
            'upload' => [10, 1], // 10 uploads per minute
            'message' => [20, 1], // 20 messages per minute
        ];

        [$maxAttempts, $decayMinutes] = $limits[$limiter] ?? $limits['api'];

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => "Too many requests. Please try again in {$seconds} seconds.",
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $maxAttempts),
        ]);

        return $response;
    }

    /**
     * Resolve the rate limiter key.
     */
    protected function resolveKey(Request $request, string $limiter): string
    {
        if ($request->user()) {
            return "rate-limit:{$limiter}:user:{$request->user()->id}";
        }

        return "rate-limit:{$limiter}:ip:{$request->ip()}";
    }
}
