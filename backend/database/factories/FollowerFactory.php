<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FollowerFactory extends Factory
{
    // Không có Model riêng cho Follower, nên không cần protected $model
    // Chúng ta sẽ tạo data trực tiếp trong Seeder hoặc bằng lệnh create
    public function definition(): array
    {
        // Tránh trường hợp người dùng tự follow chính mình
        $followerId = User::inRandomOrder()->first()->id;
        $followedId = User::where('id', '!=', $followerId)->inRandomOrder()->first()->id;

        return [
            'follower_id' => $followerId,
            'followed_id' => $followedId,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}