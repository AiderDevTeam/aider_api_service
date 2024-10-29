<?php

namespace App\Http\Controllers;

use App\Http\Actions\Payment\GetAllTransactionsAction;
use App\Http\Actions\Payment\StoreVASPaymentAction;
use App\Http\Actions\Payment\VASDiscountAction;
use App\Http\Requests\Payment\StoreVASPaymentRequest;
use App\Http\Requests\VASDiscountRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VASController extends Controller
{
    public function index(Request $authRequest, GetAllTransactionsAction $action): JsonResponse
    {
        return $action->handle($authRequest);
    }

    public function store(Request $authRequest, StoreVASPaymentRequest $vasRequest, StoreVASPaymentAction $action): JsonResponse
    {
        return $action->handle($authRequest, $vasRequest);
    }

    public function discount(VASDiscountRequest $request, VASDiscountAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
