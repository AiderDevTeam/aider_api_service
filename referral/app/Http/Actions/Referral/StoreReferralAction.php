<?php

namespace App\Http\Actions\Referral;

use App\Http\Requests\ReferralRequest;
use App\Http\Resources\ReferralResource;
use App\Models\Referral;
use App\Models\User;
use App\Models\UserReferralCampaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class StoreReferralAction {


    public function handle(ReferralRequest $request):JsonResponse{

        try{
            if($data = $request->validated()){
                $data['referrerId'] = $request['userReferralcampaign']->user->external_id;
                //check referrer max referrals;
                $user = User::where('external_id', $data['referrerId'])->first();
                if($user){
                    $userReferralNumber = (int)$user->points;
                    $userReferralsGivenCollection = $user->user_referral_number()->first();
                    $userReferralsGiven = (isset($userReferralsGivenCollection)) ? (int)$userReferralsGivenCollection->referrals_given : 0;

                    if( $userReferralNumber !=  $userReferralsGiven ){
                        $bearerToken = $request->bearerToken();
                        $_request = Http::withToken($bearerToken)->withHeaders(jsonHttpHeaders())->get('http://auth/api/sys/get-user/'.$data['referredId']);
                        if ($_request->successful()) {
                            logger()->info("### GET USER DATA ###");
                            $referredUser = $_request->json('data');
                            logger($referredUser);
                            //store referred user
                            getOrCreateUser($referredUser);
                        
                        try{
                            if(!isset($data['campaignId']) || (isset($data['campaignId']) && $data['campaignId'] =="0")){
                                $campaign = getRunningCampaign();
                                $data['campaignId'] = (!empty($campaign)) ? $campaign->id : "2";
                                try{
                                    $this->sendReferralToAuth($data);
                                }catch(\Exception $e){
                                    logger("ISSUE NOTIFYING REFERRAL ON AUTH::");
                                }
                            }

                            $campaign = getCampaign($data['campaignId']);
                            $check = $data['referredId'] == getCurrentUserByReferralUrl($data['referralLink'])->user->external_id ?? '';

                            if($check){
                                return errorJsonResponse(
                                    errors: ['Could not referred'],
                                    message: 'Please check referral link, you cannot refer yourself to this campaign('.$campaign->name.')',
                                    statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
                            }

                            //check if user has already been referred by the same referrer
                            logger(['referrer_id' => $data['referredId'],'referred_id' => $data['referrerId'] ]);
                            // $check = Referral::where('referred_id', $data['referrerId'])
                            // ->where('referrer_id', $data['referredId'])
                            // ->where('campaign_id', $data['campaignId'])
                            // ->first();
                            // if($check){
                            //     return errorJsonResponse(
                            //         errors: ['Could not referred'],
                            //         message: 'User has already referred other people in this campaign('.$campaign->name.')',
                            //         statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
                            // }

                             //check if user has already been referred for that same campaign
                            //  $check = Referral::where('referred_id', $data['referrerId'])->where('campaign_id', $data['campaignId'])->first();
                            //  if($check){
                            //      return errorJsonResponse(
                            //          errors: ['Could not referred'],
                            //          message: 'User has already been referred in this campaign('.$campaign->name.')',
                            //          statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
                            //  }
                            $data['externalId'] = 'R'.uniqid();
                            $data['campaignChannelId'] = "15";
                            $referral = Referral::create(toSnakeCase($data));

                            if($referral){
                                return successfulJsonResponse(new ReferralResource($referral));
                            }else{
                                return errorJsonResponse(
                                    errors: ['Could not referred'],
                                    message: 'User might already been referred by same referrer',
                                    statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
                            }
                        }catch(\Exception $e){
                            return errorJsonResponse(
                                errors: ['Could not referred'],
                                systemError:$e->getMessage(),
                                message: 'User might already been referred by same referrer',
                                statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
                            }
                    
                       

                    }else{
                        return errorJsonResponse(
                            errors: ['Referral Error'],
                            message: 'This link is now expired. Please find another link on social media, 
                            hurry before all the best deal sellout!',
                            statusCode: 404);
                    
                    }

                    }else{
                       
                        return errorJsonResponse(
                            errors: ['Referral Exceeded'],
                            message: 'Something went wrong, please contact the poynt team, thanks!',
                            statusCode: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
                    }
                }

            }
        }catch(\Exception $e){
            logger($e->getMessage());
            return errorJsonResponse(
                errors: ['Something Went Wrong'],
                message: $e->getMessage(),
                statusCode: ResponseAlias::HTTP_UNAUTHORIZED);
        }

    }


    private function sendReferralToAuth($data){
        try{
            //send to auth service
          
                $send = Http::withHeaders(jsonHttpHeaders())->post('http://auth/api/sys/user/grant-campaign-access/'.$data['referredId'], [
                    "campaignType" => $data['campaignId']
                ]);
                logger("SEND TO AUTH::");
                logger($send->json());
        }catch(\Exception $e){
            logger("ISSUE NOTIFYING REFERRAL ON AUTH::");
            logger($e->getMessage());
        }
    }
}