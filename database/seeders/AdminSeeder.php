<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'           => 'System',
            'lastname'       => 'Administrator',
            'surname'        => null,
            'email'          => 'admin@test.com',
            'password'       => Hash::make('pass1234'),
            'role'           => 'admin',
            'is_admin'       => 1,
            'is_active'      => 1,
            'picture'        => null,
            'saga_code'      => null,
            'id_number'      => 'ADMIN001',
            'career'         => null,
            'semester'       => null,
            'remember_token' => null,
        ]);
    }
}

