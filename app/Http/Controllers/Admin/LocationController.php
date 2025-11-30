<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    /**
     * Display a listing of locations.
     */
    public function index(Request $request): View
    {
        $query = Location::withCount(['businessListings', 'events', 'jobListings']);

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Parent/child filter
        if ($request->has('parent')) {
            if ($request->parent === 'main') {
                $query->whereNull('parent_id');
            } else {
                $query->whereNotNull('parent_id');
            }
        }

        // Status filter
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Popular filter
        if ($request->filled('popular')) {
            $query->popular();
        }

        $locations = $query->paginate(20);

        return view('admin.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create(): View
    {
        $parentLocations = Location::whereNull('parent_id')
            ->active()
            ->orderBy('name')
            ->get();

        return view('admin.locations.create', compact('parentLocations'));
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:country,state,city,area',
            'parent_id' => 'nullable|exists:locations,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        Location::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location): View
    {
        $location->load([
            'children',
            'businessListings' => fn($q) => $q->limit(10),
            'events' => fn($q) => $q->limit(10),
            'jobListings' => fn($q) => $q->limit(10),
        ]);

        return view('admin.locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location): View
    {
        $parentLocations = Location::whereNull('parent_id')
            ->where('id', '!=', $location->id)
            ->active()
            ->orderBy('name')
            ->get();

        return view('admin.locations.edit', compact('location', 'parentLocations'));
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:country,state,city,area',
            'parent_id' => 'nullable|exists:locations,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        // Prevent circular parent relationship
        if ($request->parent_id == $location->id) {
            return back()->withErrors(['parent_id' => 'A location cannot be its own parent.']);
        }

        $location->update($validated);

        return redirect()->route('admin.locations.show', $location)
            ->with('success', 'Location updated successfully.');
    }

    /**
     * Remove the specified location.
     */
    public function destroy(Location $location)
    {
        // Check if location has content
        $hasContent = $location->businessListings()->exists()
            || $location->events()->exists()
            || $location->jobListings()->exists()
            || $location->children()->exists();

        if ($hasContent) {
            return back()->withErrors([
                'error' => 'Cannot delete location with existing content or sub-locations.'
            ]);
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }

    /**
     * Toggle location status.
     */
    public function toggleStatus(Location $location)
    {
        $location->update(['is_active' => !$location->is_active]);

        $status = $location->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Location {$status} successfully.");
    }

    /**
     * Toggle popular status.
     */
    public function togglePopular(Location $location)
    {
        $location->update(['is_popular' => !$location->is_popular]);

        $status = $location->is_popular ? 'marked as popular' : 'unmarked as popular';

        return redirect()->back()
            ->with('success', "Location {$status} successfully.");
    }

    /**
     * Reorder locations.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'locations' => 'required|array',
            'locations.*.id' => 'required|exists:locations,id',
            'locations.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->locations as $locationData) {
            Location::where('id', $locationData['id'])
                ->update(['display_order' => $locationData['order']]);
        }

        return response()->json(['message' => 'Locations reordered successfully.']);
    }

    /**
     * Bulk import locations.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
            'type' => 'required|in:country,state,city,area',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $imported = 0;
        $errors = [];

        // Skip header row
        fgetcsv($handle);

        while (($data = fgetcsv($handle)) !== false) {
            try {
                if (empty($data[0])) {
                    continue;
                }

                Location::create([
                    'name' => $data[0],
                    'type' => $request->type,
                    'parent_id' => !empty($data[1]) ? $data[1] : null,
                    'latitude' => !empty($data[2]) ? $data[2] : null,
                    'longitude' => !empty($data[3]) ? $data[3] : null,
                    'is_active' => true,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$imported}: {$e->getMessage()}";
            }
        }

        fclose($handle);

        $message = "{$imported} locations imported successfully.";
        if (!empty($errors)) {
            $message .= ' ' . count($errors) . ' errors encountered.';
        }

        return redirect()->route('admin.locations.index')
            ->with('success', $message)
            ->with('errors', $errors);
    }
}
