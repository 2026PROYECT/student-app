<?php

use App\Models\Quiz;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();

            // Foreign key to quizzes table
            $table->foreignIdFor(Quiz::class)
                  ->constrained()
                  ->cascadeOnDelete();

            // Questions
            $table->text('question1')->nullable();
            $table->text('question2')->nullable();
            $table->text('question3')->nullable();

            // Media paths
            $table->string('picture', 255)->nullable();
            $table->string('sound', 255)->nullable();

            // Options
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');

            // Correct answer (A/B/C/D)
            $table->string('right_answer', 1);

            // Area (subject area, e.g. Math, Science)
            $table->string('area', 10);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('question_banks');
    }
};


