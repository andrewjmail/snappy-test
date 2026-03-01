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

    public function findDeliverableStores(string $postcode): Collection
    {
        $maxTimeLimit = config('delivery.max_allowed_minutes'); // Better as a value on the store model

        $stores = $this->findInRadiusOfPostcode($postcode, 15.0);

        return $stores->filter(function ($store) use ($maxTimeLimit) {
            $distanceKm = (float) $store->distance / 1000;
            $maxRadiusKm = (float) $store->delivery_radius_km;

            // 1. Calculate the estimate first to use it for filtering
            $store->estimated_delivery_minutes = $this->calculateEstimate($store->distance);

            // 2. Filter by BOTH physical radius AND time limit
            return $distanceKm <= $maxRadiusKm 
                && $store->estimated_delivery_minutes <= $maxTimeLimit;
                
        });
    }

    public function calculateEstimate(float $distanceMeters): int
    {
        $distanceKm = $distanceMeters / 1000;
        $basedTime = config('delivery.estimates.base'); 
        $minutesPerKm = config('delivery.estimates.minutes_per_km');

        // Add some calculations based on active delivery staff, order volume, traffic, geography etc
        
        return (int) ($basedTime + ($distanceKm * $minutesPerKm));
    }
}
