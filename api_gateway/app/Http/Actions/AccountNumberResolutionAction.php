<?php

namespace App\Http\Actions;

use App\Http\Resources\BankAccountResource;
use App\Http\Services\PaystackService;
use App\Models\BankAccount;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function Laravel\Prompts\error;

class AccountNumberResolutionAction
{
    public function handle(Request $request): JsonResponse
    {
        try {

            if (!isset($request->accountNumber) || !isset($request->bankCode))
                return errorJsonResponse(errors: ['account number and bank code are required'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            logger()->info('### ATTEMPTING ACCOUNT NAME RESOLUTION ###');

            $account = BankAccount::getAccountByNumber($request->accountNumber) ?: PaystackService::resolveAccount($request->accountNumber, $request->bankCode);

            if ($account) {
                logger()->info('### ACCOUNT NUMBER RESOLVED ###');
                logger($account);

                return successfulJsonResponse(
                    data: new BankAccountResource($account),
                    message: 'Account number resolved'
                );
            }
            return errorJsonResponse(errors: ['We could not verify your account number. Check and try again.'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);


        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
