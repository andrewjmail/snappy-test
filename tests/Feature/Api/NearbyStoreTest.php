<?php

namespace Tests\Feature\Api;

use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TarfinLabs\LaravelSpatial\Types\Point;
use Tests\TestCase;

class NearbyStoreTest extends TestCase
{
    use RefreshDatabase;

    private $postcode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postcode = Postcode::create([
            'postcode' => 'AB123CD',
            'location' => new Point(50.0, -2.0, 4326),
        ]);
    }

    public function test_it_returns_stores_within_delivery_range()
    {
        $nearbyStore = Store::create([
            'name' => 'Nearby Shop',
            'delivery_radius_km' => 5,
            'location' => new Point(50.03, -2.001, 4326),
            'active_at' => now()->subDay(),
        ]);

        $farStore = Store::create([
            'name' => 'Far Shop',
            'delivery_radius_km' => 5,
            'location' => new Point(52.2, -2.0, 4326),
            'active_at' => now()->subDay(),
        ]);

        // 3. Act
        $response = $this->getJson(route('api.v1.stores.nearby', [
            'postcode' => $this->postcode->postcode,
        ]));

        // 4. Assert
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.uuid', $nearbyStore->uuid)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'uuid',
                        'name',
                        'delivery' => ['radius_km', 'distance'],
                    ],
                ],
            ]);
    }

    public function test_it_returns_404_when_no_stores_deliver_to_postcode()
    {
        Store::create([
            'name' => 'Restricted Shop',
            'delivery_radius_km' => 1,
            'location' => new Point(52.05, -2.0, 4326),
            'active_at' => now()->subDay(),
        ]);

        $response = $this->getJson(route('api.v1.stores.nearby', [
            'postcode' => $this->postcode->postcode,
        ]));

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJsonFragment(['message' => '0 stores found within the specified radius.']);
    }

    public function test_it_validates_the_postcode_format()
    {
        $response = $this->getJson(route('api.v1.stores.nearby', [
            'postcode' => 'A',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['postcode']);
    }

    public function test_it_validates_postcode_regex_format()
    {
        $response = $this->getJson(route('api.v1.stores.nearby', [
            'postcode' => '12345',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['postcode'])
            ->assertJsonFragment([
                'errors' => [
                    'postcode' => ['The postcode must be a valid UK postcode.'],
                ],
            ]);
    }
}
