<?php

namespace App\Http\Actions\Payment;

use App\Http\Resources\TransactionResource;
use App\Models\Payment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentUpdateAction
{
    public function handle(Payment $payment, Request $request): JsonResponse
    {
        try{
            logger('### MANUALLY UPDATING PAYMENT ###', [$payment]);
            $payment->updateQuietly($request->all());
            return successfulJsonResponse(data: new TransactionResource($payment->transaction->refresh()));
        }catch(Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
