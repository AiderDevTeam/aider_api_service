<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class AdminMetricResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'externalId' => $this->external_id,
            'service' => $this->service,
            'createdAt' => Carbon::parse($this->created_at)->toDateTimeString(),
            'updatedAt' => Carbon::parse($this->updated_at)->toDateTimeString(),
            'data' => $this->toRealtimeData(),
        ];
    }

}
