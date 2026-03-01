<?php

namespace Tests\Feature\Api;

use App\Enums\StoreBrand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_store_with_valid_data()
    {
        $response = $this->postJson(route('api.v1.stores.store'), [
            'name' => 'Store',
            'address' => '123 High Street, London',
            'brand' => StoreBrand::TESCO->value,
            'delivery_radius_km' => 5.5,
            'latitude' => 51.5074,
            'longitude' => -0.1278,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Store created successfully.',
            ])
            ->assertJsonPath('data.name', 'Store');

        $this->assertDatabaseHas('stores', [
            'name' => 'Store',
            'delivery_radius_km' => 5.5,
            'brand' => StoreBrand::TESCO->value,
            'active_at' => null,
        ]);
    }

    public function test_store_creation_validation_errors()
    {
        $response = $this->postJson(route('api.v1.stores.store'), [
            'name' => '',
            'delivery_radius_km' => 'two',
            'brand' => 'not-a-brand',
            'latitude' => 100,
            'longitude' => 200,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Validation failed',
            ])
            ->assertJsonStructure([
                'errors' => [
                    'name',
                    'brand',
                    'delivery_radius_km',
                    'latitude',
                    'longitude',
                ],
            ]);
    }

    public function test_duplicate_store_name_and_location()
    {
        $this->postJson(route('api.v1.stores.store'), [
            'name' => 'Store',
            'address' => '123 High Street, London',
            'delivery_radius_km' => 5.5,
            'latitude' => 51.5074,
            'longitude' => -0.1278,
        ]);

        $response = $this->postJson(route('api.v1.stores.store'), [
            'name' => 'Store',
            'address' => '123 High Street, London',
            'delivery_radius_km' => 5.5,
            'latitude' => 51.5074,
            'longitude' => -0.1278,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name' => 'A store with the same name and address already exists.',
            ]);
    }
}
