<?php

use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::post('/products', [ProductController::class, 'store']);
Route::patch('/products/{id}', [ProductController::class, 'update']);

Route::get('/cart_items', [CartItemController::class, 'index']);
Route::get('/cart_items/{id}', [CartItemController::class, 'show']);
Route::delete('/cart_items/{id}', [CartItemController::class, 'destroy']);
Route::post('/cart_items/', [CartItemController::class, 'store']);
