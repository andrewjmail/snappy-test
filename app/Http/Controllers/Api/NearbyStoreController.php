<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSearchRequest;
use App\Http\Resources\StoreResource;
use App\Services\StoreService;
use App\Traits\HandlesApiResponses;
use Illuminate\Http\JsonResponse;

class NearbyStoreController extends Controller
{
    use HandlesApiResponses;

    public function __construct(
        protected StoreService $storeService
    ) {}

    public function __invoke(StoreSearchRequest $request): JsonResponse
    {
        try {
            $stores = $this->storeService->findInRadiusOfPostcode(
                $request->validated('postcode'),
                (float) $request->input('radius', 10.0)
            );

            return $this->success(
                StoreResource::collection($stores),
                "{$stores->count()} stores found within the specified radius.",
            );
        } catch (\Exception $e) {
            return $this->error(
                'There was a problem retrieving nearby stores.',
                500
            );
        }
    }
}
