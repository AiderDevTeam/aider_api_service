<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DisbursementController;
use Illuminate\Support\Facades\Route;

//Route::post('collection', CollectionController::class);
Route::post('disbursement', DisbursementController::class);
