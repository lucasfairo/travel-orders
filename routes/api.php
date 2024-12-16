<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TravelOrderController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/travel-orders', [TravelOrderController::class, 'store']);
    Route::put('/travel-orders/{id}', [TravelOrderController::class, 'update']);
    Route::get('/travel-orders/{id}', [TravelOrderController::class, 'show']);
    Route::get('/travel-orders', [TravelOrderController::class, 'index']);
    Route::post('/travel-orders/{id}/notify', [TravelOrderController::class, 'notify']);
});

