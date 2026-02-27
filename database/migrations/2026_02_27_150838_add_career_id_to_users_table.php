<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
    // Only add career_id if it doesn't exist yet
    if (!Schema::hasColumn('users', 'career_id')) {
        $table->foreignId('career_id')
              ->constrained('careers', 'id_career')
              ->onDelete('cascade');
    }
});


    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['career_id']);
            $table->dropColumn('career_id');

            // Restore old career column if needed
            $table->string('career')->nullable();
        });
    }
};

