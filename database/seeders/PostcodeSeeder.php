<?php

namespace Database\Seeders;

use App\Models\Postcode;
use Illuminate\Database\Seeder;
use TarfinLabs\LaravelSpatial\Types\Point;

class PostcodeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'postcode' => 'W1D1BS', // London Oxford St
                'lat' => 51.5154,
                'lng' => -0.1410,
            ],
            [
                'postcode' => 'AB116BB', // Aberdeen Union St
                'lat' => 57.1496,
                'lng' => -2.0942,
            ],
            [
                'postcode' => 'M11RG', // Manchester Piccadilly
                'lat' => 53.4808,
                'lng' => -2.2426,
            ],
        ];

        foreach ($data as $item) {
            Postcode::create([
                'postcode' => $item['postcode'],
                'location' => new Point($item['lat'], $item['lng'], 4326),
            ]);
        }
    }
}
