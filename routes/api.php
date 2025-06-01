<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ParentStudentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/**
 * API Routes
 *
 * Here is where you can register API routes for your application.
 * These routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 */
// API Versioning
Route::prefix('v1')->group(function () {
    // Define your API routes here
    // public routes
    Route::get('/', function () {
        return response()->json(['message' => 'welcome to the API! v1']);
    });
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/user', [AuthController::class, 'user']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('classrooms', ClassroomController::class);
        Route::apiResource('attendances', AttendanceController::class);
        Route::apiResource('days', DayController::class);
        Route::apiResource('events', EventController::class);
        Route::apiResource('homeworks', HomeworkController::class);
        Route::apiResource('lessons', LessonController::class);
        Route::apiResource('parents', ParentStudentController::class);
        Route::apiResource('schedules', ScheduleController::class)->except(['index']);
        Route::apiResource('students', StudentController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('teachers', TeacherController::class);
        // Chat Routes
        Route::prefix('chat')->group(function () {

            // Get all conversations for authenticated user
            Route::get('/conversations', [ChatController::class, 'getConversations']);

            // Start a new conversation
            Route::post('/conversations', [ChatController::class, 'startConversation']);

            // Get messages for a specific conversation
            Route::get('/conversations/{conversationId}/messages', [ChatController::class, 'getMessages']);

            // Send a message in a conversation
            Route::post('/conversations/{conversationId}/messages', [ChatController::class, 'sendMessage']);

            // Mark messages as read in a conversation
            Route::patch('/conversations/{conversationId}/read', [ChatController::class, 'markAsRead']);

            // Delete a specific message
            Route::delete('/messages/{messageId}', [ChatController::class, 'deleteMessage']);

            // Get total unread messages count
            Route::get('/unread-count/{conversationId}', [ChatController::class, 'getUnreadCount']);

        });
    });
});
