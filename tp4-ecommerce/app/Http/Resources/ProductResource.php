<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'base_price' => (float) $this->base_price,
            'image' => $this->image,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,

            'is_active' => $this->is_active,
            
            'category' => $this->whenLoaded('category', function() {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
            
            'shelf' => $this->whenLoaded('shelf', function() {
                return [
                    'id' => $this->shelf->id,
                    'description' => $this->shelf->description ?? 'N/A',
                ];
            }),
            
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}