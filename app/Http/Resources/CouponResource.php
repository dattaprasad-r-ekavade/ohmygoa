<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'code' => $this->code,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'minimum_purchase' => $this->minimum_purchase,
            'maximum_discount' => $this->maximum_discount,
            'usage_limit' => $this->usage_limit,
            'usage_per_user' => $this->usage_per_user,
            'redemption_count' => $this->redemption_count,
            'valid_from' => $this->valid_from?->toIso8601String(),
            'valid_until' => $this->valid_until?->toIso8601String(),
            'terms_conditions' => $this->terms_conditions,
            'is_featured' => $this->is_featured,
            'is_valid' => $this->isValid(),
            'views_count' => $this->views_count,
            'status' => $this->status,
            'business' => new BusinessListingResource($this->whenLoaded('business')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
