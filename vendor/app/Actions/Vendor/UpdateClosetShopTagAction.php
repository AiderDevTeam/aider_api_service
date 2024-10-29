<?php

namespace App\Actions\Vendor;

use App\Http\Requests\UpdateClosetShopTagRequest;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;

class UpdateClosetShopTagAction
{
    public function handle(string $shopTag, UpdateClosetShopTagRequest $request): JsonResponse
    {
        try {
            if (!$vendor = Vendor::where('shop_tag', $shopTag)->first()) {
                return errorJsonResponse([], 'shop not found');
            }

            $vendor->update(['shop_tag' => $request->shopTag]);
            return successfulJsonResponse([]);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
