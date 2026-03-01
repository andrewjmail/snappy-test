<?php

use App\Http\Controllers\Api\NearbyStoreController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::prefix('stores')->name('stores.')->middleware(ThrottleRequests::class.':60,1')->group(function () {
        Route::post('/', [StoreController::class, 'store'])
            ->name('store');

        Route::get('/nearby', NearbyStoreController::class)
            ->name('nearby');
    });
});
