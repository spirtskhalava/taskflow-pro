<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\ProjectMemberController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::put('password', [AuthController::class, 'updatePassword']);
    });

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::patch('projects/{project}/archive', [ProjectController::class, 'archive']);
    Route::patch('projects/{project}/restore', [ProjectController::class, 'restore']);

    // Project Members
    Route::prefix('projects/{project}/members')->group(function () {
        Route::get('/', [ProjectMemberController::class, 'index']);
        Route::post('invite', [ProjectMemberController::class, 'invite']);
        Route::patch('{member}/role', [ProjectMemberController::class, 'updateRole']);
        Route::delete('{member}', [ProjectMemberController::class, 'remove']);
    });

    // Tasks
    Route::get('projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('projects/{project}/tasks', [TaskController::class, 'store']);
    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::put('tasks/{task}', [TaskController::class, 'update']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::patch('tasks/{task}/assignee', [TaskController::class, 'updateAssignee']);
    Route::post('tasks/{task}/attachments', [TaskController::class, 'uploadAttachment']);

    // Comments
    Route::get('tasks/{task}/comments', [CommentController::class, 'index']);
    Route::post('tasks/{task}/comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
