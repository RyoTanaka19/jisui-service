<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

// 認証不要
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Googleログイン
Route::get('/auth/google',          [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// 投稿一覧・詳細は認証不要
Route::get('/posts',        [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

// コメント一覧は認証不要
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

// いいね状態の取得は認証不要
Route::get('/posts/{post}/likes', [LikeController::class, 'index']);

// 認証必要
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // 投稿CRUD
    Route::post('/posts',          [PostController::class, 'store']);
    Route::put('/posts/{post}',    [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // 画像アップロード
    Route::post('/upload', [ImageController::class, 'upload']);

    // コメント
    Route::post('/posts/{post}/comments',             [CommentController::class, 'store']);
    Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy']);

    // いいね
    Route::post('/posts/{post}/likes', [LikeController::class, 'toggle']);
});