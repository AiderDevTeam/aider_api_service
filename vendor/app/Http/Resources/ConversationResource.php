<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
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
            'isOnGoing' => $this->is_on_going,
            'externalId' => $this->external_id,
            'userExternalId' => $this->user->external_id,
            'vendorExternalId' => $this->vendor->external_id,
            'createdAt' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'userUnreadMessagesCount' => (int)$this->userUnreadMessagesCount(),
            'vendorUnreadMessagesCount' => (int)$this->vendorUnreadMessagesCount(),
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'user' => new UserResource($this->whenLoaded('user')),
            'vendor' => new UserResource($this->whenLoaded('vendor')),

        ];
    }
}
