<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'birthday' => $this->birthday,
            'roles' => $this->roles()->pluck('name'),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'bearerToken' => $this->when($this->bearer_token, $this->bearer_token)
        ];
    }
}
