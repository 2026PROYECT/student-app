<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;

class QuizAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Obtenemos IDs de usuarios (estudiantes) y quizzes
        $studentIds = User::pluck('id');
        $quizIds = Quiz::pluck('id');

        foreach ($studentIds as $studentId) {
            // Asignamos 2 quizzes aleatorios a cada estudiante
            $randomQuizzes = $quizIds->random(min(2, $quizIds->count()));

            foreach ($randomQuizzes as $quizId) {
                DB::table('quiz_assignments')->insertOrIgnore([
                    'student_id' => $studentId,
                   
                    'active'     => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}