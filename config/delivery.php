<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Delivery Settings
    |--------------------------------------------------------------------------
    |
    | base: The minimum time for any delivery.
    | minutes_per_km: How many minutes to add for every kilometer of distance.
    |
    */
    'estimates' => [
        'base' => 15,
        'minutes_per_km' => 5,
    ],
    'max_allowed_minutes' => 60,
];
