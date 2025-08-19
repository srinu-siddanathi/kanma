<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'image_url' => $this->image_url,
            'category' => new CategoryResource($this->whenLoaded('category')),
            // 'subcategory' => new SubcategoryResource($this->whenLoaded('subcategory')), // Removed as subcategory relationship was removed
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 