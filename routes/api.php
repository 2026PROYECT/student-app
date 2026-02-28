<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\QuizController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\QuizAssignmentController;
use App\Http\Controllers\Api\V1\QuestionResultController;
use App\Http\Controllers\Api\V1\CareerController;
use App\Http\Controllers\Api\V1\TestController;
use App\Http\Controllers\Api\V1\TestAssignmentController;
use App\Http\Controllers\Api\V1\ResponseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// User info route
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// v1 Public Routes
Route::prefix('v1')->middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// v1 JWT Protected Routes (if still using Passport/JWT for some parts)
Route::prefix('v1')->middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy']);
    Route::apiResource('quizzes', QuizController::class);
    Route::apiResource('questions', QuestionController::class);
});

// SPA -- Sanctum Token Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    // Students
    Route::apiResource('students', UserController::class);
    Route::get('/careers', [CareerController::class, 'index']);
    Route::apiResource('users', UserController::class);

    // v1 protected routes


Route::get('/students', [UserController::class, 'students']);

    Route::prefix('v1')->group(function () {
        // Tests
        Route::apiResource('tests', TestController::class);

        // ✅ Full CRUD for Test Assignments
        Route::apiResource('assignments', TestAssignmentController::class);

        // Responses
        Route::apiResource('responses', ResponseController::class);
    });

    // Quiz Assignments
    Route::apiResource('quiz-assignments', QuizAssignmentController::class);

    // Quizzes & Questions
    Route::apiResource('quizzes', QuizController::class);
    Route::get('questions/all', [QuestionController::class, 'all']);
    Route::apiResource('questions', QuestionController::class);

    // Results & Attendance
    Route::post('/attend/{quizId}', [QuestionResultController::class, 'attendQuiz']);
    Route::get('/results/{quizId}', [QuestionResultController::class, 'getResults']);
});
