<?php

namespace Tests\Unit;

use App\Services\PostcodeImportService;
use PHPUnit\Framework\TestCase;

class PostcodeMappingTest extends TestCase
{
    public function test_it_detects_various_header_formats()
    {
        $service = new PostcodeImportService;

        $headers = ['pcd', 'lat', 'long', 'extra_data'];

        // We now pass the specific strings we are looking for directly
        $mapping = $service->determineColumnHeaders($headers, [
            'p' => 'pcd',
            'lat' => 'lat',
            'lon' => 'long',
        ]);

        $this->assertEquals(0, $mapping['p']);
        $this->assertEquals(1, $mapping['lat']);
        $this->assertEquals(2, $mapping['lon']);
    }

    public function test_it_maps_custom_target_names()
    {
        $service = new PostcodeImportService;
        $headers = ['ColA', 'ColB', 'ColC'];

        // Simulating the user passing custom column names via CLI
        $mapping = $service->determineColumnHeaders($headers, [
            'p' => 'ColC',
            'lat' => 'ColA',
            'lon' => 'ColB',
        ]);

        $this->assertEquals(2, $mapping['p']);
        $this->assertEquals(0, $mapping['lat']);
        $this->assertEquals(1, $mapping['lon']);
    }
}
