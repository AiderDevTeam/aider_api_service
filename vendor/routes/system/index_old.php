<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomJobController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Vendor\VendorController;

Route::apiResource('product', ProductController::class)->only('store', 'update', 'show');
Route::post('update-order/{order}', [OrderController::class, 'manualOrderUpdate']);
Route::post('check-shop-tag-existence', [VendorController::class, 'checkShopTagExistence']);

Route::put('update-sections-position', [SectionController::class, 'updateSectionPosition']);
Route::apiResource('section', SectionController::class);

Route::get('products/{vendor}', [ProductController::class, 'getProductsByVendor']);
Route::get('vendor-products', [ProductController::class, 'webGetProductsByVendor']);
Route::apiResource('product', ProductController::class)->only('store', 'update', 'show');

Route::post('checkout', [OrderController::class, 'webStore']);
Route::post('add-to-cart', [CartController::class, 'webAddProducts']);

Route::put('update-closet-shoptag/{shopTag}', [VendorController::class, 'updateClosetShopTag']);

Route::put('update-user/{user}', [UserController::class, 'updateUser']);

Route::apiResource('setting', SettingController::class);

Route::prefix('custom')->group(function () {
    Route::post('/setup-colorbox-sub-shops', [VendorController::class, 'setupColorboxSubShops']);
    Route::post('run-add-product-to-colorbox-sub-shops-job/{product}', [CustomJobController::class, 'addNewProductToColorboxSubShops']);
    Route::post('run-update-product-on-colorbox-sub-shops-job/{product}', [CustomJobController::class, 'updateProductOnColorboxSubShops']);
});
