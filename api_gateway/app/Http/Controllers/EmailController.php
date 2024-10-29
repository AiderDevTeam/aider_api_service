<?php

namespace App\Http\Controllers;

use App\Http\Actions\EmailAction;
use App\Http\Requests\EmailRequest;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    public function __invoke(EmailRequest $request, EmailAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
