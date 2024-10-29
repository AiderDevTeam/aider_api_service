<?php

namespace App\Http\Controllers;

use App\Http\Actions\Webhook\ReferralCashRewardWebhookAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function cashRewardDisbursementWebhook(Request $request, ReferralCashRewardWebhookAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
