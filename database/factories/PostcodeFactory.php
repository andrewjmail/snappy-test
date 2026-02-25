<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostcodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'postcode' => 'SW1A1'.strtoupper(fake()->lexify('??')),
            'location' => new \TarfinLabs\LaravelSpatial\Types\Point(
                lat: fake()->latitude(50, 54),
                lng: fake()->longitude(-2, 0)
            ),
        ];
    }
}
