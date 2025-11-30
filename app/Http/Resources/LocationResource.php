<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'is_popular' => $this->is_popular,
            'display_order' => $this->display_order,
            'parent_id' => $this->parent_id,
            'children' => LocationResource::collection($this->whenLoaded('children')),
            'parent' => new LocationResource($this->whenLoaded('parent')),
            'listings_count' => $this->when(isset($this->business_listings_count), $this->business_listings_count),
            'events_count' => $this->when(isset($this->events_count), $this->events_count),
            'jobs_count' => $this->when(isset($this->job_listings_count), $this->job_listings_count),
        ];
    }
}
