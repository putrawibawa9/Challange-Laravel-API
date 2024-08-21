<?php

use App\Services\PaymentService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\ProductAdminController;
use App\Http\Controllers\CategoryAdminController;
use App\Http\Controllers\ProductPublicController;
use App\Http\Controllers\CategoryPublicController;



// Public Routes
Route::prefix('v1')->group(function () {
    Route::post('/register/user', [AuthUserController::class, 'register']);
    Route::post('/login/user', [AuthUserController::class, 'login']);
    Route::post('/register/admin', [AuthAdminController::class, 'register']);
    Route::post('/login/admin', [AuthAdminController::class, 'login']);
    Route::get('/public/products', [ProductPublicController::class, 'index']);
    Route::get('/public/products/{slug}', [ProductPublicController::class, 'show']);
    Route::get('/public/ratings/{productId}', [RatingController::class, 'index']);
    Route::get('/public/ratings/avg/{productId}', [RatingController::class, 'averageRating']);
    Route::get('/public/categories', [CategoryPublicController::class, 'index']);
    Route::get('/public/categories/{slug}', [CategoryPublicController::class, 'show']);
    Route::post('/webhook', [PaymentService::class, 'webHook']);
});

// Admin Routes
Route::middleware(['auth:sanctum','abilities:admin'])->prefix('v1')->group(function(){
    Route::apiResource('/admin/products', ProductAdminController::class);
    Route::apiResource('/admin/categories', CategoryAdminController::class);
});

// User Routes
Route::middleware(['auth:sanctum', 'abilities:user'])->prefix('v1')->group(function(){
    Route::resource('/carts', CartController::class);
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::post('/orders', [OrderController::class, 'buy']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/ratings/{productId}', [RatingController::class, 'index']);
    Route::get('/ratings/avg/{productId}', [RatingController::class, 'averageRating']);
    Route::post('/logout', [AuthUserController::class, 'logout']);
});

