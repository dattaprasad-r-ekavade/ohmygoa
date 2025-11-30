<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(Request $request): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'business_users' => User::where('role', 'business')->count(),
                'free_users' => User::where('role', 'free')->count(),
                'pending_approvals' => 0, // Will be implemented later
            ],
        ]);
    }
}
