<?php

namespace App\Http\Controllers;

use App\Http\Actions\GetDataBundlePackagesAction;
use App\Http\Requests\DataBundlePackagesRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataBundlePackageController extends Controller
{
    public function dataBundlePackages(DataBundlePackagesRequest $request, GetDataBundlePackagesAction $action): JsonResponse
    {
        return $action->handle($request);
    }
}
