<?php

namespace App\Http\Controllers;

use App\Actions\Webhook\ApiDeliveryWebhookAction;
use App\Actions\Webhook\DisbursementCallbackWebhookAction;
use App\Actions\Webhook\IncentiveDisbursementWebhookAction;
use App\Actions\Webhook\BookingPaymentStatusUpdateWebhookAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function bookingPaymentStatusUpdate(Request $request, BookingPaymentStatusUpdateWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function apiGatewayDeliveryWebhookHandler(Request $request, ApiDeliveryWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function disbursementCallbackWebhookHandler(Request $request, DisbursementCallbackWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function incentiveDisbursementWebhookHandler(Request $request, IncentiveDisbursementWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
