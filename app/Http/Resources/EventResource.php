<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'start_date' => $this->start_date?->toIso8601String(),
            'end_date' => $this->end_date?->toIso8601String(),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'venue' => $this->venue,
            'venue_address' => $this->venue_address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'organizer_name' => $this->organizer_name,
            'organizer_email' => $this->organizer_email,
            'organizer_phone' => $this->organizer_phone,
            'ticket_price' => $this->ticket_price,
            'is_free' => $this->is_free,
            'total_seats' => $this->total_seats,
            'available_seats' => $this->available_seats,
            'booking_url' => $this->booking_url,
            'event_image_url' => $this->event_image ? asset('storage/' . $this->event_image) : null,
            'views_count' => $this->views_count,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
