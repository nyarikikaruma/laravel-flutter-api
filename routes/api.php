<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    // User routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update', [AuthController::class, 'update']);
    Route::get('/user', [AuthController::class, 'user']);

    /**
     * Posts routes
     */
    Route::prefix('post')->group(function () {
        Route::get('/', [PostController::class, 'index']);
        Route::get('/{id}', [PostController::class, 'show']);
        Route::post('/', [PostController::class, 'store']);
        Route::post('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });

    /**
     * Comments routes
     */
    Route::prefix('comment')->group(function () {
        Route::get('/{id}', [CommentController::class, 'index']);
        Route::post('/{id}', [CommentController::class, 'store']);
        Route::post('/update/{id}', [CommentController::class, 'update']);
        Route::delete('/{id}', [CommentController::class, 'destroy']);
    });

    /**
     * Likes routes
     */
    Route::prefix('like')->group(function () {
        Route::post('/{id}', [LikeController::class, 'likeorunlike']);
    });
});
