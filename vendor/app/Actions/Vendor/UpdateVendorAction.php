<?php

namespace App\Actions\Vendor;

use App\Http\Requests\UpdateVendorRequest;
use App\Jobs\FileUploadJob;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateVendorAction
{
    public function handle(Vendor $vendor, UpdateVendorRequest $vendorRequest): JsonResponse
    {
        try {
            $vendor->updateQuietly(arrayKeyToSnakeCase($vendorRequest->validated()));

            if ($vendorRequest->has('originName') || $vendorRequest->has('longitude') || $vendorRequest->has('latitude')) {
                $vendor->address()->update([
                    'city' => $vendorRequest->city,
                    'state' => $vendorRequest->state,
                    'longitude' => $vendorRequest->longitude,
                    'latitude' => $vendorRequest->latitude,
                    'origin_name' => $vendorRequest->originName ?? '',
                    'location_response' => $vendorRequest->locationResponse
                ]);
            }

            if ($vendorRequest->filled('shopLogo')) {
                logger('### SHOP LOGO UPLOAD JOB DISPATCHED ###');
                FileUploadJob::dispatch($vendorRequest->validated(), $vendor);
            }
            manuallySyncModels([$vendor->refresh()]);

            return successfulJsonResponse(data: [], statusCode: 204);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
