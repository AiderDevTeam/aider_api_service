<?php

namespace App\Http\Controllers;

use App\Http\Actions\Guest\GuestStoreDeliveryPaymentAction;
use App\Http\Actions\Payment\GetAllDeliveryPaymentsAction;
use App\Http\Actions\Payment\StoreDeliveryPaymentAction;
use App\Http\Requests\GuestStoreDeliveryPaymentRequest;
use App\Http\Requests\Payment\StoreDeliveryPaymentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index(Request $authRequest, GetAllDeliveryPaymentsAction $action): JsonResponse
    {
        return $action->handle($authRequest);
    }

    public function store(Request $authRequest, StoreDeliveryPaymentRequest $deliveryPaymentRequest, StoreDeliveryPaymentAction $action): JsonResponse
    {
        return $action->handle($authRequest, $deliveryPaymentRequest);
    }

    public function storeGuest(GuestStoreDeliveryPaymentRequest $deliveryPaymentRequest, GuestStoreDeliveryPaymentAction $action): JsonResponse
    {
        return $action->handle($deliveryPaymentRequest);
    }
}
