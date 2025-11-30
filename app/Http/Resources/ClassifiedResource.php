<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassifiedResource extends JsonResource
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
            'price' => $this->price,
            'is_negotiable' => $this->is_negotiable,
            'condition' => $this->condition,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'address' => $this->address,
            'main_image_url' => $this->main_image ? asset('storage/' . $this->main_image) : null,
            'gallery_urls' => $this->gallery ? collect($this->gallery)->map(fn($img) => asset('storage/' . $img)) : [],
            'views_count' => $this->views_count,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
