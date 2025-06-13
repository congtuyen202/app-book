<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review; // Import Model Review
use App\Models\Book;   // Import Model Book để cập nhật rating sau khi tạo review

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo 1000 bản ghi review sử dụng ReviewFactory
        // Đảm bảo bạn đã có dữ liệu trong bookai_books và bookai_users trước khi chạy seeder này.
        Review::factory()->count(1000)->create();

        // Sau khi tạo tất cả review, cập nhật avg_rating và rating_count cho mỗi cuốn sách
        // Đây là cách tối ưu để tránh nhiều lần truy vấn database trong vòng lặp tạo review
        Book::all()->each(function ($book) {
            $book->avg_rating = $book->reviews()->avg('rating') ?? 0.00; // Đảm bảo không lỗi nếu không có review
            $book->rating_count = $book->reviews()->count();
            $book->save();
        });
    }
}