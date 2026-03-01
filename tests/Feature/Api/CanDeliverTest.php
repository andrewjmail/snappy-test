<?php

namespace Tests\Feature\Api;

use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TarfinLabs\LaravelSpatial\Types\Point;
use Tests\TestCase;

class CanDeliverTest extends TestCase
{
    use RefreshDatabase;

    private Postcode $postcode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postcode = Postcode::create([
            'postcode' => 'AB123CD',
            'location' => new Point(50.0, -2.0, 4326),
        ]);
    }

    public function test_can_find_deliverable_stores_with_dynamic_time_estimates()
    {
        $store = Store::create([
            'name' => 'Deliverable Store',
            'delivery_radius_km' => 5,
            'location' => new Point(50.001, -2.001, 4326),
            'active_at' => now()->subDay(),
        ]);

        // 2. Act - Reference the property's postcode string
        $response = $this->getJson(route('api.v1.stores.can-deliver', [
            'postcode' => $this->postcode->postcode,
        ]));

        // 3. Assert
        $response->assertStatus(200)
            ->assertJsonPath('data.0.uuid', $store->uuid)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'uuid',
                        'name',
                        'delivery' => [
                            'radius_km',
                            'distance',
                            'estimated_delivery_minutes',
                        ],
                    ],
                ],
            ]);

        $this->assertIsInt($response->json('data.0.delivery.estimated_delivery_minutes'));
    }

    public function test_returns_404_if_all_nearby_stores_have_insufficient_delivery_radius()
    {
        Store::create([
            'name' => 'Local but Limited Range',
            'delivery_radius_km' => 1.0,
            'location' => new Point(50.1, -2.001, 4326),
            'active_at' => now()->subDay(),
        ]);

        $url = route('api.v1.stores.can-deliver', ['postcode' => 'AB123CD']);

        $response = $this->getJson($url);

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
