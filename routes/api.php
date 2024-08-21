<?php

use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthAdminController;



// Public Routes
Route::prefix('v1')->group(function () {
    Route::post('/register/user', [AuthUserController::class, 'register']);
    Route::post('/login/user', [AuthUserController::class, 'login']);
    Route::post('/register/admin', [AuthAdminController::class, 'register']);
    Route::post('/login/admin', [AuthAdminController::class, 'login']);
    Route::get('/ratings/{productId}', [RatingController::class, 'index']);
    Route::get('/ratings/avg/{productId}', [RatingController::class, 'averageRating']);
    Route::post('/webhook', [PaymentService::class, 'webHook']);
    Route::post('/logout', [AuthUserController::class, 'logout']);
});

// Admin Routes
Route::middleware(['auth:sanctum','abilities:admin'])->prefix('v1')->group(function(){
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

