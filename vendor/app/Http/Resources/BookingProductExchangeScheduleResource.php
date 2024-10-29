<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingProductExchangeScheduleResource extends JsonResource
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
            'bookingProductId' => $this->booking_product_id,
            'city' => $this->city,
            'originName' => $this->origin_name,
            'country' => $this->country,
            'countryCode' => $this->country_code,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'timeOfExchange' => $this->time_of_exchange,
        ];
    }
}
