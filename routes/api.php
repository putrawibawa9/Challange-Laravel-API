<?php

use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;



// Public Routes
Route::prefix('v1')->group(function () {
    Route::post('/register/user', [AuthController::class, 'registerUser']);
    Route::post('/login/user', [AuthController::class, 'loginUser']);
    Route::post('/register/admin', [AuthController::class, 'registerAdmin']);
    Route::post('/login/admin', [AuthController::class, 'loginAdmin']);
    Route::get('/ratings/{productId}', [RatingController::class, 'index']);
    Route::get('/ratings/avg/{productId}', [RatingController::class, 'averageRating']);
    Route::post('/webhook', [PaymentService::class, 'webHook']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Admin Routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function(){
    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/categories', CategoryController::class);
});

// User Routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function(){
    Route::resource('/carts', CartController::class)->middleware('auth:sanctum');
    Route::post('/ratings', [RatingController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/orders', [OrderController::class, 'buy'])->middleware('auth:sanctum');
    Route::get('/orders', [OrderController::class, 'index'])->middleware('auth:sanctum');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->middleware('auth:sanctum');
});

