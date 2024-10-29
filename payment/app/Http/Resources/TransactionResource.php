<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'amount' => $this->amount,
            'accountNumber' => $this->account_number,
            'sortCode' => $this->sort_code,
            'type' => $this->type,
            'status' => $this->status,
            'stan' => $this->stan,
        ];
    }
}
