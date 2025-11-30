<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        $maintenanceMode = cache()->remember('maintenance_mode', 300, function () {
            return Setting::where('key', 'maintenance_mode')->value('value') === '1';
        });

        if ($maintenanceMode) {
            // Allow admins to bypass maintenance mode
            if ($request->user() && $request->user()->user_role === 'Admin') {
                return $next($request);
            }

            // Get maintenance message
            $message = cache()->remember('maintenance_message', 300, function () {
                return Setting::where('key', 'maintenance_message')->value('value') 
                    ?? 'We are currently performing scheduled maintenance. Please check back soon.';
            });

            // Return maintenance view
            return response()->view('maintenance', [
                'message' => $message,
            ], 503);
        }

        return $next($request);
    }
}
