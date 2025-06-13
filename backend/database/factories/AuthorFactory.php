<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'bio' => $this->faker->boolean(70) ? $this->faker->paragraph(mt_rand(2, 5)) : null,
            'birth_date' => $this->faker->dateTimeBetween('-100 years', '-20 years'),
            'death_date' => $this->faker->boolean(30) ? $this->faker->dateTimeBetween('-20 years', 'now') : null,
            'nationality' => $this->faker->country(),
            'created_at' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
        ];
    }
}