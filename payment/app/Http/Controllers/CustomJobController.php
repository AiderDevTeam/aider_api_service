<?php

namespace App\Http\Controllers;

use App\Jobs\CustomJob;
use Illuminate\Http\Request;

class CustomJobController extends Controller
{
    public function __invoke(Request $request): void
    {
        (new $request->name)->dispatch();
    }
}
