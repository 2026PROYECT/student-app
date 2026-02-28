<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'           => $this->faker->firstName(),
            'lastname'       => $this->faker->lastName(),
            'surname'        => $this->faker->optional()->lastName(),
            'email'          => $this->faker->unique()->safeEmail(),
            'password'       => self::$password ?: self::$password = Hash::make('pass1234'),
            'role'           => 'student',
            'is_admin'       => 0,
            'is_active'      => 1,
            'picture'        => null,
            'saga_code'      => 'SAGA' . Str::upper(Str::random(5)),
            'id_number'      => 'STU' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'career_id'         => null,
            'semester'       => null,
            'remember_token' => null,
        ];
    }
}

