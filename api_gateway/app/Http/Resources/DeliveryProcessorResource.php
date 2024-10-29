<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryProcessorResource extends JsonResource
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
            'name' => $this->name,
            'active' => $this->active,
            'express' => $this->express,
            'nextDay' => $this->next_day,
        ];
    }
}
