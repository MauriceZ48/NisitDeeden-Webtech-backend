<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {

    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    Route::apiResource('users', UserController::class);

});
