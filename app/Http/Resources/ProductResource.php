<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'sku' => $this->sku,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'discount_percentage' => $this->sale_price 
                ? round((($this->price - $this->sale_price) / $this->price) * 100, 2) 
                : 0,
            'stock_quantity' => $this->stock_quantity,
            'stock_status' => $this->stock_status,
            'brand' => $this->brand,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'specifications' => $this->specifications,
            'main_image_url' => $this->main_image ? asset('storage/' . $this->main_image) : null,
            'gallery_urls' => $this->gallery ? collect($this->gallery)->map(fn($img) => asset('storage/' . $img)) : [],
            'average_rating' => $this->average_rating,
            'total_reviews' => $this->reviews_count,
            'views_count' => $this->views_count,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
