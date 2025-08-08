<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/user/login', [AuthController::class, 'userLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/posts', PostController::class);
});