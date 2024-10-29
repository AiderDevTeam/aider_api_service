<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
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
            'sortCode' => $this->sort_code,
            'bankCode' => $this->bank_code,
            'longCode' => $this->long_code,
            'country' => $this->country,
            'currency' => $this->currency
        ];
    }
}
