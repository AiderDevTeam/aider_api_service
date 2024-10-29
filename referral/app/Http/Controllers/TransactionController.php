<?php

namespace App\Http\Controllers;

use App\Http\Actions\Transaction\UpdateTransactionAction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    //
    public function updateTransaction(Request $request, $stan, UpdateTransactionAction $action){
        return $action->handle($request, $stan);
    }
}
