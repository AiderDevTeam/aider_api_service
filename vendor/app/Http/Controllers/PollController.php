<?php

namespace App\Http\Controllers;

use App\Actions\Polls\StorePollsAction;
use App\Http\Requests\PollsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PollController extends Controller
{
    public function polls(Request $request, StorePollsAction $action, PollsRequest $pollsRequest): JsonResponse
    {
        return $action->handle($request, $pollsRequest);
    }
}
