<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Services\StoreService;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function __construct(
        protected StoreService $storeService
    ) {}

    public function store(StoreStoreRequest $request): JsonResponse
    {

        $store = $this->storeService->createStore($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Store created successfully.',
            'data' => $store,
        ], 201);

    }
}
