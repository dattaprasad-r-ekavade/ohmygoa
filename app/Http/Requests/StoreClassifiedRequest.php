<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClassifiedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string|min:30',
            'price' => 'required|numeric|min:0',
            'is_negotiable' => 'boolean',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'address' => 'nullable|string',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'gallery' => 'nullable|array|max:6',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Ad title is required.',
            'category_id.required' => 'Please select a category.',
            'location_id.required' => 'Please select a location.',
            'description.required' => 'Please provide a description.',
            'description.min' => 'Description must be at least 30 characters.',
            'price.required' => 'Price is required.',
            'price.min' => 'Price must be a positive number.',
            'condition.required' => 'Please select item condition.',
            'contact_name.required' => 'Contact name is required.',
            'contact_phone.required' => 'Contact phone number is required.',
            'main_image.max' => 'Main image size should not exceed 4MB.',
            'gallery.max' => 'You can upload a maximum of 6 gallery images.',
            'gallery.*.max' => 'Gallery images should not exceed 2MB each.',
        ];
    }
}
