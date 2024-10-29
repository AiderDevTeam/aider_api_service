<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferralRewardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'externalId' => $this->external_id,
            'rewardStatus' => $this->reward_status,
            'rewardValue' => $this->reward_value,
            'referrerAccountNumber' => $this->referrer_account_number,
            'referrerAccountNumberSortCode' => $this->referrer_account_number_sort_code,
            'referral' => new ReferralResource($this->whenLoaded('referral'))
        ];
    }
}
