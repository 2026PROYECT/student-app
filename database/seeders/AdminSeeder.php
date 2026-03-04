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
            'name'      => 'System',
            'lastname'  => 'Administrator',
            'surname'   => null,
            'email'     => 'admin@test.com',
            'password'  => Hash::make('pass1234'),
            'role'      => 'admin',
            'picture'   => null,
            // These stay here if you kept them in the users migration:
            // 'is_admin'  => 1, 
            // 'is_active' => 1,
        ]);
    }
}