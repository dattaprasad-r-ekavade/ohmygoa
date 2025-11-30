<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobListingResource extends JsonResource
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
            'responsibilities' => $this->responsibilities,
            'requirements' => $this->requirements,
            'company_name' => $this->company_name,
            'company_website' => $this->company_website,
            'company_logo_url' => $this->company_logo ? asset('storage/' . $this->company_logo) : null,
            'job_type' => $this->job_type,
            'experience_level' => $this->experience_level,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'salary_currency' => $this->salary_currency,
            'salary_period' => $this->salary_period,
            'skills' => $this->skills,
            'benefits' => $this->benefits,
            'application_email' => $this->application_email,
            'application_url' => $this->application_url,
            'application_deadline' => $this->application_deadline?->toIso8601String(),
            'vacancies' => $this->vacancies,
            'applications_count' => $this->applications_count,
            'is_remote' => $this->is_remote,
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
