<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceExpertResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_name' => $this->business_name,
            'slug' => $this->slug,
            'description' => $this->description,
            'services_offered' => $this->services_offered,
            'years_of_experience' => $this->years_of_experience,
            'certifications' => $this->certifications,
            'skills' => $this->skills,
            'hourly_rate' => $this->hourly_rate,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'website' => $this->website,
            'address' => $this->address,
            'service_areas' => $this->service_areas,
            'availability' => $this->availability,
            'is_available' => $this->is_available,
            'is_verified' => $this->is_verified,
            'profile_image_url' => $this->profile_image ? asset('storage/' . $this->profile_image) : null,
            'portfolio_urls' => $this->portfolio_images ? collect($this->portfolio_images)->map(fn($img) => asset('storage/' . $img)) : [],
            'average_rating' => $this->average_rating,
            'total_reviews' => $this->reviews_count,
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
