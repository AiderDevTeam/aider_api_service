<?php

namespace App\Http\Resources;

use App\Models\ReferralAllocation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
                "id" => $this->id,
                "campaignTypeId"=> $this->campaign_type_id,
                "rewardSplitId"=> $this->reward_split_id,
                "rewardTypeId"=> $this->reward_type_id,
                "startDate"=> $this->start_date,
                "endDate"=> $this->end_date,
                "createdAt"=> $this->created_at,
                "updatedAt"=> $this->updated_at,
                "campaignImages"=> $this->campaign_images,
                "cashPerPerson"=> $this->cash_per_person,
                "poyntPerPerson"=> $this->poynt_per_person,
                "campaignType" => new CampaignTypeResource($this->campaign_type),
                "rewardSplit" => new RewardSplitResource($this->reward_split),
                'rewardType' => new RewardTypeResource($this->reward_type),
                'referralAllocation'=>new ReferalAllocationResource($this->referral_allocation),
                'campaignCode'=> $this->campaign_code,
                'name' => $this->name
            ];
    }
}
