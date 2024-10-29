<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductLikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'userExternalId' => User::find($this->user_id)->external_id,
            'productId' => $this->product_id,
            'unliked' => (boolean)$this->unliked
        ];
    }
}
