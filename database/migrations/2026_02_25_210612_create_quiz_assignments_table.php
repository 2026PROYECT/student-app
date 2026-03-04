<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('quiz_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
        $table->boolean('active')->default(true); // ✅ control flag
        $table->timestamps();

        $table->unique(['student_id', 'quiz_id']); // one assignment per student per quiz
    });
}


    public function down(): void {
        Schema::dropIfExists('quiz_assignments');
    }
};
