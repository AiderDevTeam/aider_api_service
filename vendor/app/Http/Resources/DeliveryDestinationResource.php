<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryDestinationResource extends JsonResource
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
            'deliveryId' => $this->delivery_id,
            'destinationName' => $this->destination_name,
            'city' => $this->city,
            'state' => $this->state,
            'country'=> $this->country,
            'countryCode'=> $this->country_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}