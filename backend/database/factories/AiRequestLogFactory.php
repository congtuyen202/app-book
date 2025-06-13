<?php

namespace Database\Factories;

use App\Models\AiRequestLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiRequestLogFactory extends Factory
{
    protected $model = AiRequestLog::class;

    public function definition(): array
    {
        $aiModels = ['GPT-3.5', 'Gemini-Pro', 'Claude-3-Haiku', 'Perplexity-API'];
        $requestTypes = ['summarize', 'content_generation', 'search_query', 'image_description', 'code_assist'];
        $statuses = ['success', 'failed', 'rate_limited'];

        return [
            'user_id' => $this->faker->boolean(90) ? User::inRandomOrder()->first()->id : null, // 90% yêu cầu có user
            'ai_model_name' => $this->faker->randomElement($aiModels),
            'request_type' => $this->faker->randomElement($requestTypes),
            'input_text' => $this->faker->sentence(mt_rand(5, 20)),
            'output_text' => $this->faker->paragraph(mt_rand(1, 5)),
            'tokens_used' => $this->faker->numberBetween(10, 500),
            'cost' => $this->faker->randomFloat(4, 0.0001, 0.0500),
            'status' => $this->faker->randomElement($statuses),
            'error_message' => $this->faker->boolean(10) ? $this->faker->sentence(mt_rand(5, 10)) : null, // 10% có lỗi
            'requested_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }
}