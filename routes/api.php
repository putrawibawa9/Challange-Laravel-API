<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Admin
Route::post('/v1/register/admin', [\App\Http\Controllers\AuthController::class, 'registerAdmin']);
Route::post('/v1/login/admin', [\App\Http\Controllers\AuthController::class, 'loginAdmin']);

// User
Route::post('/v1/register/user', [\App\Http\Controllers\AuthController::class, 'registerUser']);
Route::post('/v1/login/user', [\App\Http\Controllers\AuthController::class, 'loginUser']);


// Products Resource
Route::apiResource('/v1/products', \App\Http\Controllers\ProductController::class)->middleware('auth:sanctum');

// Categories Resource
Route::apiResource('/v1/categories', \App\Http\Controllers\CategoryController::class)->middleware('auth:sanctum');

// Make Order
Route::post('/v1/orders', [\App\Http\Controllers\OrderController::class, 'buy'])->middleware('auth:sanctum');
// Get All Orders
Route::get('/v1/orders', [\App\Http\Controllers\OrderController::class, 'index'])->middleware('auth:sanctum');
// Get Single Order
Route::get('/v1/orders/{id}', [\App\Http\Controllers\OrderController::class, 'show'])->middleware('auth:sanctum');

// Revoking Tokens
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
});
