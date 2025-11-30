<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionRequired
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has active subscription
        if (!$user->subscription_ends_at || $user->subscription_ends_at->isPast()) {
            return redirect()->route('dashboard')
                ->with('error', 'This feature requires an active premium subscription.');
        }

        return $next($request);
    }
}
