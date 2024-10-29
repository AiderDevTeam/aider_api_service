<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'userExternalId' => $this->user->external_id,
            'externalId' => $this->external_id,
            'vendorId' => (int)$this->vendor_id,
            'product' => new ProductResource($this->product),
            'quantity' => (int)$this->quantity,
            'isCheckedOut' => (boolean)$this->is_checked_out,
            'deletedAt' => $this->deleted_at,
            'payOnDelivery' => $this->vendor->acceptsPaymentOnDelivery(),
            'color' => $this->color,
            'size' => $this->size,
            'vendor' => new VendorResource($this->whenLoaded('vendor')),
            'isReviewed' => $this->isReviewed(),
            'isReviewable' => $this->isReviewable(),
            'review' => new ReviewResource($this->whenLoaded('review'))
        ];
    }
}
