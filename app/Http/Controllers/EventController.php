<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request): View
    {
        $query = Event::with(['category', 'location', 'user'])
            ->active();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->upcoming();
        }

        // Free events filter
        if ($request->filled('free')) {
            $query->where('is_free', true);
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->featured();
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->where('start_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('start_date', '<=', $request->to_date);
        }

        // Sorting
        $sortBy = $request->get('sort', 'start_date');
        $query->orderBy($sortBy, 'asc');

        $events = $query->paginate(12);

        return view('events.index', [
            'events' => $events,
            'categories' => Category::active()->ofType('event')->get(),
            'locations' => Location::active()->popular()->get(),
        ]);
    }

    /**
     * Display the specified event.
     */
    public function show(string $slug): View
    {
        $event = Event::with(['category', 'location', 'user'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $event->incrementViewCount();

        // Similar events
        $similarEvents = Event::where('category_id', $event->category_id)
            ->where('id', '!=', $event->id)
            ->active()
            ->upcoming()
            ->limit(4)
            ->get();

        return view('events.show', [
            'event' => $event,
            'similarEvents' => $similarEvents,
            'isBookmarked' => auth()->check() ? $event->isBookmarkedBy(auth()->id()) : false,
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        Gate::authorize('create-event');

        return view('events.create', [
            'categories' => Category::active()->ofType('event')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-event');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'venue' => 'required|string',
            'address' => 'required|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'organizer_name' => 'nullable|string',
            'organizer_email' => 'nullable|email',
            'organizer_phone' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'required|boolean',
            'total_seats' => 'nullable|integer|min:1',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'upcoming';
        $validated['available_seats'] = $validated['total_seats'] ?? null;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $event = Event::create($validated);

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Event created successfully.');
    }

    /**
     * Show the form for editing the event.
     */
    public function edit(Event $event): View
    {
        Gate::authorize('update', $event);

        return view('events.edit', [
            'event' => $event,
            'categories' => Category::active()->ofType('event')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        Gate::authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'venue' => 'required|string',
            'address' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'organizer_name' => 'nullable|string',
            'organizer_email' => 'nullable|email',
            'organizer_phone' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'required|boolean',
            'total_seats' => 'nullable|integer|min:1',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->route('events.show', $event->slug)
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        Gate::authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Toggle bookmark for event.
     */
    public function toggleBookmark(Event $event)
    {
        $isBookmarked = $event->toggleBookmark(auth()->id());

        return response()->json([
            'bookmarked' => $isBookmarked,
            'message' => $isBookmarked ? 'Added to bookmarks' : 'Removed from bookmarks',
        ]);
    }
}
