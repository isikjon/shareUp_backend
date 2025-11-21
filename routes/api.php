<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\User\Controllers\ProfileController;
use App\Modules\Post\Controllers\PostController;
use App\Modules\Like\Controllers\LikeController;
use App\Modules\Comment\Controllers\CommentController;
use App\Modules\Point\Controllers\PointController;
use App\Modules\Admin\Controllers\AdminController;
use App\Http\Controllers\NotificationController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/users/suggestions', [ProfileController::class, 'suggestions']);
    
    Route::prefix('profile')->group(function () {
        Route::get('/{id}', [ProfileController::class, 'show']);
        Route::put('/update', [ProfileController::class, 'update']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
    });

    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index']);
        Route::post('/', [PostController::class, 'store']);
        Route::get('/{id}', [PostController::class, 'show']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
        Route::get('/user/{userId}', [PostController::class, 'userPosts']);
    });

    Route::prefix('likes')->group(function () {
        Route::post('/{postId}', [LikeController::class, 'toggle']);
    });

    Route::prefix('comments')->group(function () {
        Route::get('/post/{postId}', [CommentController::class, 'index']);
        Route::post('/post/{postId}', [CommentController::class, 'store']);
        Route::delete('/{id}', [CommentController::class, 'destroy']);
    });

    Route::prefix('points')->group(function () {
        Route::get('/balance', [PointController::class, 'balance']);
        Route::get('/transactions', [PointController::class, 'transactions']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::get('/unread/count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        
        Route::prefix('users')->group(function () {
            Route::get('/', [AdminController::class, 'users']);
            Route::post('/{userId}/ban', [AdminController::class, 'banUser']);
            Route::post('/{userId}/unban', [AdminController::class, 'unbanUser']);
            Route::delete('/{userId}', [AdminController::class, 'deleteUser']);
            Route::post('/{userId}/points/add', [AdminController::class, 'addPoints']);
            Route::post('/{userId}/points/deduct', [AdminController::class, 'deductPoints']);
        });

        Route::prefix('posts')->group(function () {
            Route::get('/', [AdminController::class, 'posts']);
            Route::delete('/{postId}', [AdminController::class, 'deletePost']);
        });

        Route::get('/logs', [AdminController::class, 'logs']);
    });
});

