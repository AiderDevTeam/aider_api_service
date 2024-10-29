<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //to Do => //find efficient way of pulling userHasReported and vendorHasReported values
        return [
            'id' => $this->id,
            'externalId' => $this->external_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'vendor' => new UserResource($this->whenLoaded('vendor')),
            'status' => $this->status,
            'userHasReported' => $this->userHasReported(),
            'vendorHasReported' => $this->vendorHasReported(),
            'collectionAmount' => (double)$this->collection_amount,
            'collectionStatus' => $this->collection_status,
            'disbursementAmount' => (double)$this->disbursement_amount,
            'disbursementStatus' => $this->disbursement_status,
            'reversalStatus' => $this->reversal_status,
            'bookingAcceptanceStatus' => $this->booking_acceptance_status,
            'vendorPickupStatus' => $this->vendor_pickup_status,
            'userPickupStatus' => $this->user_pickup_status,
            'vendorDropOffStatus' => $this->vendor_drop_off_status,
            'userDropOffStatus' => $this->user_drop_off_status,
            'bookingNumber' => $this->booking_number,
            'bookedProduct' => new BookingProductResource($this->whenLoaded('bookedProduct')),
        ];
    }
}
