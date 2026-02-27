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
    
    // ✅ STUDENTS: Using apiResource covers index, store, show, update, destroy
    // This fixed the 500 error and the "Total 0" count
    Route::apiResource('students', UserController::class);
    Route::get('/careers', [CareerController::class, 'index']);
    Route::apiResource('users', UserController::class);


    
    // QUIZ ASSIGNMENTS
    Route::apiResource('quiz-assignments', QuizAssignmentController::class);

    
    // QUIZZES & QUESTIONS
    Route::apiResource('quizzes', QuizController::class);
    
    // For Questions, we use apiResource + the custom "all" route
    Route::get('questions/all', [QuestionController::class, 'all']);
    Route::apiResource('questions', QuestionController::class);

    // RESULTS & ATTENDANCE
    Route::post('/attend/{quizId}', [QuestionResultController::class, 'attendQuiz']);
    Route::get('/results/{quizId}', [QuestionResultController::class, 'getResults']);
});