<?php

namespace App\Http\Controllers;

use App\Actions\Size\AddSizeAction;
use App\Http\Requests\SizeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function store(AddSizeAction $action, SizeRequest $request):JsonResponse
    {
        return $action->handle($request);
    }
}
