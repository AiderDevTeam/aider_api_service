<?php

namespace App\Http\Actions\Campaign;

use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use App\Models\RewardSplit;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class GetCampaignAction {

    public function handle(): JsonResponse{
        return  successfulJsonResponse(CampaignResource::collection(Campaign::where('end_date', '>=', Carbon::now()->toDateString())
        //->where('campaign_code', '!=', 'user_referrals')
        ->get()));
    }
}