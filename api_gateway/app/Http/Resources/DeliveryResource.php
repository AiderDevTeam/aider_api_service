<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
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
            'status' => $this->status,
            'trackingNumber' => $this->tracking_number,
            'currency'=> $this->currency,
            'deliveryOption' => $this->delivery_option,
            'isPickUp' => $this->is_pick_up,
            'isFulfillmentDelivery' => $this->is_fulfillment_delivery,
            'amountToCollect' => $this->amount_to_collect,
            'serviceType' => $this->service_type,
            'isPrepaidDelivery' => $this->is_prepaid_delivery
        ];
    }
}
