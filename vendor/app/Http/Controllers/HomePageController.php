<?php

namespace App\Http\Controllers;

use App\Actions\HomePage\HomePageSeeAllAction;
use App\Actions\HomePage\LoadHomePageAction;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function load(Request $request, LoadHomePageAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function seeAll(Section $section, HomePageSeeAllAction $action): JsonResponse
    {
        return $action->handle($section);
    }
}
