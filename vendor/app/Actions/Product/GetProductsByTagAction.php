<?php

namespace App\Actions\Product;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\JsonResponse;

class GetProductsByTagAction
{
    public function handle(string $type): JsonResponse
    {
        try {
            logger('### LOADING ' . strtoupper($type) . ' PRODUCTS ###');

            $products = match (strtolower($type)) {
                ProductTag::BLACK_TICKET => $this->blackTicketProducts(),
                default => []
            };

            return successfulJsonResponse(ProductResource::collection($products->load('vendor', 'tags', 'weightUnit', 'reviews.reviewable')));
        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse(message: 'Sorry, we will be back in a moment');
    }

    private function blackTicketProducts()
    {
        return Product::eligibleItems()->whereHas('tags',
            fn($query) => $query->whereIn('name', ProductTag::BLACK_TICKET_TAGS)
        )->inRandomOrder()->simplePaginate(20);
    }
}
