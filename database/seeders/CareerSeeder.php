<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('careers')->insert([
            ['career' => 'Ingenieria en Sistemas'],
            ['career' => 'Ingenieria Industrial'],
            ['career' => 'Ingenieria Civil'],
            ['career' => 'Ingenieria Comercial'],
            ['career' => 'Ingenieria Geografica'],
            ['career' => 'Ingenieria Mecatronica'],
            ['career' => 'Ingenieria Financiera'],
            ['career' => 'Ingenieria Ambiental'],
            ['career' => 'Ingenieria Petrolera'],
            ['career' => 'Ingenieria Sistemas Electronicos'],
        ]);
    }
}
