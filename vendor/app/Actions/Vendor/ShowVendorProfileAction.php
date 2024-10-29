<?php

namespace App\Actions\Vendor;

use App\Http\Requests\GetVendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\User;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowVendorProfileAction
{
    public function handle(Request $request):JsonResponse
    {
        try {
            $user = User::authUser($request->user);

            logger($user->vendor()->get());
                $user->vendor()->get();

                return successfulJsonResponse(
                    data: VendorResource::collection($user->vendor()->get()),
                    message: 'Vendor Profile'
                );

        } catch (Exception $exception) {
            report($exception);
        }

        return errorJsonResponse();
    }
}
