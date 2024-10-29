<?php

use App\Http\Controllers\ReferralController;
use Illuminate\Support\Facades\Route;

Route::post('reward-referrer', [ReferralController::class, 'rewardReferrer']);
