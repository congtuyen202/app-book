<?php

namespace Database\Factories;

use App\Models\AiSummary;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiSummaryFactory extends Factory
{
    protected $model = AiSummary::class;

    public function definition(): array
    {
        $aiModels = ['GPT-3.5', 'Gemini-Pro', 'Claude-3-Haiku'];
        $summaryTypes = ['short', 'chapter', 'full-plot'];

        return [
            'book_id' => Book::inRandomOrder()->first()->id,
            'ai_model_name' => $this->faker->randomElement($aiModels),
            'summary_type' => $this->faker->randomElement($summaryTypes),
            'content' => $this->faker->paragraphs(mt_rand(3, 10), true),
            'token_count' => $this->faker->numberBetween(100, 1000),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}