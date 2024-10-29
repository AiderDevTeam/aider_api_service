<?php

namespace App\Http\Resources;

use App\Http\Services\GetAuthUserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userDetails = $this->user->other_details;
        return [
            'externalId' => $this->external_id,
            'vendorId' => $this->vendor_id,
            'userId' => $this->user_id,
            'userExternalId' => $this->user->external_id,
            'vendorExternalId' => $this->vendor->external_id,
            'vendorUserExternalId' => $this->vendor->user->external_id,
            'status' => $this->status ?? "",
            'collectionStatus' => $this->collection_status,
            'deliveryAmount' => $this->delivery_amount,
            'discountedAmount' => $this->discounted_amount,
            'itemsAmount' => $this->items_amount,
            'amountPaid' => $this->amount_paid,
            'description' => $this->description,
            'recipientContact' => $this->recipient_contact,
            'recipientSortCode' => $this->recipient_sort_code,
            'recipientAlternativeContact' => $this->recipient_alternative_contact,
            'isAccepted' => (boolean)$this->is_accepted,
            'orderNumber' => $this->order_number,
            'date' => Carbon::parse($this->created_at)->format('jS F, Y'),
            'origin' => new DeliveryOriginResource($this->delivery->origin),
            'destination' => new DeliveryDestinationResource($this->delivery->destination),
            'recipient' => new DeliveryRecipientResource($this->delivery->recipient),
            'vendor' => new VendorResource($this->vendor), //To Do --> refactor this, use eager loading
            'user' => empty($userDetails) ? null : $userDetails, //new GuestUserResource($this->user),
            'carts' => CartResource::collection($this->whenLoaded('orderCarts')),
            'payOnDelivery' => $this->pay_on_delivery,
            'groupDate' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'time' => Carbon::parse($this->created_at)->format('g:i A'),
        ];
    }
}
