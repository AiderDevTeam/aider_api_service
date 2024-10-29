<?php

namespace App\Actions\Section;

use App\Models\Section;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeleteSectionAction
{
    public function handle(Section $section): JsonResponse
    {
        logger("### DELETING SECTION [$section->external_id] ###");
        try {
            DB::beginTransaction();

            $section->delete();
            manuallySyncModels([$section->refresh()]);

            DB::commit();
            return successfulJsonResponse([]);
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }
}
