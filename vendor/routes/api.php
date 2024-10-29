<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.user')->group(function () {

    Route::apiResource('product', ProductController::class);

    Route::prefix('product')->group(function () {
        Route::delete('delete-image/{product}', [ProductController::class, 'deleteProductImage']);
        Route::post('add-image/{product}', [ProductController::class, 'addProductImage']);
        Route::delete('delete-price/{product}', [ProductController::class, 'deletePrice']);
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'getProductsByVendor']);
        Route::get('/category/{category}', [ProductController::class, 'getProductsByCategory']);
        Route::post('/filter', [ProductController::class, 'filterProducts']);
    });

    Route::prefix('homepage')->group(function () {
        Route::get('load', [HomePageController::class, 'load']);
        Route::get('see-all/{section}', [HomePageController::class, 'seeAll']);
    });

    Route::prefix('booking')->group(function () {
        Route::post('create/{product}', [BookingController::class, 'createBooking']);
        Route::post('confirm/{booking}', [BookingController::class, 'confirmBooking']);
        Route::post('confirm-pickup/{booking}', [BookingController::class, 'confirmPickup']);
        Route::post('confirm-dropoff/{booking}', [BookingController::class, 'confirmDropOff']);
        Route::put('return-early/{bookingProduct}', [BookingController::class, 'confirmEarlyReturn']);
    });

    Route::get('bookings', [BookingController::class, 'bookings']);

    Route::prefix('conversation')->group(function () {
        Route::get('/{conversation}', [ConversationController::class, 'show']);
        Route::post('send-message/{conversation}', [ConversationController::class, 'sendMessage']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'getAuthUser']);
        Route::get('products/{user}', [ProductController::class, 'getUserProducts']);
    });

    Route::prefix('payment')->group(function () {
        Route::post('collect/{booking}', [PaymentController::class, 'collect']);
    });

    Route::prefix('search')->group(function () {
        Route::post('/', [SearchController::class, 'searchAll']);
        Route::post('/products', [SearchController::class, 'searchProduct']);
        Route::post('/profiles', [SearchController::class, 'searchProfile']);
    });

    Route::prefix('review')->group(function () {
        Route::post('/{bookingProduct}', [ReviewController::class, 'review']);
        Route::put('/{review}', [ReviewController::class, 'reviewUpdate']);
        Route::get('/product-reviews/{product}', [ReviewController::class, 'productReviews']);
        Route::get('/vendor-reviews/{user}', [ReviewController::class, 'vendorReviews']);
        Route::get('/vendor-products-reviews/{user}', [ReviewController::class, 'vendorProductReviews']);
        Route::get('/renter-reviews/{user}', [ReviewController::class, 'renterReviews']);
    });

    Route::post('report/{user}', [ReportController::class, 'report']);

});
