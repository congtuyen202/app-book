<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory; // Dùng để tạo dữ liệu mẫu (Seeder)

    // Tên bảng trong database
    protected $table = 'bookai_books';

    // Các trường có thể được gán hàng loạt (Mass Assignable)
    protected $fillable = [
        'title',
        'original_title',
        'description',
        'published_year',
        'isbn',
        'language',
        'page_count',
        'cover_image_url',
        'avg_rating',
        'rating_count',
    ];

    // Định nghĩa mối quan hệ với Reviews
    public function reviews()
    {
        return $this->hasMany(Review::class, 'book_id'); // 'book_id' là tên cột khóa ngoại trong bảng bookai_reviews
    }

    // Định nghĩa mối quan hệ với AiSummaries
    public function aiSummaries()
    {
        return $this->hasMany(AiSummary::class, 'book_id');
    }

    // Định nghĩa mối quan hệ với Authors (nhiều nhiều)
    public function authors()
    {
        return $this->belongsToMany(Author::class, 'bookai_book_authors', 'book_id', 'author_id');
    }

    // Định nghĩa mối quan hệ với Genres (nhiều nhiều)
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'bookai_book_genres', 'book_id', 'genre_id');
    }
}