<?php

namespace App\Http\Services;

use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class AccountNameResolutionService
{
    public static function resolveAccount(array $request): JsonResponse
    {
        try {
            logger("### RESOLVING ACCOUNT NUMBER ###");
            logger($url = env('PAY_STACK_BASE_URL') .
                '/bank/resolve?account_number=' .
                $request['accountNumber'] .
                '&bank_code=' .
                $request['bankCode']);

            $response = Http::withToken(env('PAY_STACK_SECRET_KEY'))
                ->withHeaders(jsonHttpHeaders())
                ->get($url);

            logger()->info('### NAME RESOLUTION RESPONSE ###');
            logger($response);

            if ($response->successful()) {
                $response = $response->json();
                if (isset($response['status']) && $response['status']) {
                    $account = BankAccount::query()->updateOrCreate([
                        'name' => $response['data']['account_name'],
                        'number' => $response['data']['account_number'],
                        'bank_code' => $request['bankCode']
                    ]);
                    return successfulJsonResponse(
                        data: new BankAccountResource($account),
                        message: 'Account name resolved'
                    );
                }
            }
            return errorJsonResponse(
                message: 'Could not resolve account name',
                statusCode: Response::HTTP_NOT_FOUND
            );

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
