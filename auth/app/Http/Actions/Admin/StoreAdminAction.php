<?php

namespace App\Http\Actions\Admin;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Exception;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreAdminAction
{
    public function handle(StoreAdminRequest $request): JsonResponse
    {
        try{
            logger()->info('### REGISTERING NEW ADMIN ###');
            logger($request->except('password'));

            if($admin = Admin::create(arrayKeyToSnakeCase($request->validated()))){
                $admin->bearer_token = JWTAuth::fromUser($admin);
                return successfulJsonResponse(data: new AdminResource($admin), message: 'Admin Registered');
            }
        }catch(Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
