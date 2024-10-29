<?php

namespace App\Actions\Section;

use App\Http\Requests\UpdateSectionsPositionRequest;
use App\Models\Section;
use Exception;
use Illuminate\Http\JsonResponse;

class UpdateSectionsPositionAction
{
    public function handle(UpdateSectionsPositionRequest $request): JsonResponse
    {
        try {
            logger('### REORDERING SECTIONS ###');
            logger($request);

            foreach ($request->sections as $requestSection) {
                if ($section = Section::findWithExternalId($requestSection['externalId']))
                    $section->update(['position' => $requestSection['position']]);
            }
            return successfulJsonResponse(data: [], message: 'Sections reordered');

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
