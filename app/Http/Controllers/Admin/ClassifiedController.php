<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassifiedController extends Controller
{
    /**
     * Display a listing of classifieds.
     */
    public function index(Request $request)
    {
        $query = Classified::with(['user', 'category', 'location'])
            ->orderBy('bumped_at', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by ad type
        if ($request->filled('ad_type')) {
            $query->byAdType($request->ad_type);
        }

        // Filter by listing type
        if ($request->filled('listing_type')) {
            $query->byListingType($request->listing_type);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $classifieds = $query->paginate(20);
        $adTypes = Classified::getAdTypes();
        $listingTypes = Classified::getListingTypes();

        return view('admin.classifieds.index', compact('classifieds', 'adTypes', 'listingTypes'));
    }

    /**
     * Display the specified classified.
     */
    public function show(Classified $classified)
    {
        $classified->load(['user', 'category', 'location']);
        return view('admin.classifieds.show', compact('classified'));
    }

    /**
     * Update the specified classified in storage.
     */
    public function update(Request $request, Classified $classified)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,sold,expired,inactive',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'listing_type' => 'required|in:free,featured,premium',
            'expires_at' => 'nullable|date|after:today',
            'featured_until' => 'nullable|date|after:today',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_urgent'] = $request->boolean('is_urgent');

        $classified->update($validated);

        return redirect()->back()
            ->with('success', 'Classified ad updated successfully.');
    }

    /**
     * Mark classified as featured.
     */
    public function toggleFeatured(Request $request, Classified $classified)
    {
        $isFeatured = !$classified->is_featured;
        
        $data = ['is_featured' => $isFeatured];
        
        if ($isFeatured && $request->filled('featured_days')) {
            $data['featured_until'] = now()->addDays($request->featured_days);
        } elseif (!$isFeatured) {
            $data['featured_until'] = null;
        }

        $classified->update($data);

        $status = $isFeatured ? 'featured' : 'unfeatured';

        return redirect()->back()
            ->with('success', "Classified ad marked as {$status}.");
    }

    /**
     * Mark classified as urgent.
     */
    public function toggleUrgent(Classified $classified)
    {
        $classified->update(['is_urgent' => !$classified->is_urgent]);

        $status = $classified->is_urgent ? 'urgent' : 'not urgent';

        return redirect()->back()
            ->with('success', "Classified ad marked as {$status}.");
    }

    /**
     * Extend expiry date.
     */
    public function extend(Request $request, Classified $classified)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $currentExpiry = $classified->expires_at ?? now();
        $newExpiry = $currentExpiry->addDays($validated['days']);

        $classified->update([
            'expires_at' => $newExpiry,
            'status' => 'active',
        ]);

        return redirect()->back()
            ->with('success', "Classified ad extended by {$validated['days']} days.");
    }

    /**
     * Upgrade listing type.
     */
    public function upgrade(Request $request, Classified $classified)
    {
        $validated = $request->validate([
            'listing_type' => 'required|in:free,featured,premium',
            'featured_days' => 'nullable|integer|min:1|max:365',
        ]);

        $data = ['listing_type' => $validated['listing_type']];

        if ($validated['listing_type'] === 'featured' || $validated['listing_type'] === 'premium') {
            $data['is_featured'] = true;
            if ($request->filled('featured_days')) {
                $data['featured_until'] = now()->addDays($validated['featured_days']);
            }
        }

        $classified->update($data);

        return redirect()->back()
            ->with('success', 'Listing type upgraded successfully.');
    }

    /**
     * Remove the specified classified from storage.
     */
    public function destroy(Classified $classified)
    {
        // Delete images
        if ($classified->images) {
            foreach ($classified->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $classified->delete();

        return redirect()->route('admin.classifieds.index')
            ->with('success', 'Classified ad deleted successfully.');
    }

    /**
     * Bulk actions on classifieds.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'classified_ids' => 'required|array',
            'classified_ids.*' => 'exists:classifieds,id',
            'action' => 'required|in:activate,deactivate,delete,mark_expired,feature,unfeature',
        ]);

        $count = 0;

        foreach ($validated['classified_ids'] as $id) {
            $classified = Classified::find($id);
            
            if (!$classified) continue;

            switch ($validated['action']) {
                case 'activate':
                    $classified->update(['status' => 'active']);
                    $count++;
                    break;
                case 'deactivate':
                    $classified->update(['status' => 'inactive']);
                    $count++;
                    break;
                case 'mark_expired':
                    $classified->update(['status' => 'expired']);
                    $count++;
                    break;
                case 'feature':
                    $classified->update(['is_featured' => true]);
                    $count++;
                    break;
                case 'unfeature':
                    $classified->update(['is_featured' => false, 'featured_until' => null]);
                    $count++;
                    break;
                case 'delete':
                    if ($classified->images) {
                        foreach ($classified->images as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                    $classified->delete();
                    $count++;
                    break;
            }
        }

        return redirect()->back()
            ->with('success', "{$count} classifieds updated successfully.");
    }

    /**
     * Get statistics dashboard data.
     */
    public function statistics()
    {
        $stats = [
            'total' => Classified::count(),
            'active' => Classified::where('status', 'active')->count(),
            'expired' => Classified::where('status', 'expired')->count(),
            'sold' => Classified::where('status', 'sold')->count(),
            'featured' => Classified::where('is_featured', true)->count(),
            'by_type' => [],
            'by_listing_type' => [],
            'total_views' => Classified::sum('views_count'),
            'total_inquiries' => Classified::sum('total_inquiries'),
        ];

        // Group by ad type
        foreach (Classified::getAdTypes() as $key => $label) {
            $stats['by_type'][$label] = Classified::where('ad_type', $key)->count();
        }

        // Group by listing type
        foreach (Classified::getListingTypes() as $key => $label) {
            $stats['by_listing_type'][$label] = Classified::where('listing_type', $key)->count();
        }

        return view('admin.classifieds.statistics', compact('stats'));
    }
}
