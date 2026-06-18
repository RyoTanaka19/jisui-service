<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// 認証不要
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Googleログイン
Route::get('/auth/google',          [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// パスワードリセット
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password',  [PasswordResetController::class, 'resetPassword']);

// 投稿一覧・詳細は認証不要
Route::get('/posts',        [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

// コメント一覧は認証不要
Route::get('/posts/{post}/comments', [CommentController::class, 'index']);

// いいね状態の取得は認証不要
Route::get('/posts/{post}/likes', [LikeController::class, 'index']);

// イベント一覧・詳細は認証不要
Route::get('/events',         [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);

// 認証必要
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // プロフィール
    Route::get('/profile',         [ProfileController::class, 'show']);
    Route::put('/profile',         [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);

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

    // イベント管理（管理者のみ）
    Route::post('/events',           [EventController::class, 'store']);
    Route::put('/events/{event}',    [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);

    // イベント参加
    Route::post('/events/{event}/join', [EventController::class, 'join']);
    Route::get('/events/joined',        [EventController::class, 'joined']);

    // 管理者画面
    Route::get('/admin/users',                      [AdminController::class, 'users']);
    Route::post('/admin/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin']);

    // 通知
    Route::get('/notifications',              [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/mark-all',    [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);

    // メッセージ
    Route::get('/messages',              [MessageController::class, 'index']);
    Route::post('/messages',             [MessageController::class, 'store']);
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead']);

    // 管理者申請
    Route::post('/admin-request', [MessageController::class, 'requestAdmin']);
});