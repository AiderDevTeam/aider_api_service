<?php

namespace App\Actions\Section;

use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\JsonResponse;

class UpdateSectionUpdateAction
{
    public function handle(UpdateSectionRequest $request, Section $section): JsonResponse
    {
        logger('### UPDATING SECTION ###');
        logger($request);
        try {
            $section->update(arrayKeyToSnakeCase($request->validated()));

            return successfulJsonResponse(new SectionResource($section->refresh()));
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

    private function setClosetsAsOfficialShops(UpdateSectionRequest $request): void
    {
        if (!$request->has('type') || !$request->has('filter'))
            return;

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
