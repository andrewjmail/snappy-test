<?php

namespace App\Models;

use App\Enums\StoreBrand;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected $casts = [
        'active_at' => 'datetime',
        'location' => LocationCast::class,
        'brand' => StoreBrand::class,
    ];

    public function scopeActive($query)
    {

        return $query->whereNotNull('active_at')
            ->where('active_at', '<=', now());
    }
}
