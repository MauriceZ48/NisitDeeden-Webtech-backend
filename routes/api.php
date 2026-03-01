<?php

use App\Http\Controllers\Api\ApplicationCategoryController;
use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\ApplicationRoundController;
use App\Http\Controllers\API\Auth\AuthenticateController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['throttle:api'])->as('api.')->group(function () {
    Route::get('/', function () {
        return [
            'version' => '1.0.0',
        ];
    })->name('root');

    Route::post('login', [AuthenticateController::class, 'login'])->name('user.login');

});

// 1. Routes that require NO authentication (Public/Testing)
Route::middleware(['throttle:api'])->as('api.')->group(function () {
    Route::post('login', [AuthenticateController::class, 'login'])->name('user.login');

    //Application
    Route::get('applications/user/{id}', [ApplicationController::class, 'applicationsByUserId']);
    Route::get('applications/head-of-dept', [ApplicationController::class, 'applicationsForHeadOfDepartment']);
    Route::get('applications/associate-dean', [ApplicationController::class, 'applicationsForAssociateDean']);
    Route::get('applications/dean', [ApplicationController::class, 'applicationsForDean']);
    Route::get('applications/committee', [ApplicationController::class, 'applicationsForCommittee']);
    Route::get('applications/approved', [ApplicationController::class, 'applicationsApprovedByCommittee']);
    Route::get('applications/rejected', [ApplicationController::class, 'applicationsRejected']);
    Route::apiResource('applications', ApplicationController::class)
        ->only(['index', 'show'])
        ->withTrashed();
    //Round
    Route::get('/rounds/next-expected', [ApplicationRoundController::class, 'getNextExpectedRound'])
        ->name('rounds.nextExpected');
    Route::apiResource('rounds', ApplicationRoundController::class)
        ->parameters(['rounds' => 'applicationRound']);
    //Category
    Route::patch('/categories/{applicationCategory}/toggle-status', [ApplicationCategoryController::class, 'toggleStatus'])
        ->name('categories.toggleStatus');
    Route::apiResource('categories', ApplicationCategoryController::class)
        ->parameters(['categories' => 'applicationCategory']);
    //User
    Route::apiResource('users', UserController::class);
});

// 2. Routes that REQUIRE authentication
Route::middleware(['auth:sanctum', 'throttle:api'])->as('api.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('me');

    Route::patch('applications/{application}/status', [ApplicationController::class, 'updateStatus']);
    // Only protect the sensitive actions
    Route::apiResource('applications', ApplicationController::class)
        ->only(['store', 'update', 'destroy'])
        ->withTrashed();

    Route::middleware(['ability:ADMIN'])->as('admin.')->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Welcome Admin']);
        })->name('dashboard');
    });
});
