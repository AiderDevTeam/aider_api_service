<?php

namespace App\Actions\Vendor;

use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;

class ListVendorsAction
{
    public function handle() : JsonResponse
    {
        try {
            return successfulJsonResponse(VendorResource::collection(Vendor::with(['address','categories','products'])->get()));
        } catch (Exception $exception)
        {
            report($exception);
        }
        return errorJsonResponse();
    }
}
