<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TarfinLabs\LaravelSpatial\Casts\LocationCast;
use TarfinLabs\LaravelSpatial\Traits\HasSpatial;

class Postcode extends Model
{
    use HasFactory, HasSpatial;

    protected $guarded = [];

    protected $casts = [
        'location' => LocationCast::class,
    ];
}
