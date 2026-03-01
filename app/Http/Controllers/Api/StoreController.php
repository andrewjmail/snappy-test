<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Services\StoreService;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    use HandlesApiResponses;

    public function __construct(
        protected StoreService $storeService
    ) {}

    public function store(StoreStoreRequest $request): JsonResponse
    {

        try {
            $store = $this->storeService->createStore($request->validated());

            return $this->success(
                $store,
                'Store created successfully.',
                201
            );
        } catch (\Exception $e) {
            return $this->error('There was a problem creating this store. Please try again later.', 500);
        }
    }
}
