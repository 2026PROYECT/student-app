<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
{
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        // This line creates the column AND the foreign key in one go
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // Make sure 'careers' table exists before this runs!
        $table->foreignId('career_id')->constrained('careers');
        
        $table->string('saga_code')->nullable();
        $table->string('id_number')->nullable();
        $table->integer('semester')->default(1);
        $table->timestamps();
    });
}
    public function down(): void {
        Schema::dropIfExists('students');
    }
};

