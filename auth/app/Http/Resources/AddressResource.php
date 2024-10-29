<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'originName' => $this->origin_name,
            'state' => $this->state,
            'city' => $this->city,
            'country' => $this->country,
            'countryCode' => $this->country_code,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'phone' => $this->phone,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'alternativePhoneNumber' => $this->alternative_phone_number,
            'additionalInformation' => $this->additional_information,
            'default' => (boolean)$this->default,
        ];
    }
}
