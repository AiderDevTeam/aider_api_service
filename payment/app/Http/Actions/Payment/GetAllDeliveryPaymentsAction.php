<?php

namespace App\Http\Actions\Payment;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllDeliveryPaymentsAction
{

    /**
     * Get Transactions with corresponding payment for user
     *
     * @param Request $authRequest
     * @return JsonResponse
     */

    public function handle(Request $authRequest): JsonResponse
    {
        try {
            $userExternalId = $authRequest->user['externalId'] ?? null;
            logger()->info('Get All Delivery Payments for user:: ' . $userExternalId);

            $user = User::where('external_id', $userExternalId)->first();

            if (!$user) {
                return errorJsonResponse();
            }

            $transactionData = $user->deliveryPayments()
                ->with('payment.transaction')
                ->get()
                ->pluck('payment.transaction')
                ->flatten();

            return successfulJsonResponse(TransactionResource::collection($transactionData));
        } catch (Exception $exception) {
            logger()->info('Get All Transactions for a user error::');
            report($exception);
            logger($exception->getMessage());
            return errorJsonResponse();
        }
    }

}
