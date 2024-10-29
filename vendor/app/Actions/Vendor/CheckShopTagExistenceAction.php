<?php

namespace App\Actions\Vendor;

use App\Models\Vendor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function Laravel\Prompts\error;

class CheckShopTagExistenceAction
{
    public function handle(Request $request): JsonResponse
    {
        try{
            logger('### CHECKING SHOP TAG EXISTENCE ###');
            if(!$request->filled('shopTag'))
                return errorJsonResponse(message:'shop tag is required', statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            if(Vendor::where('shop_tag', $request->shopTag)->exists())
                return errorJsonResponse(message:'shop tag already taken', statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            return successfulJsonResponse([]);

        }catch(Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
