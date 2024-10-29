<?php

namespace App\Http\Actions\Referral;

use App\Events\MakeSubtractionOperationEvent;
use App\Http\Requests\ReferralRewardRequest;
use App\Models\Referral;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\JsonResponse;

class RewardReferrerAction
{
    public function handle(ReferralRewardRequest $request): JsonResponse
    {
        try {
            logger('### REFERRER REWARD INITIALIZED ###');
            logger($data = $request->validated());

            $referrals = Referral::getReferredById($data['referredExternalId']);
           
            if ($referrals->count() < 1)
                return errorJsonResponse(errors: ['no referral record found'], statusCode: 404);

            $referralsWithoutRewards = $referrals->has('reward', '<', 1)->get();
            foreach ($referralsWithoutRewards as $referral) {

                //check if campaign tied to the referral is active and its cash reward is not depleted
                $reward = $referral->campaign->reward_value;
                $cash = $reward->amount;
                $poynt = $reward->point;

                if($referral->campaign->reward_type->type === "Cash"){
                    if((int) $referral->getRewardValue() > (int) $cash){
                        return errorJsonResponse(errors: ['The cash reward for this campaign is depleted '], statusCode: 404);
                    }
                }elseif($referral->campaign->reward_type->type === "Points"){
                    if((int) $referral->getRewardValue() > (int) $poynt){
                        return errorJsonResponse(errors: ['The poynts reward for this campaign is depleted '], statusCode: 404);
                    }
                }

                $referrerWallet = PaymentService::getWallet($referral->referrer_id);
                if(is_null($referrerWallet) || !isset($referrerWallet['data']))
                    continue;

               $referral->reward()->create([
                    'reward_value' => $referral->getRewardValue(),
                    'referrer_account_number' => $referrerWallet['data']['accountNumber'],
                    'referrer_account_number_sort_code' => $referrerWallet['data']['sortCode'],
                ]);
            }

            return successfulJsonResponse();

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }

}
