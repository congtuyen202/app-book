<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $postTypes = ['text', 'image', 'link'];
        $visibility = ['public', 'friends', 'private'];

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'content' => $this->faker->paragraph(mt_rand(1, 5)),
            'book_id' => $this->faker->boolean(30) ? Book::inRandomOrder()->first()->id : null, // 30% bài viết liên quan đến sách
            'post_type' => $this->faker->randomElement($postTypes),
            'image_url' => $this->faker->boolean(40) ? $this->faker->imageUrl(640, 480, 'nature', true) : null,
            'visibility' => $this->faker->randomElement($visibility),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}