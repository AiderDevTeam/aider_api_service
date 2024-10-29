<?php

namespace App\Http\Actions\Referral;

use App\Models\Referral;

use Illuminate\Support\Facades\Http;

class GetUserReferralAction{

    public function handle($request, $campaignId, $userExternalId){
        logger()->info("### VERIFY REFERRAL BY USER EXTERNAL ID ###");
        try{
                //validate user by external id
                $bearerToken = $request->bearerToken();
                $_request = Http::withToken($bearerToken)->withHeaders(jsonHttpHeaders())->get('http://auth/api/sys/get-user/'.$userExternalId);
                if ($_request->successful()) {
                    logger()->info("### GET USER DATA ###");
                    $externalUser = $_request->json('data');
                    logger($externalUser);
                    if($externalUser){
                      //check if user has referred or been referred
                      $referral = Referral::where('referrer_id', $userExternalId)->orWhere('referred_id', $userExternalId)->first();
                        if($referral && $referral->campaign_id == (string) $campaignId){
                            return successfulJsonResponse([ 
                                'message' => 'User has referred or been referred in this campaign'
                            ]);
                        }else{
                            return errorJsonResponse(message: 'User has not been referred nor have they referred anyone, check the campaign Id or user external Id', statusCode: 401);
                        }
                    }
                  
                }else{
                        return errorJsonResponse(message: 'User does not exist', statusCode: 401);
                }
                   
        }catch(\Exception $e){
            logger($e->getMessage());
            dd($e->getMessage());
        }
    }
}