<?php

namespace App\Http\Actions\Referral;

use App\Http\Requests\ReferralBalanceRequest;
use App\Models\ReferralUserNumber;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ReferralBalanceAction
{
    public function handle(ReferralBalanceRequest $request): JsonResponse
    {
        try {
            $bearerToken = $request->bearerToken();

            $_request = Http::withToken($bearerToken)->withHeaders(jsonHttpHeaders())->get('http://auth/api/sys/get-user/'.$request['userExternalId']);
            $referralAllocation = getReferralAllocation( $request->validated()['campaignId'] );
            logger($_request);
            if ($_request->successful()) {
                $externalUser = $_request->json('data');
                if(User::query()->where('external_id', $request['userExternalId'])->first()){
                    $userReferrals = User::updateOrCreate(['external_id'=>$request['userExternalId']],[
                        'points' => (isset($externalUser['userTypes'][0]) && in_array('ambassador',$externalUser['userTypes'])) ? $referralAllocation->ambassador ?? "250" : $referralAllocation->user ?? "400000",
                    ]);
                    
                    $referralUserNumber = ReferralUserNumber::query()->where('external_id', $request['userExternalId'])->where('campaign_id', $request->campaignId)->first();

                    $referralUserNumber ? $balance = intval($userReferrals->points) - intval($referralUserNumber->referrals_given) : $balance = $userReferrals->points;

                    return successfulJsonResponse(
                        data: ($balance < 0) ? 0 : $balance,
                        message: 'Referrals balance'
                    );
                } else {
                    $externalUser = $_request->json('data');
                    logger($externalUser);
                    $localUser = User::create([
                        'external_id' => $request['userExternalId'],
                        'points' => (isset($externalUser['userTypes'][0]) && in_array('ambassador',$externalUser['userTypes'])) ? $referralAllocation->ambassador ?? "250" : $referralAllocation->user ?? "400000",
                        'user_details' => json_encode($externalUser),
                        'user_type' => (isset($externalUser['userTypes'][0]) && in_array('ambassador',$externalUser['userTypes'])) ? 'ambassador' :  'user',
                        'referral_no' => Str::random(6)
                    ]);

                    return successfulJsonResponse(
                        data: $localUser->points,
                        message: 'Referrals balance');
                }

            }

        } catch (\Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
