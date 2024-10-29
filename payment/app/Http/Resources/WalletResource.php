<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
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
            'accountName' => $this->account_name ?: '',
            'accountNumber' => $this->account_number,
            'sortCode' => $this->sort_code,
            'bankCode' => $this->bank_code,
            'bankName' => $this->bank->name,
            'type' => $this->type,
            'default' => (bool)$this->default,
        ];
    }
}
