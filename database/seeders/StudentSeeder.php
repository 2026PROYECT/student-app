<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Career;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the User (Account/Identity)
        $user = User::create([
            'name'      => 'John',
            'lastname'  => 'Doe',
            'surname'   => 'Smith',
            'email'     => 'student@test.com',
            'password'  => Hash::make('pass1234'),
            'role'      => 'student',
            'picture'   => null,
        ]);

        // 2. Find a Career ID (Make sure CareerSeeder has run first!)
        $career = Career::where('name', 'Computer Science')->first();

        // 3. Create the Student Profile (Academic Data)
        // This links to the user we just created via user_id
        Student::create([
            'user_id'   => $user->id,
            'career_id' => $career ? $career->id : 1, // Fallback to ID 1 if CS not found
            'saga_code' => 'SAGA123',
            'id_number' => 'STU001',
            'semester'  => 1,
        ]);
    }
}