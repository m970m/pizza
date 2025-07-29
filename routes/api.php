<?php

use App\Http\Controllers\Api\CartProductController;
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
        Route::apiResource('/cart_product', CartProductController::class)
            ->except('update');
    });

    Route::middleware('role:admin')->group(function() {
        Route::apiResource('/products', ProductController::class);
    });
});
