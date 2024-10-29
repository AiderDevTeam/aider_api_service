<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'live' => (boolean)$this->live,
            'position' => (integer)$this->position,
            'type' => $this->type,
            'additionalData' => $this->additional_data,
            'filter' => $this->filter,
            'filters' => $this->filters,
            'deletedAt' => $this->when($this->deleted_at, Carbon::parse($this->deleted_at)->timestamp)
        ];
    }
}
