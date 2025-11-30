<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    /**
     * Get all events.
     */
    public function index(Request $request)
    {
        $query = Event::where('status', 'approved')
            ->with(['user', 'category']);

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('location_id')) {
            $query->where('city', $request->location_id);
        }

        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('start_date')
            ->paginate($request->input('per_page', 20));

        return EventResource::collection($events);
    }

    /**
     * Get single event.
     */
    public function show($id)
    {
        $event = Event::where('status', 'approved')
            ->with(['user', 'category'])
            ->findOrFail($id);

        $event->increment('view_count');

        return new EventResource($event);
    }

    /**
     * Get user's events.
     */
    public function myEvents(Request $request)
    {
        $events = Event::where('user_id', $request->user()->id)
            ->with(['category'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return EventResource::collection($events);
    }

    /**
     * Create event.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'venue' => 'required|string',
            'city' => 'required|exists:locations,id',
            'organizer_name' => 'required|string',
            'organizer_contact' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        $event = new Event($request->all());
        $event->user_id = $request->user()->id;
        $event->status = 'pending';
        $event->slug = \App\Helpers\SlugHelper::generate($request->title, Event::class);
        
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('events', 'public');
                $images[] = $path;
            }
            $event->images = json_encode($images);
        }

        $event->save();

        return response()->json([
            'message' => 'Event created successfully. Pending admin approval.',
            'event' => new EventResource($event),
        ], 201);
    }

    /**
     * Update event.
     */
    public function update(Request $request, $id)
    {
        $event = Event::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'venue' => 'sometimes|string',
            'city' => 'sometimes|exists:locations,id',
        ]);

        $event->fill($request->all());
        
        if ($request->has('title')) {
            $event->slug = \App\Helpers\SlugHelper::generate($request->title, Event::class, $event->id);
        }

        $event->save();

        return response()->json([
            'message' => 'Event updated successfully',
            'event' => new EventResource($event),
        ]);
    }

    /**
     * Delete event.
     */
    public function destroy(Request $request, $id)
    {
        $event = Event::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully',
        ]);
    }
}
