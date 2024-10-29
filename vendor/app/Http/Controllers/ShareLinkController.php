<?php

namespace App\Http\Controllers;

use App\Actions\GenerateShareLinkAction;
use App\Http\Requests\GenerateShareLinkRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShareLinkController extends Controller
{
    public function generateLink(GenerateShareLinkRequest $request, GenerateShareLinkAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
