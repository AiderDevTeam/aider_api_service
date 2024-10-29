<?php

namespace App\Http\Actions\Payment;


use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionUpdateAction
{
    public function handle(Transaction $transaction, Request $request): JsonResponse
    {
        try{
            logger('### MANUALLY UPDATING TRANSACTION ###', [$transaction]);
            $transaction->updateQuietly($request->all());
            return successfulJsonResponse(data: new TransactionResource($transaction->refresh()));
        }catch(Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
