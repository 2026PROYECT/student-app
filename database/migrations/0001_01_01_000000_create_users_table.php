<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // bigint(20)

            $table->string('name', 255);
            $table->string('lastname', 255)->nullable();
            $table->string('surname', 255)->nullable();

            $table->string('email', 255)->unique();
            $table->string('password', 255);

            $table->enum('role', ['admin', 'student'])->default('student');

            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(true);

            $table->string('picture', 255)->nullable();
            $table->string('saga_code', 255)->nullable();
            $table->string('id_number', 255)->nullable();
            $table->string('career_id', 255)->nullable();
            $table->integer('semester')->nullable();

            $table->rememberToken(); // varchar(100)
            $table->timestamps();    // created_at, updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};

