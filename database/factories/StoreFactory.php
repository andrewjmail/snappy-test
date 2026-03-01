<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TarfinLabs\LaravelSpatial\Types\Point;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'delivery_radius_km' => $this->faker->randomFloat(1, 1, 10),
            'location' => new Point(
                $this->faker->latitude(51.3, 51.7),
                $this->faker->longitude(-0.3, 0.2),
                4326 // Always include the SRID for spatial consistency
            ),
            'active_at' => null, // Default to Draft state per our logic
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'active_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    public function opening(): static
    {
        return $this->state(fn () => [
            'active_at' => now()->addDays(rand(1, 7)),
        ]);
    }
}
