<?php

namespace App\Actions\Vendor;

use App\Http\Requests\CheckVendorShopTagRequest;
use App\Http\Services\GetAuthUserService;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;

class CheckVendorShopTagAction
{
    public function handle(CheckVendorShopTagRequest $request): JsonResponse
    {
        logger('### CHECKING SHOP TAG EXISTENCE ###');
        logger($request);

        if (!Vendor::where('shop_tag', $request->shopTag)->exists() &&
            GetAuthUserService::checkUsernameExistence($request->shopTag)
        )
            return successfulJsonResponse(data: [], message: 'Great choice! The shop tag is available, and you can proceed with it.');

        return errorJsonResponse(message: 'Oops! The chosen shop tag already exists. Please choose a different shop tag.', statusCode: 200);
    }
}
