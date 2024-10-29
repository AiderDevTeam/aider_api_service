<?php

namespace App\Http\Actions\User;

use App\Custom\Status;
use App\Events\AccountDeactivationEvent;
use App\Http\Requests\AccountDeactivationRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DeactivateAccountAction
{
    public function handle(AccountDeactivationRequest $request): JsonResponse
    {
        logger('### DEACTIVATING USER ACCOUNT ###');
        logger($request->validated());
        try {
            $user = auth()->user();
            if ($user->deactivate()) {
                event(new AccountDeactivationEvent($user, $request->validated('reason')));
                return successfulJsonResponse([], statusCode: Response::HTTP_NO_CONTENT);
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

}
