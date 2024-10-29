<?php

namespace App\Http\Actions\Campaign;

use App\Models\RewardSplit;
use Illuminate\Http\JsonResponse;

class GetCampaignRewardSplitAction {

    public function handle(): JsonResponse{
        return  successfulJsonResponse(RewardSplit::all());
    }
}