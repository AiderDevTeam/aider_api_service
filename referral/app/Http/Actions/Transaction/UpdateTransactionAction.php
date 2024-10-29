<?php

namespace App\Http\Actions\Transaction;

use App\Models\Transaction;

class UpdateTransactionAction{

    public function handle($request,  $stan){
        //update transaction
        $transaction = Transaction::where('stan', $stan)->first();
        if(!empty($request->responseCode) && $request->responseCode == '000'){
            $transaction->update([
                'response_code' => $request->responseCode,
                'response_message' => 'successful'
            ]);
        }else{
            $transaction->update([
                'response_code' => '009',
                'response_message' => 'failed'
            ]);
        }
    }
}