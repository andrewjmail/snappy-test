<?php

namespace Database\Seeders;

use App\Enums\StoreBrand;
use App\Models\Store;
use Illuminate\Database\Seeder;
use TarfinLabs\LaravelSpatial\Types\Point;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $activeDate = now()->subMonth();

        Store::create([
            'name' => 'London Tesco Express',
            'address' => '1 Oxford Street, London, W1D 1BS',
            'brand' => StoreBrand::TESCO,
            'delivery_radius_km' => 10.0,
            'location' => new Point(51.5154, -0.1410, 4326),
            'active_at' => $activeDate,
        ]);

        Store::create([
            'name' => 'Aberdeen COOP',
            'address' => 'Union Street, Aberdeen, AB11 6BB',
            'brand' => \App\Enums\StoreBrand::COOP,
            'delivery_radius_km' => 8.0,
            'location' => new Point(57.1496, -2.0942, 4326),
            'active_at' => $activeDate,
        ]);

        Store::create([
            'name' => 'Piccadilly Sainsbury\'s',
            'address' => 'Piccadilly Gardens, Manchester, M1 1RG',
            'brand' => StoreBrand::SAINSBURYS,
            'delivery_radius_km' => 12.0,
            'location' => new Point(53.4808, -2.2426, 4326),
            'active_at' => $activeDate,
        ]);

        Store::create([
            'name' => 'Future Store (Draft)',
            'address' => 'Upcoming Lane, Bristol',
            'delivery_radius_km' => 5.0,
            'location' => new Point(51.4545, -2.5879, 4326),
            'active_at' => null,
        ]);
    }
}
