<?php

namespace App\Http\Controllers;

use App\Http\Actions\Delivery\CreateDeliveryAction;
use App\Http\Actions\Delivery\DeleteDeliveryAction;
use App\Http\Actions\Delivery\GetDeliveryFeeAction;
use App\Http\Actions\Delivery\GetWegooDeliveryByTrackingNumberAction;
use App\Http\Actions\Delivery\Webhook\ShaqExpressWebhookAction;
use App\Http\Actions\Delivery\Webhook\WegooDeliveryWebhookAction;
use App\Http\Requests\CreateDeliveryRequest;
use App\Http\Requests\DeliveryFeeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function createDelivery(CreateDeliveryRequest $request, CreateDeliveryAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function wegooDeliveryWebhookHandler(Request $request, WegooDeliveryWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function shaqExpressDeliveryWebhookHandler(Request $request, ShaqExpressWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function getDeliveryByTrackingNumber(string $trackingNumber, GetWegooDeliveryByTrackingNumberAction $action)
    {
        return $action->handle($trackingNumber);
    }

    public function getDeliveryFee(DeliveryFeeRequest $request, GetDeliveryFeeAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function deleteDelivery(Request $request, DeleteDeliveryAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
