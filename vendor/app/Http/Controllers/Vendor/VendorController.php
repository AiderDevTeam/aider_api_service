<?php

namespace App\Http\Controllers\Vendor;

use App\Actions\Custom\SetupColorboxSubShopsAction;
use App\Actions\Vendor\CheckShopTagExistenceAction;
use App\Actions\Vendor\CheckVendorShopTagAction;
use App\Actions\Vendor\ListVendorsAction;
use App\Actions\Vendor\SetUpPersonalShopAction;
use App\Actions\Vendor\ShowVendorProfileAction;
use App\Actions\Vendor\StoreVendorAction;
use App\Actions\Vendor\UpdateClosetShopTagAction;
use App\Actions\Vendor\UpdateVendorAction;
use App\Actions\Vendor\V2\CreateShopAction;
use App\Console\Commands\ImageDownloadCron;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckVendorShopTagRequest;
use App\Http\Requests\GetVendorRequest;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateClosetShopTagRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(ListVendorsAction $action): JsonResponse
    {
        return $action->handle();
    }

    public function store(Request $authRequest, StoreVendorAction $action, StoreVendorRequest $vendorRequest): \Illuminate\Http\JsonResponse
    {
        return $action->handle($authRequest, $vendorRequest);
    }

    public function checkShopTag(CheckVendorShopTagAction $action, CheckVendorShopTagRequest $request): JsonResponse
    {
        return $action->handle($request);
    }

    public function update(Vendor $vendor, UpdateVendorAction $action, UpdateVendorRequest $vendorRequest): JsonResponse
    {
        return $action->handle($vendor, $vendorRequest);
    }

    public function show(Request $request, ShowVendorProfileAction $getVendorAction): JsonResponse
    {
        return $getVendorAction->handle($request);
    }

    public function downloadAndStoreImage(Request $request)
    {
        return (new ImageDownloadCron)->runProcForProductPhotos();
    }

    public function checkShopTagExistence(Request $request, CheckShopTagExistenceAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function updateClosetShopTag(string $shopTag, UpdateClosetShopTagRequest $request, UpdateClosetShopTagAction $action): JsonResponse
    {
        return $action->handle($shopTag, $request);
    }

    public function setupColorboxSubShops(Request $request, SetupColorboxSubShopsAction $action): JsonResponse
    {
        return $action->handle($request);
    }

    public function createShop(Request $request, StoreVendorRequest $storeVendorRequest, CreateShopAction $action): JsonResponse
    {
        return $action->handle($request, $storeVendorRequest);
    }
}
