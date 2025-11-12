<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'shelf_id' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.size' => 'nullable|string|max:50',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.sku' => 'required|string|unique:product_variants,sku|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du produit est requis',
            'base_price.required' => 'Le prix de base est requis',
            'base_price.min' => 'Le prix doit être positif',
            'image.image' => 'Le fichier doit être une image',
            'image.max' => 'L\'image ne doit pas dépasser 2Mo',
            'variants.*.sku.unique' => 'Ce SKU existe déjà',
        ];
    }
}