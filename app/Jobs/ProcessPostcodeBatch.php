<?php

namespace App\Jobs;

use App\Models\Postcode;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use TarfinLabs\LaravelSpatial\Types\Point;

class ProcessPostcodeBatch implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        protected array $rows,
    ) {}

    public function handle()
    {
        $data = collect($this->rows)->map(function ($row) {
            $lat = $row['lat'] ?? null;
            $lng = $row['lon'] ?? null;
            $pcd = $row['p'] ?? null;

            // Ensure lat/lon are numeric and postcode isn't empty
            if (! is_numeric($lat) || ! is_numeric($lng) || ! $pcd) {
                return null;
            }

            // https://github.com/tarfin-labs/laravel-spatial?tab=readme-ov-file#bulk-operations

            return [
                'postcode' => $pcd,
                'location' => DB::raw((new Point(lat: $lat, lng: $lng))->toGeomFromText()),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->filter()->toArray();

        if (! empty($data)) {
            Postcode::upsert($data, ['postcode'], ['location', 'updated_at']);
        }
    }
}
