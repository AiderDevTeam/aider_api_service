<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardSplitResource extends JsonResource
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
            "splitType"=> $this->split_type,
            "shortCode"=> $this->short_code,
            "createdAt"=> $this->created_at,
            "updatedAt"=> $this->updated_at
        ];
    }
}