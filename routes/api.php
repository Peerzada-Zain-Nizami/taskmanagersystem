<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {

    // 🚀 Admin-Only Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);

        // User Management
        Route::get('/users', [UserController::class, 'getUsers']);
        Route::post('/approve-user/{id}', [UserController::class, 'approveUser']);
        Route::delete('/destroy-user/{id}', [UserController::class, 'destroyUser']);

        // Category Management
        Route::resource('/categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

        // Task Management
        Route::get('/tasks', [TaskController::class, 'adminIndex']);
        Route::post('/tasks/{task}/comment', [TaskCommentController::class, 'store']);
        Route::get('/tasks/{task}/comment', [TaskCommentController::class, 'index']);

        //stats
        Route::get('/stats', [StatsController::class, 'index']);
    });

    // 🚀 User-Only Routes
    Route::middleware('user')->prefix('user')->group(function () {
        Route::resource('/tasks', TaskController::class)->only(['store', 'update', 'show', 'destroy']);
        Route::get('/tasks/user', [TaskController::class, 'index']);
        Route::get('/tasks/{task}/comments', [TaskCommentController::class, 'index']);
    });
    // ✅ Logout Route (Common for all authenticated users)
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
