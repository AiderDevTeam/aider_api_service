<?php

namespace App\Http\Actions\User;

use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\ProductAddress;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UpdateAddressAction
{
    public function handle(UpdateAddressRequest $request, ProductAddress $address)
    {
        try {
            logger('### UPDATING USER ADDRESS ###');
            logger($request->validated());
            $user = auth()->user();

            DB::beginTransaction();

            if ($request->has('default') && $request->default) $user->addresses()->update(['default' => false]);

            $address->update(arrayKeyToSnakeCase($request->validated()));
            DB::commit();

            return successfulJsonResponse(data: new AddressResource($address));
        } catch (Exception $exception) {
            DB::rollBack();
            report($exception);
        }
        return errorJsonResponse();
    }
}
