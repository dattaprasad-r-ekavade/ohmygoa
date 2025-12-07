<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\ServiceBooking;
use App\Models\ServiceExpert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ServiceExpertController extends Controller
{
    /**
     * Display a listing of service experts.
     */
    public function index(Request $request): View
    {
        $query = ServiceExpert::with(['category', 'location', 'user'])
            ->active()
            ->verified();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter (service type)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        // Experience level filter
        if ($request->filled('experience')) {
            $query->where('years_of_experience', '>=', $request->experience);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('hourly_rate', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('hourly_rate', '<=', $request->max_price);
        }

        // Available now filter
        if ($request->filled('available')) {
            $query->available();
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->featured();
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        if ($sortBy === 'rating') {
            $query->orderBy('average_rating', $sortOrder);
        } elseif ($sortBy === 'price') {
            $query->orderBy('hourly_rate', $sortOrder);
        } elseif ($sortBy === 'popular') {
            $query->orderBy('views_count', $sortOrder);
        } elseif ($sortBy === 'experience') {
            $query->orderBy('years_of_experience', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $experts = $query->paginate(12);

        return view('service-experts.index', [
            'experts' => $experts,
            'categories' => Category::active()->ofType('service')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Display the specified service expert.
     */
    public function show(string $slug): View
    {
        $expert = ServiceExpert::with(['category', 'location', 'user'])
            ->where('slug', $slug)
            ->active()
            ->verified()
            ->firstOrFail();

        $expert->incrementViewCount();

        // Load reviews with pagination
        $reviews = $expert->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        // Similar experts
        $similarExperts = ServiceExpert::where('category_id', $expert->category_id)
            ->where('id', '!=', $expert->id)
            ->active()
            ->verified()
            ->limit(4)
            ->get();

        return view('service-experts.show', [
            'expert' => $expert,
            'reviews' => $reviews,
            'similarExperts' => $similarExperts,
            'isBookmarked' => auth()->check() ? $expert->isBookmarkedBy(auth()->id()) : false,
        ]);
    }

    /**
     * Show the form for creating a new service expert profile.
     */
    public function create(): View
    {
        Gate::authorize('create-service-expert');

        return view('service-experts.create', [
            'categories' => Category::active()->ofType('service')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Store a newly created service expert profile.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-service-expert');

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'services_offered' => 'required|string',
            'service_areas' => 'nullable|string',
            'years_of_experience' => 'required|integer|min:0',
            'certifications' => 'nullable|string',
            'skills' => 'nullable|string',
            'languages_spoken' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'minimum_charge' => 'nullable|numeric|min:0',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:255',
            'availability' => 'nullable|string|max:255',
            'insurance_details' => 'nullable|string',
            'response_time_hours' => 'nullable|integer|min:1',
            'completion_rate' => 'nullable|numeric|between:0,100',
            'offers_emergency_service' => 'nullable|boolean',
            'working_hours' => 'nullable|array',
            'working_hours.*' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
            'portfolio.*' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['is_available'] = true;
        $validated['services_offered'] = $this->prepareListInput($request->input('services_offered'));
        $validated['service_areas'] = $this->prepareListInput($request->input('service_areas'));
        $validated['certifications'] = $this->prepareListInput($request->input('certifications'));
        $validated['skills'] = $this->prepareListInput($request->input('skills'));
        $validated['languages_spoken'] = $this->prepareListInput($request->input('languages_spoken'));
        $validated['working_hours'] = $this->prepareWorkingHours($request->input('working_hours', []));
        $validated['offers_emergency_service'] = $request->boolean('offers_emergency_service');

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('service-experts', 'public');
        }

        if ($request->hasFile('portfolio')) {
            $portfolio = [];
            foreach ($request->file('portfolio') as $image) {
                $portfolio[] = $image->store('service-experts/portfolio', 'public');
            }
            $validated['portfolio_images'] = $portfolio;
        }

        $expert = ServiceExpert::create($validated);

        return redirect()->route('business.service-experts.index')
            ->with('success', 'Service expert profile created successfully and is pending verification.');
    }

    /**
     * Show the form for editing the service expert profile.
     */
    public function edit(ServiceExpert $serviceExpert): View
    {
        Gate::authorize('update', $serviceExpert);

        return view('service-experts.edit', [
            'expert' => $serviceExpert,
            'categories' => Category::active()->ofType('service')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Update the specified service expert profile.
     */
    public function update(Request $request, ServiceExpert $serviceExpert)
    {
        Gate::authorize('update', $serviceExpert);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'services_offered' => 'required|string',
            'service_areas' => 'nullable|string',
            'years_of_experience' => 'required|integer|min:0',
            'certifications' => 'nullable|string',
            'skills' => 'nullable|string',
            'languages_spoken' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'minimum_charge' => 'nullable|numeric|min:0',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'website' => 'nullable|url',
            'address' => 'nullable|string|max:255',
            'availability' => 'nullable|string|max:255',
            'insurance_details' => 'nullable|string',
            'response_time_hours' => 'nullable|integer|min:1',
            'completion_rate' => 'nullable|numeric|between:0,100',
            'offers_emergency_service' => 'nullable|boolean',
            'working_hours' => 'nullable|array',
            'working_hours.*' => 'nullable|string|max:255',
            'is_available' => 'boolean',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $validated['services_offered'] = $this->prepareListInput($request->input('services_offered'));
        $validated['service_areas'] = $this->prepareListInput($request->input('service_areas'));
        $validated['certifications'] = $this->prepareListInput($request->input('certifications'));
        $validated['skills'] = $this->prepareListInput($request->input('skills'));
        $validated['languages_spoken'] = $this->prepareListInput($request->input('languages_spoken'));
        $validated['working_hours'] = $this->prepareWorkingHours($request->input('working_hours', []));
        $validated['offers_emergency_service'] = $request->boolean('offers_emergency_service');

        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('service-experts', 'public');
        }

        $serviceExpert->update($validated);

        return redirect()->route('service-experts.show', ['slug' => $serviceExpert->slug])
            ->with('success', 'Service expert profile updated successfully.');
    }

    /**
     * Remove the specified service expert profile.
     */
    public function destroy(ServiceExpert $serviceExpert)
    {
        Gate::authorize('delete', $serviceExpert);

        $serviceExpert->delete();

        return redirect()->route('business.service-experts.index')
            ->with('success', 'Service expert profile deleted successfully.');
    }

    /**
     * Show the booking form for a service expert.
     */
    public function book(ServiceExpert $serviceExpert): View
    {
        if (!$serviceExpert->is_available) {
            abort(403, 'This service expert is not currently available for bookings.');
        }

        return view('service-experts.book', ['expert' => $serviceExpert]);
    }

    /**
     * Store a new service booking.
     */
    public function storeBooking(Request $request, ServiceExpert $serviceExpert)
    {
        if (!$serviceExpert->is_available) {
            return back()->withErrors(['error' => 'This service expert is not currently available for bookings.']);
        }

        if (!$request->filled('preferred_time')) {
            $request->merge(['preferred_time' => null]);
        }

        $validated = $request->validate([
            'service_description' => 'required|string',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'nullable|date_format:H:i',
            'location' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'special_instructions' => 'nullable|string',
            'estimated_hours' => 'nullable|numeric|min:1',
        ]);

        $estimatedHours = $validated['estimated_hours'] ?? null;
        unset($validated['estimated_hours']);

        $validated['service_expert_id'] = $serviceExpert->id;
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        // Calculate estimated price
        if ($serviceExpert->hourly_rate && $estimatedHours) {
            $validated['quoted_price'] = $serviceExpert->hourly_rate * $estimatedHours;
        }

        $booking = ServiceBooking::create($validated);

        return redirect()->route('service-experts.show', ['slug' => $serviceExpert->slug])
            ->with('success', 'Booking request submitted successfully. The service expert will contact you soon.');
    }

    /**
     * Toggle availability status.
     */
    public function toggleAvailability(ServiceExpert $serviceExpert)
    {
        Gate::authorize('update', $serviceExpert);

        $serviceExpert->update([
            'is_available' => !$serviceExpert->is_available,
        ]);

        return response()->json([
            'available' => $serviceExpert->is_available,
            'message' => $serviceExpert->is_available 
                ? 'You are now available for bookings' 
                : 'You are now unavailable for bookings',
        ]);
    }

    /**
     * Toggle bookmark for service expert.
     */
    public function toggleBookmark(ServiceExpert $serviceExpert)
    {
        $isBookmarked = $serviceExpert->toggleBookmark(auth()->id());

        return response()->json([
            'bookmarked' => $isBookmarked,
            'message' => $isBookmarked ? 'Added to bookmarks' : 'Removed from bookmarks',
        ]);
    }

    /**
     * Normalize comma or newline separated strings into arrays.
     */
    private function prepareListInput(null|string|array $value): array
    {
        if (is_array($value)) {
            $items = $value;
        } else {
            $items = preg_split('/[\r\n,]+/', (string) $value);
        }

        return array_values(array_filter(array_map(static function ($item) {
            return trim((string) $item);
        }, $items ?? []), static function ($item) {
            return $item !== '';
        }));
    }

    /**
     * Remove empty working-hour entries while preserving day keys.
     */
    private function prepareWorkingHours(?array $hours): array
    {
        if (!$hours) {
            return [];
        }

        $normalized = [];

        foreach ($hours as $day => $value) {
            $value = trim((string) $value);
            if ($value !== '') {
                $normalized[$day] = $value;
            }
        }

        return $normalized;
    }
}
