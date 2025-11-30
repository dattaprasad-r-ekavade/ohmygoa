<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceExpertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canCreateServiceExpert();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string|min:100',
            'services_offered' => 'required|string',
            'years_of_experience' => 'required|integer|min:0|max:100',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:255',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'service_areas' => 'nullable|array',
            'service_areas.*' => 'string|max:255',
            'availability' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'portfolio' => 'nullable|array|max:10',
            'portfolio.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'business_name.required' => 'Business name is required.',
            'category_id.required' => 'Please select a service category.',
            'location_id.required' => 'Please select a location.',
            'description.required' => 'Please provide a description.',
            'description.min' => 'Description must be at least 100 characters.',
            'services_offered.required' => 'Please describe the services you offer.',
            'years_of_experience.required' => 'Years of experience is required.',
            'years_of_experience.max' => 'Years of experience cannot exceed 100.',
            'contact_phone.required' => 'Contact phone number is required.',
            'profile_image.max' => 'Profile image size should not exceed 2MB.',
            'portfolio.max' => 'You can upload a maximum of 10 portfolio images.',
            'portfolio.*.max' => 'Portfolio images should not exceed 3MB each.',
        ];
    }
}
