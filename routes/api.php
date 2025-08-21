<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\UserController;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/self', [AuthController::class, 'self']);

    Route::middleware('is.admin')->group(function () {
        Route::post('/auth/register', [AuthController::class, 'register']);
        Route::patch('/courses/{course}/modules/reorder', [ModuleController::class, 'reorder'])
              ->name('courses.modules.reorder');
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('users', UserController::class)->except(['store']); 
        Route::apiResource('courses.modules', ModuleController::class)->shallow();
        Route::post('/users/{user}/balance', [UserController::class, 'addBalance'])
              ->name('users.balance');
    });
});

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Grocademy API!',
        'status' => 'OK'
    ]);
});