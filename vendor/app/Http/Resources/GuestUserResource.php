<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuestUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $nameParts = explode(' ', $this->full_name);
        $firstName = $nameParts[0];

        return [
            'username' => strtolower($firstName) ?? null
        ];
    }
}
