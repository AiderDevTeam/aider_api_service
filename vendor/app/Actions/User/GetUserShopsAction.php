<?php

namespace App\Actions\User;

use App\Http\Resources\VendorResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetUserShopsAction
{
    public function handle(User $user): JsonResponse
    {
        try {
            logger("### LOADING SHOPS FOR [$user->external_id] ###");

            $vendors = $user->vendor()->whereNot('shop_tag', $user->other_details['username'] ?? '')->get();
            $vendors->map(fn($vendor) => $vendor->products = $vendor->eligibleProducts->take(3));
            return successfulJsonResponse(VendorResource::collection($vendors->load('products')));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
