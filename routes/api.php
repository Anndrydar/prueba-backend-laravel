<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductPriceController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\AuthController;

// Ruta publica login
Route::post('/login', [AuthController::class, 'login']);

// Rutas privadas requieren un token
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Productos
    Route::apiResource('products', ProductController::class);

    // Precios de productos
    Route::get('/products/{product}/prices', [ProductPriceController::class, 'index']);
    Route::post('/products/{product}/prices', [ProductPriceController::class, 'store']);

    // Monedas
    Route::get('/currencies', [CurrencyController::class, 'index']);
    Route::post('/currencies', [CurrencyController::class, 'store']);
});