<?php

namespace App\Actions\Cart;

use App\Http\Requests\DeleteCartRequest;
use App\Http\Resources\CartResource;
use App\Http\Services\ManuallySyncService;
use App\Jobs\DeleteCartJob;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;

class DeleteCartAction
{
    public function handle(DeleteCartRequest $request): JsonResponse
    {
        try {
            Cart::whereIn('external_id', $request['cartExternalIds'])->delete();

            DeleteCartJob::dispatch($request['cartExternalIds']);

            return successfulJsonResponse(data: [],message: 'Cart Deleted Successfully', statusCode: 204);
        } catch (\Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }

}
