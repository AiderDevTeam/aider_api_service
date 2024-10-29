<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomJobController;
use App\Http\Controllers\DiscoveryPageController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductLikeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShareLinkController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SyncModelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\WeightUnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/products', function (Request $request) {
    $user = $request->user;
    return $user['externalId'];
})->middleware('auth.user');

Route::get('products', [ProductController::class, 'index']);
Route::get('product/sub-categories/{category}', [ProductController::class, 'getSubCategories']);

Route::get('products/see-all/{category}', [ProductController::class, 'getProductsByCategory']);

Route::get('photo/download', [VendorController::class, 'downloadAndStoreImage']);

Route::middleware('auth.user')->group(function () {
    Route::post('store', [VendorController::class, 'store']);
    Route::get('vendors', [VendorController::class, 'index']);
    Route::put('vendors/{vendor}', [VendorController::class, 'update']);
    Route::get('show-user-shops', [VendorController::class, 'show']);


    Route::post('polls', [PollController::class, 'polls']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::put('categories/{category}', [CategoryController::class, 'update']);

    Route::post('sub-category', [SubCategoryController::class, 'store']);
    Route::get('sub-categories', [SubCategoryController::class, 'index']);
    Route::post('user-sub-category', [SubCategoryController::class, 'storeUserCategory']);

    Route::post('product/delete/{product}', [ProductController::class, 'destroy']);
    Route::apiResource('product', ProductController::class)->only('store', 'update', 'show');
    Route::post('rejection-reason', [ProductController::class, 'rejectionReason']);

    Route::post('add-to-cart', [CartController::class, 'addProducts']);
    Route::put('carts/{cart}', [CartController::class, 'quantity']);
    Route::post('carts/delete', [CartController::class, 'delete']);
    Route::get('carts', [CartController::class, 'list']);

    Route::post('checkout', [OrderController::class, 'store']);
    Route::put('order', [OrderController::class, 'update']);
    Route::put('delivery', [OrderController::class, 'delivery']);

    Route::post('product-like/{product}', ProductLikeController::class);

    Route::post('get-express-delivery-fee', [OrderController::class, 'getWegooExpressDeliveryFee']);

    Route::post('create-delivery-fees', [OrderController::class, 'createDeliveryFee']);
    Route::get('get-delivery-fees', [OrderController::class, 'getDeliveryFee']);
    Route::put('update-delivery-fees/{deliveryFee}', [OrderController::class, 'updateDeliveryFee']);

    Route::post('accept-order', [OrderController::class, 'acceptOrder']);

    Route::prefix('search')->group(function () {
        Route::post('products', [SearchController::class, 'searchProduct']);
        Route::post('shops', [SearchController::class, 'searchVendor']);
        Route::post('all', [SearchController::class, 'searchAll']); //vendors(shops) & products search
    });

    Route::post('report', [ReportController::class, 'report']);

    Route::prefix('products')->group(function () {
        Route::get('/{vendor}', [ProductController::class, 'getProductsByVendor']);
        Route::get('/sub-category/{subCategory}', [ProductController::class, 'getProductsBySubCategory']);
        Route::get('/vendor-type/{vendorType}', [ProductController::class, 'getProductsByTag']);//remove after release
        Route::get('/tag/{tag}', [ProductController::class, 'getProductsByTag']);
    });

    Route::post('generate-share-link', [ShareLinkController::class, 'generateLink']);

    Route::prefix('homepage')->group(function () {
        Route::get('load', [HomePageController::class, 'load']);
        Route::get('see-all/{section}', [HomePageController::class, 'seeAll']);
    });

    Route::prefix('discovery-page')->group(function () {
        Route::get('load', [DiscoveryPageController::class, 'load']);
        Route::get('see-all/{section}', [HomePageController::class, 'seeAll']);
    });

    Route::get('/orders/{externalId}/load', [OrderController::class, 'getOrders']);

    Route::get('get-user-shops/{user}', [UserController::class, 'getUserShops']);
    Route::get('get-liked-products/{user}', [UserController::class, 'getLikedProducts']);

    Route::post('/product/add-image/{product}', [ProductController::class, 'addProductImage']);
    Route::post('/product/delete-image/{product}', [ProductController::class, 'deleteProductImage']);
    Route::get('/product/similar-products/{product}', [ProductController::class, 'similarProducts']);

    Route::prefix('review')->group(function () {
        Route::post('product-review/{cart}', [ReviewController::class, 'productReview']);
        Route::put('/{review}', [ReviewController::class, 'reviewUpdate']);
        Route::get('/product-reviews/{product}', [ReviewController::class, 'productReviews']);
        Route::get('/vendor-reviews/{vendor}', [ReviewController::class, 'vendorReviews']);
    });

});

Route::post('check-shop-tag', [VendorController::class, 'checkShopTag']);

Route::apiResource('weight-unit', WeightUnitController::class);
Route::put('update-sub-categories', [SubCategoryController::class, 'update']);

Route::post('manually-sync', [SyncModelController::class, 'sync']);

Route::post('run-custom-job', CustomJobController::class);

Route::post('run-cron-job', [CustomJobController::class, 'testCronJob']);


Route::post('add-size', [SizeController::class, 'store']);

Route::post('manually-sync-individual-models', [SyncModelController::class, 'syncIndividualModels']);

Route::post('check-status', [OrderController::class, 'checkOrderCollectionStatus']);
Route::get('checkout-delivery-fee', [OrderController::class, 'checkoutDelivery']);

Route::post('create-shop', [VendorController::class, 'createShop']);
