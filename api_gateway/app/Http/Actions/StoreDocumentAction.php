<?php

namespace App\Http\Actions;

use App\Http\Requests\FirestoreRequest;
use App\Http\Services\FirestoreService;
use Exception;
use Illuminate\Http\JsonResponse;

class StoreDocumentAction
{
    public function handle(FirestoreRequest $request): JsonResponse
    {
        try {
            logger('### SYNCING DATA TO FIRESTORE ###');
            logger($request);
            $response = (new FirestoreService())->createOrUpdateDocument(
                $request->validated('externalId'),
                $request->validated('collection'),
                $request->validated('data')
            );
            logger('### RESPONSE FROM FIRESTORE ###');
            logger($response);

            return successfulJsonResponse($response);

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
