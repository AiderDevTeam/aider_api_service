<?php

namespace App\Http\Actions\Webhook;

use App\Models\ReferralReward;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralCashRewardWebhookAction
{
    public function handle(Request $request): JsonResponse
    {
        logger('### REFERRAL CASH REWARD WEBHOOK ACTION ###');
        logger($data = $request->all());
        try {

            if (!isset($data['transactionId']) || !isset($data['status'])) {
                logger('### EXPECTED REQUEST PARAMETER NOT SET ###');
                return errorJsonResponse(message: 'expected request parameters not set', statusCode: 422);
            }

            if (!$referralReward = ReferralReward::where('external_id', $data['transactionId'])->first()) {
                logger('### REFERRAL REWARD RECORD NOT FOUND ###');
                return errorJsonResponse(message: 'referral reward record not found', statusCode: 422);
            }

            logger('### UPDATING REFERRAL REWARD ###');
            $referralReward->update(['reward_status' => $data['status']]);

            return successfulJsonResponse();

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
