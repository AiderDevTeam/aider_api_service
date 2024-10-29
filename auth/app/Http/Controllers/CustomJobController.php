<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomJobController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!isset($request->name))
            return errorJsonResponse(errors: ['name required'], statusCode: Response::HTTP_UNPROCESSABLE_ENTITY);

        (new $request->name)->dispatch()->onQueue('high');

        return successfulJsonResponse();
    }
}
