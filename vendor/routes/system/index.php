<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomJobController;
use App\Http\Controllers\PriceStructureController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SubCategoryItemsController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;


Route::put('update-sections-position', [SectionController::class, 'updateSectionPosition']);
Route::apiResource('section', SectionController::class);
Route::apiResource('price-structure', PriceStructureController::class);

Route::apiResource('category', CategoryController::class);
Route::apiResource('sub-category', SubCategoryController::class);
Route::apiResource('sub-category-item', SubCategoryItemsController::class);

Route::apiResource('setting', SettingController::class);

Route::middleware('auth.admin')->group(function () {

    Route::prefix('products')->group(function () {
        Route::get('/', [SuperAdminController::class, 'productsByStatus']);
    });

    Route::prefix('product')->group(function(){
        Route::put('/{product}', [ProductController::class, 'update']);
        Route::post('/reject/{product}', [ProductController::class, 'rejectProduct']);
    });

    Route::prefix('bookings')->group(function () {
        Route::get('/', [SuperAdminController::class, 'getBookings']);
        Route::get('{booking}', [SuperAdminController::class, 'getBooking']);
    });

    Route::get('users/{user}', [SuperAdminController::class, 'getUser']);

    Route::prefix('reports')->group(function () {
        Route::get('/', [SuperAdminController::class, 'getReports']);
        Route::put('/resolve/{report}', [SuperAdminController::class, 'resolveReport']);
    });
});

Route::post('run-custom-job', CustomJobController::class);
