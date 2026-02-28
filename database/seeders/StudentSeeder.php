<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'           => 'John',
            'lastname'       => 'Doe',
            'surname'        => 'Smith',
            'email'          => 'student@test.com',
            'password'       => Hash::make('pass1234'),
            'role'           => 'student',
            'is_admin'       => 0,
            'is_active'      => 1,
            'picture'        => null,
            'saga_code'      => 'SAGA123',
            'id_number'      => 'STU001',
            'career_id'         => 1,
            'semester'       => 1,
            'remember_token' => null,
        ]);
    }
}

