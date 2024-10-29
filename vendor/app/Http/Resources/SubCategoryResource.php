<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class SubCategoryResource extends JsonResource
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
            'categoryId' => $this->category_id,
            'name' => $this->name,
            'subCategoryItems' =>  SubCategoryItemsResource::collection($this->whenLoaded('subCategoryItems'))
        ];
    }
}
