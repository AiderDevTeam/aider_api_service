<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "campaignId"=> $this->campaign_id,
            "referrerId"=> $this->referrer_id,
            "referredId"=> $this->referred_id,
            "referrer" => json_decode($this->referrer->user_details),
            "referred" => json_decode($this->referred->user_details),
            "narration" => json_decode($this->referrer->user_details)->firstName." just referred ".json_decode($this->referred->user_details)->firstName,
            "campaignChannelId"=> $this->campaign_channel_id,
            "referralLink"=> $this->referral_link,
            "campaignChannel" => new CampaignChannelsResource($this->campaign_channel),
            "id"=> $this->id
        ];
    }
}
