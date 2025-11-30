<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canCreateListing();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string|min:50',
            'short_description' => 'nullable|string|max:500',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'opening_hours' => 'nullable|array',
            'amenities' => 'nullable|array',
            'social_media' => 'nullable|array',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Business name is required.',
            'category_id.required' => 'Please select a business category.',
            'location_id.required' => 'Please select a location.',
            'description.required' => 'Please provide a business description.',
            'description.min' => 'Description must be at least 50 characters.',
            'phone.required' => 'Contact phone number is required.',
            'logo.image' => 'Logo must be an image file.',
            'logo.max' => 'Logo size should not exceed 2MB.',
            'banner_image.max' => 'Banner image size should not exceed 4MB.',
        ];
    }
}
