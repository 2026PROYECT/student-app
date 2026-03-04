<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentQuizController extends Controller
{
    /**
     * Check if the student is authorized to take an exam.
     */
    public function checkStatus()
    {
        // Use auth('sanctum') to ensure the token is being read correctly
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['active' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            // Check if assignment exists
            $assignment = QuizAssignment::where('student_id', $user->id)
                ->where('active', 1)
                ->first();

            return response()->json([
                'active' => (bool)$assignment,
                'subject' => 'General' 
            ]);
        } catch (\Exception $e) {
            // If it crashes here, it will tell you if 'student_id' column is missing
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * The Randomizer
     */
    public function generateQuiz()
    {
        $user = auth('sanctum')->user();

        if (!$user) {
             return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 1. Verify authorization
        $assignment = QuizAssignment::where('student_id', $user->id)
            ->where('active', 1)
            ->first();

        if (!$assignment) {
            return response()->json(['message' => 'Exam session not active.'], 403);
        }

        // 2. Random Selection
        $quiz = Quiz::inRandomOrder()->first();

        if (!$quiz) {
            return response()->json(['message' => 'No quizzes found in database.'], 404);
        }

        return response()->json([
            'quiz_id' => $quiz->id,
            'title' => $quiz->title
        ]);
    }
}