<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "externalId"=> $this->external_id,
            "points"=> $this->points,
            "userDetails"=> json_decode($this->user_details),
            "createdAt"=> $this->created_at,
            "updatedAt"=> $this->updated_at,
            "userType"=> $this->user_type,
            "referralsGiven"=> $this->user_referral_number->referrals_given ?? "",
            "referralLinks" => $this->user_referral_campaign()->selectRaw('user_referral_campaigns.campaign_id AS campaignId, user_referral_campaigns.referral_url AS referralUrl')->get()
        ];
    }
}
