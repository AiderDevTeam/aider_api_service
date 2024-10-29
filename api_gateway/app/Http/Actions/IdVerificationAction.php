<?php

namespace App\Http\Actions;

use App\Http\Requests\IdVerificationRequest;
use App\Http\Services\HubtelVerificationService;
use App\Http\Services\MarginsVerificationService;
use App\Models\IdVerification;
use Exception;
use Illuminate\Http\JsonResponse;

class IdVerificationAction
{
    public function handle(IdVerificationRequest $request): JsonResponse
    {
        try {
            logger()->info('### ID VERIFICATION STARTED ###');
            logger($request);

            return $request->has('file') ?
                (new MarginsVerificationService($request))->verifyId() :
                (new HubtelVerificationService($request))->verifyId();

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
