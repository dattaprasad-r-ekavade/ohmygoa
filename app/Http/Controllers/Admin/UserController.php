<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->role, function($query) use ($request) {
                $query->where('role', $request->role);
            })
            ->when($request->status, function($query) use ($request) {
                if ($request->status === 'active') {
                    $query->whereNull('deleted_at');
                } elseif ($request->status === 'deleted') {
                    $query->onlyTrashed();
                }
            })
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'LIKE', "%{$request->search}%")
                      ->orWhere('email', 'LIKE', "%{$request->search}%")
                      ->orWhere('phone', 'LIKE', "%{$request->search}%");
                });
            })
            ->withCount(['listings', 'jobs', 'products'])
            ->with('subscription')
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::withTrashed()
            ->with(['subscription', 'listings', 'jobs', 'products', 'payments'])
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:free,business,admin',
            'is_verified' => 'boolean'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => $validated['is_verified'] ?? false ? now() : null
        ]);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:free,business,admin',
            'is_verified' => 'boolean'
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if (isset($validated['is_verified'])) {
            $updateData['email_verified_at'] = $validated['is_verified'] ? now() : null;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User restored successfully!');
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot permanently delete your own account!');
        }

        $user->forceDelete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User permanently deleted!');
    }

    public function verifyEmail($id)
    {
        $user = User::findOrFail($id);
        $user->update(['email_verified_at' => now()]);

        return back()->with('success', 'Email verified successfully!');
    }

    public function changeRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|in:free,business,admin'
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('success', 'Role updated successfully!');
    }

    public function suspendUser($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account!');
        }

        $user->update(['is_suspended' => true]);

        return back()->with('success', 'User suspended successfully!');
    }

    public function unsuspendUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_suspended' => false]);

        return back()->with('success', 'User unsuspended successfully!');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:delete,verify,suspend,unsuspend,change_role',
            'role' => 'required_if:action,change_role|in:free,business,admin'
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();

        foreach ($users as $user) {
            if ($user->id === auth()->id()) {
                continue; // Skip current user
            }

            switch ($validated['action']) {
                case 'delete':
                    $user->delete();
                    break;
                case 'verify':
                    $user->update(['email_verified_at' => now()]);
                    break;
                case 'suspend':
                    $user->update(['is_suspended' => true]);
                    break;
                case 'unsuspend':
                    $user->update(['is_suspended' => false]);
                    break;
                case 'change_role':
                    $user->update(['role' => $validated['role']]);
                    break;
            }
        }

        return back()->with('success', 'Bulk action completed successfully!');
    }

    public function statistics()
    {
        $stats = [
            'total' => User::count(),
            'free' => User::where('role', 'free')->count(),
            'business' => User::where('role', 'business')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'suspended' => User::where('is_suspended', true)->count(),
            'deleted' => User::onlyTrashed()->count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'new_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // User growth over last 12 months
        $growth = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Active subscriptions
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();

        return view('admin.users.statistics', compact('stats', 'growth', 'activeSubscriptions'));
    }
}
