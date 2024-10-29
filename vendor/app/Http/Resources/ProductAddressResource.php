<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAddressResource extends JsonResource
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
            'productId' => $this->product_id,
            'city' => $this->city,
            'originName' => $this->origin_name,
            'country' => $this->country,
            'countryCode' => $this->country_code,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
        ];
    }
}
