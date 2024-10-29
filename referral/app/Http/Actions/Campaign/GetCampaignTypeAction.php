<?php 

namespace App\Http\Actions\Campaign;

use App\Models\CampaignType;
use Illuminate\Http\JsonResponse;

class GetCampaignTypeAction{

    public function handle(): JsonResponse{
        return  successfulJsonResponse(CampaignType::all());
    }

}