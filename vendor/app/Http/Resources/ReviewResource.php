<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'comment' => $this->comment,
            'postedAt' => Carbon::parse($this->created_at)->format('jS F, Y g:iA'),
            'time' => Carbon::parse($this->created_at)->format('g:i A'),
            'date' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'reviewer' => new UserResource($this->whenLoaded('reviewer')),
        ];
    }
}
