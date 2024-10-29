<?php

namespace App\Http\Actions;

use App\Http\Requests\DataBundlePackagesRequest;
use App\Http\Services\HubtelService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class GetDataBundlePackagesAction
{
    public function handle(DataBundlePackagesRequest $request): JsonResponse
    {
        try{
            if($response = HubtelService::getDataBundlePackages($request->validated('accountNumber'), $request->validated('rSwitch'))){
                logger($response);
                if(isset($response->json()['ResponseCode']) && $response->json()['ResponseCode'] === '0000')
                    return successfulJsonResponse($response->json()['Data'], 'Data Bundle Packages');

                return errorJsonResponse(errors: [$response->json()['Message']]);
            }
        }catch(Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
