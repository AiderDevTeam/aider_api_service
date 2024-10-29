<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClosetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'externalId' => $this->external_id,
            'shopLogoUrl' => $this->shop_logo_url_b ?? $this->shop_logo_url,
            'businessName' => $this->business_name,
            'userExternalId' => $this->user->external_id,
            'createdAt' => $this->created_at,
            'shopTag' => $this->shop_tag,
            'commission' => $this->commission,
            'insurance' => $this->insurance,
            'default' => (boolean)$this->default,
            'vendorName' => $this->user->full_name,
            'verified' => $this->user->verification_status === 'approved',
            'address' => new AddressResource($this->address),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'joinedAt' => Carbon::parse($this->created_at)->format('jS F, Y'),
            'shareLink' => $this->share_link,
            'closetImageUrl' =>$this->closetImageUrl,
            'official' => (boolean)$this->official,
        ];
    }
}
