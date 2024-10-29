<?php

namespace App\Actions\SubCategory;

use App\Http\Requests\UserSubCategoryRequest;
use App\Models\User;
use Illuminate\Http\Request;

class StoreUserSubCategoryAction
{
    public function handle(Request $request, UserSubCategoryRequest $userSubCategoryRequest)
    {
        try {
            if (isset($request->user['externalId'])) {

                $user = User::authUser($request->user);

                $user->subcategories()->syncWithoutDetaching($userSubCategoryRequest['subCategoryIds']);

                return successfulJsonResponse(
                    data: [],
                    message: 'User Subcategories successfully created',
                    statusCode: 201
                );
            }

            return errorJsonResponse(
                message: 'User Authentication failed',
                statusCode: 401
            );

        } catch (Exception $exception){
            report($exception);
        }
        return errorJsonResponse();

    }
}
