<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingList extends Model
{
    use HasFactory;

    protected $table = 'bookai_reading_lists';
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'visibility',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'bookai_reading_list_books', 'reading_list_id', 'book_id')
                    ->withPivot(['status', 'started_reading_at', 'finished_reading_at']) // Lấy thêm các trường pivot
                    ->withTimestamps();
    }
}