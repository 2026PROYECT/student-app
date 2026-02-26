<?php

use App\Models\Quiz;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Quiz::class)->constrained()->cascadeOnDelete();

            // Questions
            $table->text('question1')->nullable();
            $table->text('question2')->nullable();
             $table->text('question3')->nullable();


            // Media
            $table->string('picture', 255)->nullable(); // path to image
            $table->string('sound', 255)->nullable();   // path to audio file

            // Options (can be text or picture paths)
            $table->text('option_a')->nullable();
            $table->text('option_b')->nullable();
            $table->text('option_c')->nullable();
            $table->text('option_d')->nullable();

            // Correct answer (A/B/C/D)
            $table->string('right_answer', 1);

            // Area (subject area, e.g. M = Math, S = Science)
            $table->string('area', 10)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('question_banks');
    }
};

