<?php

namespace App\Http\Resources;

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'reporter' => $this->reporter ? $this->reporter->external_id : null,
            'reportee' => $this->reportable ? $this->reportable->external_id : null,
            'bookingId' => optional(Booking::find($this->booking_id))->external_id,
            'resolvedBy' => $this->resolved_by,
            'resolvedOn' => Carbon::parse($this->resolved_on)->timestamp,
            'reason' => $this->reason,
            'reportedOn' => Carbon::parse($this->created_at)->timestamp
        ];
    }
}
