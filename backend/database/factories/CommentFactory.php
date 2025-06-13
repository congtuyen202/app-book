<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use App\Models\Post;
use App\Models\Review; // Import Review model
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        $commentableType = $this->faker->randomElement(['post', 'review']); // Chọn comment cho post hoặc review

        $commentableId = null;
        if ($commentableType === 'post') {
            $commentableId = Post::inRandomOrder()->first()->id;
        } else { // 'review'
            $commentableId = Review::inRandomOrder()->first()->id;
        }

        $parentId = null;
        // 20% khả năng là comment lồng nhau
        if ($this->faker->boolean(20) && Comment::count() > 0) {
            $parentId = Comment::inRandomOrder()->first()->id;
        }

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'post_id' => ($commentableType === 'post') ? $commentableId : null,
            'review_id' => ($commentableType === 'review') ? $commentableId : null,
            'parent_comment_id' => $parentId,
            'content' => $this->faker->sentence(mt_rand(3, 10)),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}