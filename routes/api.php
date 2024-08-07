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
Route::post('/register/admin', [\App\Http\Controllers\AuthController::class, 'registerAdmin']);
Route::post('/login/admin', [\App\Http\Controllers\AuthController::class, 'loginAdmin']);

// User
Route::post('/register/user', [\App\Http\Controllers\AuthController::class, 'registerUser']);
Route::post('/login/user', [\App\Http\Controllers\AuthController::class, 'loginUser']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
});
