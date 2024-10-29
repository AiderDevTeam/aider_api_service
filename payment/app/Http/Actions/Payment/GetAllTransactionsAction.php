<?php

namespace App\Http\Actions\Payment;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAllTransactionsAction
{

    /**
     * Get Transactions with corresponding payment for user
     *
     * @param Request $authRequest
     * @return JsonResponse
     */
    public function handle(Request $authRequest): JsonResponse
    {
        try{
            $user = (!empty($authRequest->user['externalId'])) ? $authRequest->user['externalId'] : '';
            logger()->info('Get All Transactions for a user:: '.$user);
            $trans =  User::where('external_id',$user)->first()->transactions;
            logger($trans);
            if(isset($trans)):
                $sortedTrans = $trans->sortByDesc('created_at');
                return successfulJsonResponse(TransactionResource::collection($sortedTrans));
            else:
                return errorJsonResponse();
            endif;
        }catch (Exception $exception) {
            logger()->info('Get All Transactions for a user error::');
            report($exception);
            logger($exception->getMessage());
            return errorJsonResponse();
        }

    }

}
