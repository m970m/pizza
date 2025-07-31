<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/products', ProductController::class)->only('index', 'show');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('role:customer')->group(function() {
        Route::prefix('cart')->group(function() {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/', [CartController::class, 'store']);
            Route::delete('/{id}', [CartController::class, 'remove']);
            Route::delete('/{id}/all', [CartController::class, 'removeAll']);
            Route::put('/', [CartController::class, 'update']);
        });
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
    });


    Route::middleware('role:admin')->group(function() {
        Route::apiResource('/products', ProductController::class)->except('index', 'show');
        Route::patch('/orders/{order}', [OrderController::class, 'update']);
        Route::get('/orders/all', [OrderController::class, 'getAllOrders']);
    });
});
