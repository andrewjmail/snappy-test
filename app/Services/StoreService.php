<?php

namespace App\Services;

use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Support\Collection;
use TarfinLabs\LaravelSpatial\Types\Point;

class StoreService
{
    public function createStore(array $data): Store
    {
        $location = new Point(lat: $data['latitude'], lng: $data['longitude']);

        return Store::create([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'brand' => $data['brand'] ?? null,
            'delivery_radius_km' => $data['delivery_radius_km'],
            'location' => $location,
            'active_at' => null, // Store isn't open at creation
        ]);
    }

    public function findInRadiusOfPostcode(string $postcode, float $radiusInKm): Collection
    {
        $postcode = Postcode::where('postcode', strtoupper(str_replace(' ', '', $postcode)))
            ->first();

        if (! $postcode) {
            // Fall back to a service to find missing postcodes?
            return collect();
        }

        // https://github.com/tarfin-labs/laravel-spatial?tab=readme-ov-file#4--scopes
        return Store::query()
            ->active()
            ->withinDistanceTo('location', $postcode->location, $radiusInKm * 1000) // distance is in meters
            ->selectDistanceTo('location', $postcode->location) // Adds a 'distance' attribute (in meters)
            ->orderByDistanceTo('location', $postcode->location) // Sorts by nearest
            ->get();
    }
}
