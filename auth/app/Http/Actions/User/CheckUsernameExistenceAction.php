<?php

namespace App\Http\Actions\User;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckUsernameExistenceAction
{
    public function handle(Request $request)
    {
        try{
            logger('### CHECKING USERNAME EXISTENCE ###');
            if(!$request->filled('username'))
                return errorJsonResponse(message:'username is required', statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            if(User::where('username', $request->username)->exists())
                return errorJsonResponse(message:'username already taken', statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

            return successfulJsonResponse([]);

        }catch(Exception $exception){
            report($exception);
        }
        return errorJsonResponse();
    }
}
