<?php

namespace App\Http\Controllers;

use App\Http\Actions\AccountNumberResolutionAction;
use App\Http\Requests\AccountNumberResolutionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function __invoke(Request $request, AccountNumberResolutionAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
