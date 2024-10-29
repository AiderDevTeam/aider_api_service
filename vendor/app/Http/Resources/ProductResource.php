<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        $this->productReviews ??= $this->reviews()->with('reviewable.user')->limit(3)->get();

        return [
            'id' => $this->id,
            'externalId' => $this->external_id,
            'postedAt' => Carbon::parse($this->created_at)->format('jS F, Y'),
            'name' => $this->name,
            'description' => $this->description,
            'quantity' => (int)$this->quantity,
            'status' => $this->status,
            'value' => (float)$this->value,
            'prices' => ProductPriceResource::collection($this->whenLoaded('prices')),
            'photos' => ProductPhotoResource::collection($this->whenLoaded('photos')),
            'shareLink' => $this->share_link,
            'rejectionDate' => $this->rejection_date,
            'approvalDate' => $this->approval_date,
            'updatedAt' => $this->updated_at->timestamp,
            'deletedAt' => $this->deleted_at,
            'address' => new ProductAddressResource($this->whenLoaded('address')),
            'user' => new UserResource($this->whenLoaded('vendor')),
            'subCategoryItem' => new SubCategoryItemsResource($this->whenLoaded('subCategoryItem')),
            'rating' => (float)$this->rating,
            'unavailableBookingDates' => BookingDateResource::collection($this->whenLoaded('unavailableBookingDates')),
//            'numberOfReviews' => $this->numberOfReviews(),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews'))
        ];
    }
}
