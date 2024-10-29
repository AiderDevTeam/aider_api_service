<?php

namespace App\Http\Controllers;

use App\Http\Actions\ListBanksAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function __invoke(Request $request, ListBanksAction $action): JsonResponse
    {
        return $action->handle();
    }
}
