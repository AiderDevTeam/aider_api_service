<?php

namespace App\Actions\Cart;

use App\Http\Resources\CartResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListProductAction
{
    public function handle(Request $request):JsonResponse
    {
        try {
            $user = $request->user;
            if($user = User::findWithExternalId($user['externalId'])){
                $activeCarts = $user->carts->where('is_checked_out', false)->whereNull('deleted_at');
                return successfulJsonResponse(
                    data: CartResource::collection($activeCarts->load('vendor')),
                    message: 'Users Carts List',
                );
            }
            return errorJsonResponse(message: 'user not found', statusCode: 404);
        }catch (Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
