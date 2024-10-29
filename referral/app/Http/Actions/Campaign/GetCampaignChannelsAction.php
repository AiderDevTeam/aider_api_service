<?php

namespace App\Http\Actions\Campaign;

use App\Http\Resources\CampaignChannelsResource;
use App\Models\CampaignChannel;
use Illuminate\Http\JsonResponse;

class GetCampaignChannelsAction{

    public function handle(): JsonResponse{
        return successfulJsonResponse(CampaignChannelsResource::collection(CampaignChannel::get()));
    }
}