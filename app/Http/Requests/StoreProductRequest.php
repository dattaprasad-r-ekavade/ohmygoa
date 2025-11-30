<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canCreateProduct();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|min:50',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'nullable|string|unique:products,sku|max:100',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'brand' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'specifications' => 'nullable|array',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'gallery' => 'nullable|array|max:8',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'category_id.required' => 'Please select a product category.',
            'description.required' => 'Please provide a product description.',
            'description.min' => 'Description must be at least 50 characters.',
            'price.required' => 'Product price is required.',
            'price.min' => 'Price must be a positive number.',
            'sale_price.lt' => 'Sale price must be less than regular price.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'sku.unique' => 'This SKU is already in use.',
            'gallery.max' => 'You can upload a maximum of 8 gallery images.',
            'main_image.max' => 'Main image size should not exceed 4MB.',
            'gallery.*.max' => 'Gallery images should not exceed 2MB each.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Auto-generate SKU if not provided
        if (!$this->sku) {
            $this->merge([
                'sku' => 'PRD-' . strtoupper(uniqid()),
            ]);
        }
    }
}
