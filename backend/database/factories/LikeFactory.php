<?php

namespace Database\Factories;

use App\Models\Like;
use App\Models\User;
use App\Models\Post;
use App\Models\Review;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    protected $model = Like::class;

    public function definition(): array
    {
        $likeable = $this->faker->randomElement([
            Post::inRandomOrder()->first(),
            Review::inRandomOrder()->first(),
            Comment::inRandomOrder()->first(),
        ]);

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'likeable_type' => $likeable::class,
            'likeable_id' => $likeable->id,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}