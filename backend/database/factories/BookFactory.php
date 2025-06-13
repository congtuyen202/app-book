<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Danh sách các ngôn ngữ phổ biến
        $languages = ['Vietnamese', 'English', 'French', 'German', 'Spanish', 'Japanese', 'Chinese'];

        return [
            'title' => $this->faker->sentence(mt_rand(3, 8)), // Tạo một câu ngẫu nhiên làm tiêu đề
            'original_title' => $this->faker->boolean(50) ? $this->faker->sentence(mt_rand(3, 8)) : null, // 50% có tiêu đề gốc
            'description' => $this->faker->paragraphs(mt_rand(2, 5), true), // Tạo 2-5 đoạn văn
            'published_year' => $this->faker->year(),
            'isbn' => $this->faker->unique()->isbn13(),
            'language' => $this->faker->randomElement($languages), // Chọn ngẫu nhiên ngôn ngữ
            'page_count' => $this->faker->numberBetween(100, 1000),
            'cover_image_url' => $this->faker->imageUrl(400, 600, 'books', true), // URL ảnh bìa giả
            'avg_rating' => $this->faker->randomFloat(2, 1, 5), // Điểm đánh giá từ 1 đến 5 với 2 chữ số thập phân
            'rating_count' => $this->faker->numberBetween(0, 5000), // Số lượt đánh giá
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}