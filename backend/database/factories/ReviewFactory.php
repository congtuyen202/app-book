<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Book; // Import Book model
use App\Models\User; // Import User model (giả định đã có)
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Lấy ngẫu nhiên một Book ID từ các sách đã có trong database
        $bookId = Book::inRandomOrder()->first()->id;
        // Lấy ngẫu nhiên một User ID từ các người dùng đã có trong database
        // Bạn cần đảm bảo đã có dữ liệu trong bảng bookai_users, nếu không hãy tạo UserFactory và UserSeeder trước.
        $userId = User::inRandomOrder()->first()->id;

        return [
            'user_id' => $userId,
            'book_id' => $bookId,
            'rating' => $this->faker->numberBetween(1, 5), // Điểm đánh giá từ 1 đến 5
            'review_text' => $this->faker->boolean(80) ? $this->faker->paragraphs(mt_rand(1, 3), true) : null, // 80% có nội dung review, 20% chỉ có rating
            'status' => $this->faker->randomElement(['approved', 'pending']), // Trạng thái review
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}