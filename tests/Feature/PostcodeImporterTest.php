<?php

namespace Tests\Feature;

// Adjust to your actual class name
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostcodeImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_postcodes_from_csv_into_database_using_queue()
    {
        // 1. Arrange
        Storage::fake('local');

        config(['queue.default' => 'sync']);
        $this->withoutExceptionHandling();
        $csvContent = "pcd,lat,long\n".
                    "AB1 1XY,57.149,-2.130\n".
                    "AB2 2XY,57.150,-2.131\n";

        Storage::disk('local')->put('test_postcodes.csv', $csvContent);
        $path = Storage::disk('local')->path('test_postcodes.csv');

        // 2. Act
        $this->artisan('import:postcodes', ['file' => $path])
            ->assertExitCode(0);

        // 3. Assert

        $this->assertDatabaseCount('postcodes', 2);
        $this->assertDatabaseHas('postcodes', ['postcode' => 'AB11XY']);
    }
}
