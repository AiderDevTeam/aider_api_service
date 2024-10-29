<?php

namespace App\Actions\Section;

use App\Http\Requests\StoreSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\JsonResponse;

class StoreSectionAction
{
    public function handle(StoreSectionRequest $request): JsonResponse
    {
        logger('### CREATING NEW SECTION ###');
        logger($request);
        try {

            return successfulJsonResponse(
                new SectionResource(
                    Section::create(
                        arrayKeyToSnakeCase($request->validated())
                    )
                )
            );
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function setClosetsAsOfficialShops(StoreSectionRequest $request): void
    {
        if (strtolower($request->validated('type')) === Section::CLOSET) {
            logger('### SETTING CLOSET SHOPS AS VERIFIED SHOPS ###');
            $closetVendors = Vendor::whereIn('external_id', array_column($request->validated('filter'), 'externalId'));

            $closetVendors->update(['official' => true]);

            foreach ($closetVendors->get() as $vendor) {
                manuallySyncModels([$vendor]);
            }
        }
    }
}
