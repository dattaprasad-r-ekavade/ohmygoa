<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PointsRequired
{
    /**
     * Handle an incoming request.
     *
     * @param int $points Required points amount
     */
    public function handle(Request $request, Closure $next, int $points = 0): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has sufficient points
        if ($user->points_balance < $points) {
            return redirect()->back()
                ->with('error', "You need at least {$points} points for this action. Current balance: {$user->points_balance}");
        }

        return $next($request);
    }
}
