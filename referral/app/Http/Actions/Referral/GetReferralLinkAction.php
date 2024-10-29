<?php

namespace App\Http\Actions\Referral;

use App\Events\GetReferralLinkEvent;
use App\Http\Requests\GetReferralLinkRequest;
use App\Http\Requests\ReferralRequest;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserReferralCampaign;
use App\Services\GoogleDynamicLinksService;
use Illuminate\Support\Facades\Http;

class GetReferralLinkAction{

    public function handle(GetReferralLinkRequest $request){
        logger()->info("### GET REFERRAL LINK ###");
        $data = $request->validated();
        try{
            $bearerToken = $request->bearerToken();
            if($data){
                //validate user by external id
                $_request = Http::withToken($bearerToken)->withHeaders(jsonHttpHeaders())->get('http://auth/api/sys/get-user/'.$data['userExternalId']);
                if ($_request->successful()) {
                    logger()->info("### GET USER DATA ###");
                    $externalUser = $_request->json('data');
                    logger($externalUser);
                    if($externalUser){
                        $referralAllocation = getReferralAllocation( $data['campaignId'] );
                        if($user  = User::where('external_id', $data['userExternalId'])->first()){
                            $localUser = $user->updateOrCreate(['external_id' => $data['userExternalId']],[
                                'external_id' => $data['userExternalId'],
                                'points' => (isset($externalUser['userTypes'][0]) && in_array('ambassador',$externalUser['userTypes'])) ? $referralAllocation->ambassador ?? "250" : $referralAllocation->user ?? "5",
                                'user_details' => json_encode($externalUser),
                                'user_type' => (isset($externalUser['userTypes'][0]) && in_array('ambassador',$externalUser['userTypes'])) ? 'ambassador' :  'user',
                            ]);
                        }else{
                            $localUser = User::create([
                                'external_id' => $data['userExternalId'],
                                'points' => (isset($externalUser['userTypes'][0]) && in_array('ambassador',$externalUser['userTypes'])) ? $referralAllocation->ambassador ?? "250" : $referralAllocation->user ?? "5",
                                'user_details' => json_encode($externalUser),
                                'user_type' => (isset($externalUser['userTypes'][0]) && in_array('ambassador',$externalUser['userTypes'])) ? 'ambassador' :  'user',
                                'referral_no' => Str::random(6)
                             ]);
                        }
                      
                    }
                   
                    //check if referral link exists and then return 
                    $referralLink = UserReferralCampaign::where("campaign_id", $data['campaignId'])->where("user_id", $localUser->id)->first();

                    $userReferralCampReferralLink = (!empty($referralLink->referral_url)) ? $referralLink->referral_url: '';

                    if($userReferralCampReferralLink){
                        return successfulJsonResponse([
                            'userExternalId' => $externalUser['externalId'],
                            'referralLink' => "Hi, I have an exclusive Black Ticket for the POYNT app and I want to give it to you! Click on the link to join for exclusive deals\n\n".$userReferralCampReferralLink
                      ]);
                    }

                    $referral = GoogleDynamicLinksService::getDynamicLink($localUser->referral_no, $data['campaignId'], $localUser);
                    if(isset($referral['shortLink'])){
                        $extras = ['link' => $referral['shortLink'], 'campaignId' =>  $data['campaignId']];
                        //get dynamic link and store 
                        event(new GetReferralLinkEvent($localUser,$externalUser,$extras));
                        return successfulJsonResponse([
                            'userExternalId' => $externalUser['externalId'],
                            'referralLink' => "Hi, I have an exclusive Black Ticket for the POYNT app and I want to give it to you! Click on the link to join for exclusive deals\n\n".$referral['shortLink']
                      ]);
                    }else{
                        return errorJsonResponse(message: 'Unable to generate referral link', statusCode: 401);
                    }
                   
                  
                } else {
                    return errorJsonResponse(message: 'User authorization failed', statusCode: 401);
                }
            }
        }catch(\Exception $e){
            logger($e->getMessage());
            dd($e->getMessage());
        }
    }
}