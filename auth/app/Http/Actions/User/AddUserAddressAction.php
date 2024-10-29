<?php

namespace App\Http\Actions\User;

use App\Http\Requests\AddAddressRequest;
use App\Http\Resources\AddressResource;
use Exception;
use Illuminate\Http\JsonResponse;

class AddUserAddressAction
{
    public function handle(AddAddressRequest $request): JsonResponse
    {
        try {
            logger('### CREATING ADDRESS FOR USER ###');
            logger($request->validated());
            $user = auth()->user();

            if ($request->validated('default')) $user->addresses()->update(['default' => false]);

            $address = $user->addresses()->create([
                'external_id' => uniqid('AD'),
                ...arrayKeyToSnakeCase($request->validated()),
                'city' => $request->validated('city') ?? $request->validated('destinationName'),
            ]);

            return successfulJsonResponse(data: new AddressResource($address));

        } catch (Exception $exception) {
            report($exception);
        }
        return errorJsonResponse();
    }
}
