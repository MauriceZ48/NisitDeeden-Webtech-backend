<?php

use App\Http\Controllers\Api\ApplicationCategoryController;
use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\ApplicationRoundController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {

    Route::apiResource('categories', ApplicationCategoryController::class)->only(['index', 'show']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('applications', ApplicationController::class);
    Route::apiResource('rounds', ApplicationRoundController::class);

});
