<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
    return true;
    }


    public function rules(): array
    {
        $productId = $this->route('product');
        
        return [
            'name' => 'sometimes|string|max:255',
            'slug' => "nullable|string|unique:products,slug,{$productId}|max:255",
            'description' => 'nullable|string',
            'base_price' => 'sometimes|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'shelf_id' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'required|string|max:100',
        ];
    }
}