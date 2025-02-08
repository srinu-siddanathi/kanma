<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_days' => $this->duration_days,
            'features' => $this->features,
            'is_popular' => $this->is_popular,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 