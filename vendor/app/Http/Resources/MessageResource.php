<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'conversationId' => $this->conversation_id,
            'senderExternalId' => $this->sender->external_id,
            'type' => $this->type,
            'readAt' => $this->read_at,
            'sentAt' => $this->created_at,
            'onGoing' => $this->conversation_on_going,
            'senderMessage' => $this->isBooking() ? new BookingResource($this->whenLoaded('bookingData')) : $this->sender_message,
            'receiverMessage' => $this->receiver_message
        ];
    }
}
