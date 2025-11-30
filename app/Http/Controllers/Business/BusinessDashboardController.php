<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessDashboardController extends Controller
{
    /**
     * Display the business dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('business.dashboard', [
            'user' => $user,
            'stats' => [
                'listings' => $user->businessListings()->count(),
                'total_views' => 0,
                'reviews' => 0,
                'enquiries' => 0,
            ],
        ]);
    }
}
