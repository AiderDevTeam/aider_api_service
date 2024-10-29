<?php

namespace App\Http\Controllers;

use App\Actions\LoadDiscoveryPageAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscoveryPageController extends Controller
{
    public function load(Request $request, LoadDiscoveryPageAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
