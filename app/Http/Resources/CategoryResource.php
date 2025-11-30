<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'description' => $this->description,
            'icon' => $this->icon,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'display_order' => $this->display_order,
            'parent_id' => $this->parent_id,
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'listings_count' => $this->when(isset($this->business_listings_count), $this->business_listings_count),
            'products_count' => $this->when(isset($this->products_count), $this->products_count),
            'events_count' => $this->when(isset($this->events_count), $this->events_count),
            'jobs_count' => $this->when(isset($this->job_listings_count), $this->job_listings_count),
        ];
    }
}
