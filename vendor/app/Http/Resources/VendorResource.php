<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'username' => $this->user->other_details['username'] ?? '',
            'createdAt' => $this->created_at,
            'shopTag' => $this->shop_tag,
            'commission' => $this->commission,
            'insurance' => $this->insurance,
            'default' => (boolean)$this->default,
            'vendorName' => $this->user->full_name,
            'verified' => $this->user->verification_status === 'approved',
            'address' => new AddressResource($this->address),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'joinedAt' => Carbon::parse($this->created_at)->format('jS F, Y'),
            'shareLink' => $this->share_link,
            'official' => (boolean)$this->official,
            'payOnDelivery' => $this->pay_on_delivery,
            'paymentLink' => env('PAYMENT_LINK_URL') . str_replace(" ", "%20", $this->shop_tag),
            'listedItemsCount' => optional($this->statistics, fn() => $this->statistics->listed_items_count),
            'soldItemsCount' => optional($this->statistics, fn() => $this->statistics->sold_items_count),
            'rating' => $this->statistics?->rating,
            'numberOfReviews' => $this->numberOfReviews() ?? 0,
            'individualRatingCounts' => $this->statistics?->individual_rating_counts,
//            'reviews' => ReviewResource::collection($this->reviews()->get()),
        ];


    }
}
