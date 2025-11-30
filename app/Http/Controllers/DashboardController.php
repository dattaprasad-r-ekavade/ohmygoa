<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'user' => $user,
            'stats' => [
                'bookmarks' => 0, // Will be implemented later
                'reviews' => 0,
                'applications' => 0,
            ],
        ]);
    }
}
