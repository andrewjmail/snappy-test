<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'brand' => [
                'id' => $this->brand?->value ?? 'independent',
                'name' => $this->brand ? $this->brand->label() : 'Independent Store',
            ],
            'address' => $this->address,
            'is_active' => $this->isActive,
            'location' => [
                'lat' => $this->location->getLat(),
                'lng' => $this->location->getLng(),
            ],
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}