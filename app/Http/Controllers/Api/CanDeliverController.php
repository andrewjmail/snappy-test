<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSearchRequest;
use App\Http\Resources\StoreResource;
use App\Services\StoreService;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\JsonResponse;

class CanDeliverController extends Controller
{
    use HandlesApiResponses;

    public function __construct(
        protected StoreService $storeService
    ) {}

    public function __invoke(StoreSearchRequest $request): JsonResponse
    {
        try {
            $stores = $this->storeService->findDeliverableStores(
                $request->input('postcode')
            );

            return $this->success(
                StoreResource::collection($stores),
                "{$stores->count()} stores can deliver to your postcode.",
            );
        } catch (\Exception $e) {
            return $this->error('There was a problem finding stores that can deliver to your postcode.', 500);
        }
    }
}
