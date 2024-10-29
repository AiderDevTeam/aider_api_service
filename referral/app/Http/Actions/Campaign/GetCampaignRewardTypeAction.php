<?php 

namespace App\Http\Actions\Campaign;

use App\Models\RewardType;
use Illuminate\Http\JsonResponse;

class GetCampaignRewardTypeAction {

    public function handle(): JsonResponse{
        return  successfulJsonResponse(RewardType::all());
    }
}