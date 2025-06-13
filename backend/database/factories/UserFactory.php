<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => \Hash::make('password'), // Mật khẩu mặc định là 'password'
            'full_name' => $this->faker->name(),
            'avatar_url' => $this->faker->boolean(70) ? $this->faker->imageUrl(100, 100, 'people', true) : null,
            'bio' => $this->faker->boolean(50) ? $this->faker->sentence(mt_rand(5, 15)) : null,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'email_verified_at' => now(), // Đặt là đã xác minh để đơn giản
            'remember_token' => Str::random(10),
            'last_login_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}