<?php

namespace App\Http\Actions\Campaign;

use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class GetRunningCampaignAction {

    public function handle(): JsonResponse{
        $campaign = Campaign::where('end_date', '>=', Carbon::now()->toDateString())
        ->where('running','true')
       // ->where('campaign_code', '!=', 'user_referrals')
        ->first();

        if($campaign){
            return  successfulJsonResponse(new CampaignResource($campaign));
        }else{
            return errorJsonResponse(message: 'No running campaign', statusCode: 401);
        }
    }
}