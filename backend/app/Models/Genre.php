<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $table = 'bookai_genres';
    protected $fillable = ['name'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'bookai_book_genres', 'genre_id', 'book_id');
    }
}