<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PageApiController;
use App\Http\Controllers\Api\V1\PostApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public auth routes
    Route::post('/auth/login', [AuthController::class, 'login']);

    // CMS Public REST API
    Route::get('/posts', [PostApiController::class, 'index']);
    Route::get('/posts/{slug}', [PostApiController::class, 'show']);
    Route::get('/categories/{slug}/posts', [PostApiController::class, 'categoryPosts']);
    Route::get('/tags/{slug}/posts', [PostApiController::class, 'tagPosts']);
    Route::get('/posts/{id}/preview/{token}', [PostApiController::class, 'preview']);
    Route::get('/pages', [PageApiController::class, 'index']);
    Route::get('/pages/{slug}', [PageApiController::class, 'show']);

    // Protected API routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });
});
