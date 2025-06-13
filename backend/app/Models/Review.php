<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Tên bảng trong database
    protected $table = 'bookai_reviews';

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'review_text',
        'status',
    ];

    // Định nghĩa mối quan hệ với User (một review thuộc về một user)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // 'user_id' là tên cột khóa ngoại trong bảng bookai_reviews
    }

    // Định nghĩa mối quan hệ với Book (một review thuộc về một cuốn sách)
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id'); // 'book_id' là tên cột khóa ngoại trong bảng bookai_reviews
    }
}