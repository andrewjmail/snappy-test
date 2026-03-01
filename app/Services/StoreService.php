<?php

namespace App\Services;

use App\Models\Store;
use TarfinLabs\LaravelSpatial\Types\Point;

class StoreService
{
    public function createStore(array $data): Store
    {
        $location = new Point(lat: $data['latitude'], lng: $data['longitude']);

        return Store::create([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'delivery_radius_km' => $data['delivery_radius_km'],
            'location' => $location,
            'active_at' => null, // Store isn't open at creation
        ]);
    }
}
