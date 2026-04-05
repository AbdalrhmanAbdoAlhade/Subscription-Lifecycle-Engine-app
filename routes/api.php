<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Auth Section
    |--------------------------------------------------------------------------
    */
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Plans Section (Read Only for Users)
    |--------------------------------------------------------------------------
    */
    Route::get('/plans', [PlanController::class, 'index']);
    Route::get('/plans/{id}', [PlanController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Subscriptions Section
    |--------------------------------------------------------------------------
    */
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Payments Section
    |--------------------------------------------------------------------------
    */
    Route::post('/subscriptions/{id}/pay', [PaymentController::class, 'pay']);
    Route::post('/subscriptions/{id}/fail', [PaymentController::class, 'fail']);

    /*
    |--------------------------------------------------------------------------
    | Admin Only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {

        // Plan Management
        Route::post('/plans', [PlanController::class, 'store']);
        Route::put('/plans/{id}', [PlanController::class, 'update']);
        Route::delete('/plans/{id}', [PlanController::class, 'destroy']);

        // Subscription Management
        Route::post('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel']);

    });

});