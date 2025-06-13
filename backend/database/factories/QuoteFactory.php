<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteFactory extends Factory
{
    protected $model = Quote::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'book_id' => Book::inRandomOrder()->first()->id,
            'content' => $this->faker->paragraph(mt_rand(1, 3)),
            'page_number' => $this->faker->boolean(70) ? $this->faker->numberBetween(1, 500) : null,
            'context' => $this->faker->boolean(50) ? $this->faker->sentence(mt_rand(5, 10)) : null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}