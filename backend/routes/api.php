<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// 認証不要
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// 投稿一覧・詳細は認証不要で見られる
Route::get('/posts',      [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

// 認証必要
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',      [AuthController::class, 'logout']);
    Route::get('/me',           [AuthController::class, 'me']);

    // 投稿の作成・更新・削除
    Route::post('/posts',             [PostController::class, 'store']);
    Route::put('/posts/{post}',       [PostController::class, 'update']);
    Route::delete('/posts/{post}',    [PostController::class, 'destroy']);
});