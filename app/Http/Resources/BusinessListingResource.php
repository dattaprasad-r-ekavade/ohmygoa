<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessListingResource extends JsonResource
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
            'description' => $this->description,
            'short_description' => $this->short_description,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'whatsapp' => $this->whatsapp,
            'opening_hours' => $this->opening_hours,
            'amenities' => $this->amenities,
            'social_media' => $this->social_media,
            'logo_url' => $this->logo ? asset('storage/' . $this->logo) : null,
            'banner_url' => $this->banner_image ? asset('storage/' . $this->banner_image) : null,
            'images' => $this->images ? collect($this->images)->map(fn($img) => asset('storage/' . $img)) : [],
            'average_rating' => $this->average_rating,
            'total_reviews' => $this->reviews_count,
            'views_count' => $this->views_count,
            'is_verified' => $this->is_verified,
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
