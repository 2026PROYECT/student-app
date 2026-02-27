<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('quiz_assignments', function (Blueprint $table) {
    $table->id();
    // Reference users table, since students are stored there
    $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
    $table->boolean('active')->default(true);
    $table->timestamps();

    // Prevent duplicate assignment per student
    $table->unique('student_id');
});

    }

    public function down(): void {
        Schema::dropIfExists('quiz_assignments');
    }
};
