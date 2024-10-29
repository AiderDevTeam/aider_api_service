<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingProductResource extends JsonResource
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
            'amount' => (double)$this->product_amount,
            'quantity' => (int)$this->product_quantity,
            'value' => (double)$this->product_value,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'duration' => (int)$this->booking_duration,
            'daysSpan' => (int)$this->daysSpan(),
            'isOverdue' => (boolean)$this->isOverdue(),
            'returnedEarly' => (boolean)$this->returned_early,
            'isReviewed' => (boolean)$this->is_reviewed,
            'review' => new ReviewResource($this->whenLoaded('review')),
            'renterReview' => new ReviewResource($this->whenLoaded('renterReview')),
            'exchangeSchedule' => new BookingProductExchangeScheduleResource($this->whenLoaded('exchangeSchedule')),
            'bookingDates' => BookingDateResource::collection($this->whenLoaded('bookingDates')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'ts' => Carbon::parse($this->created_at)->timestamp,
        ];
    }
}
