<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\QuizController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\QuizAssignmentController;
use App\Http\Controllers\Api\V1\QuestionResultController;
use App\Http\Controllers\Api\V1\StudentQuizController;

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {


Route::get('reports/students', [ReportController::class, 'exportStudents']);
    // --- RUTAS PÚBLICAS ---
    Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

    // --- RUTAS PROTEGIDAS (Sanctum) ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('careers', [App\Http\Controllers\Api\V1\CareerController::class, 'index']);

        // Info del usuario actual
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::post('/logout', [AuthController::class, 'destroy']);

        // --- ADMINISTRACIÓN (CRUDs) ---
        Route::apiResource('students', UserController::class);
        Route::apiResource('quizzes', QuizController::class);
        Route::apiResource('quiz-assignments', QuizAssignmentController::class);
        
        // Configuración de preguntas (Ruta específica antes del resource)
        Route::get('questions/all', [QuestionController::class, 'all']);
        Route::apiResource('questions', QuestionController::class);

        // --- CATÁLOGOS ---
        
       // });

        // --- PORTAL DEL ESTUDIANTE (Exámenes) ---
        Route::prefix('student')->group(function () {
            Route::get('/check-status', [StudentQuizController::class, 'checkStatus']);
            Route::post('/generate-quiz', [StudentQuizController::class, 'generateQuiz']);
            
            // Acciones de examen y resultados
            Route::post('/attend/{quizId}', [QuestionResultController::class, 'attendQuiz']);
            Route::get('/results/{quizId}', [QuestionResultController::class, 'getResults']);
        });

    });
});