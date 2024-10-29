<?php

namespace App\Http\Resources;

use App\Custom\Identification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserIdentificationResource extends JsonResource
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
            'idNumber' => $this->id_number,
            'documentUrl' => $this->document_url,
            'selfieUrl' => $this->selfie_url,
            'type' => $this->type,
            'status' => $this->status,
            'rejectionReason' => $this->when($this->status === Identification::STATUS['REJECTED'], $this->lastRejectionReason()?->reason ?? 'Verification Failed')
        ];
    }
}
